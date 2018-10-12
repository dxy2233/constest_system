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
class TabsAsset extends AssetBundle
{
    public $baseUrl = '/static/tabs/';
    public $js = [
        'bootstrap-tabs-x.min.js',
    ];

    public $css = [
        'bootstrap-tabs-x.min.css',
    ];

    //定义按需加载JS方法，注意加载顺序在最后
    public static function addScript($view, $jsfile) {
        $view->registerJsFile(self::$baseUrl.$jsfile, [AppAsset::className(), 'depends' => 'admin\assets\TabsAsset']);
    }


    //定义按需加载css方法，注意加载顺序在最后
    public static function addCss($view, $cssfile) {
        $view->registerCssFile(self::$baseUrl.$cssfile, [AppAsset::className(), 'depends' => 'admin\assets\TabsAsset']);
    }


    public $depends = [
       'admin\assets\AppAsset',
    ];
}
