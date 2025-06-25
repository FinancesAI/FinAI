<div class="row">
	<div class="col-xs-12 col-md-6">
	<?php 
	/* @var $searchModel app\models\search\LoanSearch */
	use yii\bootstrap\Collapse;
	use yii\grid\GridView;
	use yii\data\ArrayDataProvider;

	$filteredAttrs = [];
	foreach($searchModel->getAttributes() as $attr => $attrValue) {
		if ($attrValue !== null && $attrValue !== "")
			$filteredAttrs[] = ["title" => $searchModel->getAttributeLabel($attr), "value" => $searchModel->getValue($attr, $searchModel)];
	}
	
	$provider = new ArrayDataProvider([
			'allModels' => $filteredAttrs,
	]);

//	echo Collapse::widget([
//			'items' => [
//					[
//							'label' => Yii::t('app/loan', 'Filtered parameters: ').count($filteredAttrs),
//							'content' => GridView::widget([
//						        'dataProvider' => $provider,
//						        'filterModel' => false,
//						        'summary' => false,
//						    	'filterPosition' => GridView::FILTER_POS_FOOTER,
//						]),
//					],
//			]
//	]);
	?>
	</div>
	
	<div class="col-xs-12 col-md-6">
		<form action="">
			<div class="input-group fast-search">
		      	<input type="text" name="q" class="form-control">
		      	<span class="input-group-btn">
		        	<button class="btn btn-default" type="submit"><?= Yii::t("app/loan", "Search") ?></button>
		      	</span>
		    </div>
	    </form>
	</div>
	
</div>