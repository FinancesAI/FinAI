<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\jobs\InbankCheckStatusJob;
use Yii;
use yii\console\Controller;

use app\models\Loan;
use app\models\Person;
use app\models\LoanExtra;
use app\components\XLSXWriter;
use yii\helpers\Json;
use app\models\User;
use app\components\api\Ntlmservice;
use app\components\api\AizdevumsApi;
use app\components\SolrDataProvider;
use app\models\LoanProgerss;
use app\controllers\MailController;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LoanController extends Controller
{
	
	private $writeArray = [];

    public function actionInbankQueue($id)
    {
        Yii::$app->queue->push(new InbankCheckStatusJob([
            'loanId' => $id,
        ]));
    }

    public function actionAutoReject()
	{
		$loans = Loan::find()->where(["status" => 1])->andWhere(["<", "update_time", strtotime("-2 weeks")])->all();
		foreach ($loans as $loan) {
			$loan->description = "Auto reject no activity last two weeks";
			$loan->status = Loan::STATUS_REJECTED;
			
			try {
				$loan->afterFind();
				if (MailController::send($loan, "25")) {
					$loan->save();
				}
			} catch(\Exception $e) {
				
			}
			//echo $loan->id."\r\n";
		}
	}
	
    /**
     * This command updates waiting time only for loans with no changes
     */
    public function actionWaitingTime()
    {
    	return;
    	
    	$newDataProvider = new SolrDataProvider();
    	$newDataProvider->solr->setCollectionUrlByType(2);
    	$newDataProvider->setClassName("app\models\Loan");
    	$newDataProvider->solr->setQuery('status:0 AND user_id:0');
    	$newDataProvider->solr->setOrder("waiting_time desc");
    	$loans = $newDataProvider->getModels();
    	
		foreach ($loans as $loan) {
			$loan->detachBehaviors();

			if ($loan->create_time == $loan->update_time) {
				$time = $loan->create_time;
			} else {
				$time = $loan->update_time;
			}
			
			$loan->waiting_time = round(abs(time() - $time) / 60);
			$loan->save();
			//echo $loan->id." updated  ".$loan->waiting_time."\r\n";
		}
    }
    
    public function actionReminderTime() {
    	$loans = Loan::find()->where(["<", "reminder_time", time()])->all();
    	foreach ($loans as $loan) {
    		$loan->skipReminder = true;
			//$loan->status = Loan::STATUS_NEW;
			$loan->reminder_time = null;
			$loan->save();
			//echo $loan->id." updated\r\n";
    	}
    }
    
    public function actionEfinanceStatusRefresh()
    {
    	$loans = Loan::findBySql("SELECT loan.* FROM loan INNER JOIN efinance_api ON loan.id=efinance_api.loan_id WHERE loan.status!=4 AND efinance_api.is_updated=0")->all();
    	 
    	foreach ($loans as $loan) {
    		$api = \Yii::$app->getDb()->createCommand("SELECT * FROM `efinance_api` WHERE `loan_id` = ".$loan->id)->queryOne();
    		if ($api) {
    			//var_dump($loan->id);
    			$customerId = null;
    			$note = null;
    			$contractId = $api["contract_id"];
    			$personalCode = $loan->person->personal_code;
    			
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_URL, \Yii::$app->params["efinanceapi_url_check"]);
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    			curl_setopt($ch, CURLOPT_POST,1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    					"login" => \Yii::$app->params["efinanceapi_login"],
    					"pass" => \Yii::$app->params["efinanceapi_pass"],
    					"data" => json_encode([
    							"person_code" => $personalCode
    					])
    			]));
    			$result = curl_exec($ch);
    			$clientList = json_decode($result, true);
    			
    			if (isset($clientList["customers"])) {
    				foreach ($clientList["customers"] as $customer) {
    					if ($personalCode == $customer["person_code"]) {
    						$customerId = $customer["customer_id"];
    						$note = $customer["notes"];
    						break;
    					}
    				}
    			}
    			
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_URL, "http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.getCustomerLoanList");
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    			curl_setopt($ch, CURLOPT_POST,1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    					"login" => \Yii::$app->params["efinanceapi_login"],
    					"pass" => \Yii::$app->params["efinanceapi_pass"],
    					"data" => json_encode([
    							"customer_id" => $customerId
    					])
    			]));
    			$result = curl_exec($ch);
    				//var_dump($result);
    				
    			$clientList = json_decode($result, true);
    				
    			if (isset($clientList["customerLoans"])) {
    				foreach ($clientList["customerLoans"] as $update) {
    					if ($update["credit_id"] == $contractId) {
    						if ($update["c_confirmed"] == "1") {
    							$loan->description = "EFinance api - Status: accepted - Notes about person: ".$note;
    							$loan->status = Loan::STATUS_NEW;
    							$loan->waiting_time = 1;
    							$isUpdate = true;
    						}
    				
    						if ($update["c_confirmed"] == "-1") {
    							$loan->description = "EFinance api - Status: rejected - Notes about person: ".$note;
    							$loan->status = Loan::STATUS_NEW;
    							$loan->waiting_time = 1;
    							$isUpdate = true;
    						}
    						break;
    					}
    				}
    				if (isset($clientList["customerRejectedLoans"])) {
    					foreach ($clientList["customerRejectedLoans"] as $update) {
    						if ($update["id"] == $contractId) {
    							$loan->description = "EFinance api - Status: rejected - Reason: ".(isset($update["reason"]) ? $update["reason"] : null)." - Notes about person: ".$note;
    							$loan->status = Loan::STATUS_NEW;
    							$loan->waiting_time = 1;
    							$isUpdate = true;
    							break;
    						}
    					}
    				}

    				if (isset($isUpdate)) {
    					$loan->save();
    					\Yii::$app->getDb()->createCommand("UPDATE `efinance_api` SET `is_updated` = 1, `status_desc` = '".(isset($update["reason"]) ? $update["reason"] : null)."' WHERE `efinance_api`.`id` = {$api["id"]};")->execute();
    				}
    				
    			}
    		}
    	}
    }
    
    public function actionAizdevumsStatusRefresh()
    {

	    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		    echo 'This is a server using Windows!';
		    $lockFile = 'lock.txt';
	    } else {
		    $lockFile = '/tmp/lock.txt';
	    }
    	$fp = fopen($lockFile, "w+");

    	if (flock($fp, LOCK_EX | LOCK_NB)) { // do an exclusive lock

    		require(__DIR__ . '/../components/api/AizdevumsApi.php');

    		$login = \Yii::$app->params["aizdevumsapi_login"];
    		$password = \Yii::$app->params["aizdevumsapi_pass"];
    		$save_wsdl_path = __DIR__.'/../components/api/wsdl.xml';
    		$wsdl = \Yii::$app->params["aizdevumsapi_url"];

    		$service = new Ntlmservice($wsdl, [
    				'trace' => 1,
    				'login' => $login,
    				'password' => $password,
    				'url' => $wsdl,
    				'local_path' => $save_wsdl_path,
    				'cache_wsdl' => WSDL_CACHE_NONE,
    		]);

    		$loans = Loan::findBySql("SELECT loan.* FROM loan INNER JOIN aizdevums_api ON loan.id=aizdevums_api.loan_id WHERE loan.status=1")->all();

    		foreach ($loans as $loan) {
    			$api = \Yii::$app->getDb()->createCommand("SELECT * FROM `aizdevums_api` WHERE `loan_id` = ".$loan->id)->queryOne();
    			if ($api) {
    				$response = $service->CHECKSTATUS([
    						'webAppNo' => $api["contract_id"],
    						'responseXML' => '',
    				]);

    				$responseStatus = $response->responseXML->Status;
    				$responseComment = $response->responseXML->Comment;

    				if ($api["status"] !== $responseStatus || $api["status_desc"] !== $responseComment) {
    					$text = "Credico api update - Status: ".$responseStatus." Comment: ".$responseComment;

    					$loan->description = $text;
    					$loan->status = Loan::STATUS_NEW;
    					$loan->waiting_time = 1;
    					//$loan->create_time = time();

    					if ($loan->save()) {
    						//echo "saved: ".$loan->id;
    						\Yii::$app->getDb()->createCommand("UPDATE `aizdevums_api` SET `status` = ".\Yii::$app->db->quoteValue($responseStatus).", `status_desc` = ".\Yii::$app->db->quoteValue($responseComment)." WHERE `aizdevums_api`.`id` = {$api["id"]};")->execute();
    					}
    				} else {
    					//echo "nothing to update: ".$loan->id;
    				}
    			}
    		}

    		flock($fp, LOCK_UN); // release the lock
    	} else {
    		//echo "Couldn't get the lock!";
    	}

    	fclose($fp);
    }
    
    /**
     * This command updates inbank status manual to positive or negative
     */
    public function actionInbankStatusRefresh()
    {
		//echo "Script start\r\n";
    	
    	$loans = Loan::find()
		->joinWith(["person"])
		->where("loan.create_time > ".strtotime("-1 week"))
		->andWhere(["person.credit_history" => 3])
		->andWhere(["in", "loan.status", [0,1]])
		->all();
		
		$contracts = "";
		$contractsArray = [];
		foreach ($loans as $loan) {
			if ($loan->extra && $loan->extra->inbank_contract_id) {
				$contracts .= $loan->extra->inbank_contract_id.",";
				$contractsArray[substr($loan->extra->inbank_contract_id, 1)] = $loan;
			}
		}
		
		$contracts = rtrim($contracts, ",");
		
		if (!$contracts)
			return true;
		
		//echo "Curl start\r\n";
		
		$ch = curl_init(\Yii::$app->params["inbankapi_url"]."/applications/statuses");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, ["application_numbers" => $contracts]);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Authorization: '.\Yii::$app->params["inbankapi_key"],
				'Accept-Version:3',
			]
		);
		
		$result = curl_exec($ch);
		//var_dump($contracts);
		if ($result !== false){
			$responseJson = json_decode($result);

			foreach ($responseJson->statuses as $status) {
				//var_dump(substr($status->application_number, 1));
				if (!property_exists($status, "decision")) {
					//echo "no decision\r\n";
					continue;
				}
				if ($status->decision->status == "positive") {
					if (isset($contractsArray[substr($status->application_number, 1)])) {
						$updateLoan = $contractsArray[substr($status->application_number, 1)];
						$updateLoan->person->credit_history = $this->_getCreditHistory($status->decision->status);
						$updateLoan->extra->api_status_description = $status->decision->message;
						$updateLoan->status = 0;
						$updateLoan->waiting_time = 1;
						//$updateLoan->create_time = time();
						
						$updateLoan->extra->save();
						$updateLoan->person->save();
						$updateLoan->save();
						//var_dump($status);
						
						//echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! UPDATE ".$updateLoan->id." !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\r\n";
					} else {
						//var_dump($status);
						//echo "no contract\r\n";
					}
				} else {
					//echo "status still manual\r\n";
				}
			}
		}
		
		curl_close($ch);
		
		//echo "Script end\r\n";
    }
    

    /**
     * positive, negative, manual
     * @param string $title
     * @return integer
     */
    private function _getCreditHistory($title) {
    	if ($title == "positive")
    		return 1;
    
    	if ($title == "negative")
    		return 2;
    
    	if ($title == "manual")
    		return 3;
    
    	if ($title == "error")
    		return 4;
    
    	return 0;
    }
    
    /**
     * Generates export file and sends email
     * @param unknown $needHeaders
     * @param unknown $attrs
     * @param unknown $format
     * @return void
     */
    public function actionExport($needHeaders, $partners, $format, $userId)
    {
   		$dataProvider = unserialize(file_get_contents(\Yii::getAlias('@app')."/export_object"));
   		$attrs = unserialize(file_get_contents(\Yii::getAlias('@app')."/export_post"));

		$isSolr = (property_exists($dataProvider, "query") ? false : true);
		if ($isSolr) {
			$dataProvider->solr->setCustomFilter("rows", 50);
			$dataProvider->solr->retrieve();
				
			$count = $dataProvider->solr->itemsFound();
		} else {
			$count = $dataProvider->query->count();
		}
		echo $count;
   		$limit = 50;
   		
   		if (file_exists(__DIR__.'/../output.'.$format))
   			unlink(__DIR__.'/../output.'.$format);
   		
   		$headerAdd = false;
   			
   		for ($i = 0; $i < $count/$limit; $i++) {
   			$offset = $i*$limit;
   			echo $offset;
   			if ($isSolr) {
   				$exportData = $dataProvider->solr->setOffset($offset);
   				$exportData = $dataProvider->solr->setLimit($limit);
   				$dataProvider->solr->retrieve();
   				$dataProvider->refresh();
   				$exportData = $dataProvider->getModels();
   			} else {
   				$exportData = $dataProvider->query->offset($offset)->limit($limit)->all();
   			}
   			 
   			$dateFormat = "d.m.Y";
   			$timeFormat = "H:m:s";
   			 
   			$writeArray = [];
   			foreach ($exportData as $data) {
   				$writeData = [];
   				foreach ($attrs as $attrKey => $attrValue) {
   					if ((strpos($attrKey, '_time') !== false)) {
   						if ($attrKey !== "waiting_time") {
   							$value = $data->getValue($attrKey, $data);
   							$writeData[str_replace("_time", "_date", $attrKey)] = ($value ? date($dateFormat, $value) : null);
   							$writeData[str_replace("_time", "_time", $attrKey)] = ($value ? date($timeFormat, $value) : null);
   							continue;
   						}
   					}
   					 
   					if (strpos($attrKey, 'person.') !== false) {
   						$writeData[$attrKey] = $data->person->getValue(str_replace("person.", "", $attrKey), $data->person);
   					} elseif (strpos($attrKey, 'loan.') !== false) {
   						$writeData[$attrKey] = $data->loan->getValue(str_replace("loan.", "", $attrKey), $data->loan);

   					} else {
   						if ((strpos($attrKey, 'bill_amount') !== false)) {
   							$billAmount = $data->getValue($attrKey, $data);
   							$writeData[$attrKey] = ($billAmount ? number_format($billAmount/1.21,2) : $billAmount);
   						} else {
   							$writeData[$attrKey] = $data->getValue($attrKey, $data);
   						}
   					}
   				}
   				if ($partners) {
   					//add partner data
   					$progresses = $data->getProgressFormated();
   					foreach ($data->getInProgress() as $progressId => $progressTitle) {
   						if ($progressId == 0 || $progressId == 10) {
   							continue;
   						}
   						if (isset($progresses[$progressId])) {
   							$progressData = $progresses[$progressId];
   							$writeData[$progressTitle." status"] = LoanProgerss::getStatuses()[$progressData->status];
   							$writeData[$progressTitle." amount"] = $progressData->amount;
   							$writeData[$progressTitle." text"] = $progressData->text;
   						} else {
   							$writeData[$progressTitle." status"] = "";
   							$writeData[$progressTitle." amount"] = "";
   							$writeData[$progressTitle." text"] = "";
   						}
   					}
   				
   				}
   				
   				 
   				if (!$headerAdd && $needHeaders) {
   					$keys = [];
   					foreach ($writeData as $key => $value) {
   						$keys[] = $key;
   					}
   					if ($partners) {
   						
   						if ($data) {
   							foreach ($data->getInProgress() as $progressId => $progressTitle) {
   								if ($progressId == 0 || $progressId == 10) {
   									continue;
   								}
   								$keys[] = $progressTitle." status";
   								$keys[] = $progressTitle." amount";
   								$keys[] = $progressTitle." text";
   							}
   						}
   						
   					}
   					$writeArray[] = $keys;
   					$headerAdd = true;
   				}
   					
   				$writeArray[] = $writeData;
   			}
   			$this->_writeExport($format, $writeArray);
   		}
   		
   		if ($format == "xlsx") {
   			$writer = new XLSXWriter();
   			$writer->writeSheet($this->writeArray);
   			$writer->writeToFile(__DIR__.'/../output.xlsx');
   		}
   		
   		$user = User::find()->where(["id" => $userId])->one();
   		
   		if (isset(\Yii::$app->params["mailer"]["system"])) {
	    	\Yii::$app->getMailer()->setTransport(\Yii::$app->params["mailer"]["system"]);
	    	$mailer = \Yii::$app->mailer->compose()
	    	->setFrom('system@onefinance.lv')
	    	->setTo($user->email)
	    	->setSubject("Exports gatavs")
	    	->setHtmlBody("Tavs exports gatavs: <a href='https://server.lv/download/index/?file=output.".$format."'>Spied šeit lai lejupielādētu</a><br/><small>Lai lejupielādētu failu tev ir jābūt autorizētam sistēmā.</small>")->send();
   		}
   		
   		unlink(\Yii::getAlias('@app')."/export_object");
   		unlink(\Yii::getAlias('@app')."/export_post");
    }
    
    private function _writeExport($format, $writeArray) {
    	if ($format == "xlsx") {
			$this->writeArray = array_merge($this->writeArray, $writeArray);
    	}
    	 
    	if ($format == "csv") {
    		$file = fopen(__DIR__.'/../output.csv', "a+");
    	
    		foreach ($writeArray as $line)
    			fputcsv($file, $line, "\t");
    					
    		fclose($file);
    	}
    }
}
