<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vakorovin\datetimepicker\Datetimepicker;

/* @var $model app\models\Field */
/* @var $form yii\widgets\ActiveForm */
/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$this->title = Yii::t('app/field', 'Update {modelClass}: ', [
    'modelClass' => 'Bill',
]) . $model->loan_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/field', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/field', 'Update');
?>
<div class="field-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

	<div class="field-form">
	
	    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'status')->dropDownList($model->getStatuses()) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/bill', 'Create') : Yii::t('app/bill', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	
	    <?php ActiveForm::end(); ?>
	
	</div>

</div>
