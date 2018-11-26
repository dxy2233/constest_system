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
use common\services\UserService;
use common\services\VoteService;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BSetting;
use common\models\business\BCurrency;
use common\models\business\BUserCurrencyDetail;

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

    public $start;
    public $end;

    public function options($actionID)
    {
        return ['start', 'end'];
    }
    /**
     * 重算投票赠送GDT
     * 开始时间和结束时间未时间戳
     * @return void
     */
    public function actionRetryGiveGdt()
    {
        $voteModel = BVote::find()
        ->select(['create_time'])
        ->where(['<>', 'create_time', '']);
        $this->start  = $this->start ?? $voteModel->orderBy(['create_time' => SORT_ASC])->scalar();
        $this->end  = $this->end ?? $voteModel->orderBy(['create_time' => SORT_DESC])->scalar();
        $date = date('Y-m-d H:i:s', time());
        $begin =  $date . '重算投票赠送GDT开始.';
        $timeSlot = '重算时间段：' . date('Y-m-d H:i:s', $this->start) .'-'. date('Y-m-d H:i:s', $this->end);
        Yii::info($begin, 'vote');
        Yii::info($timeSlot, 'vote');
        echo $begin .PHP_EOL;
        echo $timeSlot.PHP_EOL;
        $voteDate = $voteModel->select(['*'])->all();
        $ordinaryGdt = (float) SettingService::get('vote', 'ordinary_gdt')->value;
        $payGdt = (float) SettingService::get('vote', 'pay_gdt')->value;
        $relate_table = 'vote';
        foreach ($voteDate as $key => $model) {
            $voteInfo = '该投票记录ID： '.$model->id.' 用户ID：'.$model->user_id;
            if (in_array($model->type, [$model::TYPE_ORDINARY, $model::TYPE_PAY])) {
                $hasGive = BUserCurrencyDetail::find()
                ->active()
                ->where([
                    'user_id' => $model->user_id,
                    'relate_id' => $model->id,
                    'type' => BUserCurrencyDetail::$TYPE_REWARD,
                ])->exists();
                if ($hasGive) {
                    continue;
                    // 不提示
                    $msg = $voteInfo.' 已赠送GDT';
                    Yii::info($msg, 'vote');
                    echo $msg . PHP_EOL;
                }
                if ($model->type == $model::TYPE_ORDINARY) {
                    $giveAmount = round($model->consume * $ordinaryGdt, 8);
                } else {
                    $giveAmount = round($model->consume * $payGdt, 8);
                }
                if ($giveAmount <= 0) {
                    $msg = $voteInfo.' 赠送数量小于等于 0 则不赠送';
                    Yii::info($msg, 'vote');
                    echo $msg . PHP_EOL;
                    continue;
                }
                $remark = BUserCurrencyDetail::getType(BUserCurrencyDetail::$TYPE_VOTE);
                $giveDate = [
                    'user_id' => $model->user_id,
                    'relate_table' => $relate_table,
                    'relate_id' => $model->id,
                    'type' => BUserCurrencyDetail::$TYPE_REWARD,
                    'amount' => $giveAmount,
                    'remark' => $remark . '- GDT赠送',
                ];
                $give = VoteService::giveCurrency($giveDate);
                if ($give->code) {
                    $msg = $voteInfo.$give->msg;
                    Yii::error($msg, 'vote');
                    echo $msg . PHP_EOL;
                    continue;
                }
                $sign = UserService::resetCurrency($model->user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
                if ($sign === false) {
                    $msg = $voteInfo.' reset user position fail';
                    Yii::error($msg, 'vote');
                    echo $msg . PHP_EOL;
                    continue;
                }
                $msg = $voteInfo.' 赠送成功';
                Yii::error($msg, 'vote');
                echo $msg . PHP_EOL;
            }
        }
        $date = date('Y-m-d H:i:s', time());
        $end = $date . '重算投票赠送GDT结束';
        Yii::info($end, 'vote');
        echo $end.PHP_EOL;
    }
}
