<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone'=>'Asia/Shanghai',
    // 'language' => "zh-CN",
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

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],

        'diskfile' => [
            'class' => 'common\models\file\DiskDriver',
            'base' => '/home/kitral/shuguang/files',
            'host' => '',
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

        'frurl' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'hostInfo' => 'http://localhost:8055/index.php',
            'baseUrl' => '',
        ],

        'apiurl' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'hostInfo' => 'http://localhost:8054',
            'baseUrl' => '/appapi'
        ]
    ],
];
