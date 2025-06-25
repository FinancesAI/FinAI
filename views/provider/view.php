<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Provider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/provider', 'Providers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="provider-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/provider', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?//= Html::a(Yii::t('app/provider', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('app/provider', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'label' => Yii::t('app/provider', 'Sources'),
                'format' => 'ntext',
                'attribute'=>'sources',
                'value' => function($model) {
                    foreach ($model->sources as $source) {
                        $sourceNames[] = $source->name;
                    }
                    return $sourceNames ? implode("\n", $sourceNames) : '-';
                },
            ],
            [
                'label' => Yii::t('app/provider', 'Activity'),
                'format' => 'ntext',
                'attribute'=>'enable',
                'value' => function($model) {
                    return $model->enable ? Yii::t('app/provider', 'Activated') : Yii::t('app/provider', 'Deactivated');
                },
            ],
        ],
    ]) ?>

</div>
