<?php

return [
	'app_mail_from' => 'info@finlat.lv',
    'site_name' => 'FINLAT',
    'solrPerson' => '',
	'solrLoan'   => '',
	'loadLimit'  => 20,

	'maillink'   => '',
	'maillink2'  => '',
	'adminEmail' => '',

	'salt'               => '$6$rounds=5000$usasvmesillyatriggforsala$',
	'base_dir'           => '/var/www/www-root/data/www/broker.finlat.lv',
	'api_data_log_path'  => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/api.log',
	'postback_log_path'  => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/postback.log',
	'analytics_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/analystics.log',


	'welcomesms_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/sms.log',

	'unoapi_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/uno.log',
	'unoapi_beat'     => '',
	'unoapi_key'      => '',
	'unoapi_url'      => '',

	'tfbankapi_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/tfbank.log',
	//live params        
	'tfbank_username'    => '',
	'tfbank_password'    => '',
	'tfbank_url'         => 'https://webservice.tfbank.se:32259/v1/ApplicationOutService.svc?singleWsdl',
    'tfbank_api_debug'    => true,

	'inbankapi_key'      => '',
	'inbankapi_url'      => 'https://api.cofi.lv',
	'inbankapi_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/inbank.log',

    'inbankapi_test'      => false,

    'mogo_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/mogo.log',
    'mogo_test'      => false,

	'aizdevumsapi_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/aizdevums.log',
	'aizdevumsapi_login'    => '',
	'aizdevumsapi_pass'     => '',
	'aizdevumsapi_code'     => '',
	'aizdevumsapi_url'      => '',

	'efinanceapi_log_path'  => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/efinance.log',
	'efinanceapi_beat'      => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.getLoanStatus',
	// 'efinanceapi_beat' => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.getRegistrationFields',
	'efinanceapi_url'       => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.registerClient',
	'efinanceapi_url2'      => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.requestLoan',
	'efinanceapi_url_check' => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.getCustomerList',
	'efinanceapi_url_file'  => 'http://e-finance.lv/api/v1.0/credits/banks?RPC=agents.attachFile',
	//'efinanceapi_url' => 'http://e-finance-lv.egroup.eu/api/v1.0/credits/credit?RPC=agents.registerClient',
	'efinanceapi_login'     => '',
	'efinanceapi_pass'      => '',

	'increditapi_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/incredit.log',
	'increditapi_url'      => '',
	'increditapi_login'    => '',
	'increditapi_pass'     => '',
	'increditapi_orgid'    => '',

	'monify_url' => 'https://hooks.zapier.com/hooks/catch/3854717/p7iwel/',

    'elizings_log_path' => '/var/www/www-root/data/www/broker.finlat.lv/runtime/logs/elizings.log',


	'postbackurl' => 'http://p.trackmytarget.com/',
	'modules'     => [
		"LoanTargetCycleBehavior"        => true,
		"LoanAnalyticsEcommerceBehavior" => false,
		"LoanWelcomeSmsBehavior"         => false,
		"LoanPropelleradsBehavior"       => false,
	],
	'api'         => [
		"UNOApi"           => false,
		"TestApi"          => false,
		"EFinanceApi"      => false,
		"InbankApi"        => false,
		"IncreditApi"      => false,
		"EfinanceEmailApi" => false,
		"MonifyApi"        => false,
		"MonenzaApi"       => false,
		"ElatsEmailApi"    => false,
		"TFBankApi"        => true,
		"AizdevumsApi"     => false, //this should be last
	],

    /**
     * @deprecated
     */
	'wordpress' => [
		'forms' => [
			3 => [ 'name' => 'Patēriņa kredīts', 'lead_event_sid' => '4ah5bj', 'sales_event_sid' => 'ovi91f', 'order' => 1], // Потребительский кредит
            13 => [ 'name' => 'Online aizdevums līdz 1500 eiro', 'lead_event_sid' => 'ob3d71', 'sales_event_sid' => 'v90773', 'order' => 2 ], // Онлайн кредит до 1500 евро
            11 => [ 'name' => 'Aizdevumu refinansēšana', 'lead_event_sid' => 'bnm2d1', 'sales_event_sid' => 'us1n2t', 'order' => 3 ], // Рефинансирование кредитов
            7 => [ 'name' => 'Kredīts pret nekustamo īpašumu', 'lead_event_sid' => 'tvwr7o', 'sales_event_sid' => '66x805', 'order' => 4 ], // Кредит под залог недвижимости
            5 => [ 'name' => 'Kredīts pret automašīnas ķīlu', 'lead_event_sid' => '625w40', 'sales_event_sid' => '50900c', 'order' => 5 ], // Кредит под залог автомобиля
            9 => [ 'name' => 'Kredīts uzņēmējdarbībai', 'lead_event_sid' => 'qc1963', 'sales_event_sid' => 'qe0d30', 'order' => 6], // Кредит для бизнеса
            4 => [ 'name' => 'Потребительский кредит', 'lead_event_sid' => '4ah5bj', 'sales_event_sid' => 'ovi91f',  'order' => 7],
            14 => [ 'name' => 'Онлайн кредит до 1500 евро', 'lead_event_sid' => 'ob3d71', 'sales_event_sid' => 'v90773', 'order' => 8 ],
            12 => [ 'name' => 'Рефинансирование кредитов', 'lead_event_sid' => 'bnm2d1', 'sales_event_sid' => 'us1n2t', 'order' => 9 ],
            8 => [ 'name' => 'Кредит под залог недвижимости', 'lead_event_sid' => 'tvwr7o', 'sales_event_sid' => '66x805', 'order' => 10 ],
            6 => [ 'name' => 'Кредит под залог автомобиля', 'lead_event_sid' => '625w40', 'sales_event_sid' => '50900c', 'order' => 11 ],
            10 => [ 'name' => 'Кредит для бизнеса', 'lead_event_sid' => 'qc1963', 'sales_event_sid' => 'qe0d30', 'order' => 12 ],

            15 => [ 'name' => 'Pieteikties apdrošināšanai', 'lead_event_sid' => 'g1d158', 'sales_event_sid' => '8oaww5', 'order' => 13 ], // Заявка на страхование
            16 => [ 'name' => 'Заявка на страхование', 'lead_event_sid' => 'g1d158', 'sales_event_sid' => '8oaww5', 'order' => 14 ],
			17 => [ 'name' => 'Faktorings', 'order' => 15 ], // Факторинг
            18 => [ 'name' => 'Факторинг', 'order' => 16 ]
		]
	],

	'mailer'        => [
		"finlat" => [
			'class'      => 'Swift_SmtpTransport',
			'host'       => 'smtp.gmail.com',
			'username'   => 'info@finlat.lv',
			'password'   => '',
			'port'       => '465',
			'encryption' => 'tls',
		],
		"source2"    => [
			'class'      => 'Swift_SmtpTransport',
			'host'       => 'smtp.gmail.com',
			'username'   => '',
			'password'   => '',
			'port'       => '465',
			'encryption' => 'ssl',
		],
		"system"     => [
			'class'      => 'Swift_SmtpTransport',
			'host'       => 'smtp.yandex.com',
			'username'   => 'info@example.lv',
			'password'   => '',
			'port'       => '465',
			'encryption' => 'ssl',
		],
	],
	'text2reach'    => "",
	'changes_limit' => 20,

    'yandexMetrika.id'      => 87814805,
    'yandexMetrika.params'  => [
        'webvisor'              => true,
        'trackHash'             => true,
        'clickmap'              => true,
        'trackLinks'            => true,
        'accurateTrackBounce'   => true,
    ],

    'onlineThreshold' => 600, // 10 minutes
    'calc.currencySign' => '₽',
    'calc.currencyCode' => 'RUB',
    'calc.maxCreditConfig' => [
        'term' => 20,
        'calcMonthlyPayment' => 160,
        'fixedHouseholdCosts' => 560,
        'guarantorCosts' => 405,
        'householdMemberCosts' => 225,
        'pl_buffer' => [
            'pl_buffer_parameter' => 3,
            'pl_existing_coef' => 1.37,
            'pl_liabilities' => 20000,
            'pl_off' => 0,
        ],
        'dsti_calculation' => [
            'dsti_parameter' => 50,
            'dsti_interest' => 6,
            'dsti_coefficient' => 1.49,
            'dsti_liabilities' => 20000,
            'dsti_off' => 0,
        ],
        'periodForMaxLti' => 72,
        'pl_buffer_parameter' => 3,
        'max_month_credit_percentage' => 40,
    ],
];
