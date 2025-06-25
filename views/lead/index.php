<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<section class="container">

    <h1><?= Html::encode( $this->title ) ?></h1>

    <h1 class="title">Demo Lead form</h1>

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field( $model, 'name' ) ?>
	<?= $form->field( $model, 'surname' ) ?>

	<?= $form->field( $model, 'email' ) ?>
	<?= $form->field( $model, 'phone' ) ?>
	<?= $form->field( $model, 'personalCode' ) ?>
	<?= $form->field( $model, 'loanAmount' ) ?>
	<?= $form->field( $model, 'loanTerm' ) ?>
	<?= $form->field( $model, 'income' ) ?>
	<?= $form->field( $model, 'outcome' ) ?>

    <div class="form-group">
		<?= Html::submitButton( 'Submit', [ 'class' => 'btn btn-primary' ] ) ?>
    </div>

	<?php ActiveForm::end(); ?>

</section>
