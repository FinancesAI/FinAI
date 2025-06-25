<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 *
 */
class AngularJsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/angularjs';

    /**
     * @var string[]
     */
    public $css = [
        'angular-csp.css',
    ];

    /**
     * @var string[]
     */
    public $js = [
        'angular.min.js',
    ];

    /**
     * @var string[]
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
