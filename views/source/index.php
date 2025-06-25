<?php

use app\models\Source;
use himiklab\sortablegrid\SortableGridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\base\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/source', 'Sources');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/source', 'Create Source'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= SortableGridView ::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        "tableOptions" => ['class' => Yii::$app->setting->get("table_class")." table-sort"],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'wordpress_id',
            'name',
//            'lead_event_sid',
//            'sales_event_sid',
            //'order',
            [
                'class' => ActionColumn::className(),
                'template'=>'{update}',
                'urlCreator' => function ($action, Source $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
