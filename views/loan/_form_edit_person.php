<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Person;
use app\models\FieldView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Person */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
		'size' => Modal::SIZE_LARGE,
		'options' => ["id" => "lp"],
		'header' => '<h2>'.($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update person')).'</h2>',
		//'toggleButton' => ['label' => ($model->isNewRecord ? Yii::t('app/loan', 'Create') : Yii::t('app/loan', 'Update')), 'class' => 'btn btn-primary'],
]);
?>

<div class="person-form">

    <?php 		$form = ActiveForm::begin(["action" => Url::toRoute(["person/update", "id" => $model->id])]); ?>
	<input type="hidden" name="lid" value="<?= $lid ?>">
	<?= Yii::$app->field->getFormFields(Person::TYPE, FieldView::VIEW_PERSON_FORM_EDIT, $form, $model) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app/person', 'Create') : Yii::t('app/person', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::end();
?>