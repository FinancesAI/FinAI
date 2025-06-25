<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\form\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app/user', 'Login') . ' | ' . Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Yii::t('app/user', 'Login') ?></h1>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
	<?= $form->field($model, 'password')->passwordInput() ?>
        <div class="form-group">
        	<?= Html::submitButton(
                Yii::t('app/user', 'Login'),
                ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']
            ) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
