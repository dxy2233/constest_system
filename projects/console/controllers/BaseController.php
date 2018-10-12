<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class BaseController extends Controller
{
    protected $cache;
    protected $redis;

    public function init()
    {
        parent::init();
        //定义常量
        defined('NOW_TIME') or define('NOW_TIME', time());
    }

    /**
     * 调用缓存
     *
     * @return void
     */
    public static function cache()
    {
        return Yii::$app->cache;
    }
    /**
     * 调用redis组件
     *
     * @return void
     */
    public static function redis()
    {
        return Yii::$app->redis;
    }
}
