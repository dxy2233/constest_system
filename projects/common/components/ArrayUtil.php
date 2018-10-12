<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 2016/11/4
 * Time: 下午10:48
 */

namespace common\components;


class ArrayUtil {

    /**
     * 返回真的空数组
     * @param $delimiter
     * @param $string
     * @return array
     */
    public static function safeExplode($delimiter, $string) {
        $array = explode($delimiter, $string);
        if (count($array) == 1 && $array[0] == '') {
            return [];
        }
        return $array;
    }
}