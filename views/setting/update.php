<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Setting */

$this->title = Yii::t('app/setting', 'Update {modelClass}: ', [
    'modelClass' => 'Setting',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/setting', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/setting', 'Update');
?>
<div class="setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
