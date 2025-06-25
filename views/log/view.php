<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Log */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/log', 'Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <h1><?= Html::encode($this->title) ?></h1>

	<?= $this->render("/admin/_nav"); ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
    				"attribute" => "type",
    				"value" => $model->typeLabels()[$model->type],
    		],
            'data:ntext',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
