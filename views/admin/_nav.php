<?php

use yii\bootstrap\Nav;
use yii\widgets\Breadcrumbs;

$items = [];
$items['admin'] = [
    'label' => Yii::t('app/admin', 'Index'),
    'url' => ['admin/index'],
];
$items['providers'] = [
    'label' => Yii::t('app/admin', 'Providers'),
    'url' => ['provider/index'],
];
$items['users'] = [
    'label' => Yii::t('app/admin', 'Users'),
    'url' => ['user/index'],
];
$items['partial-data'] = [
    'label' => Yii::t('app/admin', 'Partial Data'),
    'url' => ['partial-data/index'],
];
$items['sources'] = [
    'label' => Yii::t('app/admin', 'Sources'),
    'url' => ['source/index'],
];
$items['fields'] = [
    'label' => Yii::t('app/admin', 'Fields'),
    'url' => ['field/index'],
];
$items['modules'] = [
    'label' => Yii::t('app/admin', 'Modules'),
    'url' => ['admin/modules'],
];
$items['logs'] = [
    'label' => Yii::t('app/admin', 'Logs'),
    'url' => ['log/index'],
];
$items['settings'] = [
    'label' => Yii::t('app/admin', 'Settings'),
    'url' => ['setting/index'],
];
$items['others'] = [
    'label' => Yii::t('app/admin', 'Others'),
    'items' => []
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Stats overview'),
    'url' => ['admin/stats'],
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Finance'),
    'url' => ['admin/finance'],
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Mail'),
    'url' => ['mail/index'],
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Bill'),
    'url' => ['bill/index'],
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Page'),
    'url' => ['page/index'],
];
$items['others']['items'][] = [
    'label' => Yii::t('app/admin', 'Wordpress Link'),
    'url' => ['wordpress-link/index'],
];
/*
?>
<div class="admin-breadcrumbs"><?= Breadcrumbs::widget([
        'links' => $this->params['breadcrumbs'] ?? [],
    ]) ?></div>
*/
?>
<div class="admin-navigation"><?= Nav::widget(
        [
            'items' => $items,
            'options' => ['class' => 'nav nav-pills nav-justified'],
        ]
    ) ?></div>