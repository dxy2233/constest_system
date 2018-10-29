<?php

namespace common\services;

use common\models\business\BSetting;
use common\models\business\BNode;
use common\models\business\BNotice;
use common\models\business\BHistory;
use Yii;

class JobService extends ServiceBase
{
    public static function beginPut($type = 0)
    {
        $setting = BSetting::find()->where(['key' => 'stop_vote'])->one();
        if ($setting->value == BNotice::STATUS_INACTIVE) {
            return false;
        }
        if ($type == 1) {
            self::putDo();
            return true;
        } else {
            $time = BSetting::find()->where(['key' => 'count_time'])->one();
            if (abs($time->value - time()) <= 30) {
                self::putDo();
            }
        }
    }

    public static function putDo()
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
        
        $data = BNode::find()->where(['is_tenure' => BNotice::STATUS_ACTIVE])->all();

        foreach ($data as $v) {
            $v->is_tenure = BNotice::STATUS_INACTIVE;
            if (!$v->save()) {
                $msg[] = $v->getFirstErrorText().$v->id;
            }
        }
        $last_time = BSetting::find()->where(['key' => 'end_update_time'])->one();
        $last_time->value = time();
        if (!$last_time->save()) {
            $msg[] = $last_time->getFirstErrorText();
        }
        $stop_vote = BSetting::find()->where(['key' => 'stop_vote'])->one();
        $stop_vote->value = BNotice::STATUS_INACTIVE;
        if (!$stop_vote->save()) {
            $msg[] = $stop_vote->getFirstErrorText();
        }

        if (count($msg) > 0) {
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'history');
        } else {
            $transaction->commit();
            Yii::info('执行成功', 'history');
        }
    }
}
