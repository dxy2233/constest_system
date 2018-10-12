<?php
namespace admin\widgets;
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午10:25
 */
class TextInputWidget extends FormWidget{


    public $readonly = false;
    public $plugins;
    public $options;
    public $phpDatetimeFormat;
    public $model;
    public $attribute;
    public function init() {
        parent::init();

        if ($this->readonly == false) {
            $this->readonly = '';
        } else {
            $this->readonly = 'readonly';
        }

        $this->type = $this->type ?? 'text';
    }

    public function run() {
        return $this->render('text_input');
    }
}