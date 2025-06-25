<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Field */

$this->title = Yii::t('app/field', 'Create Field');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/field', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
