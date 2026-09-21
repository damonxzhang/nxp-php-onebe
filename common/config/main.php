<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=nxp_server_db',
            'username' => 'root',
            'password' => 'Zhangxin@123', 
            'tablePrefix' => 'nxp_',              
            'charset' => 'utf8',
        ],
        'bbsDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.193.226.69;dbname=mcd_bbs',
            'username' => 'root',
            'password' => 'machangdao',
            'tablePrefix' => 'bbs_',
            'charset' => 'utf8',
        ],
	'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];
