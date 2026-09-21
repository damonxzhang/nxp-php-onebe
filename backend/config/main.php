<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'index',
    'bootstrap' => ['log'],
    'modules' => [
        'appeal' => [
            'class' => 'backend\modules\Appeal\Module',
        ],
		 'MaintainApi' => [
            'class' => 'backend\modules\MaintainApi\Modules',
        ],
    ],
	'homeUrl' => '/',
    'components' => [
'assetManager' => [
    'appendTimestamp' => true,
],
        'request' => [
            'csrfParam' => '_csrf-backend',
			'baseUrl' => '',
        ],
        'user' => [
            //'identityClass' => 'backend\models\MyUser',
            'identityClass' => 'backend\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
			'rules' => [
                //materials
                'POST v2/materials' =>  'materials/materials/insert',
                'POST v2/apply'     =>  'materials/materials/apply',
                //email remind
                'GET email-remind/timing-remind' => 'MaintainApi/default/index',
                'GET email-remind/time-out-remind' => 'MaintainApi/default/time-out-remind',
                'GET email-remind/boss-time-out-remind' => 'MaintainApi/default/boss-time-out-remind',

            ]
        ],
    ],
    'params' => $params,
];
