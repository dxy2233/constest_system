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
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\business\BAdminRole;
use common\models\business\BAdminRule;

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
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
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

            $my_rule = BAdminRole::find()->where(['id' => $this->user->role_id])->one();
            if ($my_rule->id != 1) {
                // 权限判断
                $this_rule_str = \Yii::$app->request->getPathInfo();
                $my_rule_list = json_decode($my_rule->rule_list, true);
                $this_rule = BAdminRule::find()->where(['like', 'url', $this_rule_str])->one();
                if ($this_rule && !in_array($this_rule->id, $my_rule_list)) {
                    if (YII_DEBUG) {
                        header('Access-Control-Allow-Origin:*');
                    }
                    echo $this->respondJson(2, '您的权限不足');
                    exit();
                } elseif ($this_rule) {
                    if ($this_rule->parent_id && !in_array($this_rule->parent_id, $my_rule_list)) {
                        if (YII_DEBUG) {
                            header('Access-Control-Allow-Origin:*');
                        }
                        echo $this->respondJson(2, '您的权限不足');
                        exit();
                    }
                }
            }
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
