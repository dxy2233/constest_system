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
    // 设定一个计数值，用于方法赋值调用
    public static $number = 0;
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
        ->select(['sum(C.vote_number) as vote_number','A.name','B.mobile','A.grt', 'A.tt', 'A.bpt','A.is_tenure','A.create_time','A.status','A.id','A.is_tenure','D.name as type_name']);
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
        foreach ($data as &$v) {
            if ($v['vote_number'] == null) {
                $v['vote_number'] = 0;
            }
        }
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
    public static function getNodeRule(int $node_id, int $order = 1)
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



    /**
     * 获取节点列表 以及节点投票信息
     *
     * @param integer $nodeType
     * @param integer $page
     * @param integer $pageSize 15
     * @param string $field people_number|vote_number
     * @param integer $sort SORT_DESC|SORT_ASC
     * @return void
     */
    public static function getNodeList(int $nodeType = null, int $page = null, int $pageSize = 15, string $field = 'vote_number', int $sort = SORT_DESC)
    {
        // $cacheKey = 'nodeList_'.$nodeType;
        // $cache = \Yii::$app->cache;
        $nodeModel = BNode::find()
        ->alias('n')
        ->select(['n.id', 'n.name', 'n.desc', 'n.logo', 'n.is_tenure', 'SUM(v.vote_number) as vote_number'])
        ->active(BNode::STATUS_ACTIVE, 'n.')
        ->joinWith(['votes v' => function ($query) {
            $query->andWhere(['v.status' => BVote::STATUS_ACTIVE]);
        }], false)
        ->filterWhere(['n.type_id' => $nodeType])
        ->groupBy('n.id');
        self::$number = $nodeModel->count();
        $nodeModel->cache(true);
        if (!is_null($page)) {
            $nodeModel->page($page, $pageSize);
        }
        $nodeModel->asArray();
        $nodeList = $nodeModel->all();
        $nodeIds = ArrayHelper::getColumn($nodeList, 'id');
        // 获取节点user 去重统计
        $voteUser = NodeService::getPeopleNum($nodeIds);
        foreach ($nodeList as $key => &$node) {
            $node['logo'] = FuncHelper::getImageUrl($node['logo']);
            $node['is_tenure'] = (bool) $node['is_tenure'];
            $node['people_number'] = isset($voteUser[$node['id']]) ? $voteUser[$node['id']] : 0;
            unset($node['votes']);
        }
        ArrayHelper::multisort($nodeList, $field, $sort);
        return $nodeList;
    }

    /**
     * 获取节点排名或者节点类型下所有节点列表
     *
     * @param integer $nodeType
     * @param integer $nodeId
     * @return void
     */
    public static function getNodeRanking(int $nodeType = 0, int $nodeId = 0)
    {
        $cacheKey = 'ranking';
        $cache = \Yii::$app->cache;
        if (empty($nodeType) && empty($nodeId)) {
            return 0;
        }
        $ranking = [];
        if ($cache->exists($cacheKey)) {
            $ranking = $cache->get($cacheKey);
        }
        
        if (!isset($ranking[$nodeType])) {
            $ranking[$nodeType] = self::getNodeList($nodeType);
            $cache->set($cacheKey, $ranking, 300);
        }

        $rank = 0;
        foreach ($ranking[$nodeType] as $key => $node) {
            if ($node['id'] == $nodeId) {
                $rank = $key + 1;
            }
        }
        return !$nodeId ? $ranking[$nodeType] : $rank;
    }


    /**
     * 蒋投票缓存到排名中
     *
     * @param array $data
     * @return void
     */
    public static function RefreshPushRanking(int $nodeId) :array
    {
        $cacheKey = 'ranking';
        $cache = \Yii::$app->cache;
        if (empty($nodeId) || empty($userId)) {
            return [];
        }

        $nodeModel = BNode::findOne($nodeId);
        $nodeTypeModel = $nodeModel->nodeType;
        if (is_null($nodeModel) || is_null($nodeTypeModel)) {
            return false;
        }
        $ranking[$nodeTypeModel->id] = self::getNodeList($nodeTypeModel->id);
        $cache->set($cacheKey, $ranking);
        return $ranking;
    }

    /**
     * 上个方法备份 （暂不删除）
     *
     * @return void
     */
    public static function old()
    {
        $ranking = [];
        // 初始化数组
        $ranking[$nodeTypeModel->id] = [];
        if ($cache->exists($cacheKey)) {
            $ranking = $cache->get($cacheKey);
        }
        // 判断节点类型数组是否存在
        if (!isset($ranking[$nodeTypeModel->id])) {
            $ranking[$nodeTypeModel->id] = [];
        }
        $nodeTypes = $ranking[$nodeTypeModel->id];
        $peopleNumbers = NodeService::getPeopleNum([$nodeModel->id]);
        $baseInfo = [
            'id' => $nodeModel->id,
            'type_id' => $nodeTypeModel->id,
            'desc' => $nodeModel->desc,
            'type_name' => $nodeTypeModel->name,
            'is_tenure' => (bool) $nodeModel->is_tenure,
            'logo' => $nodeModel->logoText
        ];
        if (empty($nodeTypes)) {
            $baseInfo['baseInfo']['vote_number'] = $voteNumber;
            $baseInfo['baseInfo']['people_number'] =  $peopleNumbers[$nodeModel->id];
           
            $nodeTypes[] = $baseInfo;
        } else {
            // 循环重算
            foreach ($nodeTypes as $key => &$node) {
                if ($node['id'] == $nodeModel->id) {
                    $node = array_merge($node, $baseInfo);
                    // 进行数据变更
                    if (isset($node['vote_number'])) {
                        $node['vote_number'] = (int) $node['vote_number'] + $voteNumber;
                    } else {
                        $node['vote_number'] = (int) $voteNumber;
                    }
                    if ($node['vote_number'] < 0) {
                        $node['vote_number'] = 0;
                    }
                    $node['people_number'] = $peopleNumbers[$nodeModel->id];
                }
            }
        }

        ArrayHelper::multisort($nodeTypes, 'vote_number');
        $nodeData = [];
        foreach ($nodeTypes as $key => $node) {
            $nodeData[$key + 1] = $node;
        }
        $ranking[$nodeTypeModel->id] = $nodeData;
        $cache->set($cacheKey, $ranking);
        return $ranking;
    }
}
