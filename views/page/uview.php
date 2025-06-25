<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Page */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/page', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-view" style="margin-top: 20px;">
<div class="well" style="white-space: pre-line;">
<h1><?= Html::encode($this->title) ?></h1>
<?= $model->content ?>
</div>	
</div>
