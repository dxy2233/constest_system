<?php

namespace common\modules\sms;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritsms
     */
    public $controllerNamespace = 'common\modules\sms\controllers';

    /**
     * @inheritsms
     */
    public function init()
    {
        parent::init();
    }
}
