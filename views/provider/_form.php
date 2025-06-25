<?php

use app\models\Source;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Provider */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provider-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<!--    --><?//= $form->field($model, 'sources')->dropDownList(Source::find()->select(['name', 'id'])->indexBy('id')->column(), ['class'=>'form-control', 'multiple' => 'multiple']) ?>

    <?
    echo $form->field($model, 'sourceIds')->widget(Select2::class, [
        'data' => Source::find()->select(['name', 'id'])->indexBy('id')->column(),
        'language' => 'ru',
        'options' => ['multiple' => true, 'placeholder' => Yii::t('app/provider','Select a sources ...')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);


    echo $form->field($model, 'enable')->widget(SwitchInput::class, []);

    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/provider', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
