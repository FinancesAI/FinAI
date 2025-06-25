<?php

use app\models\Provider;
use kartik\file\FileInput;
use kartik\switchinput\SwitchInput;
use vakorovin\datetimepicker\Datetimepicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    	if (Yii::$app->getRequest()->get("task") !== "change-password")
   			echo $form->field($model, 'fullname')->textInput(['maxlength' => true]);
    ?>

    <?php if (Yii::$app->getRequest()->get("task") !== "change-password"): ?>

    <?php if($model->getThumbUploadUrl('avatar')): ?>

        <div class="form-group">

            <label class="control-label"><?= Yii::t( 'app/user', 'Avatar' )?></label>

            <div class="row">
                <div class="col-xs-12">
                    <!-- Thumb 1 (thumb profile) -->
                    <?= Html::img($model->getThumbUploadUrl('avatar'), ['class' => 'img-thumbnail']) ?>
                </div>
            </div>

            <?= Html::checkbox('delete_avatar', false, ['label' => Yii::t( 'app/user', 'Delete avatar' )]) ?>
        </div>

    <?php endif; ?>

<!--        --><?php //echo $form->field($model, 'avatar')->fileInput(['accept' => 'image/*']) ;

        echo $form->field($model, 'avatar')->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/*'
            ],
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => true,
                'showUpload' => false,
                'browseLabel' =>  Yii::t( 'app/user', 'Picture'),
            ]
        ]);
        ?>
    <?php endif;?>

    <?php
    	if (Yii::$app->getRequest()->get("task") !== "change-password")
   			echo $form->field($model, 'email')->textInput(['maxlength' => true]);
    ?>

    <?php
    	if (Yii::$app->getRequest()->get("task") == "change-password" || Yii::$app->controller->getRoute() == "user/create")
    		echo $form->field($model, 'password')->passwordInput()
    ?>

    <?php
    	if (Yii::$app->getRequest()->get("task") !== "change-password")
   			echo $form->field($model, 'role')->dropDownList($model->getRoles());
    ?>

    <?php
    if (Yii::$app->getRequest()->get("task") !== "change-password")
        echo $form->field($model, 'status')->dropDownList($model->getStatuses());
    ?>

    <?php
    if (Yii::$app->getRequest()->get("task") !== "change-password")
        echo $form->field($model, 'activationDateTime')->widget(Datetimepicker::class, [
            'options' => [
                'class' => 'form-control',
                'format' => 'd.m.Y H:i',
                'timepicker' => true
            ]
        ]);
    ?>

    <?php
    	if (Yii::$app->getRequest()->get("task") !== "change-password")
            echo $form->field($model, 'provider_id')->dropDownList(
                Provider::find()->select(['name', 'id'])->indexBy('id')->column());
    ?>

    <?php
    	if (Yii::$app->getRequest()->get("task") !== "change-password")
   			echo $form->field($model, 'access_token')->textInput();
    ?>

    <?php
    if (Yii::$app->getRequest()->get("task") !== "change-password")
    echo $form->field($model, 'chat_enable')->widget(SwitchInput::class, []);

    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/user', 'Create') : Yii::t('app/user', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
