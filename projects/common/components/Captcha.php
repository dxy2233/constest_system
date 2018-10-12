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


class Captcha {

    private static $KEY = 'asdf#@$@#%ASDFGF234#$sfa2asdf{})';

    /**
     * 生成验证码图片数据 html inline格式
     * @return string
     */
    public static function generateCode($key){
        $builder = new \Gregwar\Captcha\CaptchaBuilder();
        $builder->build();

        $code = $builder->getPhrase();

        $securityManager = new Security();
        setcookie($key, $securityManager->encryptByKey($code, self::$KEY), null, '/');

        return $builder->inline();
    }

    /**
     * 直接生成验证码图片文件流
     */
    public static function generateCodeImg($key) {
        $builder = new \Gregwar\Captcha\CaptchaBuilder();
        $builder->build();

        $code = $builder->getPhrase();

        $securityManager = new Security();
        setcookie($key, $securityManager->encryptByKey($code, self::$KEY), null, '/');

        header('Content-type: image/jpeg');
        $builder->output();
    }


    /**
     * 校验验证码正确与否
     * @param $userCode
     * @return bool
     */
    public static function verifyCode($userCode, $key) {
        $vcode = isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;

        if ($vcode == null) {
            return false;
        }

        $securityManager = new Security();
        return @$securityManager->decryptByKey($vcode, self::$KEY) == $userCode;
    }
}