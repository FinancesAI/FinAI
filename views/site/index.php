<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;
use vakorovin\datetimepicker\Datetimepicker;
use yii\base\Widget;

use app\models\Loan;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use app\models\Changes;

/* @var $this yii\web\View */

$this->title = Yii::t("app/stats", "Stats");

function ignore_divide_by_zero($errno, $errstring)
{
	return ($errstring == 'Division by zero');
}

set_error_handler('ignore_divide_by_zero', E_WARNING);
?>
<div class="site-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
			<div class="well">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center" data-toggle="tooltip" data-placement="top" title="<?= $tooltip ?>"><?= (\Yii::$app->getRequest()->get("from") ? Yii::t("app/stats", "Period total") : Yii::t("app/stats", "Total")) ?></h4>
						<h3 class="lead text-center blue"><?= floor($total) ?> €</h3>
					</div>
					<?php if ($month) { ?>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center"><?= Yii::t("app/stats", "Month") ?></h4>
						<h3 class="lead text-center blue"><?= floor($month) ?> €</h3>
					</div>
					<?php } ?>
					<?php if ($week) { ?>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center"><?= Yii::t("app/stats", "Week") ?></h4>
						<h3 class="lead text-center blue"><?= floor($week) ?> €</h3>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="well">
  				<?= Html::beginForm(['index'], 'get') ?>
   				<div class="form-group">
  					<?= Html::dropDownList("user_id", Yii::$app->getRequest()->get("user_id"), $partners,['class'=>'form-control','prompt' => Yii::t("app/stats", "All users")]) ?>
  				</div>
   				<div class="form-group">
  					<?= Html::dropDownList("product", Yii::$app->getRequest()->get("product"), $products,['class'=>'form-control','prompt' => Yii::t("app/stats", "All products")]) ?>
  				</div>
   				<div class="form-group">
  					<?= Html::dropDownList("source", Yii::$app->getRequest()->get("source"), $sources,['class'=>'form-control','prompt' => Yii::t("app/stats", "All sources")]) ?>
  				</div>
   				<div class="form-group">
  					<?= Html::dropDownList("deal_stage", Yii::$app->getRequest()->get("deal_stage"), $dealStages,['class'=>'form-control','prompt' => Yii::t("app/stats", "All deal stage")]) ?>
  				</div>
   				<div class="form-group">
   					<div class="row">
	   					<div class="col-xs-6">
		   					<?= DateTimePicker::widget([
		   						 'name' => 'from',
		   						 'value' => Yii::$app->getRequest()->get("from"),
		   						 'options' => ["format" => "d.m.Y", "id" => "datepicker2", "placeholder" => Yii::t("app/stats", "Date from"), 'timepicker' => false]
							]); ?>
	   					</div>
	   					<div class="col-xs-6">
		   					<?= DateTimePicker::widget([
		   						'name' => 'to',
		   						'value' => Yii::$app->getRequest()->get("to"),
		    					'options' => ["format" => "d.m.Y", "id" => "datepicker4", "placeholder" => Yii::t("app/stats", "Date to"), 'timepicker' => false]
							]); ?>
	   					</div>
   					</div>
  				</div>
  				<div class="form-group">
			        <?= Html::submitButton(Yii::t('app/stats', 'Search'), ['class' => 'btn btn-primary']) ?>
			    </div>
  				<?= Html::endForm() ?>
			</div>
		</div>


        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php
			$loan = new Loan();

			$columns = [];
			$columns[] = [
					"attribute" => Yii::t('app/stats', "All"),
					"format" => "raw",
					"value" => function ($model, $key, $index) {
						return $model["All"];
						//return "<a href=\"".Url::toRoute(["loan/index"])."\">".$model["All"]."</a>";
				}
			];
			foreach ($loan->getStatuses() as $statusId => $statusName) {

                if (in_array($statusId, [7,13,14])) {
                    continue;
                }

                $columns[] = [
						"attribute" => $statusName,
						"format" => "raw",
						"value" => function ($model, $key, $index) use ($statusName) {
							$statusId = array_flip((new Loan())->getStatuses())[$statusName];
							return $model[$statusName];
							//return "<a href=\"".Url::toRoute(["loan/index"])."?LoanSearch%5Bstatus%5D=".$statusId."\">".$model[$statusName]."</a>";
				}
				];
			}

			$provider = new ArrayDataProvider([
					'allModels' => [$data],
					'pagination' => [
							'pageSize' => 10,
					],
			]);
			?>

			<p>
			    <?= GridView::widget([
			        'dataProvider' => $provider,
			        'filterModel' => false,
			        'summary' => false,
			    	'filterPosition' => GridView::FILTER_POS_FOOTER,
			    	"tableOptions" => ['class' => Yii::$app->setting->get("table_class")." table-status"],
			    	'columns' => $columns,
			    ]); ?>
			</p>
		</div>


		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="well">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center"><?= Yii::t("app/stats", "Clients") ?></h4>
						<h2 class="text-center blue"><?= $clients ?></h2>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center"><?= Yii::t("app/stats", "Loans") ?></h4>
						<h2 class="text-center blue"><?= $loans ?></h2>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<h4 class="text-center"><?= Yii::t("app/stats", "Loans closed") ?></h4>
						<h2 class="text-center blue"><?= $loansClosed ?></h2>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="row">
				<div class="col-xs-4">
					<div class="well">
						<h2 class="text-center" data-toggle="tooltip" data-placement="top" title="<?= $compare[2]["tooltip"] ?>"><?= $compare[2]["month"] ?></h2>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total loan count"><small><?= $compare[2]["countAll"] ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total unique loan count"><small><?= $compare[2]["countUnique"] ?></small></p>

						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Fully completed loans"><small><?= $compare[2]["countUnique"]-$compare[2]["countUniqueF"] ?> - <?= number_format((($compare[2]["countUnique"]-$compare[2]["countUniqueF"])/$compare[2]["countUnique"])*100, 2) ?>%</small></p>

						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="Paid out loan count"><?= $compare[2]["count"] ?></p>
						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="CR - paid out/unique count"><small><?= number_format($compare[2]["count"]/$compare[2]["countUnique"], 3) ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Paid out amount"><?= $compare[2]["sum"] ?> €</p>
											<?= getUserPeriodChanges($compare[2]["s"], $compare[2]["e"]) ?>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="well">
						<h2 class="text-center" data-toggle="tooltip" data-placement="top" title="<?= $compare[1]["tooltip"] ?>"><?= $compare[1]["month"] ?></h2>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total loan count"><small><?= $compare[1]["countAll"] ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total unique loan count"><small><?= $compare[1]["countUnique"] ?></small></p>

						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Fully completed loans"><small><?= $compare[1]["countUnique"]-$compare[1]["countUniqueF"] ?> - <?= number_format((($compare[1]["countUnique"]-$compare[1]["countUniqueF"])/$compare[1]["countUnique"])*100, 2) ?>%</small></p>

						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="Paid out loan count"><?= $compare[1]["count"] ?></p>
						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="CR - paid out/unique count"><small><?= number_format($compare[1]["count"]/$compare[1]["countUnique"], 3) ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Paid out amount"><?= $compare[1]["sum"] ?> €</p>
											<?= getUserPeriodChanges($compare[1]["s"], $compare[1]["e"]) ?>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="well">
					 	<h2 class="text-center" data-toggle="tooltip" data-placement="top" title="<?= $compare[0]["tooltip"] ?>"><?= $compare[0]["month"] ?></h2>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total loan count"><small><?= $compare[0]["countAll"] ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Total unique loan count"><small><?= $compare[0]["countUnique"] ?></small></p>

						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Fully completed loans"><small><?= $compare[0]["countUnique"]-$compare[0]["countUniqueF"] ?> - <?= number_format((($compare[0]["countUnique"]-$compare[0]["countUniqueF"])/$compare[0]["countUnique"])*100, 2) ?>%</small></p>

						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="Paid out loan count"><?= $compare[0]["count"] ?></p>
						<p class="text-center mb5"  data-toggle="tooltip" data-placement="top" title="CR - paid out/unique count"><small><?= number_format($compare[0]["count"]/$compare[0]["countUnique"], 3) ?></small></p>
						<p class="text-center mb5" data-toggle="tooltip" data-placement="top" title="Paid out amount"><?= $compare[0]["sum"] ?> €</p>
						<?= getUserPeriodChanges($compare[0]["s"], $compare[0]["e"]) ?>
					</div>
				</div>
				<div id="stats-more"></div>
				<div class="clearfix"></div>
				<div class="text-center"><a href="" class="btn btn-primary stats-load" data-offset="3"><?= Yii::t('app/site', 'load_more') ?></a></div>
			</div>
<!--			<small>--><?//= Yii::t('app/site', 'note_data_based_on_create_time' ) ?><!--</small>-->
		</div>
	</div>
</div>

<?php
function getUserPeriodChanges($from, $to) {
	if (!Yii::$app->setting->get("user_stats")) {
		return "";
	}
	$html = "";
	foreach (User::find()->where(["role" => 0])->all() as $user) {
		if ($user->id == 8) {
			continue;
		}
		$u = Changes::find()->where(["user_id" => $user->id])->andWhere(["type" => "2"])->andFilterWhere([
				'between',
				'create_time',
				$from,
				$to,
		])->count();
		$u2a = Changes::find()->where(["user_id" => $user->id])->andWhere(["type" => "2"])->andFilterWhere([
				'between',
				'create_time',
				$from,
				$to,
		])->groupBy("type_id")->count();
		$u2 = @($u/$u2a);
		$html .= '<p class="text-center mb5">
			
'.$user->fullname.'
 -
<span data-toggle="tooltip" data-placement="top" title="'.$user->fullname.' - '.$u.' changes made">'.$u.'</span>
 -
<span data-toggle="tooltip" data-placement="top" title="'.$user->fullname.' - '.number_format($u2, 2).' avarage per unit">'.number_format($u2, 2).'</span> </p>';
	}

	return $html;
}
?>
