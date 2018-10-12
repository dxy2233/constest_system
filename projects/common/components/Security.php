<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2016/11/4
 * Time: 下午4:02
 */

namespace common\components;


class Security {

    /**
     * html中安全输出
     * @param $str
     */
    public static function htmlContent($str) {
        return htmlentities($str);
    }
}