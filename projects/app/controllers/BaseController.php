<?php
/**
 * Created by IntelliJ IDEA.
 * User: MacPro
 * Date: 2017/12/8
 * Time: 上午10:58
 */

namespace app\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\Cors;

class BaseController extends \common\dzbase\DzController
{
    public $user = null;
    public $user_id = 0;


    public function beforeAction($action)
    {
        $res = parent::beforeAction($action);

        if (\Yii::$app->user->id) {
            $this->user = \Yii::$app->user->identity;
            $this->user_id = \Yii::$app->user->id;
        }

        return $res;
    }
}
