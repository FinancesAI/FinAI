<?php

use yii\helpers\Html;
use vakorovin\datetimepicker\Datetimepicker;

/* @var $this yii\web\View */

$this->title = Yii::t('app/admin', 'Admin index');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("_nav"); ?>
	
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<h3>Export</h3>
			<?= Html::beginForm("", "post", ["id" => "checkAllCheck"]) ?>
			<?= Html::hiddenInput("export", "true") ?>
			<?= Html::hiddenInput("what", "close_time") ?>
			<div class="row">
				<div class="col-xs-6">
					<div class="form-group">
						<label>Date from</label>
						<?= Datetimepicker::widget([
						    'name' => "from",
			    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
						]) ?>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<label>Date to</label>
						<?= Datetimepicker::widget([
						    'name' => "to",
			    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
						]) ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?= Html::submitInput(\Yii::t("app/export", "Export data"), ["class" => 'btn btn-primary']) ?>
			</div>
			
			<?= Html::endForm() ?>
		</div>
		<div class="col-xs-12 col-md-6">
			<h3>Total</h3>
			<?= Html::beginForm("", "post", ["id" => "checkAllCheck2"]) ?>
			<?= Html::hiddenInput("total", "true") ?>
			<div class="row">
				<div class="col-xs-6">
					<div class="form-group">
						<label>Date from</label>
						<?= Datetimepicker::widget([
						    'name' => "from",
			    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
						]) ?>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<label>Date to</label>
						<?= Datetimepicker::widget([
						    'name' => "to",
			    			'options' => ['class' => 'form-control', 'format' => 'd.m.Y', 'timepicker' => false]
						]) ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?= Html::submitInput(\Yii::t("app/export", "Calculate total amount"), ["class" => 'btn btn-primary']) ?>
			</div>
			
			<?= Html::endForm() ?>
			
			<?php 
			if (isset($total)) {
				echo "Total ceo: ".number_format($totalCeo, 2)."<br/>";
				echo "Total bill: ".number_format($totalAm, 2)."<br/>";
				echo "Total: ".number_format($total, 2)."<br/>";
			}
			?>
		</div>
	</div>

</div>