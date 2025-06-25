<?php

use app\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t( 'app/user', 'Users' );
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode( $this->title ) ?></h1>

	<?= $this->render( "/admin/_nav" ); ?>

	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?= Html::a( Yii::t( 'app/user', 'Create User' ), [ 'create' ], [ 'class' => 'btn btn-success' ] ) ?>
    </p>
	<?= GridView::widget( [
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		"tableOptions" => [ 'class' => Yii::$app->setting->get( "table_class" ) ],
		'rowOptions'   => function ( $model, $key, $index, $grid ) {
			return [
				"onclick" => "window.location = '" . Url::toRoute( [ "user/view", "id" => $model["id"] ] ) . "';"
			];
		},
		'columns'      => [
			'id',
//			'provider_id',
            [
                "attribute" => "provider_id",
                "value"     => function ( $model ) {
                    return $model->provider ? $model->provider->name . ' (№ ' . $model->provider->id . ')' : '-';
                },
            ],
			'fullname:ntext',
			'email:email',
			[
				"attribute" => "role",
				'filter'    => Html::activeDropDownList( $searchModel, 'role', ( new  User() )->getRoles(), [
					'class'  => 'form-control',
					'prompt' => 'All'
				] ),
				"value"     => function ( $model ) {
					return $model->getRoles()[ $model->role ];
				},
			],
		],
	] ); ?>
</div>
