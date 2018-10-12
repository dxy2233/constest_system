<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace common\widgets;


class RichEditorWidget extends FormWidget{

    public function init() {
        parent::init();

        $this->id = rand(1, 99999) . time();
    }


    public function run() {
        return $this->render('rich_editor');
    }

}