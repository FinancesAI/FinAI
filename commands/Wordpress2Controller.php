<?php

namespace app\commands;

use app\components\services\ApiServiceManager;
use app\models\Loan;
use app\models\Person;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Wordpress2Controller extends Controller {

	private string $servername = '207.154.249.174';
	private string $database = 'ewjmdrmhwt';
	private string $username = 'ewjmdrmhwt';
	private string $password = 'ZpMX6rTpe8';
	private ?Connection $db = null;

	public function initDB() {
		$this->db = new Connection([
			'dsn' => "mysql:host=$this->servername;dbname=$this->database",
			'username' => $this->username,
			'password' => $this->password,
			'charset' => 'utf8',
		]);
		try {
			$this->db->open();
		} catch ( Exception $e ) {
			echo "Connection failed: " . $e->getMessage();
			exit;
		}
	}

	public function formIds(): array {
		return [
			11
		];
	}

	public function getSubmissions(): array {
		$query = $this->db->createCommand(
			'SELECT * FROM `wp_wsf_submit` WHERE form_id IN (:ids) and starred = 0'
		);
		$query->bindValue(':ids', $this->formIds());
		try {
			$submissions = $query->queryAll();
		} catch ( Exception $e ) {
			echo "Submissions query failed: " . $e->getMessage();
			exit;
		}
		foreach ($submissions as $submission) {
			$submission['@meta'] = [];
			$query = $this->db->createCommand(
				'SELECT * FROM `wp_wsf_submit_meta` WHERE parent_id = :id'
			);
			$query->bindValue(':id', $submission['id']);
			try {
				$metaFields = $query->queryAll();
			} catch ( Exception $e ) {
				echo "Submission meta query failed: " . $e->getMessage();
				exit;
			}
			foreach ($metaFields as $metaField) {
				$submission['@meta'][$metaField['meta_key']] = $metaField;
			}
		}
		return $submissions;
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
		return $loan['realEstate'];
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
			echo Json::encode( [ "status" => true, "save" => true ] ) . '\r\n';
			try {
				$this->_log( "update save - " . json_encode( $data ) );
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

	protected function wsFormFieldMap(): array {
		return [
			272 => 'loan.term',
			272 => 'loan.term',
		];
	}

	public function actionIndex() {
		$this->initDB();

		foreach ( $this->getSubmissions() as $submission ) {
			$createLoanSuccess = $this->post(
				[
					"Loan"    => [
						"amount"        => (double) ArrayHelper::getValue( $submission['@meta'], 'field_179', 0 ),
						"term"          => (int) ArrayHelper::getValue( $submission['@meta'], 'field_180', 0 ),
						"source"        => $formToSource[$formData['form_post_id']],
						"product"       => 1,
						"description"   => '',
						"first_payment" => 0,
						"referral"      => self::prepareReferral($loan),
						"query_string"  => '',
						"ip_ountry"     => '',
						"type"          => ArrayHelper::getValue( $submission['@meta'], 'post_id' ),
						"tmt_data"      => '',

						"real_estate_object"  => self::getSafeParam( $loan['type'] ),
						"property_address"    => self::getSafeParam( $loan['mesto'] ),
						"legal_entity_number" => self::getSafeParam( $loan['reg'] ),
						"insurance_type"      => self::getSafeParam( $loan['tip'][0] ),

						"car_value"          => self::getSafeParam( $loan['car_value'] ),
						"autoregnr"          => self::getSafeParam( $loan['autoregnr'] ),
						"year"               => self::getSafeParam( $loan['god'] ),
						"techpass"           => self::getSafeParam( $loan['techpass'] ),
						"car_insurance_term" => self::getSafeParam( $loan['srok'] ),

						"company_name"      => ArrayHelper::getValue( $submission['@meta'], 'field_184' ),
						"deposit"           => self::getSafeParam( $loan['deposit'] ),
						"revenue"           => self::getSafeParam( $loan['gada'][0] ),
						"amount_taken"           => (double) self::getSafeParam( $loan['suma'] ), //+
						'company_length_of_service' => (int) self::getSafeParam( $loan['darbojas'][0] ), //
						'document' => self::getSafeParam( $loan['doc'] ),
//                'realEstate'   => self::getSafeParam( $loan['nomerdebit'] ),
						'realEstate'   => self::getSafeParam( $loan['checkbox-470'][0] ),
					],
					"Person"  => [
						"name"            => ArrayHelper::getValue( $submission['@meta'], 'field_176' ),
						"surname"         => ArrayHelper::getValue( $submission['@meta'], 'field_175' ),
						"personal_code"   => ArrayHelper::getValue( $submission['@meta'], 'field_178' ),

						"phone"           => ArrayHelper::getValue( $submission['@meta'], 'field_172' ),
						"email"           => ArrayHelper::getValue( $submission['@meta'], 'field_173' ),
						"salary"          => ArrayHelper::getValue( $submission['@meta'], 'field_185' ),
						'workplace'         => self::getSafeParam( $loan['mestorab'] ) ?: self::getSafeParam( $loan['darbavietas'] ),//+
						"income"          => (double) self::getSafeParam( $loan['netto'] ),//+
						"outcome"         => (double) self::getSafeParam( $loan['platez'] ),//+
						"town"         => self::getSafeParam( $loan['town'] ), // -
						'position'          => self::getSafeParam( $loan['amats'] ), // +
						'length_of_service' => (int) self::getSafeParam( $loan['stazs'] ), //+

						"address"           => $loan['adrese'] ? self::getSafeParam( $loan['adrese'] ) : self::getSafeParam( $loan['adress'] ), //+
						"credit_history"  => 0,
						"dependants"      => (int) self::getSafeParam( $loan['izdivency'] ),//

//				'annual_turnover' => self::getSafeParam( $loan['gada'] ),
//				'working_time'    => self::getSafeParam( $loan['darbojas'] ),

						'subscribe' => self::getSafeParam( $loan['predlozenie'] ),//
						'courier'   => self::getSafeParam( $loan['kurjer'] ),//

					],
					"id"      => $formData['form_id'], //
					"form_id" => strval($formData['form_id']+6000), //
				] );

			if ( $createLoanSuccess ) {
				// Checkbox Real Estate checked -> make another loan as Real Estate loan
				$this->ifPersonHaveRealEstateMakeAnotherLoanAsRealEstateLoan( $submission );
			}
		}
		$this->db->close();
	}

	public function post( $data ) {
		$uniqueId     = $data['form_id'];
		$loanExists   = Loan::find()->where( [ "unique_id" => $uniqueId ] )->one();
		$personExists = Person::find()->where( [ "unique_id" => $uniqueId ] )->one();

		if ( $uniqueId && $loanExists ) {
			echo "Loan exists. Name: " . $loanExists->person->name . ", ID: $loanExists->id Eur, form_post_id: " . $data["id"] . " uniqueId: $uniqueId \r\n";

			return false;
		}
		if ( $uniqueId && $personExists ) {
			echo "Person exists. Name: " . $personExists->name . ", ID: $personExists->id Eur, form_post_id: " . $data["id"] . "  \r\n";

			return false;
		}

		$person = new Person();
		$person->load( $data );
		$person->unique_id = $uniqueId;

		// Check if Person has errors
		$person->validate();
		echo ' Person Errors --- ' .  json_encode($person->errors);

		$person->save();

		$loan = new Loan();
		$loan->load( $data );
		$loan->person_id     = $person->id;
		$loan->status        = Loan::STATUS_NEW;
		$loan->reminder_time = null;
		$loan->update_time   = time();
		$loan->unique_id     = $uniqueId;
		$loan->source     = $data['Loan']['source'];

		if ( $data['Loan'] && $loan->save() ) {
			echo Json::encode( [ "status" => true, "save" => true ] ) . '\r\n';
			try {
				$this->_log( "update save - " . json_encode( $data ) );
			} catch ( \Exception $e ) {
				echo $e->getMessage() . ' ' . $e->getTrace();
			}
			$this->updateClientDB( $data );

			try {
				$apiServiceManager = new ApiServiceManager( $loan );
				$apiServiceManager->trySendApiRequest();
			} catch ( \Exception $e ) {
				print_r( 'Error while trying to send Api Request: ' . $e );
			}

			return true;
		} else {
			$person->delete();

			print_r( $loan->getErrors() );

			return false;
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
		$statement = "UPDATE wp_wsf_submit SET starred = 1 WHERE form_id = " . $id . ";";

		$this->db->exec( $statement );
	}

}
