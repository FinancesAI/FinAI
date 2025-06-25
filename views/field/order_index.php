<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\FieldView */

$this->title = Yii::t('app/field', 'Field order');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>
	<?php 
	echo GridView::widget([
			'dataProvider' => $dataProvider,
			"tableOptions" => ['class' => Yii::$app->setting->get("table_class")],
			'rowOptions' => function ($model, $key, $index, $grid) {
				return [
						"onclick" => "window.location = '". Url::toRoute(["field/order", "id" => $model["id"]])  ."';"
				];
			},
			'columns' => [
					[
							'attribute' => 'name',
							'value' => 'name',
					],
			]
	]);
	?>
</div>
