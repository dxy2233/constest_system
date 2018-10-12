<?php

namespace common\modules\doc;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\doc\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (YII_DEBUG) {
            //throw new Exception('only accessed on debug evn!', 500);
            parent::init();
        }
        // custom initialization code goes here
    }
}
