<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/21 0021
 * Time: 16:49
 */

namespace common\widgets;


class DateTimePicker extends FormWidget
{

    public function init() {
        parent::init();

        $this->id = rand(1, 99999) . time();
    }


    public function run() {
        return $this->render('datetime_control');
    }
}