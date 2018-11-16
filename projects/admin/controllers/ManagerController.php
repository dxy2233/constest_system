<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;

use admin\services\AdminLogin;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;

/**
 * Site controller
 */
class ManagerController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    public function actionIndex()
    {
        $user = $this->user;
        $return['id'] = $user->id;
        $return['name'] = $user->name;
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionChangePassword()
    {
        $user = $this->user;
        $password = $this->pString('password');
        if (empty($password)) {
            return $this->respondJson(1, '原密码不能为空');
        }
        $new_password = $this->pString('new_password');
        if (empty($password)) {
            return $this->respondJson(1, '新密码不能为空');
        }
        $new_password_2 = $this->pString('new_password_2');
        if ($new_password_2 != $new_password) {
            return $this->respondJson(1, '两次密码输入不一致');
        }
        if (strlen($new_password_2)>18 || strlen($new_password)<6) {
            return $this->respondJson(1, '新密码长度必须大于5并小于18');
        }
        $res = AdminLogin::login($user->name, $password);
        if ($res === false) {
            return $this->respondJson(1, "用户名或密码错误！");
        }
        $user->password = md5($new_password_2 . $user->pwd_salt);
        if (!$user->save()) {
            return $this->respondJson(1, '修改失败', $user->getFirstErrorText());
        }
        return $this->respondJson(0, '修改成功');
    }
}
