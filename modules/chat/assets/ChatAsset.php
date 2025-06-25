<?php

namespace app\modules\chat\assets;

use app\assets\AngularJsAsset;
use app\assets\FontAwesome;
use app\assets\HowlerJsAsset;
use yii\bootstrap4\BootstrapPluginAsset;
use yii\web\AssetBundle;

/**
 *
 */
class ChatAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/chat/assets/dist';


    /**
     * @var string[]
     */
    public $css = [
        'css/chat.min.css',
    ];

    /**
     * @var string[]
     */
    public $js = [
        'js/chat.min.js',
    ];

    public $publishOptions = [
        'only' => [
            'fonts/feather/*',
            'js/*',
            'css/*',
        ],
        'forceCopy' => true
    ];

    /**
     * @var string[]
     */
    public $depends = [
        FontAwesome::class,
        BootstrapPluginAsset::class,
        AngularJsAsset::class,
        HowlerJsAsset::class,
    ];
}
