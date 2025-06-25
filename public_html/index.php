<?php

// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);defined('YII_ENV') or define('YII_ENV', 'dev');
//ini_set('memory_limit', '800M');
//defined('IS_WEB') or define('IS_WEB', true);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

include '../components/functions.php';

(new yii\web\Application($config))->run();

