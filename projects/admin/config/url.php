<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午3:36
 */


return [
    'enablePrettyUrl'     => true,
    'showScriptName'      => false,
    'enableStrictParsing' => false,
    'rules'               => [
        '/'                             => 'index',
        'login'                         => 'login/index',
        '/logout'                         => 'login/logout',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ]
];
