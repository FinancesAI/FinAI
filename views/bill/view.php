<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Changes;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
	<?= $this->render("/admin/_nav"); ?>

    <p>
        <?= Html::a(Yii::t('app/user', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/user', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/user', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

	<?php 
	$sentInfo = Changes::find()
	->where(["type_id" => $model->loan_id, "attr" => "actions_13"])
	->orFilterWhere(["type_id" => $model->loan_id, "attr" => "actions_12"])
	->orFilterWhere(["type_id" => $model->loan_id, "attr" => "actions_20"]) // debt email
	->orFilterWhere(["type_id" => $model->loan_id, "attr" => "actions_35"]) //info sms
	->orFilterWhere(["type_id" => $model->loan_id, "attr" => "actions_36"]) //alert sms
	->orFilterWhere(["type_id" => $model->loan_id, "attr" => "actions_38"]) //debt sms
	->orderBy("create_time desc")->all();
	
	if ($sentInfo) {
	    $html = "<h2>Contact activities</h2>";
	    foreach ($sentInfo as $info) {
	        $html .= "<small>".Yii::$app->formatter->asDate($info->create_time)."</small> - ".Changes::getAttrLabel($info->attr, 2)."<br/>";
	    }
	} else {
	    $html = "Nav nekas sūtīts.";
	}
	echo $html;
	?>

</div>
