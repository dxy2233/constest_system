<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2017/9/12
 * Time: 下午2:28
 */

namespace console\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\services\VoteService;
use common\models\business\BSetting;

class VoteController extends BaseController
{
    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    /**
     * 投票赎回
     *
     * @return void
     */
    public function actionRevoke()
    {
        // 赎回投票操作时间加上后台设定撤回时间是否大于当前控制台执行时间（判断当前时间小于等于执行解冻操作, 反之不处理)
        $revokeList = VoteService::getRevokeList(0, function ($query) {
            return $query->andWhere(['<=', 'undo_time', NOW_TIME]);
        });
        $count = 0;
        $success = 0;
        $fail = 0;
        foreach ($revokeList as $key => $revoke) {
            $count++;
            $result = VoteService::revokeAction($revoke->user_id, $revoke->id);
            if ($result->code) {
                $fail++;
                Yii::error($result->msg, 'vote');
                echo $result->msg . PHP_EOL;
            } else {
                Yii::info($result->msg, 'vote');
                $success++;
                echo 'success' . PHP_EOL;
            }
        }
        
        $action = 'Action count: '.$count.' success: '. $success . ' fail: '.$fail . PHP_EOL;
        Yii::info($action, 'vote');
        echo $action;
    }
}
