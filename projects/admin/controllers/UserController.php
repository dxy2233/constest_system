<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use common\services\RechargeService;
use common\services\UserService;
use common\services\VoteService;
use common\services\NodeService;
use yii\helpers\ArrayHelper;
use common\models\business\BArea;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BUserIdentify;
use common\models\business\BUserWallet;
use common\models\business\BUserOther;
use common\models\business\BUserVoucher;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BNodeType;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BCurrency;
use common\models\business\BUserRecommend;
use common\models\business\BUserRechargeAddress;
use common\components\FuncHelper;
use common\models\business\Traits\UserCurrencyTrait;

/**
 * Site controller
 */
class UserController extends BaseController
{
    use UserCurrencyTrait;
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'download',
            'recommend-download'
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
        $find = BUser::find()
        ->from(BUser::tableName()." A")

        ->select(['A.mobile', 'A.status', 'A.create_time', 'A.last_login_time', 'A.id','sum(B.vote_number) as num'])
        ->groupBy(['A.id'])
        ->join('left join', BVote::tableName().' B', 'B.user_id = A.id && B.status = '.BNotice::STATUS_ACTIVE)
        ->join('left join', BNode::tableName().' C', 'C.user_id = A.id && C.status = '.BNotice::STATUS_ACTIVE);
        $pageSize = $this->pInt('pageSize');
        $page = $this->pInt('page', 1);
        
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.username',$searchName],['like','C.name', $searchName]]);
        }
        $str_time = $this->pString('str_time');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        $end_time = $this->pString('end_time');
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'sum(B.vote_number)', 2 => 'A.create_time', 3 => 'A.last_login_time', 4 => 'sum(B.vote_number) desc', 5 => 'A.create_time desc', 6 => 'A.last_login_time desc'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time desc';
        }
        $find->orderBy($order);
        $count = $find->count();
        $is_download = $this->pInt('is_download', 0);
        if ($page != 0 && $is_download == 0) {
            $find->page($page);
        }
        
        //echo $find->createCommand()->getRawSql();
        $list = $find->asArray()->all();
        //var_dump($list);
        foreach ($list as &$v) {
            $node = BNode::find()
            ->from(BNode::tableName()." A")
            ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id'], 'A.status' => BNotice::STATUS_ACTIVE])->asArray()->one();
            if ($node) {
                $v['userType'] = $node['name'];
                $v['nodeName'] = $node['nodeName'];
            } else {
                $v['userType'] = '普通用户';
                $v['nodeName'] = '——';
            }
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['last_login_time'] = $v['last_login_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['last_login_time']);
            $v['status'] = BUser::getStatus($v['status']);
            if ($v['num'] == null) {
                $v['num'] = 0;
            }
            $recommend = BUserRecommend::find()
            ->from(BUserRecommend::tableName()." A")
            ->select(['B.mobile'])
            ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')->where(['A.user_id' => $v['id']])->asArray()->one();

            if (empty($recommend)) {
                $v['referee'] = '-';
            } else {
                $v['referee'] = $recommend['mobile'];
            }
        }
        if ($is_download == 1) {
            $headers = ['mobile'=> '用户','userType' => '类型', 'nodeName' => '拥有节点', 'num' => '已投票数', 'referee' => '推荐人', 'status' => '已投票数', 'create_time' => '注册时间', 'last_login_time' => '最后登录时间'];
            $this->download($list, $headers, '用户列表'.date('YmdHis'));
            return;
        }
        $return = ['count' => $count, 'list' => $list];
        return $this->respondJson(0, '获取成功', $return);
    }


    public function actionDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        $find = BUser::find()
        ->from(BUser::tableName()." A")
        ->select(['A.mobile', 'A.status', 'A.create_time', 'A.last_login_time', 'A.id','sum(B.vote_number) as num'])
        ->groupBy(['A.id'])
        ->join('left join', BVote::tableName().' B', 'B.user_id = A.id && B.status = '.BNotice::STATUS_ACTIVE);
        
        $searchName = $this->gString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','A.username',$searchName]);
        }
        $str_time = $this->gString('str_time');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        $end_time = $this->gString('end_time');
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        
        $order = $this->gString('order');
        if ($order != '') {
            $order_arr = [1 => 'sum(B.vote_number)', 2 => 'A.create_time', 3 => 'A.last_login_time', 4 => 'sum(B.vote_number) desc', 5 => 'A.create_time desc', 6 => 'A.last_login_time desc'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time desc';
        }
        $find->orderBy($order);

        //echo $find->createCommand()->getRawSql();
        $list = $find->asArray()->all();
        //var_dump($list);
        foreach ($list as &$v) {
            $node = BNode::find()
            ->from(BNode::tableName()." A")
            ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id'], 'A.status' => BNotice::STATUS_ACTIVE])->asArray()->one();
            if ($node) {
                $v['userType'] = $node['name'];
                $v['nodeName'] = $node['nodeName'];
            } else {
                $v['userType'] = '普通用户';
                $v['nodeName'] = '-';
            }
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['last_login_time'] = $v['last_login_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['last_login_time']);
            $v['status'] = BUser::getStatus($v['status']);
            if ($v['num'] == null) {
                $v['num'] = "0";
            }
            $recommend = BUserRecommend::find()
            ->from(BUserRecommend::tableName()." A")
            ->select(['B.mobile'])
            ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')->where(['A.user_id' => $v['id']])->asArray()->one();

            if (empty($recommend)) {
                $v['referee'] = '-';
            } else {
                $v['referee'] = $recommend['mobile'];
            }
        }

//        return $this->respondJson(0, '获取成功', $list);
        
        $headers = ['mobile'=> '用户','userType' => '类型', 'nodeName' => '拥有节点', 'num' => '已投票数', 'referee' => '推荐人', 'status' => '状态', 'create_time' => '注册时间', 'last_login_time' => '最后登录时间'];

        $this->download($list, $headers, '用户列表'.date('YmdHis'));

        return;
    }


    // 获取用户基本信息
    public function actionGetUserInfo()
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
        ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $userId, 'A.status' => BNotice::STATUS_ACTIVE])->asArray()->one();
        if ($node) {
            $info['userType'] = $node['name'];
            $info['nodeName'] = $node['nodeName'];
        } else {
            $info['userType'] = '普通用户';
            $info['nodeName'] = '——';
        }
        $vote = BVote::find()->select(['sum(vote_number) as num'])->where(['user_id' => $userId])->active(BNotice::STATUS_ACTIVE)->asArray()->one();
        $info['num'] = $vote['num'] == null ? 0 : $vote['num'];
        $recommend = BUserRecommend::find()
            ->from(BUserRecommend::tableName()." A")
            ->select(['B.mobile'])
            ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')->where(['A.user_id' => $userId])->asArray()->one();

        if (empty($recommend)) {
            $info['referee'] = '-';
        } else {
            $info['referee'] = $recommend['mobile'];
        }
        $info['recommend_code'] = $user->recommend_code;
        return $this->respondJson(0, '获取成功', $info);
    }


    // 获取用户实名信息 只获取已通过的
    public function actionGetUserIdentify()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $identify = [];
        $userIdentify = BUserIdentify::find()->where(['user_id'=> $userId])->orderBy('id DESC')->active(BNotice::STATUS_ACTIVE)->one();
        if (!empty($userIdentify)) {
            $identify['realName'] = $userIdentify->realname;
            $identify['number'] = $userIdentify->number;
            $identify['picFront'] = FuncHelper::getImageUrl($userIdentify->pic_front, 640, 640);
            $identify['picBack'] = FuncHelper::getImageUrl($userIdentify->pic_back, 640, 640);
        }
        return $this->respondJson(0, '获取成功', $identify);
    }
    // 用户钱包信息
    public function actionGetCurrency()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $currency_data = BUserCurrency::find()
        ->from(BUserCurrency::tableName()." A")
        ->select(['A.id', 'A.currency_id', 'A.position_amount', 'A.frozen_amount', 'A.use_amount','B.address','C.name'])
        ->join('left join', BUserRechargeAddress::tableName().' B', 'B.currency_id = A.currency_id && B.user_id = '.$userId)
        ->join('inner join', BCurrency::tableName().' C', 'C.id = A.currency_id')
        ->where(['A.user_id'=> $userId])->asArray()->all();
        if (!empty($currency_data)) {
            foreach ($currency_data as &$v) {
                $in_and_out_data = BUserCurrencyDetail::find()->where(['user_id' => $userId, 'currency_id' => $v['currency_id'], 'status' => BNotice::STATUS_ACTIVE])->orderBy('create_time desc')->all();
                foreach ($in_and_out_data as $val) {
                    if ($val['amount'] == 0) {
                        continue;
                    }
                    $in_and_out = [];
                    $in_and_out['type'] = UserCurrencyTrait::getType($val['type']);
                    $in_and_out['remark'] = $val['remark'];
                    $in_and_out['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                    $in_and_out['amount'] = ($val['amount'] > 0) ? '+'.$val['amount'] : $val['amount'];
                    $v['in_and_out'][] = $in_and_out;
                }
                $frozen_data = BUserCurrencyFrozen::find()->where(['user_id' => $userId, 'currency_id' => $v['currency_id'], 'status' => BNotice::STATUS_ACTIVE])->orderBy('create_time desc')->all();
                foreach ($frozen_data as $val) {
                    if ($val['amount'] == 0) {
                        continue;
                    }

                    $frozen = [];
                    $frozen['type'] = UserCurrencyTrait::getType($val['type']);
                    $frozen['remark'] = $val['remark'];
                    $frozen['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                    //$frozen['amount'] = ($val['amount'] > 0) ? '-'.$val['amount'] : '+'.abs($val['amount']);
                    $frozen['amount'] = '+'.abs($val['amount']);
                    $v['frozen'][] = $frozen;
                }
            }
        } else {
            $currency = BCurrency::find()->where(['status' => BCurrency::$CURRENCY_STATUS_ON, 'recharge_status' => BCurrency::$RECHARGE_STATUS_ON])->all();
            foreach ($currency as $v) {
                $returnInfo = RechargeService::getAddress($v['id'], $user->id);
                if ($returnInfo->code) {
                    return $this->respondJson(1, $returnInfo->msg);
                }
                $content = $returnInfo->content;
                $currency_data[] = array('address'=>$content['address'],'currency_id'=>$v['id'],'in_and_out'=>[],'frozen'=>[],'name'=>$v['name']);
            }
        }
        return $this->respondJson(0, '获取成功', $currency_data);
    }



    // 获取用户投票信息
    public function actionGetUserVote()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $vote = [];
        // 投票记录
        $vote_log = BVote::find()->from(BVote::tableName()." A")
        ->join('inner join', 'gr_node C', 'A.node_id = C.id')
        ->join('inner join', 'gr_node_type B', 'C.type_id = B.id')
        ->select(['C.name as nodeName','B.name as typeName','A.type', 'A.vote_number', 'A.create_time'])->where(['A.user_id' => $userId])->orderBy('A.create_time desc')->asArray()->all();
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
        $unvote_log = BVote::find()->from(BVote::tableName()." A")
        ->join('inner join', 'gr_node C', 'A.node_id = C.id')
        ->join('inner join', 'gr_node_type B', 'C.type_id = B.id')
        ->select(['C.name as nodeName','B.name as typeName','A.undo_time', 'A.vote_number'])->where(['A.user_id' => $userId, 'A.status' => BNotice::STATUS_INACTIVE])->asArray()->all();
        $vote_unvote = [];
        if (count($unvote_log)>0) {
            foreach ($unvote_log as $v) {
                $vote_item =[];
                $vote_item['nodeName'] =  $v['nodeName'];
                $vote_item['typeName'] = $v['typeName'];
                $vote_item['voteNumber'] = $v['vote_number'];

                $vote_item['undoTime'] = date('Y-m-d H:i:s', $v['undo_time']);
                $vote_unvote[] = $vote_item;
            }
        }
        $vote['unvote'] = $vote_unvote;
        return $this->respondJson(0, '获取成功', $vote);
    }




    // 获取用户投票券信息 change
    public function actionGetUserVoucher()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $voucher = [];
        $voucher_data = BVoucher::find()
        ->from(BVoucher::tableName()." A")
        ->join('inner join', BNode::tableName().' B', 'A.node_id = B.id')
        ->join('inner join', BNodeType::tableName().' C', 'B.type_id = C.id')
        ->select(['A.voucher_num', 'A.create_time','B.name as nodeName','C.name as typeName'])
        ->where(['A.user_id' => $userId])->orderBy('A.create_time desc')->asArray()->all();
        $all  = 0;
        foreach ($voucher_data as $v) {
            $voucher_item = [];
            $voucher_item['nodeName'] = $v['nodeName'];
            $voucher_item['typeName'] = $v['typeName'];
            $voucher_item['voucherNum'] = $v['voucher_num'];
            $voucher_item['username'] = $user->mobile;
            $voucher_item['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
            $voucher[] = $voucher_item;
        }
        $voucher_detail_data = BVoucherDetail::find()
        ->from(BVoucherDetail::tableName()." A")
        ->join('inner join', 'gr_node B', 'A.node_id = B.id')
        ->join('inner join', 'gr_node_type C', 'B.type_id = C.id')
        ->select(['A.amount', 'A.create_time','B.name as nodeName','C.name as typeName'])
        ->where(['A.user_id' => $userId])->asArray()->all();
        $voucher_detail = [];
        foreach ($voucher_detail_data as $v) {
            $voucher_item = [];
            $voucher_item['nodeName'] = $v['nodeName'];
            $voucher_item['typeName'] = $v['typeName'];
            $voucher_item['username'] = $user->mobile;
            $voucher_item['amount'] = $v['amount'];
            $voucher_item['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
            $voucher_detail[] = $voucher_item;
        }
        $return = [];
        $position = BUserVoucher::find()->where(['user_id' => $userId])->one();
        if (empty($position)) {
            $return['length'] = 0;
        } else {
            $return['length'] = $position->surplus_amount;
        }
        
        $return['voucher_list'] = $voucher;
        $return['voucher_detail_list'] = $voucher_detail;

        return $this->respondJson(0, '获取成功', $return);
    }


    // 获取用户推荐信息
    public function actionGetUserRecommend()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }

        // 推荐
        $recommend = [];
        $recommend_data = BUserRecommend::find()
        ->from(BUserRecommend::tableName()." A")
        ->join('left join', 'gr_user D', 'A.user_id = D.id')
        ->join('left join', 'gr_node B', 'B.user_id = D.id')
        ->join('left join', 'gr_node_type C', 'B.type_id = C.id')
        
        ->select(['A.create_time','B.name as nodeName','C.name as typeName', 'D.username'])
        ->where(['A.parent_id' => $userId])->orderBy('A.create_time desc')->asArray()->all();
        foreach ($recommend_data as $v) {
            $recommend_item = [];
            $recommend_item['nodeName'] = $v['nodeName'];
            $recommend_item['username'] = $v['username'];
            $recommend_item['typeName'] = $v['typeName'];
            $recommend_item['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
            $recommend[] = $recommend_item;
        }
        return $this->respondJson(0, '获取成功', $recommend);
    }

    // 冻结用户
    public function actionStopUser()
    {
        $userId = $this->pString('userId');
        $user_id = explode(',', $userId);
        $users = BUser::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $res = BUser::updateAll(['status' => BNotice::STATUS_INACTIVE], ['in', 'id', $user_id]);

        if ($res === 0) {
            return $this->respondJson(1, '冻结失败');
        }

        return $this->respondJson(0, '冻结成功');
    }

    // 解冻用户
    public function actionOpenUser()
    {
        $userId = $this->pString('userId');
        $user_id = explode(',', $userId);
        $users = BUser::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $res = BUser::updateAll(['status' => BNotice::STATUS_ACTIVE], ['in', 'id', $user_id]);

        if ($res === 0) {
            return $this->respondJson(1, '解冻失败');
        }

        return $this->respondJson(0, '解冻成功');
    }

    //编辑用户信息
    public function actionEditUser()
    {
        $userId = $this->pInt('userId', 0);
        if ($userId != 0) {
            $user = BUser::find()->where(['id' => $userId])->one();
            if (empty($user)) {
                return $this->respondJson(1, '不存在的用户');
            }
            $str = '修改';
        } else {
            $user = new BUser();
            $str = '新增';
        }
        $name = $this->pString('name');
        if (empty($name)) {
            return $this->respondJson(1, '名称不能为空');
        }
        $code = $this->pString('code', '');
        $user->username = $name;
        $transaction = \Yii::$app->db->beginTransaction();
        if ($code != '') {
            $res = UserService::checkUserRecommend($userId, $code);
            if ($res->code != 0) {
                $transaction->rollBack();
                return $this->respondJson(1, $str.'失败', $res->msg);
            }
        }

        if ($user->save()) {
            if ($code != '') {
                $res = NodeService::checkVoucher($user->id);
                if ($res->code != 0) {
                    $transaction->rollBack();
                    return $this->respondJson(1, $str.'失败', $res->msg);
                }
            }
            $transaction->commit();
            return $this->respondJson(0, $str.'成功');
        } else {
            $transaction->rollBack();
            return $this->respondJson(1, $str.'失败', $user->getFirstErrorText());
        }
    }

    // 添加用户
    public function actionCreateUser()
    {
        $mobile = $this->pString('mobile');
        if (empty($mobile)) {
            return $this->respondJson(1, '手机不能为空');
        }
        
        if (!preg_match("/^1\d{10}$/", $mobile)) {
            return $this->respondJson(1, '手机格式不正确');
        }
        $old_data = BUser::find()->where(['mobile' => $mobile])->one();
        if ($old_data) {
            return $this->respondJson(1, '手机已注册');
        }
        $code = $this->pString('code');
        
        $user = new BUser();
        $user->mobile = $mobile;
        $recommend_code = UserService::generateRemmendCode(6);
        $user->recommend_code = $recommend_code;
        
        $user->username = $mobile;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$user->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $user->getFirstErrorText());
        }
        if ($code != '') {
            $id = UserService::validateRemmendCode($code);
            $user_recommend = new BUserRecommend();
            $user_recommend->user_id = $user->id;
            $user_recommend->parent_id = $id;
            if (!$user_recommend->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '注册失败', $user_recommend->getFirstErrorText());
            }
        }
        $user_voucher = new BUserVoucher();
        $user_voucher->user_id = $user->id;
        $user_voucher->position_amount = 0;
        $user_voucher->surplus_amount = 0;
        $user_voucher->use_amount = 0;
        if (!$user_voucher->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $user_voucher->getFirstErrorText());
        }
        $currency = BCurrency::find()->where(['status' => BCurrency::$CURRENCY_STATUS_ON, 'recharge_status' => BCurrency::$RECHARGE_STATUS_ON])->all();
        foreach ($currency as $v) {
            $returnInfo = RechargeService::getAddress($v['id'], $user->id);
            if ($returnInfo->code) {
                return $this->respondJson(1, $returnInfo->msg);
            }
        }
        
        $transaction->commit();
        return $this->respondJson(0, '注册成功');
    }

    public function actionGetRecommendList()
    {
        $user_id = $this->pInt('userId');
        if (!$user_id) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        $list = BUserRecommend::find()
        ->from(BUserRecommend::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->select(['B.mobile','B.id'])->where(['parent_id' => $user_id])->asArray()->all();
        foreach ($list as &$v) {
            $old_data = BVoucher::find()->where(['user_id' => $user_id, 'give_user_id' => $v['id']])->one();
            if ($old_data) {
                $v['is_give'] = 1;
            } else {
                $v['is_give'] = 0;
            }
        }
        return $this->respondJson(0, '获取成功', $list);
    }

    public function actionGetGiveInfo()
    {
        $type = $this->pInt('type');
        if (!$type) {
            return $this->respondJson(1, '派发类型不能为空');
        }
        $user_id = $this->pInt('userId');
        if (!$user_id) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        if ($type == BVoucher::$TYPE_RECOMMEND) {// 推荐类型
            $mobile = $this->pInt('mobile');
            if (!$type) {
                return $this->respondJson(1, '被推荐人手机不能为空');
            }
            $user = BUser::find()->where(['mobile' => $mobile])->one();
            if (!$user) {
                return $this->respondJson(1, '被推荐人不存在');
            }
            $node = BNode::find()->where(['user_id' => $user->id])->one();
            if (!$node) {
                return $this->respondJson(1, '被推荐人不是节点');
            }
            $node_type = BNodeType::find()->where(['id' => $node->type_id])->one();
            if ($node_type->name == '超级节点') {
                $voucher_num = 0;
            } elseif ($node_type->name == '高级节点') {
                $voucher_num = 200000;
            } elseif ($node_type->name == '中级节点') {
                $voucher_num = 80000;
            } else {
                $voucher_num = 20000;
            }
            //$gdt = $node->grt * 0.1;
            $gdt = $voucher_num * 0.01;
            $old_data = BVoucher::find()->where(['user_id' => $user_id, 'give_user_id' => $user->id])->one();
            if ($old_data) {
                $is_give = 1;
            } else {
                $is_give = 0;
            }
            $return = ['voucher_num' => $voucher_num, 'is_give' => $is_give, 'gdt' => $gdt, 'type_name' => $node_type->name];
            return $this->respondJson(0, '获取成功', $return);
        }
    }

    //赠送投票券
    public function actionGive()
    {
        $type = $this->pInt('type');
        if (!$type) {
            return $this->respondJson(1, '派发类型不能为空');
        }
        $user_id = $this->pInt('userId');
        if (!$user_id) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        if ($type == BVoucher::$TYPE_RECOMMEND) {// 推荐类型
            $mobile = $this->pInt('mobile');
            if (!$type) {
                return $this->respondJson(1, '被推荐人手机不能为空');
            }
            $user = BUser::find()->where(['mobile' => $mobile])->one();
            if (!$user) {
                return $this->respondJson(1, '被推荐人不存在');
            }
            $node = BNode::find()->where(['user_id' => $user->id])->one();
            if (!$node) {
                return $this->respondJson(1, '被推荐人不是节点');
            }
            $voucher_num = $this->pInt('voucherNum');
            if (!$voucher_num) {
                return $this->respondJson(1, '投票券数量不能为空');
            }
            $gdt = $this->pFloat('gdt');
            $remark = $this->pString('remark', '');
            $voucher = new BVoucher();
            $voucher->user_id = $user_id;
            $voucher->give_user_id = $user->id;
            $voucher->node_id = $node->id;
            $voucher->voucher_num = $voucher_num;
            $voucher->type = $type;
            $voucher->remark = $remark;
            $transaction = \Yii::$app->db->beginTransaction();
            $voucher_bool = $voucher->save();
            if (!$voucher_bool) {
                $transaction->rollBack();
                return $this->respondJson(1, '派发失败', $voucher->getFirstErrorText());
            }
            
            UserService::resetVoucher($user_id);
            if ($gdt != 0) {
                $res = [
                    'user_id' => $user_id,
                    'type' => BUserCurrencyDetail::$TYPE_REWARD,
                    'relate_table' => 'voucher',
                    'relate_id' => $voucher->id,
                    'amount' => $gdt,
                    'remark' => '推荐送GDT',
                ];
                
                $json = VoteService::giveCurrency($res);
                if ($json->code) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '派发失败', $json->msg);
                }
                
                $sign = UserService::resetCurrency($user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
            }
            $transaction->commit();
            return $this->respondJson(0, '派发成功');
        }
    }

    public function actionGetAddress()
    {
        $userId = $this->pInt('userId');
        if (empty($userId)) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        $other = BUserOther::find()->where(['user_id' => $userId])->one();
        if (empty($other)) {
            $return = [];
            $return['area_province_id'] =
            $return['area_city_id'] =
            $return['address'] =
            $return['zip_code'] =
            $return['consignee'] =
            $return['consignee_mobile'] = '';
            return $this->respondJson(0, '获取成功', $return);
        }
        $return = [];
        $return['area_province_id'] = BArea::getAreaOneName($other->area_province_id);
        $return['area_city_id'] = BArea::getAreaOneName($other->area_city_id);
        $return['address'] = BArea::getAreaOneName($other->area_province_id).' '.BArea::getAreaOneName($other->area_city_id).' '.$other->address;
        $return['zip_code'] = $other->zip_code;
        $return['consignee'] = $other->consignee;
        $return['consignee_mobile'] = $other->consignee_mobile;
        return $this->respondJson(0, '获取成功', $return);
    }

    // 推荐记录
    public function actionRecommendList()
    {
        $find = BUserRecommend::find()
        ->from(BUserRecommend::tableName()." A")
        ->select(['B.mobile as p_moblie', 'F.realname as p_realname', 'D.type_id as p_type_id', 'C.mobile as u_mobile', 'G.realname as u_realname', 'E.type_id as u_type_id', 'A.amount', 'A.create_time'])
        ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')
        ->join('left join', BUser::tableName().' C', 'A.user_id = C.id')
        ->join('left join', BNode::tableName().' D', 'A.parent_id = D.user_id')
        ->join('left join', BUserIdentify::tableName().' F', 'A.parent_id = F.user_id')
        ->join('left join', BUserIdentify::tableName().' G', 'A.user_id = G.user_id')
        ->join('left join', BNode::tableName(). ' E', 'A.user_id = E.user_id');
        $searchName = $this->pString('searchName');
        if ($searchName != '') {
            $find->andWhere(['like', 'B.mobile', $searchName]);
        }
        $type = $this->pInt('type');
        if ($type != 0) {
            if ($type == 5) {
                $find->andWhere(['>', 'D.id', 0]);
            } elseif ($type <= 4) {
                $find->andWhere(['D.type_id' => $type]);
            } elseif ($type == 6) {
                $find->andWhere(['D.id' => null]);
            }
        }
        $strTime = $this->pString('strTime');
        if ($strTime != '') {
            $find->startTime($strTime, 'A.create_time');
        }
        $endTime = $this->pString('endTime');
        if ($endTime != '') {
            $find->endTime($endTime, 'A.create_time');
        }
        $count = $find->count();
        $page = $this->pInt('page', 0);
        $find->page($page);
        $data = $find->orderBy('parent_id')->asArray()->all();
        foreach ($data as &$v) {
            $v['p_type_id'] = BNodeType::GetName($v['p_type_id']);
            $v['u_type_id'] = BNodeType::GetName($v['u_type_id']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, '获取成功', $return);
    }

    // 推荐记录下载
    public function actionRecommendDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        $find = BUserRecommend::find()
        ->from(BUserRecommend::tableName()." A")
        ->select(['B.mobile as p_moblie', 'F.realname as p_realname', 'D.type_id as p_type_id', 'C.mobile as u_mobile', 'G.realname as u_realname', 'E.type_id as u_type_id', 'A.amount', 'A.create_time'])
        ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')
        ->join('left join', BUser::tableName().' C', 'A.user_id = C.id')
        ->join('left join', BNode::tableName().' D', 'A.parent_id = D.user_id')
        ->join('left join', BUserIdentify::tableName().' F', 'A.parent_id = F.user_id')
        ->join('left join', BUserIdentify::tableName().' G', 'A.user_id = G.user_id')
        ->join('left join', BNode::tableName(). ' E', 'A.user_id = E.user_id');
        $searchName = $this->pString('searchName');
        if ($searchName != '') {
            $find->andWhere(['like', 'B.mobile', $searchName]);
        }
        $type = $this->pInt('type');
        if ($type != 0) {
            if ($type == 5) {
                $find->andWhere(['>', 'D.id', 0]);
            } elseif ($type <= 4) {
                $find->andWhere(['D.type_id' => $type]);
            } elseif ($type == 6) {
                $find->andWhere(['D.id' => null]);
            }
        }
        $strTime = $this->pString('strTime');
        if ($strTime != '') {
            $find->startTime($strTime, 'A.create_time');
        }
        $endTime = $this->pString('endTime');
        if ($endTime != '') {
            $find->endTime($endTime, 'A.create_time');
        }
        $data = $find->orderBy('parent_id')->asArray()->all();
        foreach ($data as &$v) {
            $v['p_type_id'] = BNodeType::GetName($v['p_type_id']);
            $v['u_type_id'] = BNodeType::GetName($v['u_type_id']);
        }
        $return = [];
        $return['list'] = $data;
        $headers = ['p_mobile'=> '用户', 'p_realname' => '姓名', 'p_type_id' => '类型', 'u_mobile' => '被推荐用户', 'u_realname' => '姓名', 'u_type_id' => '类型', 'amount' => '赠送投票券', 'create_time' => '推荐时间'];
        $this->download($return['list'], $headers, '推荐列表'.date('YmdHis'));

        return;
    }
}
