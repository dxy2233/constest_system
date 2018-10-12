<?php
defined('APP_NAME') or exit('No APP_NAME defined');
define('PROJECTS_ROOT', __DIR__);
define('ROOT', __DIR__ . '/../');
define('APP_PATH', PROJECTS_ROOT . DIRECTORY_SEPARATOR . APP_NAME);
file_exists(PROJECTS_ROOT . '/debug.php') ? define('YII_DEBUG', true): define('YII_DEBUG', false);

$paramsfile = YII_DEBUG  ? '/params_debug.php' : '/params.php';
require_once(ROOT.'/thirdpart/thirdpart_autoload.php');
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
register_shutdown_function(function () {
    $error = error_get_last();
    if (is_array($error)) {
//        exit('系统终止服务,' . var_export($error, true));
    }
});

$application = new yii\web\Application($config);

$application->language = !empty(\Yii::$app->request->post('lang')) ? \Yii::$app->request->post('lang') :  "zh-CN" ;
$application->run();
