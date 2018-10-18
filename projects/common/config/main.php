<?php
return [
    'charset' => 'utf-8',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'bootstrap' => ['queue'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => ROOT . '/vendor',
    'modules' => [
        'doc' => [
            'class' => 'common\modules\doc\Module',
        ],
        'sms' => [
            'class' => 'common\modules\sms\Module',
        ],
        'captcha' => [
            'class' => 'common\modules\captcha\Module',
        ],
        'upload' => [
            'class' => 'common\modules\upload\Module',
        ],
        'validate' => [
            'class' => 'common\modules\validate\Module',
        ]
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=gr_contest',
            'username' => 'root',
            'password' => 'trans.pwd',
            'charset' => 'utf8',
            'tablePrefix'=>'gr_',
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'currencyCode' => 'CNY',
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories'=> ['notification'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/notification.log'
                ],
            ],
        ],
        'ApiCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/data/cache/api',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@runtime/queue',
            // 驱动的其他选项
        ],
    ],

];
