<?php
/**
 *
 * User: Dazhengtech.com
 * Date: 15/7/3
 * Time: 下午4:14
 */
namespace common\components;

class UnitUtil {

    /**
     *
     * 格式化为 1,3333.22 的形式
     *
     * @param $amount
     * @return string
     */
    public static function formatMoney($amount) {
        if ($amount == 0) return $amount;

        return number_format(floor($amount * 100) / 100, 2);
    }


    /**
     * 格式化为 12222.22的形式
     * @param $amount
     * @return string
     */
    public static function roundMoney($amount) {
        if ($amount == 0) return $amount;

        return number_format(floor($amount * 100) / 100, 2, '.', '');
    }

    /**
     * 把时间规约为天或者月为单位
     * @param $days
     * @return string
     */
    public static function formatDuration($days) {
        if ($days <= 31 ) {
            return '<span class="focus-number">' . $days . '</span>天';
        }

        return '<span class="focus-number">' . trim(trim(number_format(($days / 30), 1), '0'), ".")
            . '</span> 个月';
    }

    /**
     * 得到剩余时间的完整表达
     * @param $duration
     * @return string
     */
    public static function getCountdownTime ($duration) {
        $text = '';
        if ($duration / 3600 > 24) {
            $text .= floor($duration / 3600 / 24) . ' 天 ';
        }

        if ($duration / 3600 % 24 > 0) {
            $text .= $duration / 3600 % 24 . ' 小时 ';
        }

        if ($duration % 3600 / 60 > 0) {
            $text .= floor($duration % 3600 / 60) . ' 分';
        }

        return $text;

    }

    /**
     * 把一个时间规约为这一天的零时
     *
     */
    public static function roundTimeToDay($time) {
        return strtotime(date('Y-m-d', $time));
    }

    /**
     * 得到长时间表示
     * @param $time
     * @return bool|string
     */
    public static function getLongDate($time) {
        if ($time) {
            return date('Y-m-d H:i:s', $time);
        } else {
            return '';
        }
    }

    /**
     * 得到短时间表示
     * @param $time
     * @return bool|string
     */
    public static function getShortDate($time) {
        if ($time) {
            return date('Y-m-d', $time);
        } else {
            return '';
        }
    }



}