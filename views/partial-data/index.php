<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\PartialData;

$this->title = Yii::t('app/partial-data', 'Partial Data');
$this->params['breadcrumbs'][] = $this->title;

$url = Url::to(['partial-data/send-sms']);
?>

<div class="partial-data-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render("/admin/_nav"); ?>

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
        'columns' => [
        [
            'attribute' => 'user_name',
            'header' => Yii::t('app/partial-data', 'First Name'), 
        ],
        [
            'attribute' => 'user_surname', 
            'header' => Yii::t('app/partial-data', 'Last Name'), 
        ],
        [
              'label' => Yii::t('app/partial-data', 'Email'),
              'value' => 'email', 
         ],
         [
              'label' => Yii::t('app/partial-data', 'Personal Code'),
              'value' => 'personal_code', 
         ],
         [
              'label' => Yii::t('app/partial-data', 'Form Id'),
              'value' => 'form_id', 
         ],
         [
              'label' => Yii::t('app/partial-data', 'Phone'),
              'value' => 'phone',  
         ],
           [
            'label' => Yii::t('app/partial-data', 'Created Date'),
            'value' => function ($model) {
                $existingRecord = PartialData::find()
                    ->where(['form_id' => $model['form_id'], 'email' => $model['email']])
                    ->one();         

                return $existingRecord ? $existingRecord->created_date : $model['time'];
            },
            'format' => ['date', 'php:Y-m-d H:i:s'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{send-sms}',
            'buttons' => [
                'send-sms' => function ($url, $model, $key) {
                  
                    if ($model->is_send != 1) {
                     return Html::button(Yii::t('app/partial-data', 'Send SMS'), [
                            'type' => 'button',
                            'class' => 'btn btn-primary btn-export',
                            'data-id' => $model->id, 
                            'onclick' => 'sendSms(this)',
                        ]);
                    }
                    return ''; 
                },
            ],
        ],
    ],
]); ?>

</div>
<?php
$this->registerJs(<<<JS
window.sendSms = function(button) {
    // console.log('sendSms function called');
    var id = $(button).data('id');
    // console.log('Button ID:', id); 
    // if (!id) {
    //     console.error('No ID found. Cannot send SMS.');
    //     return; 
    // }
    // console.log('CSRF Token: ', yii.getCsrfToken());
    $.ajax({
        url: '{$url}',
        type: 'POST',
        data: {
            id: id,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                // alert('SMS sent successfully!');
                $(button).remove(); 
            } else {
                alert('Failed to send SMS. Please try again.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('An error occurred. Please try again later.');
        }
    });
}
JS
);
?>

