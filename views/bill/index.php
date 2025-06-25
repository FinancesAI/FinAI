<?php

use app\models\User;
use yii\helpers\Html;
use app\components\ExportGridView;
use yii\helpers\Url;

use vakorovin\datetimepicker\Datetimepicker;
use app\models\Changes;
use app\models\Bill;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/user', 'Bill');
$this->params['breadcrumbs'][] = $this->title;
$issuedSum = Bill::find()->where(["status" => 0])->sum("amount");
$lateSum = Bill::find()->where(["status" => 0])->andFilterWhere(["<", "due_time", time()])->sum("amount");
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>
    
    <p>
        <?= Html::a(Yii::t('app/user', 'Create Bill'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app/user', 'Unpaid bills'), "?unpaid=ture", ['class' => 'btn btn-danger']) ?>
         <span class="lead"> Issued total <?= $issuedSum ?> EUR of which <?= $lateSum ?> EUR is late payment</span>
    </p>
    
    <?= ExportGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	"tableOptions" => ['class' => Yii::$app->setting->get("table_class")],
    	'rowOptions' => function ($model, $key, $index, $grid) {
    		return [
    				"class" => ((($model->due_time < time()) && $model->status == 0) ? "danger" : ""),
    		];
    	},
        'columns' => [
            [
				"attribute" => "loan_id",
        		"value" => function ($model) {
        			return "<a href=\"".Yii::$app->urlManager->createUrl(["loan/view", "id" => $model->loan_id])."\">".$model->loan_id."</a>";
        		},
        		"format" => "raw"
        	],
           	[
				"attribute" => "amount",
        		"value" => function ($model) {
        			return $model->amount;
        		},
        	],
        	[
				"attribute" => "type",
        		'filter' => Html::activeDropDownList($searchModel, 'type', $searchModel->getTypes(),['class'=>'form-control','prompt' => 'All']),	 
        		"value" => function ($model) {
        			return $model->getTypes()[$model->type];
        		},
        	],
        	[
				"attribute" => "status",
        		'filter' => Html::activeDropDownList($searchModel, 'status', $searchModel->getStatuses(),['class'=>'form-control','prompt' => 'All']),	 
        		"value" => function ($model) {
        			return $model->getStatuses()[$model->status];
        		},
        	],
        	[
				"attribute" => "create_time",
        		'filter' => Datetimepicker::widget([
	    			'model' => $searchModel,
				    'attribute' => "create_time",
	    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
				]),	 
        		"value" => function ($model) {
        			return Yii::$app->formatter->asDate($model->create_time);
        		},
        	],
        	[
				"attribute" => "due_time",
        		'filter' => Datetimepicker::widget([
	    			'model' => $searchModel,
				    'attribute' => "due_time",
	    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
				]),	 
        		"value" => function ($model) {
        			return Yii::$app->formatter->asDate($model->due_time);
        		},
        	],
        	[
				"attribute" => "close_time",
        		'filter' => Datetimepicker::widget([
	    			'model' => $searchModel,
				    'attribute' => "close_time",
	    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
				]),	 
        		"value" => function ($model) {
        			return Yii::$app->formatter->asDate($model->close_time);
        		},
        	],
        	[
				"attribute" => "",
				"format" => "raw",
        		"value" => function ($model) {
        			$notPayd = ($model->due_time < time() && $model->status == 0);
    				if ($notPayd) {
    					$html = "<button id='bill-danger-reminder' data-id='".$model->loan_id."' class='btn btn-block btn-info'>".\Yii::t("app/bill", "LAST CHANCE")."</button>";
    					return $html;
    				} else {
    					return null;
    				}
        		},
        	],
        	['class' => 'app\base\grid\ActionColumn', 'template'=>'{view} {update} {delete}',],
        ],
    ]); ?>
</div>
