<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\NetUtil;
use common\services\ReturnInfo;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BUserWallet;
use common\models\business\BUserAccessToken;
use common\models\business\BUserRefreshToken;
use common\models\business\BTypeRuleContrast;
use common\models\business\BNodeRule;
use common\models\business\BNotice;
use common\models\business\BNodeType;

class NodeService extends ServiceBase
{
    /**
     * 统计节点票数
     *
     * @param BUser $user
     * @return void
     */
    public static function getList(int $page = 0, string $searchName = '', string $str_time = '', string $end_time = '', int $type = 0, int $status = 0, $order = '')
    {
        $find = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('left join', 'gr_user B', 'A.user_id = B.id')
        ->join('left join', 'gr_vote C', 'A.id = C.node_id')
        ->join('left join', BNodeType::tablename().' D', 'A.type_id = D.id')
        ->groupBy(['A.id'])
        ->select(['sum(C.vote_number) as vote_number','A.name','B.username','A.grt', 'A.tt', 'A.bpt','A.is_tenure','A.create_time','A.status','A.id','A.is_tenure','D.name as type_name']);
        // ->orderBy('sum(C.vote_number) desc');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.name',$searchName],['like','B.username',$searchName]]);
        }
        
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }

        if ($type != '') {
            $find->where(['A.type_id' => $type]);
        }

        if ($status != '') {
            $find->where(['A.status' => $status]);
        }
        $count = $find->count();
        
        if ($order != '') {
            $find->orderBy($order. ' desc');
        }

        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        //echo $find->createCommand()->getRawSql();
        return $data;
    }
    

    /**
     * 统计支持人数
     *
     * @param BUser $user
     * @return void
     */
    public static function getPeopleNum(array $id_arr = [], string $str_time = '', string $end_time = '')
    {
        $voteMode = BVote::find()->select(['node_id', 'COUNT(DISTINCT user_id) as people_number']);
        if (!empty($id_arr)) {
            $voteMode->where(['node_id' => $id_arr]);
        }
        
        if ($str_time != '') {
            $voteMode->startTime($str_time, 'create_time');
        }
        if ($end_time != '') {
            $voteMode->endTime($end_time, 'create_time');
        }

        $res = $voteMode->groupBy(['node_id'])
        ->indexBy('node_id')
        ->asArray()
        ->all();
        $data = [];
        foreach ($res as $v) {
            $data[$v['node_id']] = $v['people_number'];
        }
        return $data;
    }


    // 获取节点当前权益
    public static function getNodeRule(int $node_id, int $order)
    {
        $node = BNode::find()->where(['id' => $node_id])->one();
        $find = BTypeRuleContrast::find()
        ->from(BTypeRuleContrast::tableName()." A")
        ->join('left join', BNodeRule::tableName().' B', 'A.rule_id = B.id')
        ->where(['A.type_id' => $node->type_id]);
        if ($node->is_tenure == BNotice::STATUS_ACTIVE) {
            $where = ['or', ['A.is_tenure'=> BTypeRuleContrast::$TYPE_ALL], ['A.is_tenure' => BTypeRuleContrast::$TYPE_TENURE], ['and', ['A.is_tenure' => BTypeRuleContrast::$TYPE_ORDER], ['<=', 'A.min_order', $order], ['>=', 'A.max_order', $order]]];
        } else {
            $where = ['or', ['A.is_tenure'=> BTypeRuleContrast::$TYPE_ALL], ['and', ['A.is_tenure' => BTypeRuleContrast::$TYPE_ORDER], ['<=', 'A.min_order', $order], ['>=', 'A.max_order', $order]]];
        }
        $data = $find->andWhere($where)->select(['A.id as aid','B.*'])->asArray()->all();
        return $data;
    }
}
