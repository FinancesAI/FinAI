<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app/user', 'Update {modelClass}: ', [
        'modelClass' => 'User',
    ]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/user', 'Update');
?>
<div class="user-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render("/admin/_nav"); ?>
    <?= Html::a(
        Yii::t("app/user", "Change password"),
        ["user/update", "id" => $model->id, "task" => "change-password"],
        [
            "class" => "btn btn-info"
        ]
    ) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
