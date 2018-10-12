<?php



namespace admin\assets;

use yii\web\AssetBundle;


class DateRangePickerAsset extends AssetBundle
{


    public $baseUrl = '/static/dateRange/';
    public $js = [
        'js/daterangepicker.min.js',

    ];
    public $css = [
        'css/daterangepicker.min.css',
        'css/daterangepicker-kv.min.css',
    ];
    public $depends = [
        'admin\assets\AppAsset',
    ];

}
