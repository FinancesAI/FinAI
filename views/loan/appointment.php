<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\Loan;
use app\models\FieldView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LoanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t( 'app/site', 'Appointment' );
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-index">

    <h1><?= Html::encode( $this->title ) ?></h1>

	<?= GridView::widget( [
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		"tableOptions" => [ 'class' => Yii::$app->setting->get( "table_class" ) . ' loan-table loan-table-' . \Yii::$app->getUser()->getIdentity()->isAdmin() ],
		'rowOptions'   => function ( $model, $key, $index, $grid ) {
			return [
				"onclick"   => "checkRedirect(this, '" . Url::toRoute( [
						"loan/view",
						"id" => $model["loan_id"]
					] ) . "');",
				"class"     => "context-menu-click ",
				"data-id"   => $model->loan_id,
				"id"        => $model->loan_id,
				"data-link" => Url::toRoute( [ "loan/view", "id" => $model["loan_id"] ] ),
			];
		},
		'columns'      => [

			[
				"attribute" => "date",
                "label" => Yii::t( 'app/appointment', 'Date' ),
				'filter'    => false,
				"value"     => function ( $model ) {
					return Yii::$app->formatter->asDate( $model->date, 'dd-MM-yyyy');
				},
			],
            [
                "attribute" => "date",
                "label" => Yii::t( 'app/appointment', 'Time' ),
                'filter'    => false,
                "value"     => function ( $model ) {
                    return Yii::$app->formatter->asTime( $model->date, 'short');
                },
            ],
			[
				"attribute" => "loan.person.name",
				'filter'    => false,
				"value"     => function ( $model ) {
					return $model->loan->person->getValue( "name", $model->loan->person );
				},
			],
			[
				"attribute" => "loan.person.surname",
				'filter'    => false,
				"value"     => function ( $model ) {
					return $model->loan->person->getValue( "surname", $model->loan->person );
				},
			],
			[
				"attribute" => "loan.person.phone",
				'filter'    => false,
				"value"     => function ( $model ) {
					return $model->loan->person->getValue( "phone", $model->loan->person );
				},
			],
//			[
//				"attribute" => "loan.status",
//				'filter'    => false,
//				"value"     => function ( $model ) {
//					return $model->loan->getValue( "status", $model->loan );
//				},
//			],
//			[
//				"attribute" => "loan.prog",
//				'filter'    => false,
//				'format'    => "raw",
//				"value"     => function ( $model ) {
//					return "<div class='center-block text-center'>" . $model->loan->getValue( "prog", $model->loan ) . "</div>";
//				},
//			],
//			[
//				"attribute" => "loan.create_time",
//				'filter'    => false,
//				'format'    => "raw",
//				"value"     => function ( $model ) {
//					return Yii::$app->formatter->asDate( $model->loan->create_time );
//				},
//			],
			[
				"attribute" => "loan.product",
				'filter'    => false,
				"value"     => function ( $model ) {
					return $model->loan->getValue( "product", $model->loan );
				},
			],
            [
                "attribute" => "loan.description",
                "label" => Yii::t( 'app/appointment', 'Description' ),
                'filter'    => false,
                "value"     => function ( $model ) {
                    return $model->loan->getValue( "description", $model->loan );
                },
            ],
            [
                "attribute" => "manager",
                "label" => Yii::t( 'app/appointment', 'Manager' ),
                'filter'    => false,
                "value"     => function ( $model ) {
                    return $model->manager->fullname;
                },
            ],
			[
				"attribute" => "",
				'filter'    => false,
				'format'    => "raw",
				"value"     => function ( $model ) {
					return "<span class='appointment-rm glyphicon glyphicon-remove'><span class='hide'>" . $model->loan_id . "</span></span>";
				},
			],
		],
	] ); ?>

</div>
