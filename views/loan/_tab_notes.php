<?php
/* @var $model app\models\Loan */

/* @var $this \yii\web\View */

use app\models\Changes;
use app\models\Loan;
use app\models\LoanExtra;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<h2><?= Yii::t( "app/loan", "" ) ?></h2>

<?php
if ( $model->person->loan_count > 1 ) {
	Modal::begin( [
		'header'       => '<h2>Person loan change history</h2>',
		'toggleButton' => [
			'label' => "<strong>Info:</strong> This person has " . $model->person->loan_count . " loans, of which " . $model->person->getLoans()->where( [ "status" => Loan::STATUS_REJECTED ] )->orWhere( [ "status" => Loan::STATUS_REFUSES ] )->count() . " are rejected & refused loans.",
			'class' => 'alert alert-info pointer',
			'id'    => 'changes-all-btn',
			'style' => 'width:100%;display:block;'
		],
	] );

	?>
    <div id="changes-all" data-type="<?= Loan::TYPE ?>,<?= LoanExtra::TYPE ?>" data-type-id="<?= $model->id ?>"
         data-offset="0" data-offsetdef="<?= \Yii::$app->params["changes_limit"] ?>"
         data-url="<?= Url::to( [ "changes/all" ] ) ?>">
        <img src="<?= \Yii::$app->urlManager->createUrl( "images/loading.gif" ) ?>" class="loading" alt="Loading">
        <div class="list-group">
        </div>
    </div>
    <a id="more-changes-all" class="btn btn-block btn-primary hide"><?= Yii::t( "app/loan", "Load more changes" ) ?></a>
	<?php

	Modal::end();
}
if ( $model->bill ) {
	?>
    <div class="alert alert-info text-center"><strong>Info:</strong> This loan has bill generate.</div><?php
}
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field( $model, 'description' )->textarea( [
	"rows"        => 9,
//	"value"       => "",
	"placeholder" => Yii::t( "app/loan", 'Start typing to leave a note...')
] )->label(  Yii::t( "app/loan", "Notes") ) ?>
<?php
$sC = Changes::find()->where( [ "attr" => "actions_61", "type" => 2, "type_id" => $model->id ] )->count();
$nC = Changes::find()->where( [ "attr" => "actions_64", "type" => 2, "type_id" => $model->id ] )->count();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-4"><?= $form->field( $model, 'actions[66]' )->checkbox( [], false )
			                                         ->label( Yii::t( 'app/loan', 'Kurjers' ) ) ?></div>
            <div class="col-xs-12 col-md-4"><?= $form->field( $model, 'actions[70]' )->checkbox( [], false )
			                                         ->label( Yii::t( 'app/loan', 'Nav oficiāla darba' ) ) ?></div>
            <div class="col-xs-12 col-md-4"><?= $form->field( $model, 'actions[71]' )->checkbox( [], false )
			                                         ->label( Yii::t( 'app/loan', 'Dekrētā' ) ) ?></div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-4">
                <div class="form-group"><label><?=Yii::t( 'app/loan', 'Sazvanīts')?> - <?= $sC ?>
                         <?= Html::checkbox( "Loan[actions][61]", false, [ "value" => 1 ] ) ?></label></div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div class="form-group"><label><?=Yii::t( 'app/loan', 'Neatbild')?> - <?= $nC ?>
                         <?= Html::checkbox( "Loan[actions][64]", false, [ "value" => 1 ] ) ?></label></div>
            </div>
            <div class="col-xs-12 col-md-4">
                <p style="font-size: 15px;"><b><span class="glyphicon glyphicon-phone"></span> <?=Yii::t( 'app/loan', 'Veikto zvanu skaits')?>
                        - <?= $nC + $sC ?></b></p>
            </div>
            <!-- 			<div class="col-xs-12 col-md-4">
				<div class="form-group"><label>Atsakās <?= Html::checkbox( "Loan[actions][69]", false, [ "value" => 1 ] ) ?></label></div>
			</div> -->
        </div>

    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-md-4">
    </div>

    <div class="col-xs-12 col-md-4">
        <?= $form->field( $model, 'acceptance' )->checkbox([], false) ?>
    </div>

        <div class="col-xs-12 col-md-4">
        </div>
</div>

<p class="lead"><?=Yii::t( 'app/loan', 'Change status')?></p>
<div class="row">
    <div class="col-xs-12 col-md-6">
		<?= $form->field( $model, 'status' )->dropDownList( $model->getStatuses() ) ?>
    </div>

</div>

<!--<p class="lead">Change color</p>-->
<!--<div class="form-group">-->
<!--	--><?php //foreach ( $model->getAllColors()->all() as $color ): ?>
<!---->
<!--		--><?php
//		$loanColor = $model->getLoanColor()->one();
//
//		if ( $loanColor != null ) {
//			$loanColor     = $model->getLoanColor()->one()->name;
//			$isActiveColor = $loanColor === $color->name;
//			$loanColorID   = $model->getLoanColor()->one()->id;
//		} else {
//			$isActiveColor = false;
//			$loanColor     = '';
//			$loanColorID   = null;
//		}
//		?>
<!---->
<!--        <button-->
<!--                class="btn btn-default btn-change-color js--change-color --><?//= $isActiveColor ? 'active' : '' ?><!--"-->
<!--                style="color: #000; background: --><?//= $color->css_color ?><!--"-->
<!--                data-value="--><?//= $color->id ?><!--"-->
<!--        >-->
<!--			--><?//= $color->name ?>
<!--        </button>-->
<!---->
<!--	--><?php //endforeach; ?>
<!---->
<!--	--><?//= Html::activeHiddenInput( $model, 'color_id', [ 'name' => 'Loan[color_id]', 'value' => $loanColorID ] ) ?>
<!---->
<!--</div>-->

<div class="form-group">
	<?= Html::submitButton( $model->isNewRecord ? Yii::t( 'app/loan', 'Create' ) : Yii::t( 'app/loan', 'Update' ), [
		'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
		'id'    => 'notes-tab-update'
	] ) ?>
</div>

<?php ActiveForm::end(); ?>
