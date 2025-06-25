<?php

namespace app\commands;

use app\components\services\FinanzaService;
use app\models\Loan;

class FinanzaController extends \yii\console\Controller {
	public function actionIndex2() {

		$data = [
			'userData' => [
				'name'             => 'Janis',
				'surname'          => 'Jaunzems',
				'persId'           => '011186-10548',
				'phone'            => 37120956800,
				'email'            => 'test5@gmail.com',
				'workplace'        => 'Sia Abu dabi',
				'job'              => 'Boss',
				'bankName'         => 'Swedbank',
				'actualAddrStreet' => 'Jurmalas',
				'actualAddrCity'   => 'Riga',
				'actualAddrPostal' => '1052',
				'legalAddrStreet'  => 'Jurmalas',
				'legalAddrCity'    => 'Riga',
				'legalAddrPostal'  => '1052',
				'salary'           => 1500,
				'expenses'         => '100',
				'earnings'         => '500',
				'passw'            => '78dJvWNcPGQM8E5H',
				'passwRepeat'      => '78dJvWNcPGQM8E5H',
				'birthDate'        => '1985-11-11',
				'lang'             => 'lv',
				'newsletter'       => 1
			],
			'loanData' => [
				'amount' => 2500 * 100,
				'days'   => 7
			],
			'miscData' => [
				'vsaaAgreement' => 1
			]
		];

		$data = "password=tTyfeCff9cIC&uniqueId=75017d1793&data=" . json_encode($data);

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://www.finanza.lv/api/broker/action/userRegister' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, ( $data ) );
		$output = curl_exec( $ch );
		curl_close( $ch );

		print_r($output);


//		$data = json_encode( $data );

		//$this->send2( 'https://www.finanza.lv/api/broker/action/userRegister', $data );
	}


	public function actionIndex( $id ) {
		$loan = Loan::find()->where( [ 'id' => $id ] )->one();

		if ( $loan ) {
			$fs   = new FinanzaService();
			$resp = $fs->sendFinanzaApiRequest( $loan );

			print_r( $resp );
		}

	}

	function send2( $url, $params ) {
		$resp = $this->send( $url, $params );

//		print_r( $resp );

		$resp = json_decode( $resp, true );
		$res  = [
			'Request'  => $params,
			'Response' => $resp
		];
		print_r( $res );
	}

	function send( $postUrl, $data ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $postUrl );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, ( $data ) );
		$output = curl_exec( $ch );


		print_r( $output );


		curl_close( $ch );

		return $output;
	}
}
