<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Setting */

$this->title = Yii::t('app/setting', 'Create Setting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/setting', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
