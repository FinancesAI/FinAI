<?php

use app\services\ProviderService;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;

Modal::begin([
    'title' => Yii::t('modules/chat', 'Newsletter'),
    'id' => 'newsletter',
    'closeButton' => false,
//    'options' => [
//        'ng-app'=> 'yiiChat'
//    ],
    'bodyOptions' => [
        'ng-controller' => 'NewsLetterController as NewsLetter'
    ]
]);

echo Alert::widget([
    'body' => '{{ newsLetterError }}',
    'closeButton' => false,
    'options' => [
        'class' => 'alert-danger',
        'ng-show' => 'newsLetterError'
    ],
]);


?>

    <div class="form-group">
        <?= Html::textarea('message', '', [
            'class' => 'form-control',
            'ng-model' => 'newsLetterMessage',
            'rows' => '5',
            'placeholder' => Yii::t("modules/chat", "Enter your message..."),
        ]) ?>
    </div>
    <div class="form-group">

<!--        --><?//= Html::label(Yii::t("app/stats", "All users")); ?>

        <?= Html::dropDownList(
            "partnerId",
            null,
            ProviderService::getProvidersList(),
            [
                'class' => 'form-control',
                'prompt' => Yii::t("app/stats", "All users"),
                'ng-model' => 'newsLetterPartnerId',
                'label' => '123'
            ]
        ) ?>
    </div>

    <div class="form-group float-right">
        <?= Html::buttonInput(Yii::t('modules/chat', 'Close'), [
            'class' => 'btn btn-secondary',
            'data-dismiss' => 'modal'
        ]) ?>

        <?= Html::buttonInput(Yii::t('modules/chat', 'Submit'), [
            'class' => 'btn btn-primary',
            'ng-click' => 'sendNewsLetter()',
        ]) ?>
    </div>

<?php
Modal::end();