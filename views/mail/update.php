<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */

$this->title = Yii::t('app/mail', 'Update {modelClass}: ', [
    'modelClass' => 'Mail',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/mail', 'Mails'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/mail', 'Update');
?>
<div class="mail-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
