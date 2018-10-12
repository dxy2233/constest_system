<?php
$params = array_merge(
    require(PROJECTS_ROOT . '/common/config/params.php'),
    require(APP_PATH . '/config/params.php')
);

return [
    'id' => APP_NAME,
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',

    'defaultRoute'=>'index/index',
    'controllerNamespace' => APP_NAME . '\controllers',
    'layoutPath'=>'@admin/views/layouts/adminlte',
    'on beforeRequest' => function ($event) {
    },
    'components' => [
        /*模板定义*/
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@admin/themes/adminlte'
                ],
            ],


        ],


        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        'assetManager' => [
            'bundles' => [

                'yii\web\JqueryAsset'=>[
                    'js'=>['/static/js/jquery.min.js', '/static/js/vue.min.js'],
                    'jsOptions'=>['position'=>\yii\web\View::POS_HEAD],
                ],
                'yii\bootstrap\BootstrapPluginAsset'=>false,
                'yii\bootstrap\BootstrapAsset' => [
                    'js'=>['/static/js/bootstrap.min.js'],
                    'css'=>['/static/css/bootstrap.css'],
                ],
                'yii\validators\ValidationAsset'=>[
                    'js' => ['/static/js/yii.validation.js']
                ],
                'yii\web\YiiAsset'=>[
                    'js' => ['/static/js/yii.js']
                ],
                'yii\widgets\ActiveFormAsset'=>[
                    'js' => ['/static/js/yii.activeForm.js']
                ],
            ],
        ],

        'user' => [
            'identityClass' => 'common\models\AdminUser',
            'loginUrl' => '/login'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                    'maxLogFiles' => 100,
                    'categories' => ['service'],
                    'maxFileSize' => '102400'
                ],
            ],
        ],
        'urlManager'   => require('url.php'),
        'request' => [
            'cookieValidationKey' => 'A#$aa234%^as34@#$dt23@#$4234sdf@',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => $params,
];
