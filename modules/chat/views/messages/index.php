<?php


use app\modules\chat\assets\ChatAsset;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('modules/chat', 'Messages');
$this->params['body.cssClass'] = 'body-messages';
$this->params['pageWrapper.cssClass'] = 'd-flex min-h-100';
$this->params['footer.cssClass'] = 'footer d-none d-sm-block';

ChatAsset::register($this);
?>

<div class="card card-messages my-0 my-sm-3 my-md-5"
     data-user-id="<?= Yii::$app->user->id ?>"
     data-user-avatar="<?= Html::encode('//gravatar.com/avatar/' . md5(Yii::$app->user->identity->email) . '?s=48') ?>"
     data-user-fullname="<?= Html::encode(Yii::$app->user->identity->fullname) ?>">

    <div ng-controller="MessagesController as Messages" class="h-100">

        <?= $this->render('_loader') ?>
        <?= $this->render('_empty') ?>

        <div class="row no-gutters h-100 w-100 ng-hide" ng-hide="!hasContacts() && !conversationsQuery.length">
            <div class="col-md-4 col-lg-3 d-flex flex-column col-messages-conversations">
                <?= $this->render('_sidebar') ?>
            </div>
            <div class="col-md-8 col-lg-9 col-messages-conversation">
                <div class="messages-conversation d-flex flex-column">
                    <?= $this->render('_header') ?>
                    <?= $this->render('_items') ?>
                    <?= $this->render('_input') ?>
                </div>
            </div>
        </div>
    </div>
</div>