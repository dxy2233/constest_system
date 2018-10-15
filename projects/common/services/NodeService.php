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

class NodeService extends ServiceBase
{
    /**
     * 统计节点票数
     *
     * @param BUser $user
     * @return void
     */
    public static function getList(int $page = 0, string $searchName = '', string $str_time = '', string $end_time = '', int $type = 0)
    {
        $find = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('left join', 'gr_user B', 'A.user_id = B.id')
        ->join('left join', 'gr_vote C', 'A.id = C.node_id')
        ->groupBy(['A.id'])
        ->select(['sum(C.vote_number) as vote_number','A.name','B.username','sum(consume) as consume','A.is_tenure','A.create_time','A.status','A.id'])
        ->orderBy('sum(C.vote_number) desc');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.name',$searchName],['like','B.username',$searchName]]);
        }
        
        if ($str_time != '') {
            $find->startTime($str_time, 'C.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'C.create_time');
        }

        if ($type != '') {
            $find->where(['A.type_id' => $type]);
        }
        $count = $find->count();
        
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
        $where = [];
        if ($id_arr != []) {
            $where[] = "node_id  in (".implode(',', $id_arr).")";
        }
        
        
        if ($str_time != '') {
            $str_time = strtotime($str_time);
            $where[] = "create_time  >= $str_time";
        }
        
        if ($end_time != '') {
            $end_time = strtotime($end_time);
            $where[] = "create_time  <= $end_time";
        }
        if (count($where)>0) {
            $where = 'where '. implode(' && ', $where);
        } else {
            $where = '';
        }
        $sql = "select node_id,count(*) as count from (select node_id from gr_vote $where group by user_id,node_id) c group by node_id";
        //echo $sql;
        $command = \Yii::$app->db->createCommand($sql);
        $res     = $command->queryAll();
        $data = [];
        foreach ($res as $v) {
            $data[$v['node_id']] = $v['count'];
        }
        return $data;
    }
}
