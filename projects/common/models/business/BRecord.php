<?php

namespace common\models\business;

use common\components\FuncHelper;

class BRecord extends \common\models\Record
{
    public static $RECORD_SEX_BOY = 1; // 男
    public static $RECORD_SEX_GIRL = 2; // 女
    public static function getSex($key = '')
    {
        $arr = [
            self::$RECORD_SEX_BOY => \Yii::t('app', '男'),
            self::$RECORD_SEX_GIRL => \Yii::t('app', '女')
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }
    public static $RECORD_IS_PASSPORT_YES = 1; // 男
    public static $RECORD_IS_PASSPORT_NO = 0; // 女
    public static function getIsPassport($key = '')
    {
        $arr = [
            self::$RECORD_IS_PASSPORT_YES => \Yii::t('app', '有'),
            self::$RECORD_IS_PASSPORT_NO => \Yii::t('app', '无')
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    public static $RECORD_MY_IDENTITY_STORE_MANAGER = 1;//店长
    public static $RECORD_MY_IDENTITY_DIRECTOR = 2;//总监/
    public static $RECORD_MY_IDENTITY_MANAGER = 3;//经理/
    public static $RECORD_MY_IDENTITY_OTHERS = 4;//其他
    public static $RECORD_MY_IDENTITY_AREA = 5;//区
    public static $RECORD_MY_IDENTITY_PROVINCE = 6;//省
    public static $RECORD_MY_IDENTITY_CITY = 7;//市

    public static function getMyIdentity($key = '')
    {
        $arr = [
            self::$RECORD_MY_IDENTITY_AREA => \Yii::t('app', '区级服务中心'),
            self::$RECORD_MY_IDENTITY_PROVINCE => \Yii::t('app', '省级服务中心'),
            self::$RECORD_MY_IDENTITY_CITY => \Yii::t('app', '市级服务中心'),
            self::$RECORD_MY_IDENTITY_STORE_MANAGER => \Yii::t('app', '店长'),
            self::$RECORD_MY_IDENTITY_DIRECTOR => \Yii::t('app', '总监'),
            self::$RECORD_MY_IDENTITY_MANAGER => \Yii::t('app', '经理'),
            self::$RECORD_MY_IDENTITY_OTHERS => \Yii::t('app', '其他')
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    public static function getShopName($shop_name, $my_identity)
    {
        $id_name = self::getMyIdentity($my_identity);
        if (strlen($id_name) == 6 && $id_name != '其它') {
            $id_name = '';
        } elseif ($id_name == '其它') {
            $id_name = '家属';
        }
        $str = $shop_name.$id_name;
        return $str;
    }

    public static function getPhoto($key = '')
    {
        if ($key != '' && $key != '[]') {
            $json = json_decode($key, true);
            $data = '';
            foreach ($json as $v) {
                $data .= "<img style='height:200px;' src='".FuncHelper::getImageUrl($v)."' />";
            }
            return $data;
        }
        return '';
    }

    public static function getPhotoToString($key = '')
    {
        if ($key != '' && $key != '[]') {
            $json = json_decode($key, true);
            $data = [];
            foreach ($json as $v) {
                $data[] = FuncHelper::getImageUrl($v);
            }
            return implode(',', $data);
        }
        return '';
    }
}
