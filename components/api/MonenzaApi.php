<?php

namespace app\components\api;

use app\components\HTTPRequester;
use app\components\services\MonenzaService;
use app\models\Changes;
use app\models\Loan;
use app\models\LoanProgerss;
use yii\bootstrap\Html;

class MonenzaApi extends ApiModel {


	public function __construct( $model ) {
		$this->setModel( $model );
		$this->setTitle( "Monenza API" );
		$this->setContent( $this->_getContent() );

		if ( \Yii::$app->getRequest()->get( "task" ) == "monenza" ) {
			$this->_post();

			return \Yii::$app->getResponse()->redirect( [ "loan/view", "id" => $model->id ] );
		}
	}

	public function setSendData() {
		$errors   = [];
		$model    = $this->getModel();
		$sendData = null;

		if ( ! isset( $model->getProgressFormated()[16] ) ) {
			array_push( $errors, 'progress' );
		} else {
			$sendData = json_encode( [
				'broker'        => 'finlat',
				'applicationId' => $model->getProgressFormated()[16]->api_response_id
			] );
		}

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
				$html .= Html::beginForm( "?task=monenza", "get" );
				$html .= Html::tag( "div", Html::submitButton( \Yii::t( 'app/field', 'Send data' ), [ 'class' => 'btn btn-primary btn-block' ] ), [ "class" => "form-group" ] );
				$html .= Html::endForm();
			}
		} else {
			$html .= '<p>Invalid field validation</p> Errors: ' . implode( ',', $sendData['errors'] );
		}

		return $html;
	}

	private function _post() {
		$monenzaService = new MonenzaService($this->getModel());
		$monenzaService->approveSendRequest();
	}

}
