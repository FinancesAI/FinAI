<?php

use app\models\Loan;
use app\models\Page;

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

NavBar::begin([
    'brandLabel' => '<div class="navbar-logo"></div>',
    'innerContainerOptions' => ['class' => 'contailer-fluid'],
    'renderInnerContainer' => false,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-expand-lg navbar-light navbar-inverse',
    ],
]);
$p = [];
$pages = Page::find()->where(["in_menu" => 1])->all();
foreach ($pages as $page) {
    $p[] = ['label' => $page->title, 'url' => Url::toRoute(["/page/uview", "id" => $page->id])];
}

$itemsPages = ['label' => Yii::t("app/site", "Templates"), 'url' => ['/message-template'], 'items' => $p];


$items = [
    ['label' => Yii::t("app/site", "Dashboard"), 'url' => ['/site/index']],
    [
        'label' => Yii::t("app/site", "Loans"),
        'url' => Url::toRoute(["/loan/index"]) . Yii::$app->setting->get("default_loan_url")
    ],
    ['label' => Yii::t("app/site", "Appointment"), 'url' => Url::toRoute(["/loan/appointment"])],
    [
        'label' => Yii::t("app/site", "Contacts"),
        'url' => Url::toRoute(["/person/index"]) . Yii::$app->setting->get("default_person_url")
    ],
    $itemsPages,
];


if (Yii::$app->getUser()->getIdentity() && Yii::$app->getUser()->getIdentity()->isAdmin()) {
    $items = [
        ['label' => Yii::t("app/site", "Dashboard"), 'url' => ['/site/index']],
        [
            'label' => Yii::t("app/site", "Loans"),
            'url' => Url::toRoute(["/loan/index"]) . Yii::$app->setting->get("default_loan_url")
        ],
        ['label' => Yii::t("app/site", "Appointment"), 'url' => Url::toRoute(["/loan/appointment"])],

        ['label' => Yii::t("app/loan", "Accepted loans"), 'url' => Url::toRoute(["/loan/index", "acceptance" => 1])],
        [
            'label' => Yii::t("app/site", "Contacts"),
            'url' => Url::toRoute(["/person/index"]) . Yii::$app->setting->get("default_person_url")
        ],
        $itemsPages,
        [
            'label' => Yii::t("app/site", "Admin"),
            'url' => ['/admin/index'],
            'items'  => [
                [
                    'label' => Yii::t( "app/site", "Chat" ),
                    'url'   => Url::toRoute( [ "/chat/messages" ] ),
                ],
                [
                    'label' => Yii::t( "app/site", "Settings" ),
                    'url'   => Url::toRoute( [ "/admin/index" ] ),
                ],
            ],
        ],
    ];
}

/* Global filters box */
$filters = "<div class='global-filters'>";
$checkedFilters = (isset($_COOKIE["global_filter_op"]) ? $_COOKIE["global_filter_op"] : "0");
$checkedFiltersOp = (isset($_COOKIE["global_filter_op"]) ? $_COOKIE["global_filter_op"] : "0");
foreach ((new Loan())->getProducts() as $productId => $productName) {
    $checked = ((strpos((string)$checkedFilters, (string)$productId) !== false) ? "checked=checked" : "");
    $filters .= '<div class="checkbox">
		    <label>
		      <input type="checkbox" data-id="' . $productId . '" ' . $checked . '> ' . str_replace(
            "-",
            "All",
            $productName
        ) . '
		    </label>
		  </div>';
}
$filters .= "</div>";
//$filters .= '<div class="checkbox" style="padding:10px;">
//		    <label>
//		      <input type="checkbox" value="1" data-id="" ' . ($checkedFiltersOp ? "checked='checked'" : "") . ' class="global-filters-op"> Hide Operator
//		    </label>
//		  </div>';

$items[] = [
    'label' => "<span class='glyphicon glyphicon-cog'></span>",
//    'submenuOptions' => [ 'class' => 'dropdown-menu-right' ],
    'items' => [
        $filters,
        '<div class="dropdown-divider"></div>',
        ['label' => Yii::t("app/site", "Logout"), 'url' => ['/user/logout'], ["method" => "post"]],
    ],
    'encode' => false
];

$items[] = [
    'label' => Yii::t("app/site", "Language"),
//    'submenuOptions' => [ 'class' => 'dropdown-menu-right' ],
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

echo Nav::widget([
    'options' => ['class' => 'navbar-nav ml-auto'],
    'items' => $items,
]);
NavBar::end();
