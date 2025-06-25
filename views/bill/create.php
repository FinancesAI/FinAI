<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vakorovin\datetimepicker\Datetimepicker;

/* @var $form yii\widgets\ActiveForm */
/* @var $this yii\web\View */
/* @var $model app\models\Bill */

$this->title = Yii::t('app/field', 'Create bill');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/field', 'Bill'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'loan_id')->input("text") ?>
    
    <?= $form->field($model, 'amount')->input("text") ?>
   
    <?= $form->field($model, 'type')->dropDownList($model->getTypes()) ?>
    
    <?= $form->field($model, 'status')->dropDownList($model->getStatuses()) ?>
    
    <?= $form->field($model, 'create_time')->widget(Datetimepicker::classname(), [
			'options' => ["format" => "d.m.Y", "id" => "datepicker2", 'timepicker' => false]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/bill', 'Create') : Yii::t('app/bill', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
