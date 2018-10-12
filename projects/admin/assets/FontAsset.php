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
class FontAsset extends AssetBundle
{
    public $baseUrl = '/static/css/';
    public $css =[
        'font-awesome.css'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
