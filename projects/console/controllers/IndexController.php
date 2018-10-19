<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2017/9/12
 * Time: 下午2:28
 */

namespace console\controllers;

use common\models\business\BSetting;
use common\services\JobService;

class IndexController extends BaseController
{

    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    public function actionBeginSettlement()
    {
        JobService::beginPut();
    }
}
