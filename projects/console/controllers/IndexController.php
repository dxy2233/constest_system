<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2017/9/12
 * Time: 下午2:28
 */

namespace console\controllers;

use Yii;
use common\services\JobService;
use common\services\NodeService;
use common\models\business\BNode;
use common\services\UpdateService;
use common\models\business\BSetting;

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

    // 重置所有用户上级列表字段
    public function actionUpdateRecommend()
    {
        UpdateService::update_recommend_begin();
    }

    // 检查用户推荐关系是否循环
    public function actionCheckRecommend()
    {
        UpdateService::checkRecommend();
    }

    /**
     * 已存在推荐关系赠送投票劵以及GDT
     *
     * @return void
     */
    public function actionResetVoucherGdt()
    {
        $date = date('Y-m-d H:i:s', time());
        $begin =  $date . '重算推荐关系绑定赠送投票劵和GDT开始.';
        echo $begin . PHP_EOL;
        Yii::info($begin, 'VoucherGDT');
        $nodeModel = BNode::find()->active()->orderBy(['user_id' => SORT_DESC])->all();
        foreach ($nodeModel as $model) {
            $giveInfo = '用户ID：'.$model->user_id;
            $result = NodeService::checkVoucher($model->user_id);
            $msg = $giveInfo.$result->msg;
            if ($result->code) {
                Yii::error($msg, 'VoucherGDT');
                echo $msg . PHP_EOL;
                continue;
            }
            Yii::info($msg, 'VoucherGDT');
            echo $msg . PHP_EOL;
        }
        
        $date = date('Y-m-d H:i:s', time());
        $end = $date . '重算推荐关系绑定赠送投票劵和GDT结束';
        Yii::info($end, 'VoucherGDT');
        echo $end . PHP_EOL;
    }
}
