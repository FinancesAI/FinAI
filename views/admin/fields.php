<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Property */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app/admin', 'Admin fields');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-fields">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("_nav"); ?>
	
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList([
    		"1" => Yii::t("app/field", "Person"),
    		"2" => Yii::t("app/field", "Loan"),
    ]) ?>

    <?= $form->field($model, 'value')->textInput() ?>
    
    <?= $form->field($model, 'show_table')->dropDownList([
    		"0" => Yii::t("app/field", "No"),
    		"1" => Yii::t("app/field", "Yes"),
    ]) ?>
        
    <?= $form->field($model, 'show_role')->dropDownList((new User())->getRoles()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
