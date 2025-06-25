<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBasicAuth;


class LoanApiController extends \yii\rest\ActiveController {

	public $modelClass = 'app\models\LoanProgerss';

	public function init() {
		parent::init();
		\Yii::$app->user->enableSession = false;
	}

	public function behaviors() {
		$behaviors                  = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => HttpBasicAuth::className(),
		];

		return $behaviors;
	}

	public function actions() {
		return [
			'index'  => [
				'class'       => 'yii\rest\IndexAction',
				'modelClass'  => $this->modelClass,
				'checkAccess' => [ $this, 'checkAccess' ],
			],
			'update' => [
				'class'       => 'yii\rest\UpdateAction',
				'modelClass'  => $this->modelClass,
				'checkAccess' => [ $this, 'checkAccess' ],
				'scenario'    => $this->updateScenario,
			]
		];
	}

	protected function verbs() {
		return [
			'update' => [ 'POST' ],
		];
	}

	function isValidUpdateParams() {
		if (
			null !== ( Yii::$app->request->post( 'prog-status' ) )
			&& null !== ( Yii::$app->request->post( 'prog-val' ) )
			&& null !== ( Yii::$app->request->post( 'prog-comment' ) )
		) {
			return true;
		}

		return false;
	}

	function thisUserLoanProgress( $model ) {
		$progressData         = $model->progress;
		$userProviderId       = \Yii::$app->getUser()->identity->provider_id;
		$thisUserLoanProgress = null;

		foreach ( $progressData as $progress ) {
			if ( $progress->provider_id === $userProviderId ) {
				$thisUserLoanProgress = $progress;
			}
		}

		return $thisUserLoanProgress;
	}

	function updateBankLoanProgress( $loanModel ) {
		$loanProgress = $this->thisUserLoanProgress( $loanModel );

		if ( ! $loanProgress ) {
			$loanProgress              = new LoanProgerss();
			$loanProgress->loan_id     = $loanModel->id;
			$loanProgress->provider_id = Yii::$app->getUser()->identity->provider_id;
		}

		$loanProgress->status = Yii::$app->request->post( 'prog-status' );
		$loanProgress->amount = Yii::$app->request->post( 'prog-val' );
		$loanProgress->text   = Yii::$app->request->post( 'prog-comment' );


		if ( $loanProgress->save() ) {
			return $this->redirect( [ 'view', 'id' => $loanModel->id ] );
		} else {
			throw new BadRequestHttpException( 'Could not update Bank loan data ' );
		}
	}

}
