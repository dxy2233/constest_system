<?php
/*
 * @Author: jayden
 * @Date: 2018-05-22 15:36:36
 * @Last Modified by:   mikey.zhaopeng
 * @Last Modified time: 2018-05-22 15:36:36
 */

namespace common\dzbase;

use yii\rest\ActiveController;
use common\traits\DzControllerTrait;

class DzApiController extends ActiveController
{
    use DzControllerTrait;

    public function init()
    {
        parent::init();
        //定义常量
        defined('NOW_TIME') or define('NOW_TIME', time());
    }
}
