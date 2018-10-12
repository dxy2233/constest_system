<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/4/7
 * Time: 上午9:31
 */

namespace common\components;


class TextUtil {

    public static function processImagesUrl($text) {
        $text = preg_replace('$（(http.+?)）$', '<a href="$1">$1</a>', $text);
        $text = preg_replace('$【(http.+?)】$', '<a href="$1"><img src="$1" /></a>', $text);
        $text = str_replace("\n", '<br/><br/>', $text);
        return $text;
    }


    /**
     * 文本串载取，追加...
     * @param $text
     * @param $len
     * @return string
     */
    public static function subStrText($text,$len){

        if(mb_strlen($text) < $len){
            return $text;
        }
        return mb_substr($text,0,$len,'utf-8').'..';

    }

    public static function transFromCamelToLine($str) {
        $count = preg_match_all('$([A-Z][a-z]+)$', $str, $matches);
        if ($count == 0) {
            return strtolower($str);
        }

        $returnStr = '';
        foreach ($matches[1] as $match) {
            $returnStr .= strtolower($match) . '-';
        }

        return trim($returnStr, '-');
    }



}