<?php

use yii\helpers\Html;
use himiklab\sortablegrid\SortableGridView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\FieldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/field', 'Fields');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/field', 'Create Field'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app/field', 'Order fields'), ['order-index'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= SortableGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	'filterPosition' => GridView::FILTER_POS_FOOTER,
    	"tableOptions" => ['class' => Yii::$app->setting->get("table_class")." table-sort"],
        'columns' => [
    		[
    				"attribute" => "type",
    				"value" => function ($model) {
    					return $model->attributeLabels()["type-".$model->type];
    				},
    		],
            'name',
            [
    				"attribute" => "filter_type",
    				"value" => function ($model) {
    					return $model->getFilterTypes()[(int)$model->filter_type];
    				},
    		],

            ['class' => 'app\base\grid\ActionColumn', 'template'=>'{update} {delete}',],
        ],
    ]); ?>
</div>
