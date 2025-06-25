<?php

use yii\helpers\Html;
use vakorovin\datetimepicker\Datetimepicker;
use yii\data\ArrayDataProvider;
use app\models\Loan;
use yii\helpers\Url;
use app\models\LoanProgerss;

/* @var $this yii\web\View */

$this->title = Yii::t('app/admin', 'Partner stats');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-stats">



    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("_nav"); ?>

	<?php 
	if ($id = \Yii::$app->getRequest()->get("id")) {
		?>
	<style>
.admin-stats tr:nth-child(1) {
    font-weight: bold;
}
.admin-stats tr:nth-child(8) {
    font-weight: bold;
}
.admin-stats tr:nth-child(15) {
    font-weight: bold;
}
	</style>
		<?php
		$partners = [];
		$l = new Loan();
		$title = $l->getInProgress()[$id];
		$data = [];
		
		echo '<h1>'.$title.'</h1>';
		
		$totalCount = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
				? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->count()
				: LoanProgerss::find()->where(["provider_id" => $id])->count()
		);
		
		$data[] = [
				" " => "Iesniegts kopā",
				"Produkts" => "",
				"Skaits" => $totalCount,
				"Procenti" => "100",
				"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
						? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->sum("loan.amount")
						: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->sum("loan.amount")
				),
				"Peļņa" => "",
				"Komisija" => "",
		];
		foreach ($l->getProducts() as $productId => $productTitle) {
			if ($productId == 0) {
				continue;
			}
			$count = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
					? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["product" => $productId])->count()
					: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["product" => $productId])->count()
			);
			$data[] = [
					" " => "",
					"Produkts" => $productTitle,
					"Skaits" => $count,
					"Procenti" => number_format(@($count/$totalCount)*100, 2),
					"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
							? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["product" => $productId])->sum("loan.amount")
							: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["product" => $productId])->sum("loan.amount")
					),
					"Peļņa" => "",
					"Komisija" => "",
			];
		}
		
		//////
		$count = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
			? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["loan_progerss.status" => 3])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->count()
			: LoanProgerss::find()->where(["provider_id" => $id])->andWhere(["status" => 3])->count()
		);
		$data[] = [
		" " => "Akceptēts kopā",
		"Produkts" => "",
		"Skaits" => $count,
		"Procenti" => number_format(@($count/$totalCount)*100, 2),
		"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
			? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["loan_progerss.status" => 3])->sum("loan.amount")
			: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["loan_progerss.status" => 3])->sum("loan.amount")
		),
		"Peļņa" => "",
		];
		foreach ($l->getProducts() as $productId => $productTitle) {
			if ($productId == 0) {
				continue;
			}
			$count = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
					? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["loan_progerss.status" => 3])->andWhere(["product" => $productId])->count()
					: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["loan_progerss.status" => 3])->andWhere(["product" => $productId])->count()
			);
			$data[] = [
					" " => "",
					"Produkts" => $productTitle,
					"Skaits" => $count,
					"Procenti" => number_format(@($count/$totalCount)*100, 2),
					"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
							? LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["loan_progerss.status" => 3])->andWhere(["product" => $productId])->sum("loan.amount")
							: LoanProgerss::find()->joinWith("loan")->where(["provider_id" => $id])->andWhere(["loan_progerss.status" => 3])->andWhere(["product" => $productId])->sum("loan.amount")
					),
					"Peļņa" => "",
					"Komisija" => "",
			];
		}
		
		///
		$am = 0;
		$am2 = 0;
		$loans = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
				? Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->all()
				: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->all()
		);
		foreach ($loans as $loan) {
			$am = $am+$loan->getCeo($loan);
			$am2 = $am2+($loan->bill ? $loan->bill->amount : 0);
		}
		$count = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
				? Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->count()
				: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->count()
		);
		$data[] = [
				" " => "Izsniegti kopā",
				"Produkts" => "",
				"Skaits" => $count,
				"Procenti" => number_format(@($count/$totalCount)*100, 2),
				"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
						? Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->sum("loan.amount")
						: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->sum("loan.amount")
				),
				"Peļņa" => $am,
				"Komisija" => $am2,
		];
		foreach ($l->getProducts() as $productId => $productTitle) {
			if ($productId == 0) {
				continue;
			}
			$am = 0;
			$am2 = 0;
			$loans = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
					? Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->all()
					: Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(["status" => 5])->all()
			);
			foreach ($loans as $loan) {
				$am = $am+$loan->getCeo($loan);
				$am2 = $am2+($loan->bill ? $loan->bill->amount : 0);
			}
			$count = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
					? Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->count()
					: Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(["status" => 5])->count()
			);
			$data[] = [
					" " => "",
					"Produkts" => $productTitle,
					"Skaits" => $count,
					"Procenti" => number_format(@($count/$totalCount)*100, 2),
					"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
							? Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->sum("loan.amount")
							: Loan::find()->where(["deal_stage" => $id])->andWhere(["product" => $productId])->andWhere(["status" => 5])->sum("loan.amount")
					),
					"Peļņa" => $am,
					"Komisija" => $am2,
			];
		}
		
		
		$provider = new ArrayDataProvider([
				'allModels' => $data,
				'pagination' => false,
				'sort' => false,
		]);
		
		echo "<h2>Partners</h2>";
		echo yii\grid\GridView::widget([
				'dataProvider' => $provider,
		]);
		

	} else {
		$partners = [];
		$l = new Loan();
		foreach ($l->getInProgress() as $id => $title) {
			if (in_array($id, [0,10])) {
				continue;
			}
			
			$am = 0;
			$am2 = 0;
			$loans = (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
					? Loan::find()->where(["deal_stage" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->all()
					: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->all()
			);
			foreach ($loans as $loan) {
				$am = $am+$loan->getCeo($loan);
				$am2 = $am2+($loan->bill ? $loan->bill->amount : 0);
			}
			
			$partners[] = [
					"ID" => $id,
					"Nosaukums" => $title,
					"Skaits" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
							? Loan::find()->where(["deal_stage" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->count()
							: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->count()
					),
					"Summa" => (Yii::$app->getRequest()->get("from") && Yii::$app->getRequest()->get("to")
							? Loan::find()->where(["deal_stage" => $id])->andWhere(['between', Yii::$app->getRequest()->get("what"), getFrom(), getTo()])->andWhere(["status" => 5])->sum("loan.amount")
							: Loan::find()->where(["deal_stage" => $id])->andWhere(["status" => 5])->sum("loan.amount")
					),
					"Peļņa" => $am,
					"Komisija" => $am2,
			];
		}
		
		$provider = new ArrayDataProvider([
				'allModels' => $partners,
				'pagination' => false,
				'sort' => false,
		]);
		
		echo "<h2>Partners</h2>";
		echo yii\grid\GridView::widget([
				'dataProvider' => $provider,
				'rowOptions' => function ($model, $key, $index, $grid) {
				return [
						"onclick" => "checkRedirect(this, '". Url::toRoute(["admin/stats", "id" => $model["ID"]])  ."');",
				];
		},
		]);
		$id = null;
	}
	?>
			<div class="well">
				<?php 
				if ($id) {
					echo Html::beginForm(['admin/stats', "id" => $id], 'get');
				} else {
  					echo Html::beginForm(['admin/stats'], 'get');
				}
				?>
   				<div class="form-group">
   					<div class="row">
	   					<div class="col-xs-4">
		   					<?= DateTimePicker::widget([
		   						 'name' => 'from',
		   						 'value' => Yii::$app->getRequest()->get("from"),
		   						 'options' => ["format" => "d.m.Y", "id" => "datepicker2", "placeholder" => Yii::t("app/stats", "Date from"), 'timepicker' => false]
							]); ?>
	   					</div>		  				
	   					<div class="col-xs-4">
		   					<?= DateTimePicker::widget([
		   						'name' => 'to',
		   						'value' => Yii::$app->getRequest()->get("to"),	
		    					'options' => ["format" => "d.m.Y", "id" => "datepicker4", "placeholder" => Yii::t("app/stats", "Date to"), 'timepicker' => false]
							]); ?>
	   					</div>	
	   					<div class="col-xs-4">
	   						<div class="form-group">
								<select name="what" class="form-control">
									<option value="create_time" <?= (\Yii::$app->getRequest()->get("what") == "create_time" ? " selected='selected'" : "") ?>>Create date</option>
									<option value="close_time" <?= (\Yii::$app->getRequest()->get("what") == "close_time" ? " selected='selected'" : "") ?>>Close date</option>
									<option value="update_time" <?= (\Yii::$app->getRequest()->get("what") == "update_time" ? " selected='selected'" : "") ?>>Update date</option>
								</select>
							</div>
	   					</div>	  				
   					</div>		  				
  				</div>
  				<div class="form-group">
			        <?= Html::submitButton(Yii::t('app/stats', 'Search'), ['class' => 'btn btn-primary']) ?>
			    </div>
  				<?= Html::endForm() ?>
			</div>
		<?php
	?>
	
</div>






<?php 
 function getFrom() {
	return strtotime("midnight", strtotime(Yii::$app->getRequest()->get("from")));
}

 function getTo() {
	return strtotime("tomorrow", strtotime(Yii::$app->getRequest()->get("to"))) - 1;
}
?>

