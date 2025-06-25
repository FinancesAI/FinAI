<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/mail', 'Mails');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/mail', 'Create Mail'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			'order_by',
            'custom_id',
        	'menu_title',
        	'title',
        	'in_menu',
        	'api_type',
        		
            ['class' => 'app\base\grid\ActionColumn'],
        ],
    ]); ?>
    
    <?= $this->render("_about"); ?>
    
</div>
