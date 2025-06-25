<?php

namespace app\controllers;

use Yii;
use app\base\Controller;
use yii\filters\AccessControl;
use app\models\Property;
use yii\helpers\Url;
use app\models\Loan;
use app\models\Bill;
use app\models\Changes;
use app\models\search\BillSearch;
use app\components\numbers2words\Speller;
use yii\web\BadRequestHttpException;

require_once( Url::to( "@app/components/fpdf/fpdf.php" ) );
require_once( Url::to( "@app/components/tcpdf/tcpdf.php" ) );
require_once( Url::to( "@app/components/fpdf/fpdi.php" ) );
require_once( Url::to( "@app/components/fpdf/fpdf_tpl.php" ) );

class BillController extends Controller {
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only'  => [ 'index', 'delete', 'update', 'create', 'generate' ],
				'rules' => [
					[
						'actions' => [ 'generate' ],
						'allow'   => true,
						'roles'   => [ '@' ],
					],
					[
						'actions'       => [ 'index', 'delete', 'update', 'create' ],
						'allow'         => true,
						'matchCallback' => function ( $rule, $action ) {
							return Yii::$app->getUser()->getIdentity()->isAdmin();
						},
					],
				],
			],
		];
	}

	/**
	 *
	 */
	public function actionIndex() {
		$searchModel  = new BillSearch();
		$dataProvider = $searchModel->search( Yii::$app->request->queryParams );

		return $this->render( 'index', [
			'searchModel'  => $searchModel,
			'dataProvider' => $dataProvider,
		] );
	}

	/**
	 * Creates a new Bill model.
	 * If creation is successful, the browser will be redirected to the 'index' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Bill();
		$model->detachBehaviors();

		if ( $model->load( Yii::$app->request->post() ) ) {
			if ( $model->validate() ) {
				$model->create_time = strtotime( $model->create_time );
				if ( $model->type == 1 ) {
					$model->due_time = strtotime( '+7 day', $model->create_time );
				}
				if ( $model->type == 2 ) {
					$model->due_time = strtotime( '+10 day', $model->create_time );
				}
			}

			if ( $model->save() ) {
				return $this->redirect( [ 'index' ] );
			}
		}

		return $this->render( 'create', [
			'model' => $model,
		] );
	}

	/**
	 * View an existing Bill model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionView( $id ) {
		$model = $this->findModel( $id );

		return $this->render( 'view', [
			'model' => $model,
		] );
	}

	/**
	 * Updates an existing Bill model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionUpdate( $id ) {
		$model = $this->findModel( $id );

		if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {
			return $this->redirect( [ 'index' ] );
		} else {
			return $this->render( 'update', [
				'model' => $model,
			] );
		}
	}

	/**
	 * Deletes an existing Bill model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function actionDelete( $id ) {
		$bill    = $this->findModel( $id );
		$loan_id = $bill->loan_id;

		if ( $bill->delete() ) {
			Changes::setChanges( Loan::TYPE, $loan_id, "actions_97", null, 1 );
		}

		return $this->redirect( [ 'index' ] );
	}

	/**
	 * Finds the Bill model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param string $id
	 *
	 * @return Bill the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel( $id ) {
		if ( ( $model = Bill::findOne( $id ) ) !== null ) {
			return $model;
		} else {
			throw new NotFoundHttpException( 'The requested page does not exist.' );
		}
	}

	/**
	 * Generate bill
	 *
	 * @param integer $id
	 */
	public function actionGenerate( $id, $type ) {
		if ( Yii::$app->getRequest()->post() ) {
			if ( ! in_array( $type, [ "1", "2" ] ) ) {
				throw new BadRequestHttpException( "Bad type" );
			}

			$model = Loan::find()->where( [ "id" => $id ] )->one();

			$pdf = new PDF();
			$pdf->SetFont( "freeserif", "", 11 );
			$pdf->SetTextColor( 0, 0, 0 );
			$pdf->AddPage();
			$billNr     = $id;
			$billAmount = ( \Yii::$app->getRequest()->get( "bill_amount" ) ? \Yii::$app->getRequest()->get( "bill_amount" ) : \Yii::$app->getRequest()->post( "bill_amount" ) );
			$pvn        = $billAmount - ( $billAmount / 1.21 );
			$billAmount = $billAmount - $pvn;

			$pdf->Text( 96, 28, date( "d.m.Y", time() ) );
			$pdf->Text( 110, 47.5, ucfirst( strtolower( $model->person->name ) ) . " " . ucfirst( strtolower( $model->person->surname ) ) );
			$pdf->Text( 110, 56.8, $model->person->personal_code );
			$pdf->Text( 110, 66.7, $model->person->phone );
			$pdf->Text( 110, 76.7, $model->person->email );
			$pdf->Text( 152.5, 135.5, number_format( $billAmount, 2 ) );
			$pdf->Text( 182.5, 135.5, number_format( $billAmount, 2 ) );
			$pdf->Text( 182.5, 148.3, number_format( $pvn, 2 ) );

			$pdf->Text( 50, 161.5, ucfirst( Speller::spellCurrency( $billAmount + $pvn, Speller::LANGUAGE_LATVIAN, Speller::CURRENCY_EURO, true, true ) ) );

			if ( $type == 1 ) {
				$payDate = strtotime( "+3 day" );
			}
			if ( $type == 2 ) {
				$payDate = strtotime( "+3 day" );
			}
			if ( \Yii::$app->getRequest()->get( "task" ) ) {
				$payDate = \Yii::$app->getRequest()->get( "date" );
			}

			$pdf->Text( 74.3, 105, date( "d.m.Y", $payDate ) );
			$pdf->Text( 74.3, 114.5, $this->_month( date( "m", time() ) ) );
			$pdf->Text( 100.2, 199, $billNr );

			$pdf->SetFont( "freeserif", "", 13 );
			$pdf->Text( 135, 22.5, $billNr );

			$pdf->SetFont( "freeserif", "b", 11 );

			$pdf->Text( 182.5, 143.3, number_format( $billAmount, 2 ) );
			$pdf->Text( 182.5, 153.3, number_format( $billAmount + $pvn, 2 ) );

			if ( $pdf->numPages > 1 ) {
				for ( $i = 2; $i <= $pdf->numPages; $i ++ ) {
					$pdf->_tplIdx = $pdf->importPage( $i );
					$pdf->AddPage();
				}
			}

			$pdf->Header();
			$pdf->Output();

			if ( ! \Yii::$app->getRequest()->get( "task" ) ) {
				if ( in_array( 99, $model->actions ) ) {
					Changes::setChanges( Loan::TYPE, $model->id, "actions_99", null, 1 );
				} else {
					$model->setAttribute( "actions", $model->actions + [ 99 => 1 ] );
					$model->save();
				}

				$bill           = new Bill();
				$bill->loan_id  = $model->id;
				$bill->type     = $type;
				$bill->amount   = $billAmount + $pvn;
				$bill->due_time = $payDate;
				$bill->save();
			}

		} else {
			return $this->goHome();
		}

		\Yii::$app->end();
	}

	/**
	 * Latvian month name
	 *
	 * @param unknown $month
	 *
	 * @return string
	 */
	private function _month( $month ) {
		$month = str_replace( "0", "", $month );

		$menesis     = [];
		$menesis[1]  = 'Janvāris';
		$menesis[2]  = 'Februāris';
		$menesis[3]  = 'Marts';
		$menesis[4]  = 'Aprīlis';
		$menesis[5]  = 'Maijs';
		$menesis[6]  = 'Jūnijs';
		$menesis[7]  = 'Jūlijs';
		$menesis[8]  = 'Augusts';
		$menesis[9]  = 'Septembris';
		$menesis[10] = 'Oktobris';
		$menesis[11] = 'Novembris';
		$menesis[12] = 'Decembris';

		if ( isset( $menesis[ $month ] ) ) {
			return $menesis[ $month ];
		}

		return null;
	}
}

/**
 * Bill class
 * @author arturs
 */
class PDF extends \FPDI {

	/**
	 * @var unknown
	 */
	var $_tplIdx;

	/**
	 * (non-PHPdoc)
	 * @see FPDF::Header()
	 */
	function Header() {
		if ( is_null( $this->_tplIdx ) ) {
			$sourceFile = $this->setSourceFile( Url::to( "@app/components/fpdf/sample.pdf" ) );

			$this->numPages = $sourceFile;
			$this->_tplIdx  = $this->importPage( 1 );

		}
		$this->useTemplate( $this->_tplIdx, 0, 0, 200 );
	}

	/**
	 * (non-PHPdoc)
	 * @see FPDF::Footer()
	 */
	function Footer() {
	}
}