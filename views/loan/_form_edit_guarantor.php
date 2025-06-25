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

use app\models\LoanGuarantor;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $modelPerson app\models\Person */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
		'size' => Modal::SIZE_LARGE,
		'header' => '<h2>'.($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update guarantor info')).'</h2>',
		'options' => ["id" => "lg"]
		//'toggleButton' => ['label' => ($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update')), 'class' => 'btn btn-primary'],
]);
if (!$model->guarantor) {
	$modelG = NEW LoanGuarantor();
} else {
	$modelG = $model->guarantor;
}
?>

<div class="loan-form">

	<?php 
		$form = ActiveForm::begin(["action" => Url::toRoute(["loan/guarantor", "id" => $model->id])]);
		echo $form->field($modelG, 'full_name')->textInput();
		echo $form->field($modelG, 'personal_code')->textInput();

		echo $form->field($modelG, 'phone')->textInput();
		echo $form->field($modelG, 'email')->textInput();
		
		echo $form->field($modelG, 'address')->textInput();
		echo $form->field($modelG, 'postcode')->textInput();
		
		echo $form->field($modelG, 'workplace')->textInput();
		echo $form->field($modelG, 'workposition')->textInput();
		echo $form->field($modelG, 'work_experience')->textInput();

		echo $form->field($modelG, 'income')->textInput();
		echo $form->field($modelG, 'outcome')->textInput();
		?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::end();
?>
