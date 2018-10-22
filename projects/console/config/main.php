<?php
$params = array_merge(
    require(PROJECTS_ROOT . '/common/config/params.php'),
    require(APP_PATH . '/config/params.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'flushInterval' => 1,

            'targets' => [
                [
                    'exportInterval' => 1,
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['service','console'],
                    'logVars' => [],
                    'logFile' => '@console/runtime/logs/console.log'
                ],
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories'=> ['yii\db\*'],
                    'logVars' => [],
                    'logFile' => '@console/runtime/logs/db.log'
                ],
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories'=> ['notification'],
                    'logVars' => [],
                    'logFile' => '@console/runtime/logs/notification.log'
                ],
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories'=> ['vote'],
                    'logVars' => [],
                    'logFile' => '@console/runtime/logs/vote.log'
                ],
            ],
        ],
    ],

    'params' => $params,
];
