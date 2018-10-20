<?php

namespace common\modules\site;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritsms
     */
    public $controllerNamespace = 'common\modules\site\controllers';

    /**
     * @inheritsms
     */
    public function init()
    {
        parent::init();
    }
}
