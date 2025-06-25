<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Loan|\yii\db\ActiveRecord */
use yii\bootstrap\Modal;
use app\models\Mail;
?>

<h2><?= Yii::t("app/loan", "") ?></h2>
<?php 
$emails = Mail::find()->where(["in_menu" => 1])->orderBy("order_by asc")->all();
?>
<div class="row">
<div class="col-xs-12 col-md-6">
<p class="lead "><?= Yii::t("app/loan", "Send email") ?></p>

<form method="post" action="<?= \Yii::$app->urlManager->createUrl(["mail/send"]) ?>" enctype="multipart/form-data">
	<input type="hidden" value="<?= $model->id ?>" name="id">
	<input type="hidden" name="_csrf" value="<?= \Yii::$app->getRequest()->csrfToken ?>">
	
	<div class="form-group">
		<label>Email</label>
		<select class="form-control" name="type" id="mail-box">
			<?php 
			foreach ($emails as $email) {
				?><option value="<?= $email->custom_id ?>"><?= $email->menu_title ?></option><?php
			}
			?>
		</select>
	</div>
	
	<div id="mail-5" class="row hide">
		<div class="form-group col-xs-6"><label>Amount</label><input type="text" class="form-control" id="mail-amount" name="amount" value=""></div>
		<div class="form-group col-xs-6"><label>Max amount</label><input type="text" class="form-control" id="mail-max-amount" name="max_amount" value=""></div>
	</div>
	
	<div id="mail-9" class="hide">
		<div class="form-group"><label id="email-custom-text-label">Custom text</label><textarea class="form-control" id="email-custom-text" name="email_custom_text">Labdien <?= $model->person->name ?>,</textarea></div>
		<div class="form-group"><label>File</label><input type="file" name="email_custom_file[]" id="email-custom-file" multiple="multiple"></div>
	</div>
	
	<button	class="btn btn-block btn-primary" type="submit" data-confirm="Are you sure?"><?= Yii::t("app/loan", "Send email") ?></button>
</form>
</div>
<div class="col-xs-12 col-md-6">
<p class="lead"><?= Yii::t("app/loan", "Send sms") ?></p>


<form method="post" action="<?= \Yii::$app->urlManager->createUrl(["sms/send"]) ?>" enctype="multipart/form-data">
	<input type="hidden" value="<?= $model->id ?>" name="id">
	<input type="hidden" name="_csrf" value="<?= \Yii::$app->getRequest()->csrfToken ?>">
	
	<div class="form-group">
		<label>Sms</label>
		<select class="form-control" name="type" id="sms-box">
			<option value="2"><?= Yii::t("app/loan", "Information sent to email") ?></option>
			<option value="3"><?= Yii::t("app/loan", "Send address") ?></option>
			<option value="7"><?= Yii::t("app/loan", "Contact request sms") ?></option>
			<option value="1"><?= Yii::t("app/loan", "Reject loan sms") ?></option>
			<option value="9"><?= Yii::t("app/loan", "Accept, contact us sms") ?></option>
			<option value="4"><?= Yii::t("app/loan", "Send custom sms") ?></option>
		</select>
	</div>

	<div id="sms-4" class="hide">
		<div class="form-group"><label>Custom text</label><textarea class="form-control" id="sms-custom-text" name="sms_custom_text">Labdien <?= $model->person->name ?>,</textarea></div>
	</div>
	
	<button	class="btn btn-block btn-primary" type="submit" data-confirm="Are you sure?"><?= Yii::t("app/loan", "Send sms") ?></button>
</form>

</div>
<?php 
if ($model->source == "latfinance") {
	?>
	<div class="clearfix"></div>
	<div class="col-xs-12 col-md-6 col-md-offset-3">
		<a href="<?= \Yii::$app->getRequest()->absoluteUrl ?>?task=send-ext-email" class="btn btn-block btn-primary btn-external">Send external email with continue msg</a>
	</div>
	<div class="clearfix"></div>
	<?php
}
if ($model->source == "source2") {
	?>
	<div class="clearfix"></div>
	<div class="col-xs-12 col-md-6 col-md-offset-3">
		<a href="<?= \Yii::$app->getRequest()->absoluteUrl ?>?task=send-ext-email" class="btn btn-block btn-primary btn-external">Send external email with continue msg</a>
	</div>
	<div class="clearfix"></div>
	<?php
}
?>
</div>



