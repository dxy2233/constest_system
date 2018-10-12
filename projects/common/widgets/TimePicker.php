<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20 0020
 * Time: 15:42
 */

namespace common\widgets;


class TimePicker extends FormWidget
{

    public function init() {
        parent::init();

        $this->id = rand(1, 99999) . time();
    }


    public function run() {
        return $this->render('time_control');
    }
}