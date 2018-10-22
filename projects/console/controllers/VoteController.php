<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2017/9/12
 * Time: 下午2:28
 */

namespace console\controllers;

use yii\helpers\ArrayHelper;
use common\services\VoteService;
use common\models\business\BSetting;

class VoteController extends BaseController
{
    // 这里是72小时时间戳
    const REMOKE_TIME = 259200;

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
        $revokeList = VoteService::getRevokeList(0, function ($query) {
            return $query->andWhere(['>=', 'create_time', NOW_TIME - self::REMOKE_TIME]);
        });
        $count = 0;
        $success = 0;
        $fail = 0;
        foreach ($revokeList as $key => $revoke) {
            $count++;
            $result = VoteService::revokeAction($revoke->user_id, $revoke->id);
            if ($result->code) {
                $fail++;
                echo $result->msg . PHP_EOL;
            } else {
                $success++;
                echo 'success' . PHP_EOL;
            }
            // 投票时间加上设定的时间戳（判断当前时间是否大于相加时间戳，就需要执行解冻操作, 反之不处理)
            // $afterTime = (int) $revoke->create_time + self::REMOKE_TIME;
            // if ($afterTime >= NOW_TIME) {
            // }
        }
        echo 'Action count: '.$count.' success: '. $success . ' fail: '.$fail . PHP_EOL;
    }
}
