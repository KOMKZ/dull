<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'logTable' => "{{%log_api}}",
                    'levels' => ['error'],
                    // 'categories' => [
                    //     'application',
                    //     'yii\db\*',
                    // ],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/appapi'
        ],
    ],
    'params' => $params,
];
