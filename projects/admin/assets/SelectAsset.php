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
class SelectAsset extends AssetBundle
{
    public $baseUrl = '/static/select2/';
    public $css = [
    'select2.min.css',
    'select2-addl.min.css',
    'select2-bootstrap.min.css',

    ];
    public $js = [
        'select2.full.min.js',
        'zh-CN.js',
        'select2-krajee.min.js',
    ];


    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile($jsfile, [AppAsset::className(), 'depends' => 'admin\assets\AppAsset']);
    }

    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile($cssfile, [AppAsset::className(), 'depends' => 'admin\assets\AppAsset']);
    }

    public $depends = [
       'admin\assets\AppAsset',
    ];
}
