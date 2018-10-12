<?php
namespace admin\widgets;
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午10:25
 */
class TextAreaWidget extends FormWidget{

    public $readonly = false;
    public $rows = 10;
    public $cols = 10;

    public function init() {
        parent::init();

        if ($this->readonly == false) {
            $this->readonly = '';
        } else {
            $this->readonly = 'readonly';
        }
    }

    public function run() {
        return $this->render('text_area');
    }
}