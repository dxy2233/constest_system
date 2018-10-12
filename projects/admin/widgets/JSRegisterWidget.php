<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 16-9-7
 * Time: 下午4:16
 */

namespace  admin\widgets;
use yii\base\Widget;
use yii\web\View;


class JSRegisterWidget extends Widget {
    public $key = null;
    public $position = View::POS_READY;

    public static function begin($config = []){
        $widget = parent::begin($config);
        ob_start();
        return $widget;
    }


    public static function end(){
        $script = ob_get_clean();
        $widget = parent::end();
        if(preg_match("/^\\s*\\<script\\>(.*)\\<\\/script\\>\\s*$/s", $script, $matches)){
            $script = $matches[1];
        }
        $widget->view->registerJs($script, $widget->position, $widget->key);

    }
}