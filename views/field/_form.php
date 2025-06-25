<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Field */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="field-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord) {
    	echo $form->field($model, 'type')->dropDownList($model->getTypes());
	} ?>
	
    <?php if ($model->isNewRecord) {
    	echo $form->field($model, 'name')->textInput(['maxlength' => true]);
	} ?>
    
    <?= $form->field($model, 'filter_type')->dropDownList($model->getFilterTypes()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/field', 'Create') : Yii::t('app/field', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
