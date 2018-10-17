<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 2017/4/14
 * Time: 下午11:23
 */

namespace common\services;

use common\components\FuncResult;
use common\models\business\BSmsAuth;
use common\models\business\BSmsTemplate;
use common\services\ServiceBase;

class ValidationCodeSmsService extends ServiceBase
{
    public static function getInstance() : ValidationCodeSmsService
    {
        self::$instance = new ValidationCodeSmsService();
        self::$instance->init();
        return self::$instance;
    }

    /**
     * 验证短信验证码内容
     *
     * @param $mobile
     * @param $vcode
     * @param $type
     * @param int $duration 有效期分钟
     * @return FuncResult
     */
    public static function checkValidateCode($mobile, $vcode, int $type, bool $useOnce = true, int $duration = 5)
    {
        $smsAuth = BSmsAuth::find()
            ->where([
                'mobile' => $mobile,
                'status' => BSmsAuth::$STATUS_UNUSED,
                'type'   => $type])
            ->andWhere(['>', 'create_time', time() - 60 * $duration])->orderBy('create_time DESC')
            ->orderBy('id desc')
            ->one();

        if (!$smsAuth || $smsAuth->content != $vcode) {
            return new FuncResult(1, '验证码错误');
        }

        //使用过，进行标注，不能再次使用
        if ($useOnce) {
            $smsAuth->status = BSmsAuth::$STATUS_USED;
            $smsAuth->update();
        }

        return new FuncResult(0);
    }


    /**
     * 发送短信验证码
     *
     * @param $mobile 用户手机
     * @param $type 验证码类型
     * @param $userId
     * @param int $frequencyMinutes 一分钟频率限制
     * @param int $frequencyDay  一天频率限制
     * @return FuncResult
     */
    public static function sendValidationCode(
        $mobile,
        $type,
        $userId = 0,
        $frequencyMinutes = 1,
        $frequencyDay = 5
    ) {
        assert($mobile != null);

        //1分钟频率限制
        $countMinute = BSmsAuth::find()
            ->where(['mobile' => $mobile, 'type' => $type])
            ->andWhere(['>=', 'create_time', time() - 60])
            ->count();
        if ($countMinute >= $frequencyMinutes) {
            return new FuncResult(1, '超过短信发送频率,请稍后再试');
        }

        //1天频率限制
        $countToday = BSmsAuth::find()
            ->where(['mobile' => $mobile, 'type' => $type])
            ->andWhere(['>=', 'create_time', strtotime(date('Y-m-d', time()))])
            ->count();

        if ($countToday >= $frequencyDay) {
            return new ReturnInfo(1, '超过短信发送频率,请稍后再试');
        }
        //开关控制，特殊环境可不实际发送
        if (!isset(\Yii::$app->params['sendSms']) || \Yii::$app->params['sendSms'] === false) {
            $vcode = 111111;
        } else {
            $vcode = rand(100000, 999999);
        }


        //发送短信
        SmsService::send($mobile, ['vcode' => $vcode], $type, $userId);

        //记录验证码
        $smsAuth = new BSmsAuth();
        $smsAuth->mobile = $mobile;
        $smsAuth->content = (string)$vcode;
        $smsAuth->type = $type;
        $smsAuth->status = BSmsAuth::$STATUS_UNUSED;
        $smsAuth->create_time = time();
        $smsAuth->user_id = $userId;
        $smsAuth->insert();

        return new FuncResult(0, $vcode);
    }
}
