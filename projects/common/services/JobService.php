<?php

namespace common\services;

use common\services\NodeService;
use common\services\UserService;
use common\models\business\BSetting;
use common\models\business\BNode;
use common\models\business\BNotice;
use common\models\business\BHistory;
use common\models\business\BCycle;
use common\models\business\BVote;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BCurrency;
use Yii;

class JobService extends ServiceBase
{
    public static function beginPut($type = 0)
    {
        // 取出所有有效设置

        $cycle = BCycle::find()->where(['>=','tenure_end_time',time()-60])->all();
        $put = $history = false;
        foreach ($cycle  as $v) {
            // 竞选截止 生成快照
            
            if (abs($v->cycle_end_time - time()) <= 30 && !$history) {
                $res = self::HistoryDo();
                $history = true;
            }
            // 任职截止 发放奖励
            if (abs($v->tenure_end_time - time()) <= 30 && !$put) {
                $res = self::PutDo($v);
                $put = true;
            }
        }
    }

    // 生成快照
    public static function HistoryDo()
    {
        $endTime = date('Y-m-d H:i:s');
            
        $page = 0;
        $data = NodeService::getList($page, '', '', $endTime);

        $id_arr = [];
        foreach ($data as $v) {
            $id_arr[] = $v['id'];
        }
        $msg = [];
        $people = NodeService::getPeopleNum($id_arr, '', $endTime);
        $transaction = \Yii::$app->db->beginTransaction();

        $history_id = date('YmdHi');
        foreach ($data as $v) {
            if ($v['mobile'] == '') {
                continue;
            }
            $history = new BHistory();
            if (empty($people[$v['id']])) {
                $history->people_number = 0;
            } else {
                $history->people_number = $people[$v['id']];
            }
            $history->vote_number = $v['vote_number'];
            $history->node_name = $v['name'];
            $history->username = $v['mobile'];
            $history->node_id = $v['id'];
            $history->node_type = $v['type_id'];
            $history->is_tenure = $v['is_tenure'];
            $history->update_number = $history_id;

            if (!$history->save()) {
                $msg[] = $history->getFirstErrorText();
            }
        }

        $last_time = BSetting::find()->where(['key' => 'end_update_time'])->one();
        $last_time->value = time();
        if (!$last_time->save()) {
            $msg[] = $last_time->getFirstErrorText();
        }
                


        if (count($msg) > 0) {
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'history');
            return false;
        } else {
            $transaction->commit();
            Yii::info('执行成功', 'history');
            return true;
        }
    }

    // 任职结束
    public static function PutDo($cycle)
    {
        $data = BNode::find()->where(['is_tenure' => BNotice::STATUS_ACTIVE])->all();
        $msg = [];
        $user_arr = [];
        $setting = BSetting::find()->where(['in', 'key', ['pay_reward', 'ordinary_reward', 'voucher_reward']])->all();
        $reward = [];
        foreach ($setting as $v) {
            $reward[$v->key] = $v->value;
        }
        
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            // 发放投中奖励
            $vote = BVote::find()->where(['node_id'=>$v->id])->andWhere(['>=','create_time',$cycle->cycle_start_time])->andWhere(['<=','create_time',$cycle->cycle_end_time])->all();

            foreach ($vote as $val) {
                $currencyDetail = new BUserCurrencyDetail();
                $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
                $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetail->effect_time = NOW_TIME;
                $currencyDetail->remark = '投中奖励';
                $currencyDetail->user_id = $val->user_id;
                $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
                $currencyDetail->relate_table = BVote::tableName();
                $currencyDetail->relate_id = $val->id;
                $user_arr[$val->user_id] = $val->user_id;
                if ($val->type == BVote::TYPE_PAY) {
                    $currencyDetail->amount = $reward['pay_reward'] * $val->consume;
                } elseif ($val->type == BVote::TYPE_ORDINARY) {
                    $currencyDetail->amount = $reward['ordinary_reward'] * $val->consume;
                } elseif ($val->type == BVote::TYPE_VOUCHER) {
                    $currencyDetail->amount = $reward['voucher_reward'] * $val->consume;
                }
                if (!$currencyDetail->save()) {
                    $msg[] = $currencyDetail->getFirstErrorText().'发放选中奖励'.$val->id;
                }
            }
            
            // 清空任职状态
            $v->is_tenure = BNotice::STATUS_INACTIVE;
            if (!$v->save()) {
                $msg[] = $v->getFirstErrorText().'清空任职'.$v->id;
            }
        }
        foreach ($user_arr as $v) {
            $sign = UserService::resetCurrency($v, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
            if ($sign === false) {
                $msg[] = '重算失败'.$v->id;
            }
        }
        if (count($msg) > 0) {
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'history');
            return false;
        } else {
            $transaction->commit();
            Yii::info('执行成功', 'history');
            return true;
        }
    }
}
