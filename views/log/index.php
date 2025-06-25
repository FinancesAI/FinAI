<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/log', 'Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'filterPosition' => GridView::FILTER_POS_FOOTER,
    	"tableOptions" => ['class' => Yii::$app->setting->get("table_class")],
    	'rowOptions' => function ($model, $key, $index, $grid) {
    		return [
    				"onclick" => "window.location = '". Url::toRoute(["log/view", "id" => $model["id"]])  ."';",
    		];
    	},
        'columns' => [
            'id',
            [
    				"attribute" => "type",
    				"value" => function ($model) {
    					return $model->typeLabels()[$model->type];
    				},
    		],
            'update_time:datetime',
        ],
    ]); ?>
</div>
