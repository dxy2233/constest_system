<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BUserIdentify;
use common\models\business\BUnvote;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserRecommend;

/**
 * Site controller
 */
class UserController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    public function actionIndex()
    {
        $find = BUser::find();
        $pageSize = $this->pInt('pageSize');
        $page = $this->pInt('page');
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','username',$searchName]);
        }
        $str_time = $this->pString('str_time');
        if ($str_time != '') {
            $find->startTime($str_time, 'create_time');
        }
        $end_time = $this->pString('end_time');
        if ($end_time != '') {
            $find->endTime($end_time, 'create_time');
        }
        $count = $find->count();
        $list = $find->page($page)->asArray()->all();
        foreach ($list as &$v) {
            $node = BNode::find()
            ->from(BNode::tableName()." A")
            ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id']])->one();
            if ($node) {
                $v['userType'] = $node->name;
                $v['nodeName'] = $node->nodeName;
            } else {
                $v['userType'] = '普通用户';
                $v['nodeName'] = '——';
            }
            $vote = BVote::find()->select(['sum(vote_number) as num'])->where(['user_id' => $v['id']])->active(BNotice::STATUS_ACTIVE)->asArray()->one();
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['last_login_time'] = date('Y-m-d H:i:s', $v['last_login_time']);
            $v['num'] = $vote['num'] == null ? 0 : $vote['num'];
        }
        $return = ['count' => $count, 'list' => $list];
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionGetOneUser()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        // 通用信息
        $info = [];
        $info['username'] = $user->username;
        $info['mobile'] = $user->mobile;
        $node = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
        ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $userId])->asArray()->one();
        if ($node) {
            $info['userType'] = $node['name'];
            $info['nodeName'] = $node['nodeName'];
        } else {
            $info['userType'] = '普通用户';
            $info['nodeName'] = '——';
        }
        $vote = BVote::find()->select(['sum(vote_number) as num'])->where(['user_id' => $userId])->active(BNotice::STATUS_ACTIVE)->asArray()->one();
        $info['num'] = $vote['num'] == null ? 0 : $vote['num'];
        // 基础信息
        $basic = [];
        $basic['username'] = $user->username;
        // 实名认证信息
        $identify = [];
        $userIdentify = BUserIdentify::find()->where(['user_id'=> $userId])->one();
        if (!empty($userIdentify)) {
            $identify['realName'] = $userIdentify->realname;
            $identify['number'] = $userIdentify->number;
            $identify['picFront'] = $userIdentify->pic_front;
            $identify['picBack'] = $userIdentify->pic_back;
        }
        // 投票信息
        $vote = [];
        // 投票记录
        $vote_log = BVote::find()->from(BVote::tableName()." A")
        ->join('inner join', 'gr_node C', 'A.node_id = C.id')
        ->join('inner join', 'gr_node_type B', 'C.type_id = B.id')
        ->select(['C.name as nodeName','B.name as typeName','A.*'])->where(['A.user_id' => $userId])->asArray()->all();
        $vote_vote = [];
        if (count($vote_log)>0) {
            foreach ($vote_log as $v) {
                $vote_item =[];
                $vote_item['nodeName'] = $v['nodeName'];
                $vote_item['typeName'] = $v['typeName'];
                $vote_item['type'] = BVote::getType($v['type']);
                $vote_item['voteNumber'] = $v['vote_number'];
                $vote_item['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
                $vote_vote[] = $vote_item;
            }
        }
        $vote['vote'] = $vote_vote;
        // 赎回记录
        $unvote_log = BVote::find()->from(BUnvote::tableName()." A")
        ->join('inner join', 'gr_node C', 'A.node_id = C.id')
        ->join('inner join', 'gr_node_type B', 'C.type_id = B.id')
        ->select(['C.name as nodeName','B.name as typeName'])->where(['A.user_id' => $userId])->asArray()->all();
        $vote_unvote = [];
        if (count($unvote_log)>0) {
            foreach ($unvote_log as $v) {
                $vote_item =[];
                $vote_item['nodeName'] =  $v['nodeName'];
                $vote_item['typeName'] = $v['typeName'];
                $vote_item['voteNumber'] = $v['vote_number'];

                $vote_item['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
                $vote_unvote[] = $vote_item;
            }
        }
        $vote['unvote'] = $vote_unvote;
        // 钱包
        $wallet = [];
        $wallet_data = BUserWallet::find()->where(['user_id' => $userId])->all();
        foreach ($wallet_data  as $v) {
            $wallet_item = [];
            $wallet_item['name'] = $v['wallet'];
            $wallet_item['address'] = $v['address'];
            $wallet_item['list'] = [];
            $currency = BUserCurrency::find()
            ->from(BUserCurrency::tableName()." A")
            ->join('inner join', 'gr_currency B', 'A.currency_id = B.id')
            ->select(['A.*','B.name'])
            ->where(['A.user_id' => $userId, 'A.wallet_id' => $v['id']])->all();
            foreach ($currency as $val) {
                $c_item = [];
                $c_item['name'] = $val['name'];
                $c_item['positionAmount'] = $val['position_amount'];
                $c_item['frozenAmount'] = $val['frozen_amount'];
                $c_item['useAmount'] = $val['use_amount'];
                $in_and_out_detail = UserCurrencyDetail::find()->where(['user_id' => $userId, 'currency_id' => $val['id']])->all();
                foreach ($detail as $value) {
                    $d_item = [];
                    $d_item['remark'] = $value->remark;
                    $d_item['amount'] = $value->amount;
                    $d_item['effectTime'] = date('Y-m-d', $value->effect_time);
                    $c_item['inAndOut'][] = $d_item;
                }
                $frozen_detail = BUserCurrencyFrozen::find()->where(['user_id' => $userId, 'currency_id' => $val['id']])->all();
                foreach ($detail as $value) {
                    $d_item = [];
                    $d_item['remark'] = $value->remark;
                    $d_item['amount'] = $value->amount;
                    $d_item['effectTime'] = date('Y-m-d', $value->effect_time);
                    $c_item['frozen'][] = $d_item;
                }
                $wallet_item['list'][] = $c_item;
            }
        }
        // 投票券
        $voucher = [];
        $voucher_data = BVoucher::find()
        ->from(BVoucher::tableName()." A")
        ->join('inner join', 'gr_node B', 'A.node_id = B.id')
        ->join('inner join', 'gr_node_type C', 'B.type_id = C.id')
        ->select(['A.*','B.name as nodeName','C.name as typeName'])
        ->where(['A.user_id' => $userId])->andWhere(['!=', 'A.use_voucher', 0])->all();
        foreach ($voucher_data as $v) {
            $voucher_item = [];
            $voucher_item['nodeName'] = $v['nodeName'];
            $voucher_item['typeName'] = $v['typeName'];
            $voucher_item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $voucher[] = $voucher_item;
        }

        // 推荐
        $recommend = [];
        $recommend_data = BUserRecommend::find()
        ->from(BUserRecommend::tableName()." A")
        ->join('inner join', 'gr_user D', 'A.user_id = D.id')
        ->join('inner join', 'gr_node B', 'B.user_id = D.id')
        ->join('inner join', 'gr_node_type C', 'B.type_id = C.id')
        
        ->select(['A.*','B.name as nodeName','C.name as typeName', 'D.username'])
        ->where(['A.parent_id' => $userId])->all();
        foreach ($recommend_data as $v) {
            $recommend_item = [];
            $recommend_item['nodeName'] = $v['nodeName'];
            $recommend_item['username'] = $v['username'];
            $recommend_item['typeName'] = $v['typeName'];
            $recommend_item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $recommend[] = $recommend_item;
        }
        $return = [
          'info' => $info,
          'basic' => $basic,
          'identify' => $identify,
          'vote' => $vote,
          'wallet' => $wallet,
          'voucher' => $voucher,
          'recommend' => $recommend,
        ];

        return $this->respondJson(0, '获取成功', $return);
    }
}
