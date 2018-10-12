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
class DepDropAsset extends AssetBundle
{

    public $baseUrl = '/static/depDrop/';

    public $css = [
    'dependent-dropdown.min.css',
    ];

    public $js = [
        'depdrop.min.js',
        'dependent-dropdown.min.js',

    ];


    public $depends = [
       'admin\assets\AppAsset',
    ];
}
