<?php

namespace app\components\services;

use app\components\HTTPRequester;
use app\services\ApiHelper;

class FinanzaService extends ApiService {

	public function sendAPIRequest() {
		$loan      = $this->loan;
		$birthDate = SendApiRequestHelper::getDateOfBirthFromPersCode( $loan->person->personal_code );
		$data      = [
			'password' => "tTyfeCff9cIC",
			"uniqueId" => "75017d1793",
			"userData" => [
				"name"             => $loan->person->name,
				"surname"          => $loan->person->surname,
				"persId"           => $loan->person->personal_code,
				"phone"            => 371 . $loan->person->phone,
				"email"            => $loan->person->email,
				'workplace'        => $loan->person->workplace,
				'job'              => $loan->person->position,
				'bankName'         => $loan->person->bank_name,
				'actualAddrStreet' => $loan->person->address,
				'actualAddrCity'   => $loan->person->city,
				'actualAddrPostal' => $loan->person->post_code,
				'legalAddrStreet'  => $loan->person->address,
				'legalAddrCity'    => $loan->person->city,
				'legalAddrPostal'  => $loan->person->post_code,
				'salary'           => $loan->person->salary,
				'expenses'         => $loan->person->outcome,
				'earnings'         => $loan->person->income,
				'passw'            => '78dJvWNcPGQM8E5H',
				'passwRepeat'      => '78dJvWNcPGQM8E5H',

				"birthDate"  => $birthDate,
				"lang"       => "lv",
				"newsletter" => 1
			],
			"loanData" => [
				"amount" => $loan->amount,
				"days"   => $loan->term
			],
			"miscData" => [
				"vsaaAgreement" => "1"
			]
		];

		$data = 'password=tTyfeCff9cIC&uniqueId=75017d1793&data=' . json_encode( $data );

		$resp = HTTPRequester::HTTPPostString( 'https://www.finanza.lv/api/broker/action/userRegister', $data );

		$resp = json_decode( $resp, true );


		$returnData = [
			'Request'  => $data,
			'Response' => $resp
		];

		$this->handleResponse( $resp );

		return $returnData;
	}

	function handleResponse( $resp ) {
		$respStatus = $resp['status'];
		$text       = '';

		$status = 0;
		if ( ! empty( $respStatus ) && $respStatus == 'success' ) {
			$status = 1;
		} elseif ( ! empty( $resp['errors'] ) ) {
			$status = 2;
		}

		print_r( ['$resp: ' => $resp ] );

		if ( $status > 0 ) {
			if ( ! empty( $resp['status'] ) ) {
				$text = $resp['status'];
			}

			if ( ! empty( $resp['redirectLink'] ) ) {
				$text .= ' - ' . 'redirectLink: ' . $resp['redirectLink'];
			}


			if ( ! empty( $resp['errors'] ) ) {
				$text = 'Errors: ';
				foreach ( $resp['errors'] as $error ) {
					$field            = $error['field'];
					$errorDescription = $error['errorDescription'];
					$text             .= ', Field: ' . $field . ' => ErrorDescription: ' . $errorDescription;
				}
			}

		}

		$this->saveApiResponse( 0, 4, $status, $text );
	}

	public function shouldSendApiRequest() {
	    $source = (int) $this->loan->source;
		if ( ApiHelper::isRefinancingLoan( $source )
		     || ApiHelper::isConsumerLoan( $source )
		     || ApiHelper::isOnlineLoan( $source )
		) {
			return true;
		}

		return false;
	}
}
