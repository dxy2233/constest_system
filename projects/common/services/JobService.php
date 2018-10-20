<?php

namespace common\services;

use common\models\business\BSetting;
use common\models\business\BNode;
use common\models\business\BNotice;
use common\models\business\BHistory;

class JobService extends ServiceBase
{
    public static function beginPut($type = 0)
    {
        if ($type == 1) {
            self::putDo();
        } else {
            $time = BSetting::find()->where(['key' => 'count_time'])->one();
            if (abs($time->value - time()) < 30) {
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
        $people = NodeService::getPeopleNum($id_arr, '', $endTime);
        $history_id = date('YmdHi');
        foreach ($data as $v) {
            $history = new BHistory();
            $history->people_number = $people[$v['id']];
            $history->vote_number = $v['vote_number'];
            $history->node_name = $v['name'];
            $history->username = $v['username'];
            $history->node_id = $v['id'];
            $history->is_tenure = $v['is_tenure'];
            $history->update_number = $history_id;
            echo json_encode($history);
            if (!$history->save()) {
                echo $history->getFirstErrorText();
            }
        }
            
        $data = BNode::find()->where(['is_tenure' => BNotice::STATUS_ACTIVE])->all();
        foreach ($data as $v) {
            $v->is_tenure = BNotice::STATUS_INACTIVE;
            $v->save();
        }
        $last_time = BSetting::find()->where(['key' => 'end_update_time'])->one();
        $last_time->value = time();
        $last_time->save();
        $stop_vote = BSetting::find()->where(['key' => 'stop_vote'])->one();
        $stop_vote->value = BNotice::STATUS_INACTIVE;
        $stop_vote->save();
    }
}
