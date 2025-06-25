<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
$queue = require(__DIR__ . '/queue.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'aliases' => [
        '@modules' => '@app/modules'
    ],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'chat' => [
            'class' => 'app\modules\chat\Module',
        ],
    ],
    'components' => [
        'mailer' => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport'        => [
               'class'      => 'Swift_SmtpTransport',
                'host'       => 'mail.inbox.eu',
                'username'   => 'it@finlat.lv',
                'password'   => 'iHS!?nkF4H',
                'port'       => '587',
                'encryption' => 'tls',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info', 'warning'],
                    'categories' => ['yii\queue\*'],
                    'logFile' => '@app/runtime/logs/queue.log',
                    'logVars' => []
                ],
            ],
        ],
        'field' => [
            'class' => 'app\models\Field',
        ],
        'systemlog' => [
            'class' => 'app\models\Log',
        ],
        'setting' => [
            'class' => 'app\models\Setting',
        ],
        'user' => [
        	'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
    		'i18n' => [
    				'translations' => [
    						'*' => [
    								'class' => 'yii\i18n\PhpMessageSource',
    								'basePath' => '@app/messages', // if advanced application, set @frontend/messages
    								'sourceLanguage' => 'en',
    								'fileMap' => [
    								],
    						],
    				],
    		],
        'db' => $db,
        'queue' => $queue,
    ],
		
    'params' => $params,
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
//            'migrationPath' => null,
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
                'app\modules\chat\migrations'
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
