<?php

use app\models\Loan;
use vakorovin\datetimepicker\Datetimepicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LoanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $modelPerson yii\data\ActiveDataProvider */

$this->title                   = Yii::t( 'app/loan', 'Loans' );
$this->params['breadcrumbs'][] = $this->title;

$providerId = \Yii::$app->user->identity->provider_id;

$shouldHideSourceColumn = $providerId === 10 || $providerId === 11 || $providerId === 16 || $providerId === 17 ||  $providerId === 18 ;
//dd($shouldHideSourceColumn);
?>
<div class="loan-index">

    <h1><?= Html::encode( $this->title ) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?php if (\Yii::$app->getUser()->getIdentity()->isDeactivated()): ?>

    <h2><?= Yii::t( 'app/loan', 'Account deactivated!' ) ?></h2>

    <?php else: ?>

	<?php
	$columns = [
//		[ 'class' => 'yii\grid\SerialColumn' ],
        [
            'attribute' => 'person_id',
            'label'     => Yii::t( 'app/loan', 'ID' ),
            'value'     => function ( $data ) {
                return  $data->person_id;
            },
        ],
		'person.name',
        'person.surname',
        'person.phone',
		'person.personal_code',
		'person.email',
        'amount',
		[
			'attribute' => 'person.create_time',
			'label'     => Yii::t( 'app/loan', 'Create Time' ),
			'value'     => function ( $data ) {
                return  $data->person->create_time ? Yii::$app->formatter->asDate($data->person->create_time) : null;
			},
            'filter' =>
                Datetimepicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'person.create_time',
                    'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
                ]),
		],

//		'amount',
//		[
//			'attribute' => 'status',
//			'label'     => Yii::t( 'app/loan', 'Status' ),
//			'value'     => function ( $data ) {
//				$statusName = Loan::getStatuses()[ $data->status ];
//
//				return $statusName;
//			},
//		],

		// [ 'class' => 'app\base\grid\ActionColumn' ],

	];

//	if ( ! $shouldHideSourceColumn ) {
//		$sourceColumn = [
//			'attribute' => 'source',
//			'value'     => function ( $model ) {
//				return $model->source;
//			},
//			'filter'    =>
//				Html::activeDropDownList(
//					$searchModel,
//					'source',
//					$searchModel->getSources(),
//					[ 'class' => 'form-control', ]
//				),
//			'format'    => 'raw',
//		];
//
//		$columns[] = $sourceColumn;
//	}

	?>

	<?= GridView::widget( [
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'tableOptions' => [ 'class' => Yii::$app->setting->get( 'table_class' ) . ' loan-table loan-table-' . \Yii::$app->getUser()->getIdentity()->isAdmin() ],
		'rowOptions'   => function ( $model, $key, $index, $grid ) {
			return [
				'onclick'     => "checkRedirect(this, '" . Url::toRoute( [
						'bank-loans/view',
						'id' => $model['id']
					] ) . "');",
				'class'       => "context-menu-click " . $model->getWaitingClassBankLoans(),
				'style'       => "background: " . $model->getLoanCSSColor(),
				'data-id'     => $model->id,
				'data-status' => $model->status,
				'data-link'   => Url::toRoute( [ 'bank-loans/view', 'id' => $model['id'] ] ),
			];
		},
		'columns'      => $columns
	] );

	?>

    <?php endif; ?>
</div>
