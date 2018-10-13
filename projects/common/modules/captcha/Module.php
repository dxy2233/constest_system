<?php

namespace common\modules\captcha;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritsms
     */
    public $controllerNamespace = 'common\modules\captcha\controllers';

    /**
     * @inheritsms
     */
    public function init()
    {
        parent::init();
    }
}
