<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\FuncResult;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BVote;
use common\models\business\BVoucherDetail;
use common\models\business\BWalletJingtum;
use common\models\business\BUserCurrencyDetail;

class VoteService extends ServiceBase
{
    public static function getInstance(): VoteService
    {
        self::$instance = new self();
        self::$instance->init();

        return self::$instance;
    }

    /**
     * 校验投票是否能被赎回
     *
     * @param BUser $userModel
     * @param integer $id
     * @return boolean
     */
    public static function hasRevoke(BUser $userModel, int $voteId)
    {
        $voteModel = $userModel->getVotes()
        ->active()
        ->where(['type' => BVote::TYPE_ORDINARY, 'id' => $voteId]);
        $vote = $voteModel->one();
        if (is_null($vote)) {
            return new FuncResult(0, '该投票不存在或不能赎回', false);
        }
        $historyModelExists = $vote->getHistorys()
        ->select('id')
        ->andWhere(['>', 'create_time', $vote->create_time])
        ->orderBy(['update_number' => SORT_DESC])
        ->exists();
        return new FuncResult(0, '校验结果', $historyModelExists);
    }
    /**
     * 货币投票
     *
     * @param BUser $userModel
     * @param array $data
     * @param integer $type
     * @return void
     */
    public static function currencyVote(BUser $userModel, array $data = [], int $type = 1)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $isFrozen = true;
            $voteModel = new BVote();
            $voteModel->node_id = $data['node_id'];
            $voteModel->type = $type;
            $voteModel->status = BVote::STATUS_ACTIVE;
            $voteModel->vote_number = $data['vote_number'];
            if ($type == BVote::TYPE_PAY) {
                $isFrozen = false;
                $voteModel->consume = $data['amount'];
            }
            $voteModel->create_time = NOW_TIME;
            $voteModel->user_id = $userModel->id;
            if (!$voteModel->save()) {
                throw new ErrorException('投票失败', $voteModel->getFirstError());
            }
            $poundage = (int) SettingService::get('vote', 'vote_poundage')->value;
            // 发送方账户公钥
            $addressModel = $userModel->userRechargeAddress;
            if (is_null($addressModel)) {
                throw new ErrorException('wallet address no found');
            }
            $privateKey = BWalletJingtum::find()->select(['secret'])->where(['address' => $addressModel->address])->scalar();
            if (is_null($privateKey)) {
                throw new ErrorException('wallet private no found');
            }
            $poundage = 0; // 手续费
            $res = [
                'id' => $voteModel->id,
                'currency_id' => $data['currency_id'],
                'user_id' => $userModel->id,
                'type' => BUserCurrencyDetail::$TYPE_VOTE,
                'amount' => $data['amount'], // 总数量
                'poundage' => $poundage, // 手续费
                'privateKey' => $privateKey, // 发送方私钥
                'source_address' => $addressModel->address, // 发送方地址
                'remark' => BUserCurrencyDetail::getType(BUserCurrencyDetail::$TYPE_VOTE),
                'status' => BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS,
                'status_remark' => '确认',
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'user_recharge' => $isFrozen ? 'voto_frozen' : 'voto_trans',
                'poundage' => $poundage, // 手续费
            ];
            $currencyTrans = WithdrawService::withdrawCurrencyVote($res, $isFrozen);
            if ($currencyTrans->code) {
                throw new ErrorException('划账失败 '.$currencyTrans->msg);
            }
            $transaction->commit();
            return new FuncResult(0, '投票成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            return new FuncResult(1, $e->getMessage());
        }
    }
    /**
     * 投票劵投票
     *
     * @param BUser $userModel
     * @param array $data
     * @return void
     */
    public static function voucherVote(BUser $userModel, array $data = [])
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $voteModel = new BVote();
            $voteModel->node_id = $data['node_id'];
            $voteModel->type = BVote::TYPE_VOUCHER;
            $voteModel->status = BVote::STATUS_ACTIVE;
            $voteModel->vote_number = $data['vote_number'];
            $voteModel->create_time = NOW_TIME;
            $voteModel->user_id = $userModel->id;
            if (!$voteModel->save()) {
                throw new ErrorException('投票失败', $voteModel->getFirstError());
            }
            // 插入投票劵使用详情
            $voucherDetailModel = new BVoucherDetail();
            $voucherDetailModel->user_id = $userModel->id;
            $voucherDetailModel->node_id = $data['node_id'];
            $voucherDetailModel->vote_id = $voteModel->id;
            $voucherDetailModel->amount = $data['vote_number'];
            $voucherDetailModel->create_time = NOW_TIME;
            if (!$voucherDetailModel->save()) {
                throw new ErrorException('投票详情插入失败', $voucherDetailModel->getFirstError());
            }
            $transaction->commit();
            return new FuncResult(0, '投票成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());exit;
            return new FuncResult(1, '投票失败');
        }
    }

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
        } else {
            $nodeModel = BNode::find()
            ->alias('n')
            ->select(['n.id', 'n.name', 'n.desc', 'n.logo', 'n.is_tenure', 'SUM(v.vote_number) as vote_number'])
            ->active(BNode::STATUS_ACTIVE, 'n.')
            ->joinWith(['votes v' => function ($query) {
                $query->andWhere(['v.status' => BVote::STATUS_ACTIVE]);
            }])
            ->where(['n.type_id' => $nodeType])
            ->groupBy('n.id')
            ->orderBy(['vote_number' => SORT_DESC]);
            $nodeModel->cache(true);
            $nodeQuery = $nodeModel->createCommand();
            // echo ($nodeQuery->getRawSql());exit;
            $nodeList = $nodeQuery->queryAll();
            // 获取节点user 去重统计
            $nodeIds = ArrayHelper::getColumn($nodeList, 'id');
            $voteUser = \common\services\NodeService::getPeopleNum($nodeIds);
            foreach ($nodeList as $key => &$node) {
                $node['logo'] = FuncHelper::getImageUrl($node['logo']);
                $node['is_tenure'] = (bool) $node['is_tenure'];
                $node['people_number'] = isset($voteUser[$node['id']]) ? $voteUser[$node['id']] : 0;
            }
            
            ArrayHelper::multisort($nodeList, 'vote_number');
            $nodeData = [];
            foreach ($nodeList as $key => $node) {
                $nodeData[$key + 1] = $node;
            }
            $ranking[$nodeType] = $nodeData;
            $cache->set($cacheKey, $ranking);
        }

        $rank = 0;
        foreach ($ranking[$nodeType] as $key => $node) {
            if ($node['id'] == $nodeId) {
                $rank = $key;
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
    public static function cachePushRanking(int $nodeId = 0, int $userId = 0, int $voteNumber = 0) :array
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
