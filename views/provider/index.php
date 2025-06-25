<?php

use app\models\Provider;
use yii\helpers\Html;
use yii\helpers\Url;
use app\base\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ProviderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/provider', 'Providers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/provider', 'Create Provider'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'   => function ( $model, $key, $index, $grid ) {
            return [
                "onclick" => "window.location = '" . Url::toRoute( [ "provider/view", "id" => $model["id"] ] ) . "';"
            ];
        },
        "tableOptions" => [ 'class' => Yii::$app->setting->get( "table_class" ) ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'label' => Yii::t('app/provider', 'Activity'),
                'format' => 'ntext',
                'attribute'=>'enable',
                'value' => function($model) {
                    return $model->enable ? Yii::t('app/provider', 'Activated') : Yii::t('app/provider', 'Deactivated');
                },
            ],
            [
                'label' => Yii::t('app/provider', 'Sources'),
                'format' => 'ntext',
                'attribute'=>'sources',
                'value' => function($model) {
                    $sourceNames = [];
                    foreach ($model->sources as $source) {
                        $sourceNames[] = $source->name;
                    }
                    return $sourceNames ? implode("\n", $sourceNames) : '-';
                },
            ],
//            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, Provider $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                 }
//            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
