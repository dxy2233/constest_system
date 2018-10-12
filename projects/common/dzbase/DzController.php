<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午4:15
 */

namespace common\dzbase;

use yii\web\Controller;
use common\traits\DzControllerTrait;

class DzController extends Controller
{
    use DzControllerTrait;
    
    public function init()
    {
        parent::init();
        //定义常量
        defined('NOW_TIME') or define('NOW_TIME', time());
    }
}
