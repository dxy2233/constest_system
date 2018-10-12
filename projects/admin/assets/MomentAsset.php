<?php


namespace admin\assets;
use yii\web\View;
use yii\web\AssetBundle;

/**
 * Moment Asset bundle for \kartik\daterange\DateRangePicker.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class MomentAsset extends AssetBundle
{
    public $jsOptions = ['position' => View::POS_HEAD];

    public $baseUrl = '/static/dateRange/';
    public $js = [
        'js/moment.min.js',
        'js/zh-cn.js',
    ];



}
