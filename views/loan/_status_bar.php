<?php


use app\models\Loan;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
$loan = new Loan();

$whereIn = "";
if (isset($_COOKIE["global_filter"]) && $_COOKIE["global_filter"] !== "0") {
	$whereIn = $_COOKIE["global_filter"];
}

if (!$data = Yii::$app->cache->get("status_bar_data".$whereIn)) {
	$data = [];
	foreach ($loan->getStatuses() as $statusId => $statusName) {
		if ($whereIn) {
			$data[$statusName] = Loan::find()->where(["status" => $statusId])->andWhere(["in", "product", explode(",", $whereIn)])->count();
		} else {
			$data[$statusName] = Loan::find()->where(["status" => $statusId])->count();
		}
	}

	Yii::$app->cache->set("status_bar_data".$whereIn, $data);
}

$columns = [];
$columns[] = [
		"attribute" => Yii::t("app/loan", "All"),
		"label" => Yii::t("app/loan", "All"),
		"format" => "raw",
		"value" => function ($model, $key, $index) use ($whereIn) {
			if ($whereIn) {
				$count = Loan::find()->andWhere(["in", "product", explode(",", $whereIn)])->count();
			} else {
				$count = Loan::find()->count();
			}
			return "<a href=\"".Url::toRoute(["loan/index"])."\">".$count."</a>";
		}
];
foreach ($loan->getStatuses() as $statusId => $statusName) {
	$columns[] = [
			"attribute" => $statusName,
            "label" => $statusName,
			"format" => "raw",
			"value" => function ($model, $key, $index) use ($statusName) {
				$statusId = array_flip((new Loan())->getStatuses())[$statusName];
				if ($statusId == Loan::STATUS_CLOSED) {
					return "<a href=\"".Url::toRoute(["loan/index"])."?sort=-close_time&LoanSearch%5Bstatus%5D=".$statusId."\">".$model[$statusName]."</a>";
				}

				return "<a href=\"".Url::toRoute(["loan/index"])."?LoanSearch%5Bstatus%5D=".$statusId."&sort=update_time\">".$model[$statusName]."</a>";
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
