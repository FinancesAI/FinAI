<?php

/* @var $this \yii\web\View */
/* @var $searchModel \app\models\search\LoanSearch */

/* @var $dataProvider \yii\data\ActiveDataProvider */

use app\components\ExportGridView;
use app\models\Loan;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\FieldView;
use yii\bootstrap\Modal;
use \app\components\SendBulkSMS;

?>
<?php Yii::$app->field->setSearchModel( $searchModel ) ?>
<?php

try {
    $columns = [
        [
            'attribute' => 'realEstate',
            'label' => '',
            'filter' => false,
            'format' => 'raw',
            'value' => function ($data) {
                if (in_array($data->realEstate, ['Да', 'Jā', 'J'])) {
                    return Html::img('@web/images/icons8/icons8-home-address-48.png', ['alt' => 'Real Estate']);
                }
                return '';
            },
            'contentOptions' =>function ($model, $key, $index, $column){
                return in_array($model->realEstate, ['Да', 'Jā', 'J']) ? ['class' => 'real-estate'] : [];
            },
        ]
    ];

    echo ExportGridView::widget( [
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		"tableOptions" => [ 'class' => Yii::$app->setting->get( "table_class" ) . ' loan-table loan-table-' . \Yii::$app->getUser()->getIdentity()->isAdmin() ],
		'rowOptions'   => function ( $model, $key, $index, $grid ) {
			return [
				"onclick"     => "checkRedirect(this, '" . Url::toRoute( [
						"loan/view",
						"id" => $model["id"]
					] ) . "');",
//				"class"       => "context-menu-click " . $model->getWaitingClass(),
				"class"       => "context-menu-click ",
				"style"       => "background: " . $model->getLoanCSSColor(),
				"data-id"     => $model->id,
				"data-status" => $model->status,
				"data-link"   => Url::toRoute( [ "loan/view", "id" => $model["id"] ] ),
			];
		},
		'columns'      => array_merge($columns, Yii::$app->field->getFields( Loan::TYPE, FieldView::VIEW_LOAN_TABLE )),
	] );
} catch ( Exception $e ) {
	Yii::error( 'Error while rendering Loans list: ' . $e, __METHOD__ );
}

Modal::begin( [
	'header'       => '<h2>' . \Yii::t('app/loan', 'Send SMS to Customers') . '</h2>',
	'toggleButton' => [ 'label' => \Yii::t( "app/loan", "Send bulk SMS" ), 'class' => 'btn btn-primary btn-export' ],
] );

SendBulkSMS::renderModalBody();

Modal::end();


?>
    
