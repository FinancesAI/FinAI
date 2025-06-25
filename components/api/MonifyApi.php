<?php

namespace app\components\api;

use app\models\Changes;
use app\models\Loan;
use yii\bootstrap\Html;

class MonifyApi extends ApiModel {


	public function __construct( $model ) {
		$this->setModel( $model );
		$this->setTitle( "Monify API" );
		$this->setContent( $this->_getContent() );

		if ( \Yii::$app->getRequest()->get( "task" ) == "monify" ) {
			$this->_post();

			return \Yii::$app->getResponse()->redirect( [ "loan/view", "id" => $model->id ] );
		}
	}

	public function setSendData() {
		$errors = [];
		$model  = $this->getModel();

		$amount          = $model->amount;
		$companyName     = $model->company_name;
		$name            = $model->person->name;
		$personal_code   = $model->person->personal_code;
		$email           = $model->person->email;
		$working_time    = $model->person->working_time;
		$annual_turnover = $model->person->annual_turnover;
		$phone           = $model->person->phone;

		if ( empty( trim( $amount ) ) ) {
			array_push( $errors, 'amount' );
		}
		if ( empty( trim( $companyName ) ) ) {
			array_push( $errors, 'companyName' );
		}
		if ( empty( trim( $name ) ) ) {
			array_push( $errors, 'name' );
		}
		if ( empty( trim( $personal_code ) ) ) {
			array_push( $errors, 'personal_code' );
		}
		if ( empty( trim( $email ) ) ) {
			array_push( $errors, 'email' );
		}
		if ( empty( trim( $working_time ) ) ) {
			array_push( $errors, 'working_time' );
		}
		if ( empty( trim( $annual_turnover ) ) ) {
			array_push( $errors, 'annual_turnover' );
		}
		if ( empty( trim( $phone ) ) ) {
			array_push( $errors, 'phone' );
		}

		$sendData = [
			'company' => [
				'age'       => $working_time,
				'name'      => $companyName,
				'regNumber' => $personal_code,
				'turnover'  => $annual_turnover,
			],
			'contact' => [
				'appAmount' => $amount,
				'email'     => $email,
				'ipAddress' => '0.0.0.0',
				'name'      => $name,
				'phone'     => $phone,
			]
		];

		$this->data = $sendData;

		return [
			'success' => count( $errors ) === 0,
			'errors'  => $errors
		];
	}


	private function _getContent() {
		$html     = "<h2>" . $this->getLabel() . "</h2>";
		$sendData = $this->setSendData();

		if ( $sendData['success'] ) {
			$sent = Changes::find()->where( [
				"type_id" => $this->getModel()->id,
				"type"    => Loan::TYPE,
				"attr"    => "actions_41"
			] )->one();
			if ( $sent ) {
				$html .= "<p>Already sent data.</p>";
			} else {
				$html .= Html::beginForm( "?task=monify", "get" );
				$html .= Html::tag( "div", Html::submitButton( \Yii::t( 'app/field', 'Send data' ), [ 'class' => 'btn btn-primary btn-block' ] ), [ "class" => "form-group" ] );
				$html .= Html::endForm();
			}
		} else {
			$html .= '<p>Invalid field validation</p> Errors: ' . implode( ',', $sendData['errors'] );
		}

		return $html;
	}

	private function _post(  ) {
		$sent = Changes::find()->where( [
			"type_id" => $this->getModel()->id,
			"type"    => Loan::TYPE,
			"attr"    => "actions_41"
		] )->one();
		if ( ! $sent ) {

			$fullUrl = \Yii::$app->params['monify_url'] . '?' . http_build_query( $this->data );

			$result = @file_get_contents( $fullUrl );
			if ( $result ) {

				dd($result);


				$this->saveProgress($result, 1);

				$this->_log( "OK - " . $fullUrl . " - ANSWER - " . json_encode( $result ) );
			} else {
				$this->_log( "ERROR - " . $fullUrl );
			}

		}
	}

}
