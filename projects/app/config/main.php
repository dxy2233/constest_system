<?php
$params = array_merge(
    require(PROJECTS_ROOT . '/common/config/params.php'),
    require(APP_PATH . '/config/params.php')
);

return [
    'id' => APP_NAME,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'defaultRoute'=>'index',
    'controllerNamespace' => APP_NAME . '\controllers',
    'components' => [
        'user' => [
            // 'identityClass' => 'common\models\User',
            // 'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'maxLogFiles' => 100,
//                    'categories' => ['service'],
                    'maxFileSize' => '102400'
                ],
            ],
        ],
        'urlManager'   => require('url.php'),
        'request' => [
            'cookieValidationKey' => 'A#$aa234%^as34@#$dt23@#$4234sdf@',
            'enableCsrfValidation' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
    ],
    'params' => $params,
];
