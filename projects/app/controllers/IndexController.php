<?php
namespace app\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use common\services\IetSystemService;

/**
 * Site controller
 */
class IndexController extends \common\dzbase\DzController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];

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

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $data = '{"phone":"13358360058","username":"\u795e\u519c\u6c0f","cardNo":"110101199003076173","identity":1,"inviteName":"11111","inviteCode":"18581068118","selfInvite":"13358360058","upgradeFlag":"0"}';
        // $data = json_decode($data, true);
        // var_dump($data, IetSystemService::push('/customer/uip/cusIdentity/sync', $data));
        return $this->respondJson(0, "获取成功");
        exit;
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
