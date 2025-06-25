<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$countersMessagesNew = ArrayHelper::getValue($this->params, 'countersMessagesNew');

$nav = NavBar::begin([
    'brandLabel' => '<div class="navbar-logo"></div>',
    'innerContainerOptions' => ['class' => 'contailer-fluid'],
    'renderInnerContainer' => false,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-expand-lg navbar-light navbar-inverse',
    ],
]);

$items[] = [
    'label' => Yii::t("app/site", "Loans"),
    'url' => Url::toRoute(["/bank-loans"])
];

$items[] = [
    'label' => Yii::t("app/site", "Templates"),
    'url' => Url::toRoute(["/message-template"])
];

$items[] = [
    'label' => Yii::t('app/site', 'Calculators'),
    'url' => Url::toRoute(["/calc"])
];

if (Yii::$app->user->identity->role == 2 && Yii::$app->user->identity->chat_enable == 1) {
    $countersBadge = '&nbsp;' . Html::tag(
            'span',
            $countersMessagesNew,
            [
                'class' => 'badge messages-new'  . ($countersMessagesNew ? '' : ' hidden'),
                'data-count' => $countersMessagesNew
            ]
        );

    $items[] =[
        'label' => Yii::t( "app/site", "Chat" ) . $countersBadge,
        'url'   => Url::toRoute( [ "/chat/messages" ] ),
        'encode' => false
    ];
}

$items[] = [
    'label' => Yii::t("app/site", "Language"),
    'items' => [
        [
            'label' => Yii::t("app/site", "lv"),
            'url' => ['/lang/set?lang=lv'],
            ["method" => "post"]
        ],
        [
            'label' => Yii::t("app/site", "ru"),
            'url' => ['/lang/set?lang=ru'],
            ["method" => "post"]
        ],
        [
            'label' => Yii::t("app/site", "en"),
            'url' => ['/lang/set?lang=en'],
            ["method" => "post"]
        ],
    ],
    'encode' => false
];

$items[] = [
    'label' => Yii::$app->user->identity->fullname,
    'options' => ['style' => 'text-transform:capitalize'],
    'items' => [
        ['label' => Yii::t("app/site", "Logout"), 'url' => ['/user/logout'], ["method" => "post"]],
    ],
    'encode' => false
];

echo Nav::widget([
    'options' => ['class' => 'navbar-nav ml-auto'],
    'items' => $items,
]);
NavBar::end();
