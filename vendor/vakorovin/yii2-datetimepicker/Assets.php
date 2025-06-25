<?php

namespace vakorovin\datetimepicker;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
	public $sourcePath = '@vakorovin/datetimepicker/picker';

    public $js = [
        'jquery.datetimepicker.js',
    ];

    public $css = [
        'jquery.datetimepicker.css',
    ];

	public $depends = [
		'yii\web\YiiAsset',
	];
}
