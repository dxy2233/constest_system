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
class TagsAsset extends AssetBundle
{
    public $baseUrl = '/static/tagsinput/';
    public $js = [
        'angular.js',
        'bootstrap-tagsinput.js',
        'bootstrap-tagsinput-angular.js',
    ];
    public $depends = [
       'admin\assets\AppAsset',
    ];
}
