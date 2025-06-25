<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MessageTemplate */

$this->title = Yii::t('app/message-template', 'Update Message Template: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/message-template', 'Message Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/message-template', 'Update');
?>
<div class="message-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
