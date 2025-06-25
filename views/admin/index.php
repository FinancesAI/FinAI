<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app/admin', 'Admin index');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("_nav"); ?>

	<p>
		<?= Html::a(Yii::t("app/admin", "Clear cache"), ["admin/index", "task" => "clear-cache"]) ?>
		<br />
		<?= Html::a(Yii::t("app/admin", "Clear export cache"), ["admin/index", "task" => "clear-export"]) ?>
	</p>

</div>