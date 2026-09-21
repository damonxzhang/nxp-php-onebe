<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'homeUrl' => '/api',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request'    => [
            'class' => 'yii\web\Request',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' => '/api',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
        ],

    ],
    'params' => $params,
];
