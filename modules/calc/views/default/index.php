<?php

use app\modules\calc\components\CalcMaxCreditWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('modules/calc', 'Title');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="calc-max-credit-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= CalcMaxCreditWidget::widget(); ?>
</div>
