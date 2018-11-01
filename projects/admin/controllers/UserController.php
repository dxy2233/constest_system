<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use common\services\RechargeService;
use common\services\UserService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BUserIdentify;
use common\models\business\BUserWallet;
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
            'download'
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
        ->join('left join', BVote::tableName().' B', 'B.user_id = A.id && B.status = '.BNotice::STATUS_ACTIVE);
        $pageSize = $this->pInt('pageSize');
        $page = $this->pInt('page', 1);
        
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','A.username',$searchName]);
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
            $order_arr = [1 => 'sum(B.vote_number)', 2 => 'A.create_time', 3 => 'A.last_login_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $find->orderBy($order . ' DESC');
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
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id']])->asArray()->one();
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
        //header('Access-Control-Allow-Origin:*');
        // $file = './a';
        // $data = file_get_contents($file);
        // return $data;
        // exit;
        $find = BUser::find()
        ->from(BUser::tableName()." A")
        ->select(['A.mobile', 'A.status', 'A.create_time', 'A.last_login_time', 'A.id','sum(B.vote_number) as num'])
        ->groupBy(['A.id'])
        ->join('left join', BVote::tableName().' B', 'B.user_id = A.id && B.status = '.BNotice::STATUS_ACTIVE);
        
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','A.username',$searchName]);
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
            $order_arr = [1 => 'sum(B.vote_number)', 2 => 'A.create_time', 3 => 'A.last_login_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $find->orderBy($order . ' DESC');

        //echo $find->createCommand()->getRawSql();
        $list = $find->asArray()->all();
        //var_dump($list);
        foreach ($list as &$v) {
            $node = BNode::find()
            ->from(BNode::tableName()." A")
            ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id']])->asArray()->one();
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

        $down = $this->download($list, $headers, '用户列表'.date('YmdHis'));
        if (!$down) {
            exit('验证失败');
        }
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


    // 获取用户实名信息
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
                $in_and_out_data = BUserCurrencyDetail::find()->where(['user_id' => $userId, 'currency_id' => $v['currency_id'], 'status' => BNotice::STATUS_ACTIVE])->all();
                foreach ($in_and_out_data as $val) {
                    $in_and_out = [];
                    $in_and_out['type'] = UserCurrencyTrait::getType($val['type']);
                    $in_and_out['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                    $in_and_out['amount'] = ($val['amount'] > 0) ? '+'.$val['amount'] : $val['amount'];
                    $v['in_and_out'][] = $in_and_out;
                }
                $frozen_data = BUserCurrencyFrozen::find()->where(['user_id' => $userId, 'currency_id' => $v['currency_id'], 'status' => BNotice::STATUS_ACTIVE])->all();
                foreach ($in_and_out_data as $val) {
                    $frozen = [];
                    $frozen['type'] = UserCurrencyTrait::getType($val['type']);
                    $frozen['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                    $frozen['amount'] = ($val['amount'] > 0) ? '-'.$val['amount'] : '+'.abs($val['amount']);
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
        ->select(['C.name as nodeName','B.name as typeName','A.type', 'A.vote_number', 'A.create_time'])->where(['A.user_id' => $userId])->asArray()->all();
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

    // 获取用户钱包信息



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
        ->where(['A.user_id' => $userId])->asArray()->all();
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
        ->join('inner join', 'gr_user D', 'A.user_id = D.id')
        ->join('inner join', 'gr_node B', 'B.user_id = D.id')
        ->join('inner join', 'gr_node_type C', 'B.type_id = C.id')
        
        ->select(['A.create_time','B.name as nodeName','C.name as typeName', 'D.username'])
        ->where(['A.parent_id' => $userId])->asArray()->all();
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
        $code = $this->pString('code');
        $user->username = $name;
        $recommend = BUserRecommend::find()->where(['user_id' => $userId])->one();
        if (empty($recommend)) {
            $id = UserService::validateRemmendCode($code);
            if ($id === $userId) {
                return $this->respondJson(1, '推荐人不能是自己');
            }
            $user_recommend = new BUserRecommend();
            $user_recommend->user_id = $user->id;
            $user_recommend->parent_id = $id;
            if (!$user_recommend->save()) {
                return $this->respondJson(1, '修改失败', $user_recommend->getFirstErrorText());
            }
        } else {
            return $this->respondJson(1, '用户已有推荐人');
        }
        if (empty($recommend)) {
            $info['referee'] = '-';
        } else {
            $info['referee'] = $recommend['mobile'];
        }
        if ($user->save()) {
            return $this->respondJson(0, $str.'成功');
        } else {
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
        
        if (!preg_match("/^1[345678]{1}\d{9}$/", $mobile)) {
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
}
