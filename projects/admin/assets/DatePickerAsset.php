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
class DatePickerAsset extends AssetBundle
{
    public $baseUrl = '/static/datePicker/';
    public $js = [
        'bootstrap-datepicker.min.js',
        'bootstrap-datepicker.zh-CN.min.js',
    ];
    public $css = [
        'bootstrap-datetimepicker.min.css',
    ];
    public $depends = [
       'admin\assets\AppAsset',
    ];
}
