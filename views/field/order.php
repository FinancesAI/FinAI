<?php

use yii\helpers\Html;
use himiklab\sortablegrid\SortableGridView;
use yii\grid\GridView;
use app\models\Field;
use app\models\FieldView;

use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\FieldView */

$this->title = Yii::t('app/field', 'Field order: '.$model->getViews()[Yii::$app->getRequest()->get("id")]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/field', 'Field order'), 'url' => ['order-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>
	
	<?php 
		echo Html::beginForm();
		echo SortableGridView::widget([
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
									"attribute" => "show",
									"format" => "raw",
									"value" => function ($model) {
										$checked = false;
										if (FieldView::find()->where(["field_id" => $model->id, "show" => 1, "view" => Yii::$app->getRequest()->get("id")])->one()){
											$checked = true;
										}
										return "<div class=\"checkbox\"><label>".Html::checkbox("fields[".$model->id."][show]", $checked)."</label></div>";
									},
						],
						[
									"attribute" => "role",
									"format" => "raw",
									"value" => function ($model) {
										$selection = null;
										if ($field = FieldView::find()->where(["field_id" => $model->id, "view" => Yii::$app->getRequest()->get("id")])->one()){
											$selection = $field->role;
										}
										return Html::dropDownList("fields[".$model->id."][role]", $selection, Yii::$app->getUser()->getIdentity()->getRoles(), ["class" => "form-control"]);
									},
						],
				],
		    ]);
		
		echo Html::submitInput(Yii::t("app/field", "Update field order"), ["class" => "btn btn-primary"]);
		echo Html::endForm();
	
	?>
</div>
