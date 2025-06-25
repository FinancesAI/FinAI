<?php

namespace app\controllers;

use app\models\Loan;
use app\models\LoanProgerss;
use app\models\search\LoanSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use app\base\Controller;
use yii\web\NotFoundHttpException;

/**
 * BankLoansController implements the CRUD actions for Loan model.
 */
class BankLoansController extends Controller {

	public $layout = 'bank';

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class'   => VerbFilter::className(),
				'actions' => [
					'delete' => [ 'POST' ],
				],
			],
		];
	}

	/**
	 * Lists all Loan models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel  = new LoanSearch();
		$dataProvider = $searchModel->searchBankLoans( Yii::$app->request->queryParams );

		return $this->render( 'index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		] );
	}

	public function actionView( $id ) {
		$model = $this->findModel( $id );

		if ( Yii::$app->request->getIsPost() ) {
			$this->tryUpdateLoanProgress( $model );
		}

		return $this->render( 'view', [
			'model'                => $model,
			'thisUserLoanProgress' => $this->thisUserLoanProgress( $model )
		] );
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

	function tryUpdateLoanProgress( $model ) {
		if ( ! $this->isValidUpdateParams() ) {
			throw new BadRequestHttpException( 'Could not update Bank loan data, Invalid Post params' );
		}

		$this->updateBankLoanProgress( $model );
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

            $this->updateLoanAcceptance($loanModel, $loanProgress->status);

			return $this->redirect( [ 'view', 'id' => $loanModel->id ] );
		} else {
			throw new BadRequestHttpException( 'Could not update Bank loan data ' );
		}
	}

    /**
     * @param $loanModel
     * @param $status
     * @return void
     */
    function updateLoanAcceptance($loanModel, $status = null)
    {
        if (isset($status) && in_array($status, [3, 4])) {
            $loanModel->acceptance = 1;
            $loanModel->save();
        }

    }

	protected function findModel( $id ) {
		if ( ( $model = Loan::find()
		                    ->where( [ 'id' => $id ] )
		                    ->with( 'person' )
		                    ->one() ) !== null ) {

			return $model;
		} else {
			throw new NotFoundHttpException( 'The requested page does not exist.' );
		}
	}
}
