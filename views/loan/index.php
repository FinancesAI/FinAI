<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LoanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model \app\models\Loan */
/* @var $modelPerson \app\models\Person */

$this->title = Yii::$app->request->get('acceptance') ? Yii::t('app/loan', 'Accepted loans') : Yii::t('app/loan', 'Loans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="fixed-btn">
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#w0"><?= Yii::t("app/loan", "Create loan") ?></button>
    </p>

	<?= $this->render("_form_create", ["model" => $model, "modelPerson" => $modelPerson]) ?>

    <?= $this->render("_filter_params", ["searchModel" => $searchModel]) ?>

<!--    --><?//= $this->render("_status_bar") ?>

	<?= $this->render("_table", [
			"searchModel" => $searchModel,
			"dataProvider" => $dataProvider,
	]) ?>

</div>
