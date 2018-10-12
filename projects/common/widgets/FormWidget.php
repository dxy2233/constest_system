<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 下午12:05
 */

namespace common\widgets;


use yii\base\Widget;


class FormWidget extends Widget{

    public $titleLen = 1;
    public $contentLen = 3;
    public $title = '';
    public $name = '';
    public $type = '';
    public $value = '';
    public $unit = '';
    public $validation = '';
    public $class = '';
    public $context = 'default';
    public $content = '';
    public $id = null;
    public $requireValidation = false;
    public $model = null;
    public $attribute = null;
    public $form = null;

    public function init() {
    parent::init();
    $this->id = md5($this->title . $this->name);

    }


}