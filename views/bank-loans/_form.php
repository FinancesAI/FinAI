<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field( $model, 'unique_id' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'person_id' )->textInput() ?>

	<?= $form->field( $model, 'user_id' )->textInput() ?>

	<?= $form->field( $model, 'amount' )->textInput() ?>

	<?= $form->field( $model, 'term' )->textInput() ?>

	<?= $form->field( $model, 'first_payment' )->textInput() ?>

	<?= $form->field( $model, 'approved' )->textInput() ?>

	<?= $form->field( $model, 'status' )->textInput() ?>

	<?= $form->field( $model, 'source' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'product' )->textInput() ?>

	<?= $form->field( $model, 'deal_stage' )->textInput() ?>

	<?= $form->field( $model, 'deal_product' )->textInput() ?>

	<?= $form->field( $model, 'referral' )->textarea( [ 'rows' => 6 ] ) ?>

	<?= $form->field( $model, 'query_string' )->textarea( [ 'rows' => 6 ] ) ?>

	<?= $form->field( $model, 'ip_ountry' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'lang' )->textInput( [ 'maxlength' => true ] ) ?>

<!--	--><?//= $form->field( $model, 'actions' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'description' )->textarea( [ 'rows' => 6 ] ) ?>

	<?= $form->field( $model, 'create_time' )->textInput() ?>

	<?= $form->field( $model, 'update_time' )->textInput() ?>

	<?= $form->field( $model, 'close_time' )->textInput() ?>

	<?= $form->field( $model, 'reminder_time' )->textInput() ?>

	<?= $form->field( $model, 'waiting_time' )->textInput() ?>

	<?= $form->field( $model, 'cid' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'description_2' )->textarea( [ 'rows' => 6 ] ) ?>

	<?= $form->field( $model, 'last_changed_field' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'rating' )->textInput() ?>

	<?= $form->field( $model, 'reminder_class' )->textInput() ?>

	<?= $form->field( $model, 'is_tc_sent' )->textInput() ?>

	<?= $form->field( $model, 'color_id' )->textInput() ?>

	<?= $form->field( $model, 'type' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'company_name' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'tmt_data' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'year' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'autoregnr' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'techpass' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'property_address' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'pledge' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'price' )->textInput( [ 'maxlength' => true ] ) ?>

	<?= $form->field( $model, 'amount_taken' )->textInput( [ 'maxlength' => true ] ) ?>

    <div class="form-group">
		<?= Html::submitButton( $model->isNewRecord ?
			Yii::t( 'app', 'Create' ) :
			Yii::t( 'app', 'Update' ), [ 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' ] ) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
