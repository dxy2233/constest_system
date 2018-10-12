<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace admin\assets;

use yii\web\View;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class RichEditorAsset extends AssetBundle
{

    public $jsOptions = ['position' => View::POS_HEAD];
    public $baseUrl = '/static/tinymce/';
    public $js = [
        'tinymce.min.js'
    ];
    public $css = [

    ];

}
