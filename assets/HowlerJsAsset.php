<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 *
 */
class HowlerJsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var bool[]
     */
    public $publishOptions = [
        'forceCopy' => true
    ];

    /**
     * @var string[]
     */
    public $js = [
        'js/howler.min.js',
    ];
}
