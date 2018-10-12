<?php
namespace common\components;
/**
 *
 * User: simonzhang
 * Date: 15/7/3
 * Time: 下午1:46
 */
class NetUtil  {

    public static function getIp($defualt = '') {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = $defualt;
        }
        return $cip;
    }

    public static function getGeoFromIp($ip) {
        $location = IpUtil::find($ip);
        if (is_array($location) && count($location) == 4) {
            return $location;
        }

        return [null, null, null, null];
    }
}