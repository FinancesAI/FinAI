<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = [ 'label' => Yii::t( 'app/user', 'Users' ), 'url' => [ 'index' ] ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode( $this->title ) ?></h1>

	<?= $this->render( "/admin/_nav" ); ?>

    <p>
		<?= Html::a( Yii::t( 'app/user', 'Update' ), [
			'update',
			'id' => $model->id
		], [ 'class' => 'btn btn-primary' ] ) ?>
        <?= Html::a(
            Yii::t("app/user", "Change password"),
            ["user/update", "id" => $model->id, "task" => "change-password"],
            [
                "class" => "btn btn-info"
            ]
        ) ?>
        <?= Html::a( Yii::t( 'app/user', 'Delete' ), [ 'delete', 'id' => $model->id ], [
            'class' => 'btn btn-link text-danger',
            'data'  => [
                'confirm' => Yii::t( 'app/user', 'Are you sure you want to delete this item?' ),
                'method'  => 'post',
            ],
        ] ) ?>
    </p>

	<?= DetailView::widget( [
		'model'      => $model,
		'attributes' => [
			'id',
			'fullname:ntext',
			'email:email',
			[
				"attribute" => "role",
				"value"     => $model->getRoles()[ $model->role ],
			],
            [
                "attribute" => "status",
                "value"     => $model->getStatuses()[ $model->status ],
            ],
//			'provider_id',
            [
                "attribute" => "provider_id",
                "value"     => function ( $model ) {
                    return $model->provider ? $model->provider->name . ' (№ ' . $model->provider->id . ')' : '-';
                },
            ],
			'access_token',
			'create_time:datetime',
			'update_time:datetime',
            'activation_time:datetime',
		],
	] ) ?>

</div>
