<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 2016/9/23
 * Time: 下午6:29
 */

namespace admin\widgets;


class ZoneSelectWidget extends FormWidget {

    //显示区
    public $showDistricts = true;

    //只显示省份
    public $showOnlyProvince = false;

    public function init() {
        parent::init();
    }

    public function run() {
        return $this->render('zone_select');
    }
}