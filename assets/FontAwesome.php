<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 *
 */
class FontAwesome extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bower/font-awesome';


    /**
     * @var string[]
     */
    public $css = [
        'css/font-awesome.min.css',
    ];

    /**
     * @var \string[][]
     */
    public $publishOptions = [
        'only' => [
            'fonts/*',
            'css/*',
        ],
        'forceCopy' => true
    ];
}