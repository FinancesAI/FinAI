<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Field */

$this->title = Yii::t('app/field', 'Update {modelClass}: ', [
    'modelClass' => 'Field',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/field', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/field', 'Update');
?>
<div class="field-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
