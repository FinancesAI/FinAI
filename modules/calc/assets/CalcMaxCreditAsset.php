<?php

namespace app\modules\calc\assets;

use yii\web\AssetBundle;

/**
 *
 */
class CalcMaxCreditAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/modules/calc/assets/dist';


    /**
     * @var string[]
     */
    public $css = [
        'css/app.css',
    ];

    /**
     * @var string[]
     */
    public $js = [
        'js/app.js',
    ];

    public $publishOptions = [
        'only' => [
            'images/*',
            'js/*',
            'css/*',
        ],
        'forceCopy' => true
    ];

    /**
     * @var string[]
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'app\assets\AppAsset',
        'yii\jui\JuiAsset',
    ];
}
