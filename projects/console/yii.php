#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

define('APP_NAME', 'console');

defined('APP_NAME') or exit('No APP_NAME defined');
define('PROJECTS_ROOT', __DIR__ . '/../');
define('ROOT', PROJECTS_ROOT . '/../');
define('APP_PATH', PROJECTS_ROOT . DIRECTORY_SEPARATOR . APP_NAME);
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));
file_exists(PROJECTS_ROOT . '/debug.php') ? define('YII_DEBUG', true): define('YII_DEBUG', false);

$paramsfile = YII_DEBUG  ? '/params_debug.php' : '/params.php';
require_once(ROOT . '/thirdpart/thirdpart_autoload.php');
require(ROOT . '/vendor/autoload.php');
require(ROOT . '/vendor/yiisoft/yii2/Yii.php');
require(PROJECTS_ROOT . '/common/config/bootstrap.php');
require(APP_PATH . '/config/bootstrap.php');
$debugConf = YII_DEBUG ? require(PROJECTS_ROOT . '/debug.php') : [];
$appConf = YII_DEBUG ? $debugConf[APP_NAME] : [];
$config = yii\helpers\ArrayHelper::merge(
    require(PROJECTS_ROOT . '/common/config/main.php'),
    require(APP_PATH . '/config/main.php'),
    $appConf
);
$config['params'] = yii\helpers\ArrayHelper::merge(
    require(PROJECTS_ROOT . $paramsfile),
    $config['params']
);
register_shutdown_function(function(){
    $error = error_get_last();
    if (is_array($error)) {
//        exit('系统终止服务,' . var_export($error, true));
    }
});
$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
