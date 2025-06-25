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
		'options' => ["id" => "l"],
		'header' => '<h2>'.($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update')).'</h2>',
		//'toggleButton' => ['label' => ($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update')), 'class' => 'btn btn-primary'],
]);
?>

<div class="loan-form">

	<?php 
	if (isset($absolute)) {
		$form = ActiveForm::begin(["action" => Url::toRoute(["loan/index"])]);
	} else {
		$form = ActiveForm::begin();
	}
	?>
	
	<?= Yii::$app->field->getFormFields(Loan::TYPE, FieldView::VIEW_LOAN_FORM_EDIT, $form, $model, $modelPerson) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::end();
?>

<script>
	var dealStages = <?= Json::encode((new Loan())->getDealStages()) ?>;
	var dealStagesProducts = <?= Json::encode((new Loan())->getDealStageProduct()) ?>;
</script>