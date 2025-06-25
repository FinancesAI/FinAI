<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin();
echo $form->field($calendar, 'embed')->textarea(['rows' => 6]);
	
echo "<div class=\"form-group\">";
echo Html::submitButton($calendar->isNewRecord ? Yii::t('app/user', 'Create calendar') : Yii::t('app/user', 'Update calendar'), ['class' => $calendar->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
echo "</div>";
	
ActiveForm::end();