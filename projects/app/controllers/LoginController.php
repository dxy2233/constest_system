<?php
namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\CaptchaApi;
use common\components\FuncHelper;
use common\models\business\BUser;
use common\models\business\BSmsAuth;
use common\models\business\BUserWallet;
use common\models\business\BUserRecommend;
use common\models\business\BUserAccessToken;
use common\services\ValidationCodeSmsService;

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
            'exist-mobile',
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
        $type = $this->pInt('type');
        $mobile = $this->pString('mobile');
        $reCode = $this->pString('re_code');
        $vcode = $this->pString('vcode');
        if (!$mobile) {
            return $this->respondJson(1, "手机号不能为空");
        }
        if (!FuncHelper::validatMobile($mobile)) {
            return $this->respondJson(1, "手机号格式有误", $mobile);
        }
        if (!$vcode) {
            return $this->respondJson(1, "验证码不能为空");
        }
        if (!is_null($reCode) && !preg_match('/^[a-zA-Z0-9]{6}$/i', $reCode)) {
            return $this->respondJson(1, "推荐码格式错误");
        }
        //图形验证码
        if (\Yii::$app->params['imageCaptcha']) {
            $captchaCode = $this->pString('captcha_code', '');
            $imageCode = $this->pString('image_code', '');
            if (!$captchaCode) {
                return $this->respondJson(1, "图片验证码不能为空");
            }
            if (!$imageCode) {
                return $this->respondJson(1, "图片验证码参数不能为空");
            }
            if (! CaptchaApi::verifyCode($captchaCode, $type, $imageCode)) {
                return $this->respondJson(1, "图片验证码不正确");
            }
        }

        // 短信验证码
        //手机验证码是否正确, 有效期只有5分钟
        $returnInfo = ValidationCodeSmsService::checkValidateCode(
            $mobile,
            $vcode,
            BSmsAuth::$TYPE_USER_LOGIN
          );
        if ($returnInfo->code != 0) {
            return $this->respondJson(1, $returnInfo->msg);
        }

        $userModel = BUser::find()->where(['mobile' => $mobile])->one();
        //验证手机、是否存在
        if (!is_object($userModel)) {
            $random = UserService::generateRemmendCode(6);
            $user = [
                'username' => $mobile,
                'mobile' => $mobile,
                'recommend_code' => $random,
            ];
            // 如果用户不存在则创建用户
            $createUser = UserService::createUser($user);
            if ($createUser->code) {
                return $this->respondJson($createUser->code, $createUser->msg, $createUser->content);
            }
            $userModel = $createUser->content;
            // 添加推荐关系
            if (!is_null($reCode)) {
                if ($code = UserService::validateRemmendCode(strtoupper($reCode))) {
                    $recommend = new BUserRecommend();
                    $recommend->parent_id = $code;
                    $recommend->link('user', $userModel);
                }
            }
        }
        // 处理登录触发事件
        $result = UserService::login($userModel);
        return $this->respondJson($result->code, $result->msg, $result->content);
    }
   

    /**
     * 刷新登录accesstoken
     */
    public function actionRefreshToken()
    {
        $result = UserService::refreshAccessToken($this->pString('refresh_token'));

        return $this->respondJson($result->code, $result->msg, $result->content);
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
