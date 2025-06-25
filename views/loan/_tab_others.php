<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\Loan|\yii\db\ActiveRecord */
use yii\helpers\Html;
?>

<h2><?= Yii::t("app/loan", "") ?></h2>
<p class="lead"><?= Yii::t("app/loan", "Search at partners") ?></p>

<div class="col-sm-12">
<div class="col-sm-4"><a href="https://partner.cofi.lv/dashboard?per_page=50&search=<?= $model->person->personal_code ?>" class="btn btn-block btn-primary" target="_blank"><?= Yii::t("app/loan", "Inbank.lv") ?></a></div>
<div class="col-sm-4"><a href="http://partneri.mogo.lv/applications?search=<?= $model->person->name ?>+<?= $model->person->surname ?>&updated_start=&updated_end=" class="btn btn-block btn-primary" target="_blank"><?= Yii::t("app/loan", "Mogo.lv") ?></a></div>
<div class="col-sm-4"><a href="https://db.Credico/application.aspx?code=<?= $model->person->personal_code ?>" class="btn btn-block btn-primary" target="_blank"><?= Yii::t("app/loan", "Credito") ?></a></div>
</div>
<div class="gap-80"></div>

<p class="lead"><?= Yii::t("app/loan", "Fill up") ?></p>

<div class="col-sm-4">
<?php 
$add = ($model->extra ? $model->extra->car_owner_address : "");
$phone2 = ($model->extra ? $model->extra->car_phone : "");
$workplace = ($model->extra ? $model->extra->car_workplace : "");
$work_experience = ($model->extra ? $model->extra->car_work_experience : "");

$dataFillParams = "data-fill-form-params=\"?resource[client_name]=".$model->person->name." ".$model->person->surname."
&resource[client_identification_no]=".$model->person->personal_code."
&resource[client_phone]=".$model->person->phone."
&resource[client_email]=".$model->person->email."
&resource[loan_amount]=".$model->amount."
&resource[client_downpayment]=".$model->first_payment."
&resource[client_address]=".$add."
&resource[client_phone2]=".$phone2."
&resource[workplace]=".$workplace."
&resource[work_experience]=".$work_experience."
&resource[client_income]=".$model->person->income."
&resource[client_debts_amount]=0
&resource[loan_term]=".$model->term."
&resource[client_comment]=\""; 

?>
<a href="http://partneri.mogo.lv/applications/new" class="btn btn-block btn-primary" <?= $dataFillParams ?> target="_blank"><?= Yii::t("app/loan", "Fill mogo.lv form") ?></a>
</div>

<div class="col-sm-4">
    <form method="post" action="https://vitacredit.lv/pieteikums/" enctype="application/x-www-form-urlencoded" target="_blank">
        <input type="hidden" name="m" value="rf">
        <input type="hidden" name="client_name" value="<?= $model->person->name." ".$model->person->surname ?>">
        <input type="hidden" name="client_pk" value="<?= $model->person->personal_code ?>">
        <input type="hidden" name="client_bd" value="">
        <script>
          var str = '<?= $model->person->personal_code ?>';
          var bdnums = str.split('-')[0]
          var pk='';
          var bdnumsArray = bdnums.split('');
          for (var i = 0; i < bdnumsArray.length; i++) {
            pk+=bdnumsArray[i]
            if ( (i+1) % 2 === 0 ) {
              pk+= '.'
            }
          }
          pk = pk.split('')
          pk.pop()
          pk = pk.join('')
          document.querySelector('input[name="client_bd"]').value = pk
        </script>

        <input type="hidden" name="client_salary" value="<?= $model->person->income ?>">
        <input type="hidden" name="fin_sum" value="<?= $model->amount ?>">
        <input type="hidden" name="fin_type" value="buy_car">


        <input type="hidden" name="partner_phone" value="24821784">
        <input type="hidden" name="partner" value="info@latfinance.lv">

        <input type="submit" class="btn btn-block btn-primary" value="Fill Vita form">
    </form>

</div>



<div class="gap-80"></div>


<p class="lead"><?= Yii::t("app/loan", "Create bill") ?></p>
<?php if (!$model->bill) { ?>
<div class="row">
<div class="col-xs-12 col-md-6">
<p>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 1, "bill_amount" => "36.30"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 36.30 euro bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 1, "bill_amount" => "48.40"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 48.40 euro bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 1, "bill_amount" => "96.80"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 96.80 euro bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 1, "bill_amount" => "145.20"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 145.20 euro bill") ?></a>
<form method="post" action="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 1]) ?>">
<input type="hidden" name="_csrf" value="<?= \Yii::$app->getRequest()->csrfToken ?>">
<div class="form-group"><label>Amount</label><input type="text" class="form-control" id="bill-amount" name="bill_amount" value="00.00"></div>
<button	class="btn btn-block btn-primary" type="submit"><?= Yii::t("app/loan", "Print custom bill") ?></button>
</form>
</p>
</div>
<div class="col-xs-12 col-md-6">
<p>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 2, "bill_amount" => "36.30"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 36.30 euro courier bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 2, "bill_amount" => "48.40"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 48.40 euro courier bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 2, "bill_amount" => "96.80"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 96.80 euro courier bill") ?></a>
<a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 2, "bill_amount" => "145.20"]) ?>" class="btn btn-block btn-primary" data-method="post"><?= Yii::t("app/loan", "Print 145.20 euro courier bill") ?></a>
<form method="post" action="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => 2]) ?>">
<input type="hidden" name="_csrf" value="<?= \Yii::$app->getRequest()->csrfToken ?>">
<div class="form-group"><label>Amount</label><input type="text" class="form-control" id="bill-amount" name="bill_amount" value="00.00"></div>
<button	class="btn btn-block btn-primary" type="submit"><?= Yii::t("app/loan", "Print custom courier bill") ?></button>
</form>
</p>
</div>
</div>

<?php } else {
	?>
	<table class="table table-striped table-bordered">
		<thead>
		<tr>
			<td><b>Info</b></td>
			<td><b>View</b></td>
			<td><b></b></td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td><p style="margin-bottom:0px;"><?= $model->bill->amount ?> euro bill already generated.</p></td>
			<td><a href="<?= \Yii::$app->urlManager->createUrl(["bill/generate", "id" => $model->id, "type" => $model->bill->type, "date" => $model->bill->due_time, "bill_amount" => number_format($model->bill->amount, 2), "task" => "view"]) ?>" data-method="post">View generated bill</a></td>
			<td>
				<a href="<?= \Yii::$app->urlManager->createUrl(["bill/index"]) ?>?BillSearch%5Bloan_id%5D=<?= $model->id ?>" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
				<a href="<?= \Yii::$app->urlManager->createUrl(["bill/update", "id" => $model->bill->id]) ?>" title="Update" aria-label="Update" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
				<a href="<?= \Yii::$app->urlManager->createUrl(["bill/delete", "id" => $model->bill->id]) ?>" title="Delete" aria-label="Delete" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}?>






















