<?php

use app\components\WordpressLink;

$params = require( __DIR__ . '/params.php' );

$config = [
	'id'         => 'basic',
	'name'         => getenv('APP_URL') ?: 'broker.finlat.lv',
	'timezone'   => getenv('TIMEZONE') ?: 'Europe/Moscow',
	'basePath'   => dirname( __DIR__ ),
	'bootstrap'  => [ 'log', 'GlobalClass', 'queue' ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@modules' => '@app/modules'
    ],
    'modules' => [
        'chat' => [
            'class' => 'app\modules\chat\Module',
        ],
        'calc' => [
            'class' => 'app\modules\calc\Module',
        ],
    ],
	'components' => [

        'assetManager' => [
            'appendTimestamp' => true,
        ],

        'view' => [
            'as YandexMetrika' => [
                'class' => \hiqdev\yii2\YandexMetrika\Behavior::class,
                'builder' => [
                    'class' => \hiqdev\yii2\YandexMetrika\CodeBuilder::class,
                    'id' => $params['yandexMetrika.id'],
                    'params' => $params['yandexMetrika.params'],
                ],
            ],
        ],

		'GlobalClass'  => [
			'class' => 'app\components\GlobalClass'
		],

		'request'      => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'CHANGE_THIS_SECRET_KEY_IN_PRODUCTION',
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			]
		],
		'cache'        => [
			'class' => 'yii\caching\FileCache',
		],
		'field'        => [
			'class' => 'app\models\Field',
		],
		'setting'      => [
			'class' => 'app\models\Setting',
		],
		'formatter'    => [
			'class'       => 'yii\i18n\Formatter',
			'nullDisplay' => '-',
		],
		'systemlog'    => [
			'class' => 'app\models\Log',
		],
		'user'         => [
			'identityClass' => 'app\models\User',
			//'enableAutoLogin' => true,
			'authTimeout'   => 3600,
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],

		'mailer' => [
			'class'            => 'yii\swiftmailer\Mailer',
			'useFileTransport' => true,
			'transport'        => [
				'class'      => 'Swift_SmtpTransport',
				'host'       => '',
				'username'   => '',
				'password'   => '',
				'port'       => '25',
				'encryption' => 'tls',
			],
		],

		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets'    => [
				[
					'class'  => 'yii\log\FileTarget',
					'levels' => [ 'error', 'warning' ],
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
		'db'  => require( __DIR__ . '/db.php' ),
        'queue'  => require( __DIR__ . '/queue.php' ),
		/*
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'rules' => [
			],
		],
		*/

		'urlManager' => [
			'class'           => 'yii\web\UrlManager',
			// Disable index.php
			'showScriptName'  => false,
			// Disable r= routes
			'enablePrettyUrl' => true,
			'rules'           => array(
				'<controller:\w+>/<id:\d+>'              => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>'          => '<controller>/<action>',
			),
		],
		'i18n'       => [
			'translations' => [
				'*' => [
					'class'          => 'yii\i18n\PhpMessageSource',
					'basePath'       => '@app/messages', // if advanced application, set @frontend/messages
					'sourceLanguage' => 'ru', // Изменено с 'en' на 'ru'
                    'forceTranslation' => true,
					'fileMap'        => [
					],
				],
			],
		],

        'mutex' => \yii\mutex\MysqlMutex::class,
        'wordpressLinkDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=207.154.249.174;dbname=ewjmdrmhwt',
            'username' => 'ewjmdrmhwt',
            'password' => 'ZpMX6rTpe8',
            'charset' => 'utf8',
        ],
        'wordpressLink' => [
            'class' => '\app\components\WordpressLink',
            'database' => 'wordpressLinkDb',
            'plugin' => 'wsf',
            'forms' => [
                11
            ],
        ]
	],
	'params'     => $params,
	'as beforeRequest' => [
		'class' => 'app\components\LanguageHandler',
	],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'], // Только локальные IP
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'], // Только локальные IP
        'generators' => [
            'job' => [
                'class' => \yii\queue\gii\Generator::class,
            ],
        ],
    ];
}

return $config;
