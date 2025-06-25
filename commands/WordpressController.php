<?php

namespace app\commands;

use app\components\services\ApiServiceManager;
use app\models\Loan;
use app\models\Person;
use yii\db\mssql\PDO;
use yii\helpers\Json;
use app\models\PartialData;
use yii;

class WordpressController extends \yii\console\Controller {

	private $servername = 'localhost';
	private $serverdb = 'finlatlv_db';
	private $username = 'finlatlv_user'; //'finlatlv';
	private $password = 'zU6xP7rM1l'; //'r2;qW92uPf!1YB';
	private $db;

	function initDB() {

		try {
			$this->db = new PDO( "mysql:host=$this->servername;dbname=$this->serverdb", $this->username, $this->password );
			$this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->db->exec( "set names utf8" );
		} catch ( PDOException $e ) {
			echo "Connection failed: " . $e->getMessage();
			exit;
		}
	}

	public function releaseResources() {
		$this->db = null;
	}

	public function getSQLStatement() {
		$statement ='select * from wp_jb_forms_datas where is_send = 0;';

		return $statement;
	}

	public function getLoans() {
		return $this->db->query( $this->getSQLStatement() )->fetchAll();
	}

	function isRealEstateLoan( $formData ) {
		$form_post_id = (int) $formData['type_id'];

		return $form_post_id === 7 // Kredīts pret nekustamo īpašumu
		       || $form_post_id === 8; // Кредит под залог недвижимости
	}

	/**
	 * Checks if a person Have a Real Estate
	 * */
	function personHaveRealEstate( $loan ) {
		// Vai Jums pieder nekustamais īpašums?
		return $loan['realEstate'] ?? null;
	}

	function makeAnotherLoanAsRealEstateLoan( $formData ) {
		$formData['type_id'] = 7; // make it as Real Estate Loan

		$data = Loan::mapFormToFields( $formData );
        $uniqueId     = $data['form_id'];

		$person = Person::find()->where( [ "unique_id" => $uniqueId ] )->one();

		$loan = new Loan();
		$loan->load( $data );
		$loan->person_id     = $person->id;
		$loan->status        = Loan::STATUS_NEW;
		$loan->reminder_time = null;
		$loan->update_time   = time();
		$loan->unique_id     = time() . '_' . $uniqueId;

		if ( $data['Loan'] && $loan->save() ) {
			echo Json::encode( [ "status" => true, "save" => true ] ) . "\r\n";
			try {
				$this->_log( "update save - " . print_r($data, true) );
			} catch ( \Exception $e ) {
				echo $e->getMessage() . ' ' . $e->getTrace();
			}
		} else {
			$person->delete();

			print_r( $loan->getErrors() );
		}
	}

	function ifPersonHaveRealEstateMakeAnotherLoanAsRealEstateLoan( $formData ) {
		if ( $this->personHaveRealEstate( $formData ) ) {
			if ( ! $this->isRealEstateLoan( $formData ) ) {
				$this->makeAnotherLoanAsRealEstateLoan( $formData );
			}
		}
	}

	public function actionIndex() {

		$this->initDB();
		$rows = $this->getLoans();
	
		foreach ( $rows as $formData ) {
		
				// Make a loan and a person
				$createLoanSuccess = $this->post( Loan::mapFormToFields( $formData ) );
					// print_r($createLoanSuccess);
					// exit;
				if ( $createLoanSuccess ) {
					// Checkbox Real Estate checked -> make another loan as Real Estate loan
					$this->ifPersonHaveRealEstateMakeAnotherLoanAsRealEstateLoan( $formData );
				}	
			
		}
		$this->actionPartialdata(); 
		$this->releaseResources();
	}

	public function post( $data ) {
		// print_r($data);
		// exit;
		$uniqueId     = $data['form_id'];
		print_r("Starting post function with uniqueId: $uniqueId\n");
		$loanExists   = Loan::find()->where( [ "unique_id" => $uniqueId ] )->one();
		$personExists = Person::find()->where( [ "unique_id" => $uniqueId ] )->one();

		if ( $uniqueId && $loanExists ) {	
			echo "Loan exists. Name: " . $loanExists->person->name . ", ID: $loanExists->id Eur, form_post_id: " . $data["id"] . " uniqueId: $uniqueId \r\n";
			$this->sendErrorEmail($data, "Loan exists.  form_post_id: " . $data["id"] . " uniqueId: $uniqueId \r\n");
			$this->updateClientDB( $data );
			return false;
		}
		if ( $uniqueId && $personExists ) {		
			echo "Person exists. Name: " . $personExists->name . ", ID: $personExists->id Eur, form_post_id: " . $data["id"] . "  \r\n";
			$this->sendErrorEmail($data, "Person exists. form_post_id: " . $data["id"] . " uniqueId: $uniqueId \r\n");
			$this->updateClientDB( $data );
			return false;
		}
		// print_r("Data for Person:\n");
   		//  print_r($data);
		$person = new Person();
		$person->load( $data );
		$person->unique_id = $uniqueId;
		// Check if Person has errors
		$person->validate();
		echo ' Person Errors --- ' .  json_encode($person->errors) . "\r\n";
		$person->save();

		$loan = new Loan();
		$loan->load( $data );
		$loan->person_id     = $person->id;
		$loan->status        = Loan::STATUS_NEW;
		$loan->reminder_time = null;
		$loan->update_time   = time();
		$loan->unique_id     = $uniqueId;
		$loan->source     = $data['Loan']['source'];
		$loan->save();

		if ( $data['Loan'] && $loan->save() ) {
			echo Json::encode( [ "status" => true, "save" => true ] ) . "\r\n";
			try {
				$this->_log( "update save - " .  print_r($data, true) );
			} catch ( \Exception $e ) {
				echo $e->getMessage() . ' ' . $e->getTrace();
			}
			$this->updateClientDB( $data );
			try {
				$apiServiceManager = new ApiServiceManager( $loan );
				$apiServiceManager->trySendApiRequest();
				//$this->sendErrorEmail($data, $e->getMessage());
			} catch ( \Exception $e ) {
				print_r( 'Error while trying to send Api Request: ' . $e );
				$this->sendErrorEmail($data, $e->getMessage());
			}
			return true;
		} else {
			try {
				$person->delete();
				// echo "ramesh";
				print_r( $loan->getErrors() );
				return false;
			} catch (Exception $e) {
			$this->sendErrorEmail($data, $e->getMessage());
			
			}		
		}
	}

	private function _log( $msg ) {
		$fd  = fopen( \Yii::$app->params["api_data_log_path"], "a+" );
		$str = "[" . date( "Y/m/d h:i:s", time() ) . "] " . $msg;
		fwrite( $fd, $str . "\n" );
		fclose( $fd );
	}

	public function updateClientDB( $data ) {
		$id        = $data['id'];
		print_r($id);
		$statement = "UPDATE wp_jb_forms_datas SET is_send = 1 WHERE form_id = '" . $id . "'";

		$this->db->exec( $statement );
	}

	private function getPartialData() {
	    $statement = 'SELECT * FROM wp_multi_step_form_datas WHERE is_send = 0 ORDER BY id DESC;';
	    return $this->db->query($statement)->fetchAll();
	}


	public function actionPartialdata() {
	    //$this->initDB();
	    $data = $this->getPartialData();

	    try {
	        foreach ($data as $row) {
	            if (isset($row['form_data'])) {
	                $unserializedData = unserialize($row['form_data']);
	                $options = json_decode($unserializedData['_wpcf7cf_options'], true);
	                $formId = $options['form_id'] ?? null;
	                $email = $unserializedData['email'] ?? null;
	                $time = $row['time'] ?? null; 
                  	$phone = $unserializedData['phone'] ?? null;

	                 if (($email == null || $email == '') && ($phone == null || $phone == '')) {
	                    echo "Both email and phone are null for row with ID: " . $row['id'] . ", skipping.\n";
	                    continue;
	                }
	                
	                $existingRecord = PartialData::find()
	                    ->where(['form_id' => $formId, 'email' => $email])
	                    ->one();
	                if (!$existingRecord) {
	                    $partialData = new PartialData();
	                    $partialData->unique_id = $row['id'] ?? null; 
	                    $partialData->phone = $unserializedData['phone'] ?? null;
	                    $partialData->email = $email;
	                    $partialData->user_name = $unserializedData['user-name'] ?? null;
	                    $partialData->user_surname = $unserializedData['user-surname'] ?? null;
	                    $partialData->personal_code = $unserializedData['personal-code'] ?? null;    
	                    $partialData->form_id = $formId;
	                    $partialData->time = $time;
	                    $partialData->created_date = date('Y-m-d H:i:s');
	                    $partialData->updated_date = date('Y-m-d H:i:s');
	                    $partialData->is_send = 0; 

	                    if ($partialData->save()) {
	                        $this->updateIsSend($row['id']);
	                        echo "Partial data inserted succcessfully".$row['id'];
	                    } else {
	                        print_r($partialData->getErrors());
	                    }
	                } else {
	                    echo "Record with form_id: $formId and email: $email already exists. Skipping.\n";
	                }
	            } else {
	                echo "No form_data for row with ID: " . $row['id'] . "\n"; 
	            }
	        }
	    } catch (Exception $e) {
	        echo "An error occurred: " . $e->getMessage() . "\n";
	    }

	    $this->releaseResources();
	}

	private function updateIsSend($formId) {
	    $statement = "UPDATE wp_multi_step_form_datas SET is_send = 1 WHERE id = :formId";
	    $stmt = $this->db->prepare($statement);
	    $stmt->execute([':formId' => $formId]);
	}

	 protected function sendErrorEmail($data, $error) {

	 	//echo "test";
		    $errorDetails = "Below form and its data are not stored in the backend, due to some error: $error\n";    
		    $personData = isset($data['Person']) ? $data['Person'] : [];
		    $fields = [
		        'First Name' => 'name',
		        'Surname' => 'surname',
		        'E-mail' => 'email',
		        'Phone' => 'phone',
		        'Personal Code' => 'personal_code',
		        'Monthly Income' => 'salary',
		        'Payment on Existing Loans' => 'outcome',
		        'Dependants' => 'dependants',
		        'Declared Place of Residence' => 'town',
		        'Place of Work' => 'workplace',
		        'Position' => 'position',
		        'Length of Service' => 'length_of_service',
		        'Address' => 'address',
		        'Credit History' => 'credit_history',
		    ];
		    $emailContent = "Hello Admin,\n\n$errorDetails\nForm ID: " . (isset($data['form_id']) ? $data['form_id'] : 'N/A') . "\n\nPerson Details:\n";
		    foreach ($fields as $label => $field) {
		        $emailContent .= "$label: " . (isset($personData[$field]) ? $personData[$field] : 'N/A') . "\n";
		    } 
			    try {
			    Yii::$app->mailer->compose()
			        ->setFrom('info@finlat.lv')
			        //->setTo(['ramesh.bharvad@iflair.com','mitanshi.raval@iflair.com'])
			        ->setTo(['silva.luchaninova@finlat.lv', 'info@finlat.lv', 'pieteikums@finlat.lv', 'it@finlat.lv', 'info@itmarketing.lv'])
			        ->setSubject('Error while storing form data')
			        ->setTextBody($emailContent)
			        ->send();
			} catch (\Exception $e) {
			    Yii::error('Failed to send email: ' . $e->getMessage());
			}
		}

}
