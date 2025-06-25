<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\LoanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'unique_id') ?>

    <?= $form->field($model, 'person_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'term') ?>

    <?php // echo $form->field($model, 'first_payment') ?>

    <?php // echo $form->field($model, 'approved') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'product') ?>

    <?php // echo $form->field($model, 'deal_stage') ?>

    <?php // echo $form->field($model, 'deal_product') ?>

    <?php // echo $form->field($model, 'referral') ?>

    <?php // echo $form->field($model, 'query_string') ?>

    <?php // echo $form->field($model, 'ip_ountry') ?>

    <?php // echo $form->field($model, 'lang') ?>

    <?php // echo $form->field($model, 'actions') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'update_time') ?>

    <?php // echo $form->field($model, 'close_time') ?>

    <?php // echo $form->field($model, 'reminder_time') ?>

    <?php // echo $form->field($model, 'waiting_time') ?>

    <?php // echo $form->field($model, 'need_reindex') ?>

    <?php // echo $form->field($model, 'cid') ?>

    <?php // echo $form->field($model, 'description_2') ?>

    <?php // echo $form->field($model, 'last_changed_field') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'reminder_class') ?>

    <?php // echo $form->field($model, 'ceo') ?>

    <?php // echo $form->field($model, 'is_tc_sent') ?>

    <?php // echo $form->field($model, 'Zvanits') ?>

    <?php // echo $form->field($model, 'color_id') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'company_name') ?>

    <?php // echo $form->field($model, 'tmt_data') ?>

    <?php // echo $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'autoregnr') ?>

    <?php // echo $form->field($model, 'techpass') ?>

    <?php // echo $form->field($model, 'property_address') ?>

    <?php // echo $form->field($model, 'pledge') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'amount_taken') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
