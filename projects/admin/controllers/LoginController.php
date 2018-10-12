<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\UserService;
use admin\services\AdminLogin;
use yii\filters\AccessControl;
use Yii;
use common\models\AdminUser;
use yii\helpers\ArrayHelper;
use common\models\business\BUserAccessToken;

/**
 * Site controller
 */
class LoginController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'logout',
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
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
        $username = $this->pString('username');
        $password = $this->pString('password');

        if (!$username) {
            return $this->respondJson(1, "用户名不能为空");
        }
        $user = AdminLogin::login($username, $password);
        if ($user === false) {
            return $this->respondJson(1, "用户名或密码错误！");
        }
        if ($user->status == AdminUser::STATUS_DELETED) {
            return $this->respondJson(1, "账号状态异常，请联系管理员！");
        }
        $accessToken = UserService::setAccessToken($user->id);
        $msg = '登陆成功';
        if ($accessToken->code != 0) {
            $msg = $accessToken->msg;
        }
        return $this->respondJson($accessToken->code, $msg, $accessToken->content);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $accessToken = Yii::$app->getRequest();
        $heads = $accessToken->getHeaders();
        $token = preg_replace('/Bearer\s*/', '', $heads['authorization']);
        $data = BUserAccessToken::find()->where(['access_token' => $token, 'client_id' => \Yii::$app->controller->module->id])->one();
        if (!empty($data)) {
            $data->expire_time = time();
            $data->save();
        }
        return $this->respondJson(0, '退出成功');
    }
}
