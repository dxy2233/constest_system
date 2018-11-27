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
use common\services\UpdateService;

class IndexController extends BaseController
{

    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    // 定期结算
    public function actionBeginSettlement()
    {
        JobService::beginPut();
    }

    // 后台修改节点质押数据，不提供前端操作
    public function actionUpdateNode()
    {
        UpdateService::begin();
    }
    //
    public function actionUpdateRecommend()
    {
        UpdateService::update_recommend_begin();
    }
}
