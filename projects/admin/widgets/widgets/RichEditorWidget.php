<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


use admin\assets\RichEditorAsset;
use yii\base\Widget;

class RichEditorWidget extends FormWidget{

    public function init() {
        parent::init();

        $this->id = rand(1, 99999) . time();
    }


    public function run() {
        RichEditorAsset::register($this->getView());
        return $this->render('rich_editor');
    }

}