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
class PropertiesAsset extends AssetBundle
{
    public $baseUrl = '/static/properties/';
    public $js = [
        'jquery-ui.js',
        'yii.admin.js',
    ];
    public $css = [
        'jquery-ui.css',
        'list-item.css',
       
    ];
    public $depends = [
       'admin\assets\AppAsset',
    ];
}
