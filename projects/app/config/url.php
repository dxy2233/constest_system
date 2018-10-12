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
        '/'                             => 'record/index',
        '/index'                             => 'record/index',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ]
];
