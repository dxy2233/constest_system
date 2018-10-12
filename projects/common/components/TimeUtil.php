<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/8/13
 * Time: 下午8:00
 */

namespace common\components;


class TimeUtil {

    public static function longText($time) {
        return date('Y-m-d H:i:s');
    }

    public static function shortText() {
        return date('Y-m-d');
    }

    /**
     * 今天开始时间戳
     */
    public static function beginToday(){
        return mktime(0,0,0,date('m'),date('d'),date('Y'));


    }

    /**
     * 今天结束时间戳
     */
    public static function endToday(){
        return mktime(23,59,59,date('m'),date('d'),date('Y'));

    }

    /**
     * 本周开始时间
     * @return false|int
     */
    public static function beginWeek(){
        return mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
    }

    /**
     * 本周结束时间
     * @return false|int
     */
    public static function endWeek(){
        return mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));

    }

    /**
     * 上周开始时间
     * @return false|int
     */
    public static function lastWeekStart(){
        return mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"));

    }

    /**
     * 上周结束时间
     * @return false|int
     */
    public static function lastWeekEnd(){
        return mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"));
    }

    /**
     * 下周一
     * @return false|int
     */
    public static function nextWeekStart(){

        return strtotime("next Monday");
    }


    /**
     * 下周星期天
     * @return false|int
     */
    public static function nextWeekEnd(){

        return strtotime("next Sunday");
    }

    /**

     * 上周时间
     * @param null $time
     * @return false|int
     */
    public static function lastWeek($time = null){
        $time = empty($time)?self::beginToday():$time;
        return strtotime("-1 week",$time);
    }

    /*
     * 昨天开始时间
     * @return false|int
     */
    public static function yesterdayStart(){
        return strtotime('today', strtotime('-1 day'));
//        return mktime(0,0,0,date("m"),date("d")-1,date("Y"));
    }

    public static function yesterdayEnd(){
        return self::beginToday()-1;
    }

    //明天开始时间
    public static function tomorrowStart(){
        return strtotime('today', strtotime('+1 day'));
    }

    //明天结束时间
    public static function tomorrowEnd(){
        return strtotime('today', strtotime('+2 day'))-1;
    }



    /**
     * 上周星期一
     */
    public static function lastWeekMonday(){

       return strtotime('last week  Monday');
    }
    /**
     * 上周星期一
     */
    public static  function lastWeekFriday(){

      return  strtotime('last Friday');
    }

    /**
     * 昨天结束时间
     * @return false|int
     */
    public static function yseterdayEnd(){
        return strtotime('today')-1;
//        return mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
    }
    /**
     * 本月第一天
     * @return false|int
     */
    public static function firstMonthDay(){

        return mktime(0,0,0,date('m'),date('1'),date('Y'));
    }


}