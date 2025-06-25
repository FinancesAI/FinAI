<?php

/* @var $this View */
/* @var $content string */

use yii\helpers\Html;
use yii\web\View;

$bodyClass = isset($this->params['body.cssClass']) ? $this->params['body.cssClass'] : 'body-default';
$pageWrapperClass = $this->params['pageWrapper.cssClass'] ?? '';
?>
<?php
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="baseUrl" content="<?= \yii\helpers\Url::to(['/'], true) ?>">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="stylesheet"
          href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700">
    <title><?= Html::encode($this->title) ?> - <?= Yii::$app->setting->get("application_name") ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php
    $this->head() ?>
</head>
<body class="<?= $bodyClass ?>"  ng-app="yiiChat">
<?php
$this->beginBody() ?>
<div class="page page-fill-wrapper <?= $pageWrapperClass ?>">
    <div class="page-fill d-flex flex-fill flex-column align-items-stretch">
        <div class="content d-flex flex-column" style="flex: 1; ">
            <?php
                if (!Yii::$app->getUser()->getIdentity()->isAdmin()):
                    echo $this->render("@app/views/layouts/_bank_navbar.php");
                else:
                    echo $this->render("@app/views/layouts/_admin_navbar");
                endif;
            ?>
            <h1><?= Html::encode($this->title) ?></h1>

            <?php if (Yii::$app->getUser()->getIdentity()->isAdmin()): ?>

            <p class="fixed-btn">
                <button type="button" class="btn3 btn3-primary" data-toggle="modal" data-target="#newsletter"><?= Yii::t("modules/chat", "Newsletter") ?></button>
            </p>

            <?php endif; ?>

            <div class="container d-flex flex-row" style=" flex: 1;">
                <?php
                echo $content ?>
            </div>
<!--            <footer class="footer">-->
<!--                <div class="container">-->
<!--                    <div class="container">-->
<!--                        <div class="row"><div class="col">--><?//= Yii::t( "app/site", "footer_copyright" ) ?><!--</div></div>-->
<!--                        <div class="row"><div class="col">--><?//= Yii::t( "app/site", "footer_phone" ) ?><!--</div></div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </footer>-->

            <?= $this->render("_footer") ?>
        </div>
    </div>
</div>

<?php if (Yii::$app->getUser()->getIdentity()->isAdmin()): ?>

<?= $this->render("/modal/_form_newsletter") ?>

<?php endif; ?>

<?php
$this->endBody() ?>
</body>
</html>
<?php
$this->endPage() ?>
