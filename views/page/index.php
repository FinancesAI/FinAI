<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/page', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/page', 'Create Page'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
        	'title',
        	'in_menu',
        		
            ['class' => 'app\base\grid\ActionColumn'],
        ],
    ]); ?>
    
</div>
