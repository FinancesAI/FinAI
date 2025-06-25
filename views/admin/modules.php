<?php

use yii\helpers\Html;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */

$this->title = Yii::t('app/admin', 'Admin modules');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-modules">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("_nav"); ?>

	<?php 
		
		$modules = [];
		foreach (\Yii::$app->params["modules"] as $title => $enabled)
			$modules[] = ["title" => $title, "enable" => ($enabled ? "Yes" : "No")];
		
		$provider = new ArrayDataProvider([
				'allModels' => $modules,
				'pagination' => false,
				'sort' => false,
		]);
		
		echo "<h2>Modules</h2>";
		echo yii\grid\GridView::widget([
				'dataProvider' => $provider,
		]);
		

		$api = [];
		foreach (\Yii::$app->params["api"] as $title => $enabled)
			$api[] = ["title" => $title, "enable" => ($enabled ? "Yes" : "No")];
		
		$provider = new ArrayDataProvider([
				'allModels' => $api,
				'pagination' => false,
				'sort' => false,
		]);
		
		echo "<h2>API</h2>";
		echo yii\grid\GridView::widget([
				'dataProvider' => $provider,
		]);
	?>

</div>