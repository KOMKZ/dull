<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone'=>'Asia/Shanghai',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => '6379',
            'database' => 0
        ],

        'diskfile' => [
            'class' => 'common\models\file\DiskDriver',
            'base' => null,
            'host' => null,
        ],

        'ossfile' => [
            'class' => 'common\models\file\OssDriver',
            'bucket' => '',
            'access_key_id' => '',
            'access_secret_key' => '',
            'is_cname' => '',
            'endpoint' => '',
            'inner_endpoint' => '',
            'base' => ''
        ],

        'urlbuilder' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],

        // 'log' => [
        //     'traceLevel' => YII_DEBUG ? 3 : 0,
        //     'targets' => [
        //         [
        //             'class' => 'yii\log\DbTarget',
        //             'logTable' => 'log_backend',
        //             'levels' => ['error', 'warning'],
        //         ],
        //         [
        //             'class' => 'yii\log\DbTarget',
        //             'logTable' => 'log_frontend',
        //             'levels' => ['error', 'warning'],
        //         ],
        //         [
        //             'class' => 'yii\log\DbTarget',
        //             'logTable' => 'log_api',
        //             'levels' => ['error', 'warning'],
        //         ],
        //         [
        //             'class' => 'yii\log\DbTarget',
        //             'logTable' => 'log_console',
        //             'levels' => ['error', 'warning'],
        //         ],
        //         [
        //             'class' => 'yii\log\DbTarget',
        //             'logTable' => 'log_jobs',
        //             'levels' => ['error', 'warning'],
        //         ],
        //     ],
        // ],
    ],
];
