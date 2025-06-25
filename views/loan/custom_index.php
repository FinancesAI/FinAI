<?php

use yii\helpers\Html;
use app\models\search\StatsSearch;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LoanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/loan', 'Loans');
$this->params['breadcrumbs'][] = $this->title;
$stats = new StatsSearch();
$month = $stats->search(Yii::$app->request->queryParams, "month");
$goalProc = $month/\Yii::$app->setting->get("goal")*100;
?>
<div class="custom-index">

    <h1><?= Html::encode($this->title) ?><small> ( <b><?= number_format($goalProc, 2) ?>%</b> of month goal achieved )</small></h1>

    <p class="fixed-btn">
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#w0"><?= Yii::t("app/loan", "Create loan") ?></button>
    </p>

	<?= $this->render("_form_create", ["model" => $model, "modelPerson" => $modelPerson]) ?>    
    
    <?= $this->render("_filter_params", ["searchModel" => $searchModel]) ?>
    
    <?= $this->render("_status_bar") ?>
    
    <h2>New loans</h2>
    
	<?= $this->render("_table", [
			"searchModel" => $searchModel,
			"dataProvider" => $dataProvider,
	]) ?>
	
	<h2>New updated loans</h2>
	
	<?= $this->render("_table", [
			"searchModel" => $searchModel,
			"dataProvider" => $dataProvider2,
	]) ?>
	
	<h2>Reminder loans</h2>
	
	<?php 
	if ($dataProvider3) {
		echo $this->render("_table", [
				"searchModel" => $searchModel,
				"dataProvider" => $dataProvider3,
		]);
	}
	?>
    
</div>
