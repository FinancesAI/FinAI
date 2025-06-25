<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\models\Loan;
use app\models\Person;
use app\models\FieldView;
use yii\helpers\Json;


/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $modelPerson app\models\Person */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
		'size' => Modal::SIZE_LARGE,
		'header' => '<h2>'.($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update extra info')).'</h2>',
		'options' => ["id" => "le"]
		//'toggleButton' => ['label' => ($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update')), 'class' => 'btn btn-primary'],
]);
?>

<div class="loan-form">

	<?php 
		$form = ActiveForm::begin(["action" => Url::toRoute(["loan/extra", "id" => $model->id])]);
		echo $form->field($model->extra, 'property_address')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_description')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_phone')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_owner')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_owner_address')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_workplace')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_position')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'car_work_experience')->textInput(['maxlength' => true]);
		echo $form->field($model->extra, 'bank_account_nr')->textInput(['maxlength' => true]);
		?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::end();
?>
