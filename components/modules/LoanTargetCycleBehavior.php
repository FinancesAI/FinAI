<?php 
namespace app\components\modules;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Loan;

class LoanTargetCycleBehavior extends Behavior
{
	public function events()
	{
		return [
				ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
				ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
		];
	}
	
	//http://p.trackmytarget.com/?campaignID=zwpckb&productID=v04ae0&conversionType=lead&transactionID={transactionID}&tmtData={tmtData} - 1lizings
	//http://p.trackmytarget.com/?campaignID=vggfyv&productID=zk6zk7&conversionType=lead&transactionID={transactionID}&tmtData={tmtData} - 1aizdevums
	public function afterInsert($event)
	{
		if ($event->sender->query_string) {
			parse_str($event->sender->query_string, $array);
			if (isset($array["tmtData"]) && $event->sender->source) {
				if ($array["tmtData"] !== "") {
					if (Loan::find()->where(["person_id" => $event->sender->person_id])->count() == 1 && $event->sender->source == "onefinance" || $event->sender->source == "1lizings") {
						$this->_postBack([
								"campaignID" => $this->_campaignIdByTitle($event->sender->source),
								"productID" => $this->_productIdByTitle($event->sender->source, $event->sender->product),
								"conversionType" => "lead",
								"transactionID" => $event->sender->id,
								"tmtData" => $array["tmtData"],
						]);
						$event->sender->is_tc_sent = 1;
						$event->sender->save();
					}
				}
			}
		}
	}
	
	//http://p.trackmytarget.com/?campaignID=zwpckb&productID=v04ae0&conversionType=sale&transactionID={transactionID}&transactionAmount={transactionAmount}&currency=EUR&tmtData={tmtData}
	//http://p.trackmytarget.com/?campaignID=vggfyv&productID=zk6zk7&conversionType=sale&transactionID={transactionID}&transactionAmount={transactionAmount}&currency=EUR&tmtData={tmtData}
	public function afterUpdate($event)
	{
		if ($event->sender->query_string) {
			parse_str($event->sender->query_string, $array);
			if (isset($array["tmtData"]) && $event->sender->source) {
				if ($event->sender->status == Loan::STATUS_CLOSED && $event->sender->update_time == $event->sender->close_time && $event->sender->is_tc_sent) {
					if ($array["tmtData"] !== "") {
						$this->_postBack([
								"campaignID" => $this->_campaignIdByTitle($event->sender->source),
								"productID" => $this->_productIdByTitle($event->sender->source, $event->sender->product),
								"conversionType" => "sale",
								"transactionID" => $event->sender->id,
								"transactionAmount" => $event->sender->getCeo($event->sender),
								"currency" => "EUR",
								"tmtData" => $array["tmtData"],
						]);
					}
				}
			}
		}
	}
	
	private function _campaignIdByTitle($title) {
		$campaign = "";
		if ($title == "1lizings") {
			$campaign = "zwpckb";
		}
		if ($title == "1aizdevums") {
			$campaign = "vggfyv";
		}
		if ($title == "onefinance") {
			$campaign = "xd442c";
		}
		return $campaign;
	}
	
	/**
	 * one finance
	 * Autolīzings = Auto Lease = 558h80
		Naudas kredīts = Personal Loans = pcm9rv
		Kredītu apvienošana = Refinance = p1fft8
		Ārzemēs strādājošajiem = Default = btawez
		Aizdevums pret auto = Auto Credit = kbpfce

	 * @param unknown $title
	 * @param unknown $productId
	 * @return string
	 */
	private function _productIdByTitle($title, $productId = null) {
		$product = "";
		if ($title == "1lizings") {
			$product = "v04ae0";
		}
		if ($title == "1aizdevums") {
			$product = "zk6zk7";
		}
		if ($title == "onefinance") {
			$product = "btawez";

// 			if ((int)$productId == 1) {
// 				$product = "558h80";
// 			}	
// 			if ((int)$productId == 2) {
// 				$product = "pcm9rv";
// 			}	
// 			if ((int)$productId == 3) {
// 				$product = "p1fft8";
// 			}	
// 			if ((int)$productId == 5) {
// 				$product = "kbpfce";
// 			}
		}
		return $product;
	}
	
	private function _postBack($data) {
		$fullUrl = \Yii::$app->params["postbackurl"]."?".http_build_query($data);
		
		$result = @file_get_contents($fullUrl);
		if ($result) {
			$this->_log("OK - ".$fullUrl." - ANSWER - ".json_encode($result));
		} else {
			$this->_log("ERROR - ".$fullUrl);
		}
	}
	
    private function _log($msg) {
		$fd = fopen(\Yii::$app->params["postback_log_path"], "a+");
    	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg;
    	fwrite($fd, $str . "\n");
    	fclose($fd);
    }
}