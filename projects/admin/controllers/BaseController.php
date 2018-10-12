<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午4:15
 */

namespace admin\controllers;

use common\dzbase\DzController;
use common\services\AclService;
use yii\web\Error;
use yii\filters\AccessControl;

class BaseController extends DzController
{
    public $errorMsg = null;

    public $adminUser = null;

    public $user = null;

    public $user_id = null;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

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
