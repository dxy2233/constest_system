<?php

namespace common\modules\validate;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritvalidate
     */
    public $controllerNamespace = 'common\modules\validate\controllers';

    /**
     * @inheritvalidate
     */
    public function init()
    {
        parent::init();
    }
}
