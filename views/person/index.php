<?php

use yii\helpers\Html;
use app\components\ExportGridView;
use yii\helpers\Url;
use app\models\Person;
use app\models\FieldView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/person', 'Contacts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="fixed-btn">
       	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#w0"><?= Yii::t("app/loan", "Create person") ?></button>
    </p>
    
    <?= $this->render("_form_create", ["model" => $model]) ?>
    <?= $this->render("/loan/_filter_params", ["searchModel" => $searchModel]) ?>
    
    <?php Yii::$app->field->setSearchModel($searchModel) ?>
    <?= ExportGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	"tableOptions" => ['class' => Yii::$app->setting->get("table_class")],
    	'rowOptions' => function ($model, $key, $index, $grid) {
    		return [
    				"onclick" => "window.location = '". Url::toRoute(["person/view", "id" => $model["id"]])  ."';"
    		];
    	},
        'columns' => Yii::$app->field->getFields(Person::TYPE, FieldView::VIEW_PERSON_TABLE),
    ]); ?>
</div>
