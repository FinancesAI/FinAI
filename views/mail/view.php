<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Mail */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/mail', 'Mails'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/mail', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/mail', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/mail', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'custom_id',
        	'title',
        	'content:ntext',
            'menu_title',
            'in_menu',
            'api_type',
        ],
    ]) ?>

	<h1>Preview</h1>
	
	<p class="lead"><?= $model->getSubject("latfinance") ?></p>

	<div class="well">
		<?= $model->getHtml("latfinance", null) ?>
	</div>
	
</div>
