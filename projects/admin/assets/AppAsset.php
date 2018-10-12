<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace admin\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $baseUrl = '/static/';
    public $js = [
        'js/common.js',
        'js/ui-dialog.js',
        'adminlte/js/app.js',
    ];
    public $css = [
        'adminlte/css/skins/skin-blue.min.css',
        'adminlte/css/skins/_all-skins.min.css',
        'adminlte/css/AdminLTE.min.css',
        'css/common.css',
        'css/ui-dialog.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'admin\assets\FontAsset'
    ];
}
