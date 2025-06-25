<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LoanColor */

$this->title = Yii::t('app', 'Create Loan Color');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Colors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-color-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
