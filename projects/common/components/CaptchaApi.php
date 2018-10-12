<?php
namespace common\components;

/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/1/18
 * Time: 上午10:15
 */



use yii\base\Security;

require_once dirname(__FILE__) .  '/captcha/autoload.php';


class CaptchaApi
{
    private static $KEY = 'asdf#@$@#%ASDFGF234#$sfa2asdf{})';

    public static $TYPE_LOGIN = 'userLogin';
    public static $TYPE_REGISTER = 'userRegister';

    /**
     * 生成验证码图片数据 html inline格式
     * @return string
     */
    public static function generateCode($key, $colorAuto = false)
    {
        $phrase = new \Gregwar\Captcha\PhraseBuilder;
        // 设置验证码位数
        $phraseCode = $phrase->build(4);

        $builder = new \Gregwar\Captcha\CaptchaBuilder($phraseCode, $phrase);
        // 设置背景颜色
        if (!$colorAuto) {
            $builder->setBackgroundColor(24, 36, 66);
            $builder->setTextColor(mt_rand(150, 255), mt_rand(150, 255), mt_rand(150, 255));
        }
        // 设置验证码识别度
        // $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build();

        $code = $builder->getPhrase();

        $securityManager = new Security();
        $imageCode = [
            'key' => $key,
            'code' => $code,
            'time' => time(),
        ];
        $imageCode = urlencode($securityManager->encryptByKey(serialize($imageCode), self::$KEY));

        $imageData = $builder->inline();


        return [
//            'image_number' => $code,
            'image_data' => $imageData,
            'image_code' => $imageCode,
        ];
    }


    /**
     * 校验验证码正确与否
     * @param $userCode
     * @return bool
     */
    public static function verifyCode($userCode, $key, $imageCode, $duration = 3*60)
    {
        $securityManager = new Security();

        $imageCode = urldecode($imageCode);
        $imageCode = @$securityManager->decryptByKey($imageCode, self::$KEY);
        $imageCode = unserialize($imageCode);

        $vcode = isset($imageCode['key']) && $imageCode['key'] == $key ? $imageCode['code'] : null;

        if ($vcode == null) {
            return false;
        }
        //过期时间判断
        $expireTime = isset($imageCode['time']) ? intval($imageCode['time'])+$duration : 0;
        if (time() > $expireTime) {
            return false;
        }
        // 忽略大小写
        return $vcode == strtolower($userCode);
    }
}
