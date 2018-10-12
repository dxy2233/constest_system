<?php
namespace admin\widgets;
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午10:25
 */
class RadioWidget extends FormWidget{

    public $items;
    public $selectedValue = 0;

    public function init() {
        parent::init();
    }

    public function run() {
        return $this->render('radio');
    }
}