<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/setting', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/setting', 'Create Setting'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	'filterPosition' => GridView::FILTER_POS_FOOTER,	
        'columns' => [
            'name',
            'value',
            ['class' => 'app\base\grid\ActionColumn', 'template'=>'{update} {delete}',],
        ],
    ]); ?>
</div>
