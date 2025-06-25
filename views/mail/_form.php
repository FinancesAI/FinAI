<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\LoanApi;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_by')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'custom_id')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'in_menu')->dropDownList(["0" => "No", "1" => "Yes"]) ?>
    
    <?= $form->field($model, 'menu_title')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'api_type')->dropDownList(LoanApi::getTypes()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/mail', 'Create') : Yii::t('app/mail', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
