<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午3:38
 */

return [
    'admin' => [
        'components' => [
            'db' => [
                'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=192.168.3.111;dbname=record',
                'username' => 'app',
                'password' => 'app',
                'charset'  => 'utf8',
                'tablePrefix'=>'db_'
            ],
        ],
        'modules'    => [
            'gii' => [
                'class' => 'yii\gii\Module',
                'allowedIPs'=>['*'],
                'generators'=> [
                    'crud'=> [
                        'class' => 'yii\gii\generators\crud\Generator',
                        'templates'=> [
                            'default'=>'@admin/gii/adminlte/crud/default',
                        ],
                        'modelClass' =>'common\models\business\\B',
                        'searchModelClass' =>'common\models\search\\',
                        'controllerClass' =>'admin\controllers\\',
                        'viewPath' =>'@admin/views/',
                        'baseControllerClass' =>'admin\controllers\BaseController',
                    ],
                    'businessmodel'=>[
                        'class' => 'admin\gii\adminlte\business\Generator',
                        'templates'=> [
                            'default'=>'@admin/gii/adminlte/business/default',
                        ],
                    ],
                    'model'=>[
                        'class' => 'yii\gii\generators\model\Generator',
                        'ns' =>'common\models',
                        'generateLabelsFromComments'=>true,
                        'tableName'=>'db_',
                        'baseClass' => 'common\dzbase\DzModel',
                        'useTablePrefix' => true,
                    ]


                ],
            ],
        ],
    ],
    'app' => [
        'components' => [
            'db' => [
                'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=192.168.3.111;dbname=record',
                'username' => 'app',
                'password' => 'app',
                'charset'  => 'utf8',
                'tablePrefix'=>'db_'
            ],
        ]
    ],
    'console' => [
        'bootstrap' => [
            'log',
        ],
        'components' => [
            'db' => [
                'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=192.168.3.111;dbname=record',
                'username' => 'app',
                'password' => 'app',
                'charset'  => 'utf8',
                'tablePrefix'=>'db_'
            ],
        ],
    ],
];
