<?php

namespace common\services;

use common\models\NodeType;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\NetUtil;
use common\services\ReturnInfo;
use common\services\UserService;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BUser;
use common\models\business\BNotice;
use common\models\business\BCurrency;
use common\models\business\BVoucher;
use common\models\business\BNodeRule;
use common\models\business\BNodeUpgrade;
use common\models\business\BNodeRecommend;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BNodeType;
use common\models\business\BUserWallet;
use common\models\business\BUserAccessToken;
use common\models\business\BTypeRuleContrast;
use common\models\business\BUserRefreshToken;

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
        ->join('left join', 'gr_vote C', 'A.id = C.node_id && C.status = '.BNotice::STATUS_ACTIVE)
        ->join('left join', BNodeType::tablename().' D', 'A.type_id = D.id')
        ->groupBy(['A.id'])
        ->select(['sum(C.vote_number) as vote_number','A.name','B.mobile','A.grt', 'A.tt', 'A.bpt','A.is_tenure','A.create_time', 'A.examine_time','A.status','A.id','A.is_tenure','D.name as type_name', 'D.id as type_id']);
        // ->orderBy('sum(C.vote_number) desc');
        
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.name',$searchName],['like','B.mobile',$searchName]]);
        }
        
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }

        if ($type != '') {
            $find->andWhere(['A.type_id' => $type]);
        }

        if ($status != '') {
            $find->andWhere(['A.status' => $status]);
        }
        $count = $find->count();
        
        if ($order != '') {
            $find->orderBy($order);
        }
        //echo $find->createCommand()->getRawSql();
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            if ($v['vote_number'] == null) {
                $v['vote_number'] = '0';
            }
        }
        
        return $data;
    }

    public static function getIndexList(int $page = 0, string $searchName = '', string $str_time = '', string $end_time = '', int $type = 0, int $status = 0, $order = '')
    {
        $find = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('left join', BUser::tablename().' B', 'A.user_id = B.id')
        ->join('left join', BVote::tablename().' C', 'A.id = C.node_id && C.status = '.BNotice::STATUS_ACTIVE)
        ->join('left join', BNodeType::tablename().' D', 'A.type_id = D.id')
        ->groupBy(['A.id'])
        ->select(['sum(C.vote_number) as vote_number','A.name','B.mobile','A.grt', 'A.tt', 'A.bpt','A.is_tenure','A.create_time', 'A.examine_time','A.status','A.id','A.is_tenure','D.name as type_name', 'D.id as type_id', 'A.user_id']);
        // ->orderBy('sum(C.vote_number) desc');
        
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.name',$searchName],['like','B.mobile',$searchName]]);
        }
        
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }

        if ($type != '') {
            $find->andWhere(['A.type_id' => $type]);
        }

        if ($status != '') {
            $find->andWhere(['A.status' => $status]);
        } else {
            $find->andWhere(['or', ['A.status' => BNode::STATUS_ON], ['A.status' => BNode::STATUS_OFF]]);
        }
        $count = $find->count();
        
        if ($order != '') {
            $find->orderBy($order);
        }

        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            if ($v['vote_number'] == null) {
                $v['vote_number'] = '0';
            }
            $recommend =
            BNodeRecommend::find()
            ->from(BNodeRecommend::tableName()." A")
            ->join('left join', BUser::tablename().' B', 'A.parent_id = B.id')
            ->select(['B.mobile'])
            ->where(['A.user_id' => $v['user_id']])
            ->asArray()
            ->one();
            if ($recommend) {
                $v['recommend_mobile'] = $recommend['mobile'];
            } else {
                $v['recommend_mobile'] = '-';
            }
        }
        
        return array('list' => $data, 'count' => $count);
    }
    

    /**
     * 统计支持人数
     *
     * @param BUser $user
     * @return void
     */
    public static function getPeopleNum(array $id_arr = [], $str_time = '', $end_time = '')
    {
        $voteMode = BVote::find()
        ->select(['node_id', 'COUNT(DISTINCT user_id) as people_number']);
        if (!empty($id_arr)) {
            $voteMode->where(['node_id' => $id_arr]);
        }
        if ($str_time != '') {
            $voteMode->startTime($str_time, 'create_time');
        }
        if ($end_time != '') {
            $voteMode->endTime($end_time, 'create_time');
            $voteMode->andWhere(['or', ['>', 'undo_time', strtotime($end_time)], ['undo_time' => 0]]);
        } else {
            $voteMode->andWhere(['or', ['>', 'undo_time', time()], ['undo_time' => 0]]);
        }

        $res = $voteMode->groupBy(['node_id'])
        ->indexBy('node_id')
        ->asArray()
        ->all();
        $data = [];
        ArrayHelper::multisort($res, 'people_number', SORT_DESC);
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
        $nodeModel = BNode::find()
        ->alias('n')
        ->select(['n.id', 'n.name', 'n.logo', 'n.is_tenure', 'SUM(v.vote_number) as vote_number', 'nt.is_vote'])
        ->active(BNode::STATUS_ACTIVE, 'n.')
        ->joinWith(['votes v' => function ($query) {
            $query->active(BVote::STATUS_ACTIVE, 'v.');
        }, 'nodeType nt' => function ($query) {
            $query->andWhere(['is_order' => NodeType::STATUS_ACTIVE]);
        }], false)
        ->filterWhere(['n.type_id' => $nodeType])
        ->orderBy(['vote_number' => SORT_DESC])
        ->groupBy('n.id');
        $nodeModel->cache(5);
        self::$number = $nodeModel->count();
        // $nodeModel->cache(-1);
        if (!is_null($page)) {
            $nodeModel->page($page, $pageSize);
        }
        $nodeModel->asArray();
        // var_dump($nodeModel->createCommand()->getRawSql());exit;
        $nodeList = $nodeModel->all();
        $nodeIds = ArrayHelper::getColumn($nodeList, 'id');
        // 获取节点user 去重统计
        $voteUser = NodeService::getPeopleNum($nodeIds);
        foreach ($nodeList as $key => &$node) {
            $node['vote_number'] = $node['vote_number'] ?? 0;
            $node['logo'] = FuncHelper::getImageUrl($node['logo'], 100, 100);
            $node['is_tenure'] = (bool) $node['is_tenure'];
            $node['is_vote'] = (bool) $node['is_vote'];
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

    // 添加节点模拟数据
    public static function addNodeMakeLogs($node)
    {
        // 补全充值冻结信息
        $currency_arr = BCurrency::find()->all();
        $currency_id = [];
        foreach ($currency_arr  as $v) {
            $currency_id[$v['code']] = $v['id'];
        }

        //GRT
        if ($node->grt != 0) {
            $grt_return = self::addCurrencyLogs($node->user_id, $currency_id['grt'], $node->grt, $node->id);
            if ($grt_return->code != 0) {
                return new FuncResult(1, '模拟失败', $grt_return->content);
            }
        }
        if ($node->tt != 0) {
            $tt_return = self::addCurrencyLogs($node->user_id, $currency_id['tt'], $node->tt, $node->id);
            if ($grt_return->code != 0) {
                return new FuncResult(1, '模拟失败', $tt_return->content);
            }
        }
        if ($node->bpt != 0) {
            $bpt_return = self::addCurrencyLogs($node->user_id, $currency_id['bpt'], $node->bpt, $node->id);
            if ($grt_return->code != 0) {
                return new FuncResult(1, '模拟失败', $bpt_return->content);
            }
        }
        return new FuncResult(0, '模拟完成');
    }
    // 模拟数据
    public static function addCurrencyLogs($user_id, $currency_id, $amount, $transaction_id)
    {
        $withdraw = new BUserRechargeWithdraw();
        $withdraw ->currency_id = $currency_id;
        $withdraw ->user_id = $user_id;
        $withdraw ->type = BUserRechargeWithdraw::$TYPE_RECHARGE;
        $withdraw ->amount = $amount;
        $withdraw ->transaction_id = (string)$transaction_id;
        $withdraw ->order_number = FuncHelper::generateOrderCode();
        $withdraw ->status = BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS;
        $withdraw ->remark = "添加节点转入积分";
        $withdraw ->audit_time = time();
        if (!$withdraw->save()) {
            return new FuncResult(1, '模拟失败', $withdraw->getFirstErrorText());
        }
        $userRechargeWithdrawId = $withdraw->id;

        $user_c_detail = new BUserCurrencyDetail();
        $user_c_detail->user_id = $user_id;
        $user_c_detail->currency_id = $currency_id;
        $user_c_detail->type = BUserCurrencyDetail::$TYPE_RECHARGE;
        $user_c_detail->amount = $amount;
        $user_c_detail->remark = '转入积分';
        $user_c_detail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $user_c_detail->relate_table = 'user_recharge_withdraw';
        $user_c_detail->relate_id = $userRechargeWithdrawId;
        $user_c_detail->effect_time = time();
        if (!$user_c_detail->save()) {
            return new FuncResult(1, '模拟失败', $user_c_detail->getFirstErrorText());
        }

        $frozen = new BUserCurrencyFrozen();
        $frozen->user_id = $user_id;
        $frozen->currency_id = $currency_id;
        $frozen->amount = $amount;
        $frozen->remark = '节点竞选';
        $frozen->status = BUserCurrencyFrozen::STATUS_FROZEN;
        $frozen->type = BUserCurrencyFrozen::$TYPE_ELECTION;
        $frozen->relate_table = 'node_upgrade';
        $frozen->relate_id = $transaction_id;
        if (!$frozen->save()) {
            return new FuncResult(1, '模拟失败', $frozen->getFirstErrorText());
        }

        UserService::resetCurrency($user_id, $currency_id);
        return new FuncResult(0, '模拟完成');
    }

    public static function getNodeQuota($mobile)
    {
        $url = \Yii::$app->params['quotaAddress'].'/site/site/node-mobile';
        $token = \Yii::$app->params['quotaToken'];
        $request = FuncHelper::request($url, '', 'token='.$token.'&mobile[]='.$mobile, [], '', 2);
        $return = json_decode($request, true);
        if (!$return || $return['code'] != 0) {
            return $return;
        }
        if (!is_array($mobile)) {
            foreach ($return['content'] as $k => $v) {
                if ($k == $mobile) {
                    return new FuncResult(0, '获取成功', $v);
                }
            }
        } else {
            return $return;
        }
    }

    public static function putNodeData($mobile)
    {
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        if (!$user) {
            return new FuncResult(1, '用户不存在');
        }
        $node = BNode::find()->where(['user_id' => $user->id])->one();
        if (!$node) {
            return new FuncResult(1, '节点不存在');
        }
        $node_type = BNodeType::find()->where(['id' => $node->type_id])->one();
        $recommend = BNodeRecommend::find()->where(['user_id' => $user->id])->one();
        $identify = BUserIdentify::find()->where(['user_id' => $user->id])->one();
        $return = [];
        $return['mobile'] = $mobile;
        $return['node_type'] = $node_type->name;
        if ($recommend) {
            $recommend_user = BUser::find()->BUser::find()->where(['id' => $recommend->parent_id])->one();
            $return['recommend_mobile'] = $recommend_user->mobile;
        } else {
            $return['recommend_mobile'] = '无';
        }
        if ($identify) {
            $return['real_name'] = $identify->realname;
            $return['number'] = $identify->number;
        } else {
            $return['real_name'] = '';
            $return['number'] = '未填写';
        }
    }
    // 轮询判断是否需要送投票券及gdt给当前用户
    public static function checkVoucher($user_id)
    {
        // 判断此用户是否节点
        $node_parent = BNode::find()->where(['user_id' => $user_id])->active()->one();
        if (!$node_parent) {
            return new FuncResult(0, '非节点不送奖励');
        }
        // 轮询此用户推荐的用户
        $data = BNodeRecommend::find()->where(['parent_id' => $user_id])->all();

        foreach ($data as $v) {
            // 判断被推荐人是否节点
            $node = BNode::find()->where(['user_id' => $v->user_id])->one();
            if (!$node) {
                continue;
            }
            $res = self::checkVoucherDo($v, $node);
            if ($res->code != 0) {
                return new FuncResult(1, '补充失败', $res->msg);
            }
        }
        //判断用户是否有推荐人
        $parent = BNodeRecommend::find()->where(['user_id' => $user_id])->one();
        if ($parent) {
            // 判断推荐人是否是节点
            $node = BNode::find()->where(['user_id' => $parent->parent_id])->active()->one();
            if ($node) {
                $res = self::checkVoucherDo($parent, $node_parent);
                if ($res->code != 0) {
                    return new FuncResult(1, '补充失败', $res->msg);
                }
                $sign = UserService::resetCurrency($parent->parent_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
            }
        }
        $sign = UserService::resetCurrency($user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        return new FuncResult(0, '补充成功');
    }
    // 具体赠送
    public static function checkVoucherDo(BNodeRecommend $recommend, BNode $node)
    {
        $tpq_num_arr = is_array(\Yii::$app->params['recommend']['voucher']) ? \Yii::$app->params['recommend']['voucher'] : [ 1 => 0, 2 => 200000, 3 => 80000, 4 => 20000 ];
        $gdt_num_arr = is_array(\Yii::$app->params['recommend']['gdt']) ? \Yii::$app->params['recommend']['gdt'] : [ 1 => 0, 2 => 2000, 3 => 800, 4 => 200 ];
        $node_upgrade = BNodeUpgrade::find()->where(['user_id' => $node->user_id, 'status' => BNodeUpgrade::STATUS_ACTIVE])->all();
        $type = $node->type_id;
        foreach ($node_upgrade as $v) {
            if ($v->old_type <= count($gdt_num_arr) && $v->old_type > $type) {
                $type = $v->old_type;
            }
        }

        // 判断是否有投票券赠送记录
        // 由于节点可升级且升级后不再次赠送，故取消数量判断
        $voucher = BVoucher::find()->where(['give_user_id' => $recommend->user_id, 'user_id' => $recommend->parent_id, 'node_id' => $node->id])->one();
        if (!$voucher) {
            $res = VoucherService::createNewVoucher($recommend->parent_id, $node->id, $tpq_num_arr[$type], $recommend->user_id);
            if ($res->code != 0) {
                return new FuncResult(1, '补充失败');
            } else {
                $voucher = $res->content;
            }
            $recommend->node_id = $node->id;
            $recommend->amount = $tpq_num_arr[$type];
            if (!$recommend->save()) {
                return new FuncResult(1, '补充失败', $recommend->getFirstErrorText());
            }
        }

        // 判断是否有gdt赠送记录
        $old_gdt = BUserCurrencyDetail::find()->where(['type' => BUserCurrencyDetail::$TYPE_REWARD,  'relate_table' => 'voucher', 'relate_id' => $voucher->id, 'currency_id' => BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT)])->one();
        if (!$old_gdt) {
            $res = [
                    'user_id' => $recommend->parent_id,
                    'type' => BUserCurrencyDetail::$TYPE_REWARD,
                    'relate_table' => 'voucher',
                    'relate_id' => $voucher->id,
                    'amount' => $gdt_num_arr[$type],
                    'remark' => '推荐送GDT',
                ];
            $json = VoteService::giveCurrency($res);
            if ($json->code) {
                return new FuncResult(1, '补充失败', $json->msg);
            }
        }

        return new FuncResult(0, '补充成功');
    }
    
    //获取节点升级后应得配额
    public static function getUpgradeQuota($old_type_id, $new_type_id)
    {
        $old_type = BNodeType::find()->where(['id' => $old_type_id])->one();
        if (!$old_type) {
            $old_quota = 0;
        } else {
            $old_quota = $old_type->quota;
        }
        $new_type = BNodeType::find()->where(['id' => $new_type_id])->one();
        if (!$new_type) {
            return 0;
        } else {
            $new_quota = $new_type->quota;
        }
        if ($old_type_id == 5) {
            $new_quota += 82000;
        }
        return $new_quota - $old_quota;
    }
    //获取节点升级后补送gdt数量
    public static function getGiveGdtNumber($old_type_id, $new_type_id)
    {
        $old_type = BNodeType::find()->where(['id' => $old_type_id])->one();
        if (!$old_type) {
            $old_gdt = 0;
        } else {
            $old_gdt = $old_type->gdt_reward;
        }
        $new_type = BNodeType::find()->where(['id' => $new_type_id])->one();
        if (!$new_type) {
            return 0;
        } else {
            $new_gdt = $new_type->gdt_reward;
        }
        return $new_gdt - $old_gdt;
    }
    //获取节点升级需补缴grt数量
    public static function getGrtNumber($old_type_id, $new_type_id)
    {
        $old_type = BNodeType::find()->where(['id' => $old_type_id])->one();
        if (!$old_type) {
            $old_grt = 0;
        } else {
            $old_grt = $old_type->conversion;
        }
        $new_type = BNodeType::find()->where(['id' => $new_type_id])->one();
        if (!$new_type) {
            return 0;
        } else {
            $new_grt = $new_type->conversion;
        }
        return $new_grt - $old_grt;
    }
}
