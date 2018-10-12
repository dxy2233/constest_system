<?php
namespace app\controllers;

use yii\helpers\ArrayHelper;
use yii\filters\Cors;
use common\services\UserService;

/**
 * Site controller
 */
class IndexController extends \common\dzbase\DzController
{
    /**
     * Lists all Record models.
     * @return mixed
     */
    public function actionIndex()
    {
        return 'hello world';
    }
}
