<?php

namespace common\modules\sms\controllers;

use yii\filters\Cors;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use common\dzbase\DzController;

/**
 * Default controller for the `api` module
 */
class BaseController extends DzController
{
    public $errorMsg = null;

    public $adminUser = null;

    public $user = null;

    public $user_id = null;

    public function init()
    {
        // var_dump(\Yii::$app->user);
        // exit;
        // 设置返回数据类型
        // \Yii::$app->response->format = 'json';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'authenticator' => [
                'class' => \common\components\Behavior\HttpBearerAuth::className(),
                'isThrowException' => false,
            ],
        ];
        //测试环境，接口跨域
        if (YII_DEBUG) {
            $behaviors[] = [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],//定义允许来源的数组
                    'Access-Control-Request-Method' => ['GET','POST','PUT','DELETE', 'HEAD', 'OPTIONS'],//允许动作的数组
                    'Access-Control-Request-Headers' => ['*'],
                ],
                'actions' => [
                    'index' => [
                        'Access-Control-Allow-Credentials' => true,
                    ]
                ]
            ];
        }

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
    

    /**
     * 错误返回
     *
     * @return string
     */
    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $errCode = 1;
            $errMsg = $exception->getMessage();
            if ($errMsg == "授权验证失败") {
                //未授权登录
                $errCode = -1;
                $errMsg = "请登录后访问";
            }

            return $this->respondJson($errCode, $errMsg);
        }
        return $this->respondJson(1, 'error');
    }
}
