<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BAdminAccessToken;
use common\models\AdminUser;
use common\components\FuncHelper;

/**
 * Site controller
 */
class DownloadController extends BaseController
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
    public function actionGetDownloadCode()
    {
        $user = $this->user;

        $token = BAdminAccessToken::find()->where(['user_id' => $this->user_id])->andWhere(['>=', 'expire_time', time()+5 ])->one();
        $code = FuncHelper::authCode($token->access_token, 'ENCODE', '', 5);

        return $this->respondJson(0, '获取成功', $code);
    }

    // public function actionCheckCode()
    // {
    //     $code = $this->gString('code');
    //     var_dump($code);
    //     $code = FuncHelper::authCode($code);
    //     var_dump($code);

    //     $user_id = AdminUser::findIdentityByAccessToken($code);
    //     var_dump($user_id);
    // }
}