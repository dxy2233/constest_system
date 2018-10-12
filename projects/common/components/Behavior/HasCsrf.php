<?php

namespace common\components\Behavior;

use yii\base\Behavior;
use yii\web\Controller;


/**
 * 特殊需要开启的CSRF 验证
 * Class HasCsrf
 * @package common\components\Behavior
 * /**
 * 登录页面

    public function behaviors() {
        return [
            'csrf' => [
                'class' => HasCsrf::className(),
                'controller' => $this,
                'actions' => [
                    'login'
                ]
            ],

        ];
    }

 View *
 *  *  <?php  \yii\widgets\ActiveForm::begin(['options'=>['class'=>'login-form input-message','id'=>'form_login']])?>
 *  *  --> 表单内容
 *  * <?php \yii\widgets\ActiveForm::end()?>


*/


class HasCsrf extends Behavior
{
    public $actions = [];

    public $controller;


    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }


    public function beforeAction($event)
    {
        $action = $event->action->id;
        if(in_array($action, $this->actions)){
            $this->controller->enableCsrfValidation = true;
        }
    }


}