<?php
return [
    'charset' => 'utf-8',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
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
            'class' => 'shmilyzxt\queue\queues\DatabaseQueue', //队列使用的类
            'jobEvent' => [ //队列任务事件配置，目前任务支持2个事件
                'on beforeExecute' => ['shmilyzxt\queue\base\JobEventHandler','beforeExecute'],
                'on beforeDelete' => ['shmilyzxt\queue\base\JobEventHandler','beforeDelete'],
            ],
            'connector' => [//队列中间件链接器配置（这是因为使用数据库，所以使用yii\db\Connection作为数据库链接实例）
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=localhost;dbname=gr_contest',
                'username' => 'root',
                'password' => 'trans.pwd',
                'charset' => 'utf8',
            ],
            'table' => 'jobs', //存储队列数据表名
            'queue' => 'default', //队列的名称
            'expire' => 60, //任务过期时间
            'maxJob' =>0, //队列允许最大任务数，0为不限制
            'failed' => [//任务失败日志记录（目前只支持记录到数据库）
                'logFail' => true, //开启任务失败处理
                'provider' => [ //任务失败处理类
                    'class' => 'shmilyzxt\queue\failed\DatabaseFailedProvider',
                    'db' => [
                        'class' => 'yii\db\Connection',
                        'dsn' => 'mysql:host=localhost;dbname=gr_contest',
                        'username' => 'root',
                        'password' => 'trans.pwd',
                        'charset' => 'utf8',
                        'tablePrefix'=>'gr_',
                    ],
                    'table' => 'failed_jobs' //存储失败日志的表名
                ],
            ],
        ],
    ],

];
