<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app/admin', 'Admin logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-logs">

    <h1><?= Html::encode($this->title) ?></h1>


	<?= $this->render("_nav"); ?>

</div>