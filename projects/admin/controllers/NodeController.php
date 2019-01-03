<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\UserService;
use common\services\VoteService;
use common\services\NodeService;
use common\services\SmsService;
use common\services\RechargeService;
use common\services\VoucherService;
use common\services\IetSystemService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BArea;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BNodeType;
use common\models\business\BUserOther;
use common\models\business\BNodeRule;
use common\models\business\BNodeUpgrade;
use common\models\business\BNodeTransfer;
use common\models\business\BUserIdentify;
use common\models\business\BTypeRuleContrast;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BSmsTemplate;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BNodeRecommend;
use common\models\business\BSetting;
use common\models\business\BCurrency;
use common\models\business\BHistory;
use common\models\business\BCycle;
use common\models\business\BUserRechargeWithdraw;
use common\components\FuncHelper;

/**
 * Site controller
 */
class NodeController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'download',
            'examine-download',
            'history-download',
            'recommend-download'
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    // 节点管理
    public function actionIndex()
    {
        // 节点类型
        $type = $this->pInt('type', 0);
        $searchName = $this->pString('searchName', '');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $page = $this->pInt('page', 1);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'sum(C.vote_number) DESC,A.create_time ASC';
        }
        $data = NodeService::getIndexList($page, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];
        foreach ($data['list'] as $k => &$v) {
            $v['order'] = ($page-1)*20 + $k +1;
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data['list'] as &$v) {
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = 0;
            }
            
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BNode::getStatus($v['status']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    public function actionDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        // 节点类型
        $type = $this->gInt('type', 0);
        $searchName = $this->gString('searchName', '');
        $str_time = $this->gString('str_time', '');
        $end_time = $this->gString('end_time', '');
        $order = $this->gString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'sum(C.vote_number) DESC,A.create_time ASC';
        }
        $data = NodeService::getIndexList(0, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];

        foreach ($data['list'] as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        $id = $this->gString('id');
        if ($id != '') {
            $id_new_arr = explode(',', $id);
        } else {
            $id_new_arr = [];
        }
            
        foreach ($data['list'] as $key => &$v) {
            if (count($id_new_arr) > 0 && !in_array($v['id'], $id_new_arr)) {
                unset($data['list'][$key]);
            }
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = '0';
            }
            $v['key'] = $key+1;
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['status'] = BNode::getStatus($v['status']);
            $v['is_tenure'] = $v['is_tenure'] == 1 ? '任职' : '候补';

            $other = BUserOther::find()->where(['user_id' => $v['user_id']])->asArray()->one();
            if ($other) {
                $v['weixin'] = $other['weixin'];
                $v['grt_address'] = $other['grt_address'];
                $v['tt_address'] = $other['tt_address'];
                $v['bpt_address'] = $other['bpt_address'];
                $v['consignee'] = $other['consignee'];
                $v['consignee_mobile'] = $other['consignee_mobile'];
                $v['zip_code'] = $other['zip_code'];
                $v['address'] =  BArea::getAreaOneName($other['area_province_id']). BArea::getAreaOneName($other['area_city_id']). $other['address'];
            } else {
                $v['weixin'] = $v['grt_address'] = $v['tt_address'] = $v['bpt_address'] = $v['consignee'] = $v['consignee_mobile'] = $v['zip_code'] = $v['address'] = '';
            }
            $identify = BUserIdentify::find()->where(['user_id' => $v['user_id']])->active()->one();
            if ($identify) {
                $v['realname'] = $identify->realname;
                $v['number'] = $identify->number;
            } else {
                $v['number'] = $v['realname'] = '';
            }
        }

        $headers = ['key'=> '排名', 'name' => '节点名称', 'mobile' => '用户', 'recommend_mobile' => '推荐人手机号', 'vote_number' => '票数', 'count' => '支持人数', 'grt' => '质押GRT', 'bpt' => '质押BPT', 'tt' => '质押TT', 'is_tenure' => '身份', 'create_time' => '加入时间', 'status' => '状态', 'type_name' => '节点类型', 'weixin' => '微信', 'grt_address' => 'grt地址', 'tt_address' => 'tt地址', 'bpt_address' => 'bpt地址', 'consignee' => '收件人姓名', 'consignee_mobile' => '收件人电话', 'zip_code' => '邮编', 'address' => '收货地址', 'realname' => '姓名', 'number' => '身份证号'];
        $this->download($data['list'], $headers, '节点列表'.date('YmdHis'));

        return;
    }
    // 升级审核列表
    public function actionExamineUpgrade()
    {
        $status = $this->pInt('status', 2);
        if (empty($status)) {
            return $this->respondJson(1, '审核状态不能为空');
        }
        $status_arr = [1 => 1, 2 => 0, 4 => 2];
        $status = $status_arr[$status];
        $searchName = $this->pString('searchName', '');
        $page = $this->pInt('page', 1);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time DESC';
        }
        $find = BNodeUpgrade::find()
        ->from(BNodeUpgrade::tableName()." A")
        ->select(['A.id','C.mobile','D.name as old_name','E.name as new_name', 'A.status', 'A.create_time', 'A.examine_time', 'A.grt'])
        ->join('left join', BUser::tableName().' C', 'A.user_id = C.id')
        ->join('left join', BNode::tableName().' B', 'A.user_id = B.user_id')

        ->join('left join', BNodeType::tableName().' D', 'A.old_type = D.id')
        ->join('left join', BNodeType::tableName().' E', 'A.type_id = E.id');
        $searchName = $this->gString('searchName');
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'B.name', $searchName], ['like', 'C.mobile', $searchName]]);
        }
        $find->andWhere(['A.status' => $status]);
        $find->andWhere(['!=', 'A.old_type', 0]);
        $count = $find->count();
        $data =  $find->page($page)->orderBy($order)->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BNodeUpgrade::getStatus($v['status']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, '获取成功', $return);
    }
    // 升级审核详情
    public function actionUpgradeDetail()
    {
        $id = $this->pInt('id', 0);
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeUpgrade::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $identify = BUserIdentify::find()->where(['user_id' => $data->user_id])->active()->one();
        $return = [];
        $return['old_name'] = BNodeType::GetName($data->old_type);
        $return['new_name'] = BNodeType::GetName($data->type_id);
        $return['mobile'] = $user->mobile;
        $return['real_name'] = $identify->realname;
        $return['payable'] = NodeService::getGrtNumber($data->old_type, $data->type_id);
        $return['grt'] = $data->grt;
        $return['grt_address'] = $data->grt_address;
        $return['status_remark'] = $data->status_remark;
        return $this->respondJson(0, '获取成功', $return);
    }
    // 升级审核通过
    public function actionUpgradeExamineOn()
    {
        $id = $this->pInt('id', 0);
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeUpgrade::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        if ($data->status == BNodeUpgrade::STATUS_ACTIVE) {
            return $this->respondJson(1, '已处于通过状态');
        }
        $now_count = BNode::find()->where(['type_id' => $data->type_id, 'status' => BNode::STATUS_ON])->count();
        $node_type = BNodeType::find()->where(['id' => $data->type_id])->one();
        if ($now_count >= $node_type->max_candidate) {
            return $this->respondJson(1, '候选数量已达上限');
        }
        $transaction = \Yii::$app->db->beginTransaction();
    

        
        // 成为节点补送gdt
        $currencyDetail = new BUserCurrencyDetail();
        $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
        $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $currencyDetail->effect_time = NOW_TIME;
        $currencyDetail->remark = '升级节点奖励';
        $currencyDetail->user_id = $data->user_id;
        $currencyDetail->relate_table = 'node_upgrade';
        $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
        $currencyDetail->relate_id = $data->id;
        $amount = NodeService::getGiveGdtNumber($data->old_type, $data->type_id);
        $currencyDetail->amount = $amount;
        if (!$currencyDetail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $currencyDetail->getFirstErrorText());
        }

        //重算gdt
        UserService::resetCurrency($data->user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        // 补全充值冻结信息
        $log = NodeService::addNodeMakeLogs($data);
        if ($log->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败'.$log->content);
        }
        
        // 修改节点信息
        $node = BNode::find()->where(['user_id' => $data->user_id])->one();
        $node->type_id = $data->type_id;
        $node->grt = (float)$node->grt + (float)$data->grt;
        $node->tt = (float)$node->tt + (float)$data->tt;
        $node->bpt = (float)$node->bpt + (float)$data->bpt;
        // 升级时若原销售配额不为空则累加需补充部分
        $node->quota = ($node->quota == null) ? null : $node->quota + NodeService::getUpgradeQuota($data->old_type, $data->type_id);

        if ($data->old_type == 5) {
            // 微店第一次升级时，多增加微店设置值的销售配额
            $now_quota = BNodeType::find()->where(['id' => $data->type_id])->one();
            $wd_quota = BNodeType::find()->where(['id' => 5])->one();
            $node->quota = ($node->quota == null) ? $now_quota->quota + $wd_quota->quota : $node->quota += $wd_quota->quota;
        }
        $node->examine_time = NOW_TIME;
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $node->getFirstErrorText());
        }
        // 修改审核状态
        $data->status = BNodeUpgrade::STATUS_ACTIVE;
        $data->examine_time = NOW_TIME;
        $data->status_remark = '已开启';
        $data->node_id = $node->id;
        if (!$data->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        $recommend = BNodeRecommend::find()->where(['user_id' => $data->user_id])->one();
        $user = BUser::find()->where(['id' => $data->user_id])->one();

        if (!$recommend && $data->parent_id) {
            $parent = BNodeRecommend::find()->where(['user_id' => $data->parent_id])->one();
            if (!$parent) {
                $str = $data->parent_id;
            } else {
                $str = $parent->parent_list.','.$data->parent_id;
            }
            $recommend = new BNodeRecommend();
            $recommend->user_id = $data->user_id;
            $recommend->parent_id = $data->parent_id;
            $recommend->node_id = $node->id;
            $recommend->parent_list = $str;
            if (!$recommend->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '审核失败', $recommend->getFirstErrorText());
            }
            // // 向IET同步数据
            // $parent_user = BUser::find()->where(['id' => $data->parent_id])->one();
            // $inviteCode = $parent_user->mobile;
            // $parent_identify = BUserIdentify::find()->where(['user_id' => $data->parent_id])->active()->one();
            // $inviteName = $parent_identify->realname;
            // $identify = BUserIdentify::find()->where(['user_id' => $user->id])->active()->one();
            // $url = IetSystemService::IET_URL['cusIdentity_sync'];
            // $old_up = BNodeUpgrade::find()->where(['user_id' => $data->user_id, 'old_type' => 5, 'status' => BNodeUpgrade::STATUS_ACTIVE])->one();
            // $up_status = $old_up ? "1" : "0";
            // $data_arr = ['phone' => $user->mobile, 'username' => $identify->realname, 'cardNo' => $identify->number, 'identity' => $data->type_id, 'inviteName' => $inviteName, 'inviteCode' => $inviteCode, 'selfInvite' => $user->mobile, 'upgradeFlag' => $up_status];
            // $res_curl = IetSystemService::push($url, $data_arr);
            // if ($res_curl->code) {
            //     $transaction->rollBack();
            //     return $this->respondJson(1, 'IET数据同步失败', $res_curl->msg. $res_curl->content);
            // }
        } elseif ($data->type_id == 1 && $recommend) {
            // 如果升级为超级节点清除推荐关系
            $sql = "UPDATE `gr_contest`.`gr_node_recommend` SET `parent_list` = replace(`parent_list`,'".$recommend->parent_list."','') where `parent_list` like '".$recommend->parent_list.",".$data->user_id."%'";
            $connection=\Yii::$app->db;
            $command=$connection->createCommand($sql);
            $rowCount=$command->execute();
            $recommend->delete();
        }

        
        //推荐赠送
        $res = NodeService::checkVoucher($data->user_id);

        if ($res->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $res->msg);
        }
        
        // // 向IET同步数据
        // if ($data->old_type == 5) {
        //     //微店节点升级
        //     $url = IetSystemService::IET_URL['wd_upgrade'];
        //     $data_arr = ['phone' => $user->mobile, 'identity' => $data->type_id];
        // } else {
        //     $url = IetSystemService::IET_URL['node_upgrade'];
        //     $data_arr = ['phone' => $user->mobile, 'identity' => $data->type_id];
        // }

        // $res_curl = IetSystemService::push($url, $data_arr);

        // if ($res_curl->code) {
        //     $transaction->rollBack();
        //     return $this->respondJson(1, 'IET数据同步失败', $res_curl->msg. $res_curl->content);
        // }

        // 发送短信通知用户
        
        $typeName = str_replace('节点', '', $node_type->name);
        $returnInfo = SmsService::send($user->mobile, ['name' => $typeName], BSmsTemplate::$TYPE_NODE_UP_EXAMINE);
        if ($returnInfo->code != 0) {
            $transaction->rollBack();
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }
        $transaction->commit();
        return $this->respondJson(0, '审核成功');
    }

    // 升级审核不通过
    public function actionUpgradeExamineOff()
    {
        $nodeId = $this->pInt('id');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data = BNodeUpgrade::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的申请');
        }
        $data->examine_time = NOW_TIME;
        $data->status = BNodeUpgrade::STATUS_FAIL;
        $data->status_remark = $remark;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }

    // 审核列表
    public function actionExamine()
    {
        $status = $this->pInt('status', 2);
        if (empty($status)) {
            return $this->respondJson(1, '审核状态不能为空');
        }
        $status_arr = [1 => 1, 2 => 0, 4 => 2];
        $status = $status_arr[$status];
        $searchName = $this->pString('searchName', '');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $page = $this->pInt('page', 1);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time DESC';
        }
        $find = BNodeUpgrade::find()
        ->from(BNodeUpgrade::tableName()." A")
        ->select(['A.id', 'A.name', 'B.mobile', 'A.bpt', 'A.tt', 'A.grt', 'C.name as type_name', 'A.status', 'A.create_time', 'A.examine_time'])
        ->join('left join', BUser::tableName().' B', 'B.id = A.user_id')
        ->join('left join', BNodeType::tableName().' C', 'C.id = A.type_id');
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'A.name', $searchName], ['like', 'B.mobile', $searchName]]);
        }
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        
        $find->andWhere(['A.status' => $status, 'old_type' => 0]);
        // echo $find->createCommand()->getRawSql();
        //$data = NodeService::getIndexList($page, $searchName, $str_time, $end_time, 0, $status, $order);
        $return = [];
        $return['count'] = $find->count();
        $find->page($page)->orderBy($order);
        $data = $find->asArray()->all();
        foreach ($data as $v) {
            $item = [];
            $item['id'] = $v['id'];
            $item['mobile'] = $v['mobile'];
            $item['name'] = $v['name'];
            $item['bpt'] = $v['bpt'];
            $item['tt'] = $v['tt'];
            $item['grt'] = $v['grt'];
            $item['type_name'] = $v['type_name'];
            $item['status'] = BNodeUpgrade::getStatus($v['status']);
            $item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $item['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $return['list'][] = $item;
        }

        return $this->respondJson(0, '获取成功', $return);
    }


    // 审核导出
    public function actionExamineDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        $status = $this->pInt('status', 2);
        if (empty($status)) {
            return $this->respondJson(1, '审核状态不能为空');
        }
        $searchName = $this->pString('searchName', '');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time DESC';
        }
        $find = BNodeUpgrade::find()
        ->from(BNodeUpgrade::tableName()." A")
        ->select(['A.id', 'A.name', 'B.mobile', 'A.bpt', 'A.tt', 'A.grt', 'C.name as type_name', 'A.status', 'A.create_time', 'A.examine_time'])
        ->join('left join', BUser::tableName().' B', 'B.id = A.user_id')
        ->join('left join', BNodeType::tableName().' C', 'C.id = A.type_id');
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'A.name', $searchName], ['like', 'B.mobile', $searchName]]);
        }
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $find->andWhere(['A.status' => $status, 'old_type' => 0]);
        
        //$data = NodeService::getIndexList($page, $searchName, $str_time, $end_time, 0, $status, $order);
        $return = [];
        $find->orderBy($order);
        $data = $find->asArray()->all();
        foreach ($data as $v) {
            $item = [];
            $item['id'] = $v['id'];
            $item['mobile'] = $v['mobile'];
            $item['name'] = $v['name'];
            $item['bpt'] = $v['bpt'];
            $item['tt'] = $v['tt'];
            $item['grt'] = $v['grt'];
            $item['type_name'] = $v['type_name'];
            $item['status'] = BNode::getStatus($v['status']);
            $item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $item['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $return[] = $item;
        }
        $headers = ['name'=> '节点名称', 'type_name' => '节点类型', 'mobile' => '手机号','username' => '姓名','weixin' => '微信','grt_address' => 'grt地址', 'tt_address' => 'tt地址', 'bpt_address' => 'bpt地址', 'grt' => '质押GRT', 'bpt' => '质押BPT', 'tt' => '质押TT', 'status' => '状态', 'create_time' => '提交时间', 'examine_time' => '审核时间'];
        $this->download($return, $headers, '节点审核列表'.date('YmdHis'));
 
        return;
    }

    // 审核通过
    public function actionExamineOn()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeUpgrade::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的申请');
        }
        if ($data->status == BNode::STATUS_ON) {
            return $this->respondJson(1, '已处于通过状态');
        }
        $now_count = BNode::find()->where(['type_id' => $data->type_id, 'status' => BNode::STATUS_ON])->count();
        $node_type = BNodeType::find()->where(['id' => $data->type_id])->one();
        if ($now_count >= $node_type->max_candidate) {
            return $this->respondJson(1, '候选数量已达上限');
        }
        $transaction = \Yii::$app->db->beginTransaction();
    

        
        // 成为节点赠送gdt
        $currencyDetail = new BUserCurrencyDetail();
        $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
        $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $currencyDetail->effect_time = NOW_TIME;
        $currencyDetail->remark = '申请节点奖励';
        $currencyDetail->user_id = $data->user_id;
        $currencyDetail->relate_table = 'node';
        $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
        $currencyDetail->relate_id = $data->id;
        $currencyDetail->amount = $node_type->gdt_reward;
        if (!$currencyDetail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $currencyDetail->getFirstErrorText());
        }

        //重算gdt
        UserService::resetCurrency($data->user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        // 补全充值冻结信息
        $log = NodeService::addNodeMakeLogs($data);
        if ($log->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败'.$log->content);
        }
        
        // 添加节点信息
        $node = new BNode();
        $node->status = BNode::STATUS_ON;
        $node->user_id = $data->user_id;
        $node->type_id = $data->type_id;
        $node->name = $data->name;
        $node->grt = $data->grt;
        $node->tt = $data->tt;
        $node->bpt = $data->bpt;
        $node->desc = $data->desc;
        $node->scheme = $data->scheme;
        $node->logo = $data->logo;
        $node->status_remark = '已开启';
        $node->examine_time = NOW_TIME;
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $node->getFirstErrorText());
        }
        // 修改审核状态
        $data->status = BNodeUpgrade::STATUS_ACTIVE;
        $data->examine_time = NOW_TIME;
        $data->status_remark = '已开启';
        $data->node_id = $node->id;
        if (!$data->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        if ($data->parent_id) {
            $parent = BNodeRecommend::find()->where(['user_id' => $data->parent_id])->one();
            if (!$parent) {
                $str = $data->parent_id;
            } else {
                $str = $parent->parent_list.','.$data->parent_id;
            }
            $recommend = new BNodeRecommend();
            $recommend->user_id = $data->user_id;
            $recommend->parent_id = $data->parent_id;
            $recommend->node_id = $node->id;
            $recommend->parent_list = $str;
            if (!$recommend->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '审核失败', $recommend->getFirstErrorText());
            }
            $parent_identify = BUserIdentify::find()->where(['user_id' => $data->parent_id])->one();
            $inviteName = $parent_identify->realname;
            $parent_user = BUser::find()->where(['id' => $data->parent_id])->one();
            $inviteCode = $parent_user->mobile;
        } else {
            $parent_identify = BUserIdentify::find()->where(['user_id' => 97])->one();
            $inviteName = $parent_identify->realname;
            $parent_user = BUser::find()->where(['id' => 97])->one();
            $inviteCode = $parent_user->mobile;
        }
        
        //推荐赠送
        $res = NodeService::checkVoucher($data->user_id);

        if ($res->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $res->msg);
        }

        // // 向IET同步数据
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        // $identify = BUserIdentify::find()->where(['user_id' => $user->id])->active()->one();
        // $url = IetSystemService::IET_URL['cusIdentity_sync'];
        // $data_arr = ['phone' => $user->mobile, 'username' => $identify->realname, 'cardNo' => $identify->number, 'identity' => $data->type_id, 'inviteName' => $inviteName, 'inviteCode' => $inviteCode, 'selfInvite' => $user->mobile, 'upgradeFlag' => "0"];
        // $res_curl = IetSystemService::push($url, $data_arr);
        // if ($res_curl->code) {
        //     $transaction->rollBack();
        //     return $this->respondJson(1, 'IET数据同步失败', $res_curl->msg. $res_curl->content);
        // }

        // 发送短信通知用户
        $typeName = str_replace('节点', '', $node_type->name);
        $returnInfo = SmsService::send($user->mobile, ['name' => $typeName], BSmsTemplate::$TYPE_NODE_EXAMINE);
        if ($returnInfo->code != 0) {
            $transaction->rollBack();
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }
        $transaction->commit();
        return $this->respondJson(0, '审核成功');
    }

    // 审核不通过
    public function actionExamineOff()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data = BNodeUpgrade::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的申请');
        }

        $data->status = BNodeUpgrade::STATUS_FAIL;
        $data->status_remark = $remark;
        $data->examine_time = time();
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
    // 节点明细
    public function actionGetNodeData()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $return = [];
        $recommend = BNodeRecommend::find()->where(['user_id' => $data->user_id])->one();
        if ($recommend) {
            $recommend_user = BUser::find()->where(['id' => $recommend->parent_id])->one();
            $return['recommend_mobile'] = $recommend_user->mobile;
        } else {
            $return['recommend_mobile'] = '';
        }
        $return['name'] = $data->name;
        $return['desc'] = $data->desc;
        $return['status_remark'] = $data->status_remark;
        
        if ($data->quota === null) {
            $return['quota'] = '';
        } else {
            $return['quota'] = $data->quota;
        }
        $return['scheme'] = $data->scheme;
        $return['logo'] = FuncHelper::getImageUrl($data->logo, 640, 640);
        return $this->respondJson(0, '获取成功', $return);
    }
    // 节点申请基本信息
    public function actionGetNodeExamineDetail()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNodeUpgrade::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的申请');
        }
        $node_type = BNodeType::find()->where(['id' => $data->type_id])->one();
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $identify = BUserIdentify::find()->active()->where(['user_id' => $data->user_id])->one();
        $return = [];

        $return['weixin'] = $data->weixin;
        // $return['recommend_name'] = $other->recommend_name;
        // $return['recommend_mobile'] = $other->recommend_mobile;
        $return['grt_address'] = $data->grt_address;
        $return['tt_address'] = $data->tt_address;
        $return['bpt_address'] = $data->bpt_address;

        
        if ($identify) {
            $return['username'] = $identify->realname;
        } else {
            $return['username'] = '';
        }
        $return['mobile'] = $user->mobile;
        $return['type_name'] = $node_type->name;
        $return['status_remark'] = $data->status_remark;

        $msg = '获取成功';

        $return['grt'] = $data->grt;
        $return['tt'] = $data->tt;
        $return['bpt'] = $data->bpt;
        return $this->respondJson(0, $msg, $return);
    }
    // 节点基本信息
    public function actionGetNodeUserData()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $node_type = BNodeType::find()->where(['id' => $data['type_id']])->one();
        if ($data->quota === null) {
            $return['quota'] = $node_type->quota;
        } else {
            $return['quota'] = $data->quota;
        }
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $res = NodeService::getNodeQuota($user->mobile);
        $msg = '获取成功';
        if ($res  && $res->code == 0) {
            $return['use_quota'] = $return['quota'] - (float)$res->content;
        } elseif ($res) {
            $return['use_quota'] = $return['quota'];
            $msg = $res->msg;
        } else {
            $return['use_quota'] = $return['quota'];
        }
        $list_transfer = BNodeTransfer::find()->where(['node_id' => $nodeId])->active()->all();
        $list_upgrade = BNodeUpgrade::find()->where(['node_id' => $nodeId])->active()->all();
        $list = [];
        
        foreach ($list_transfer as $v) {
            $item = [];
            $item['type'] = '节点转让';
            $node_type = BNodeType::find()->where(['id' => $v['node_type']])->one();
            $user = BUser::find()
            ->from(BUser::tableName().' A')
            ->select(['C.realname', 'A.mobile'])
            ->join('left join', BUserIdentify::tableName().' C', 'C.user_id = A.id && C.status = '.BUserIdentify::STATUS_ACTIVE)
            ->where(['A.id' => $v['to_user_id']])
            ->asArray()->one();
            $item['node_type'] = $node_type->name;
            $item['realname'] = $user['realname'];
            $item['mobile'] = $user['mobile'];
            $item['create_time'] = $v['create_time'];
            $item['grt'] = $v['grt'];
            $item['tt'] = $v['tt'];
            $item['bpt'] = $v['bpt'];
            $list[] = $item;
        }
        foreach ($list_upgrade as $v) {
            $item = [];
            $item['type'] = $v['old_type'] ? '节点升级' : '创建节点';
            $node_type = BNodeType::find()->where(['id' => $v['type_id']])->one();
            $user = BUser::find()
            ->from(BUser::tableName().' A')
            ->select(['C.realname', 'A.mobile'])
            ->join('left join', BUserIdentify::tableName().' C', 'C.user_id = A.id && C.status = '.BUserIdentify::STATUS_ACTIVE)
            ->where(['A.id' => $v['user_id']])
            ->asArray()->one();
            $item['node_type'] = $node_type->name;
            $item['realname'] = $user['realname'];
            $item['mobile'] = $user['mobile'];
            $item['create_time'] = $v['create_time'];
            $item['grt'] = $v['grt'] + $v['old_grt'];
            $item['tt'] = $v['tt'] + $v['old_tt'];
            $item['bpt'] = $v['bpt'] + $v['old_bpt'];
            $list[] = $item;
        }
        $list = FuncHelper::arr_sort($list, 'create_time');
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        $return['list'] = $list;
        return $this->respondJson(0, $msg, $return);
    }

    
    // 获取节点推荐信息
    public function actionGetNodeRecommend()
    {
        $id = $this->pInt('id');
        $node = BNode::find()->where(['id' => $id])->one();
        if (!$node) {
            return $this->respondJson(1, '不存在的节点');
        }
        $user = BUser::find()->where(['id' => $node->user_id])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }

        // 推荐
        $recommend = [];
        $recommend_data = BNodeRecommend::find()
        ->from(BNodeRecommend::tableName()." A")
        ->join('left join', 'gr_user D', 'A.user_id = D.id')
        ->join('left join', 'gr_node B', 'B.user_id = D.id')
        ->join('left join', 'gr_node_type C', 'B.type_id = C.id')
        ->select(['A.create_time','B.name as nodeName','C.name as typeName', 'D.username'])
        ->where(['A.parent_id' => $node->user_id])->orderBy('A.create_time desc')->asArray()->all();
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

    // 获取地址信息
    public function actionGetAddress()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, '节点ID不能为空');
        }
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $other = BUserOther::find()->where(['user_id' => $data->user_id])->one();
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
        $return['address'] = $other->address;
        $return['zip_code'] = $other->zip_code;
        $return['consignee'] = $other->consignee;
        $return['consignee_mobile'] = $other->consignee_mobile;
        return $this->respondJson(0, '获取成功', $return);
    }
    
    // 获取节点实名信息
    public function actionGetNodeIdentify()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }

        $identify = [];
        $userIdentify = BUserIdentify::find()->where(['user_id'=> $data->user_id])->orderBy('id DESC')->active(BNotice::STATUS_ACTIVE)->one();
        if (!empty($userIdentify)) {
            $identify['realName'] = $userIdentify->realname;
            $identify['number'] = $userIdentify->number;
            $identify['picFront'] = FuncHelper::getImageUrl($userIdentify->pic_front, 640, 640);
            $identify['picBack'] = FuncHelper::getImageUrl($userIdentify->pic_back, 640, 640);
        }
        return $this->respondJson(0, '获取成功', $identify);
    }

    // 分页功能添加  使用type区分两个tab
    // 获取投票明细
    public function actionGetVoteList()
    {
        $nodeId = $this->pInt('nodeId');
        $type = $this->pInt('type', 1);
        $page = $this->pInt('page', 1);
        if ($type == 1) {
            $voteList = [];
            $find = BVote::find()
            ->from(BVote::tableName()." A")
            ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
            ->select(['A.*','B.mobile'])
            ->where(['A.node_id' => $nodeId, 'A.status' => BNotice::STATUS_ACTIVE])
            ->orderBy('A.create_time DESC');
            $count = $find->count();
            $data = $find
            ->page($page)
            ->asArray()->all();
            foreach ($data as $v) {
                $voteItem = [];
                $voteItem['mobile'] = $v['mobile'];
                $voteItem['voteNumber'] = $v['vote_number'];
                $voteItem['type'] = BVote::getType($v['type']);
                $voteItem['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
                $voteList[] = $voteItem;
            }
            $return = ['list' => $voteList, 'count' => $count];
            return $this->respondJson(0, '获取成功', $return);
        } else {
            $orderList = [];
            $find = BVote::find()
            ->from(BVote::tableName()." A")
            ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
            ->where(['A.node_id' => $nodeId, 'A.status' => BNotice::STATUS_ACTIVE])
            ->select(['sum(A.vote_number) as voteNumber','B.mobile'])
            ->groupBy(['A.user_id'])
            ->orderBy('sum(A.vote_number) desc');
            $count = $find->count();
            $data = $find
            ->page($page)
            ->asArray()->all();

            foreach ($data as $v) {
                $voteItem = [];
                $voteItem['mobile'] = $v['mobile'];
                $voteItem['voteNumber'] = $v['voteNumber'];
                $orderList[] = $voteItem;
            }
            $return = ['list' => $orderList, 'count' => $count];
            return $this->respondJson(0, '获取成功', $return);
        }
    }

    public function actionGetRule()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $order = NodeService::getNodeRanking($data->type_id, $nodeId);
        $rule_list = NodeService::getNodeRule($nodeId, $order);

        return $this->respondJson(0, '获取成功', $rule_list);
    }

    // 获取节点类型列表
    public function actionGetTypeList()
    {
        $data = BNodeType::find()->asArray()->all();
        return $this->respondJson(0, '获取成功', $data);
    }
    // //历史排名
    // public function actionGetHistoryOrder()
    // {
    //     $type = $this->pInt('type');
    //     if (empty($type)) {
    //         return $this->respondJson(1, '节点类型不能为空');
    //     }
    //     $endTime = $this->pString('endTime', '');
    //     if ($endTime == '') {
    //         $endTime = date('Y-m-d H:i:s');
    //     }
    //     $page = $this->pInt('page', 1);
    //     $history = BHistory::find()->where(['<=', 'create_time', strtotime($endTime)])->orderBy('create_time DESC')->one();
    //     if (empty($history)) {
    //         return $this->respondJson(0, '获取成功', []);
    //     }
    //     $find = BHistory::find()->where(['update_number' => $history->update_number, 'node_type' => $type]);
    //     $count = $find->count();
    //     if ($page != 0) {
    //         $find->page($page);
    //     }
    //     $find->orderBy('vote_number DESC,create_time');
    //     $data = $find->asArray()->all();
    //     foreach ($data as $k => &$v) {
    //         $v['order'] = ($page-1)*20 + $k +1;
    //         $v['count'] = $v['people_number'];
    //         $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
    //     }
    //     $return = [];
    //     $return['count'] = $count;
    //     $return['list'] = $data;
    //     return $this->respondJson(0, "获取成功", $return);
    // }
    // 历史排名下载
    public function actionHistoryDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }

        $type = $this->gInt('type');
        if (empty($type)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $endTime = strtotime($this->gString('endTime', ''));
        if ($endTime == '') {
            $endTime = time();
        }
        $cache = \Yii::$app->cache;
        
        $cache_name = "node/get-history-order/$type/$endTime";
        
        if ($cache->exists($cache_name)) {
            $data = $cache->get($cache_name);
            $headers = ['order'=> '排名','nodeName' => '节点名称', 'username' => '账号', 'vote_number' => '票数', 'count' => '支持人数', 'is_tenure' => '状态'];

            $this->download($data, $headers, '历史排名'.date('YmdHis'));
    
            return;
        }
        $find = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('left join', BVote::tableName().' B', 'B.node_id = A.id')
        ->join('left join', BUser::tableName().' D', 'A.user_id = D.id')
        ->select(['sum(B.vote_number) as vote_number','D.mobile as username', 'A.name as nodeName', 'A.is_tenure', 'A.id']);
        $id = $this->gString('id');
        if ($id != '') {
            $find->andWhere(['A.id' => explode(',', $id)]);
        }
        $find->where(['<=', 'B.create_time', $endTime]);
        $find->andWhere(['or',['>', 'B.undo_time', $endTime], ['B.undo_time' => 0]]);
        $find->andWhere(['A.type_id' => $type]);
        $find->groupBy(['A.id']);
        $find->orderBy('sum(B.vote_number) DESC,B.create_time');
        $data = $find->asArray()->all();
        $arr_id = [];
        foreach ($data as $k => &$v) {
            $arr_id[] = $v['id'];
        }
        $people_data = NodeService::getPeopleNum($arr_id, '', date('Y-m-d H:i:s', $endTime));
        foreach ($data as $k => &$v) {
            $v['count'] = $people_data[$v['id']];
            $v['order'] = $k +1;
            $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
        }


        $headers = ['order'=> '排名','nodeName' => '节点名称', 'username' => '账号', 'vote_number' => '票数', 'count' => '支持人数', 'is_tenure' => '状态'];

        $this->download($data, $headers, '历史排名'.date('YmdHis'));

        return;
    }

    // 历史排名
    public function actionGetHistoryOrder()
    {
        $page = $this->pInt('page', 1);
        $type = $this->pInt('type');
        if (empty($type)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $endTime = strtotime($this->pString('endTime', ''));
        if ($endTime == '') {
            $endTime = time();
        }
        $cache = \Yii::$app->cache;
        
        $cache_name = "node/get-history-order/$page/$type/$endTime";
        
        if ($cache->exists($cache_name)) {
            return $this->respondJson(0, "获取成功", $cache->get($cache_name));
        }
        $find = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('left join', BVote::tableName().' B', 'B.node_id = A.id')
        ->join('left join', BUser::tableName().' D', 'A.user_id = D.id')
        ->select(['sum(B.vote_number) as vote_number','D.mobile as username', 'A.name as nodeName', 'A.is_tenure', 'A.id']);
        $find->where(['<=', 'B.create_time', $endTime]);
        $find->andWhere(['or',['>', 'B.undo_time', $endTime], ['B.undo_time' => 0]]);
        $find->andWhere(['A.type_id' => $type]);
        $find->groupBy(['A.id']);
        $count = $find->count();
        $find->orderBy('sum(B.vote_number) DESC,B.create_time');
        $data = $find->page($page)->asArray()->all();
        $arr_id = [];
        foreach ($data as $k => &$v) {
            $arr_id[] = $v['id'];
        }
        $people_data = NodeService::getPeopleNum($arr_id, '', date('Y-m-d H:i:s', $endTime));
        foreach ($data as $k => &$v) {
            $v['count'] = $people_data[$v['id']];
            $v['order'] = ($page-1)*20 + $k +1;
            $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        $cache->set($cache_name, $return, 3600*24);
        return $this->respondJson(0, "获取成功", $return);
    }
    // 获取节点设置
    public function actionGetNodeSetting()
    {
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeType::find()->where(['id' => $type_id])->asArray()->one();
        
        if (!$data) {
            return $this->respondJson(1, '节点类型不存在');
        }
        $tenure = BNode::find()->where(['type_id' => $type_id])->active()->select(['count(id) as allCount', 'sum(is_tenure) as allTenure'])->asArray()->one();
        if (empty($tenure)) {
            $data['allCount'] = $data['allTenure'] = 0;
        } else {
            $data['allTenure'] = $tenure['allTenure'];
            $data['allCount'] = $tenure['allCount'];
        }
        $rule = BTypeRuleContrast::find()->select(['min_order','max_order','rule_id'])->where(['type_id' => $type_id])->asArray()->all();
        
        $data['ruleList'] = $rule;
        return $this->respondJson(0, '获取成功', $data);
    }
    // 修改节点设置
    public function actionUpdate()
    {
        $id = $this->pInt('id');
        
        $name = $this->pString('name');
        if ($id) {
            $node = BNodeType::find()->where(['id' => $id])->one();
        } else {
            return $this->respondJson(1, 'ID不能为空');
            // $node = new BNodeType();
            // $node->name = $name;
        }
        if (empty($id) && empty($name)) {
            return $this->respondJson(1, 'ID与名称不能同时为空');
        }
        $is_examine = $this->pInt('isExamine', 0);
        
        $is_candidate = $this->pInt('isCandidate', 0);
        $is_vote = $this->pInt('isVote', 0);
        $is_order = $this->pInt('isOrder', 0);
        $tenure_num = $this->pInt('tenureNum');
        if (empty($tenure_num)) {
            return $this->respondJson(1, '任职数量必须大于0');
        }
        
        $max_candidate = $this->pInt('maxCandidate');
        if ($is_candidate > 0 && empty($max_candidate)) {
            return $this->respondJson(1, '候选数量必须大于0');
        }
        $tenure = BNode::find()->where(['type_id' => $id])->active()->select(['count(id) as allCount', 'sum(is_tenure) as allTenure'])->asArray()->one();
        if ($tenure_num < $tenure['allTenure']) {
            return $this->respondJson(1, '任职数量必须大于当前任职数量');
        }
        if ($max_candidate < $tenure['allCount']) {
            return $this->respondJson(1, '候选数量必须大于当前候选数量');
        }
        $grt = $this->pInt('grt', 0);

        $tt = $this->pInt('tt', 0);

        $bpt = $this->pInt('bpt', 0);
        $conversion = $this->pFloat('conversion', 0);

        $quota = $this->pInt('quota', 0);
        if ($quota < 0) {
            $quota = 0;
        }
        $gdt_reward = $this->pInt('gdtReward', 0);
        $node->is_examine = $is_examine;
        $node->gdt_reward = $gdt_reward;
        $node->is_candidate = $is_candidate;
        $node->conversion = $conversion;
        $node->is_vote = $is_vote;
        $node->is_order = $is_order;
        $node->tenure_num = $tenure_num;
        $node->max_candidate = $max_candidate;
        $node->grt = $grt;
        $node->quota = $quota;
        $node->tt = $tt;
        $node->bpt = $bpt;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
        }
        
        // $command = $node->createCommand();
        // var_dump($command->sql);
        // exit;
        $rule = $this->pString('rule', '');
        $rule_arr = json_decode($rule, true);
        // BTypeRuleContrast::find()->where(['type_id' => $node->id])->delete();
        \Yii::$app->db->createCommand()->delete(BTypeRuleContrast::tableName(), 'type_id = '.$node->id)->execute();
        foreach ($rule_arr as $v) {
            $rule_obj = new BTypeRuleContrast();
            $rule_obj->type_id = $node->id;
            $rule_obj->is_tenure = isset($v['is_tenure'])?$v['is_tenure']:$v['isTenure'];
            $rule_obj->min_order = isset($v['min_order'])?$v['min_order']:$v['minOrder'];
            $rule_obj->max_order = isset($v['max_order'])?$v['max_order']:$v['maxOrder'];
            $rule_obj->rule_id =isset($v['rule_id'])?$v['rule_id']:$v['ruleId'];
            if (!$rule_obj->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '操作失败', $rule_obj->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '操作成功');
    }
    // 获取权益列表
    public function actionGetRuleList()
    {
        $data = BNodeRule::find()->asArray()->all();
        $return = [0 => [], 1 => [], 2 => []];
        foreach ($data as $v) {
            $return[$v['is_tenure']][] = $v;
        }
        return $this->respondJson(0, '获取成功', $return);
    }
    // 设置权益
    public function actionSetRule()
    {
        $data = json_decode($this->pString('data'), true);
        $old_data = BNodeRule::find()->all();
        $id_arr = [];
        foreach ($old_data  as $v) {
            $id_arr[$v['id']] = $v['id'];
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            foreach ($v as $val) {
                if (isset($val['id'])) {
                    unset($id_arr[$val['id']]);
                    $node = BNodeRule::find()->where(['id' => $val['id']])->one();
                    if (empty($node)) {
                        $node = new BNodeRule();
                    }
                } else {
                    $node = new BNodeRule();
                }
                $node->name = $val['name'];
                $node->content = $val['content'];
                $node->is_tenure = $val['isTenure'];
                if (!$node->save()) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
                }
            }
        }
        \Yii::$app->db->createCommand()->delete(BNodeRule::tableName(), ['in', 'id', $id_arr])->execute();
        $transaction->commit();
        return $this->respondJson(0, '操作成功');
    }
    // 任职
    public function actionTenure()
    {
        $nodeId = $this->pString('nodeId');
        $node = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($node)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $cycle = BCycle::find()->where(['>=','tenure_end_time',time()])->all();
        $bool = false;
        foreach ($cycle as $v) {
            if ($v->tenure_start_time <= time() && $v->tenure_end_time>=time()) {
                $bool = true;
            }
        }
        if (!$bool) {
            return $this->respondJson(1, '当前处于不可任职时间');
        }
        $upgrade = BNodeUpgrade::find()->where(['user_id' => $node->user_id, 'status' => BNodeUpgrade::STATUS_WAIT])->one();
        if ($upgrade) {
            return $this->respondJson(1, '当前节点有未处理的升级申请，不能任职');
        }
        $now_count = BNode::find()->where(['type_id' => $node->type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
        $setting = BNodeType::find()->where(['id' => $node->type_id])->one();
        if ($now_count >= $setting->tenure_num) {
            return $this->respondJson(1, '任职数量已达上限');
        }
        $node->is_tenure = BNotice::STATUS_ACTIVE;
        
        if (!$node->save()) {
            return $this->respondJson(1, '任职失败', $node->getFirstErrorText());
        }
    
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($nodeId);
        return $this->respondJson(0, '任职成功');
    }
    // 取消任职
    public function actionUnTenure()
    {
        $nodeId = $this->pString('nodeId');
        $node = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($node)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $node->is_tenure = BNotice::STATUS_INACTIVE;

        if (!$node->save()) {
            return $this->respondJson(1, '撤职失败', $node->getFirstErrorText());
        }
    
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($nodeId);
        return $this->respondJson(0, '撤职成功');
    }

    
    // 停用节点
    public function actionStopNode()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($users as $user) {
            $user->status = BNotice::STATUS_INACTIVE;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '停用失败', $user->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '停用成功');
    }

    // 启用节点
    public function actionOpenNode()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($users as $user) {
            $user->status = BNotice::STATUS_ACTIVE;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '启用失败', $user->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '启用成功');
    }
    //修改节点详情
    public function actionUpdateNode()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $logo = $this->pImage('logo', '');
        if (empty($logo)) {
            return $this->respondJson(1, 'logo不能为空');
        }
        $name = $this->pString('name', '');
        if (empty($name)) {
            return $this->respondJson(1, '名称不能为空');
        }
        $desc = $this->pString('desc', '');
        if (empty($desc)) {
            return $this->respondJson(1, '简介不能为空');
        }
        // 推荐人手机号
        $recommend_mobile = $this->pString('recommendMobile', '');
        if ($recommend_mobile != '') {
            $recommend = BNodeRecommend::find()->where(['user_id' => $data->user_id])->one();
            if ($recommend) {
                return $this->respondJson(1, '已有推荐人');
            } else {
                $parent = BUser::find()->where(['mobile' => $recommend_mobile])->one();
                if (!$parent) {
                    return $this->respondJson(1, '推荐人不存在');
                }
                $res = UserService::checkNodeRecommend($data->user_id, $parent->recommend_code);
                if ($res->code) {
                    return $this->respondJson($res->code, '审核失败', $res->msg);
                }
                //推荐赠送
                $res = NodeService::checkVoucher($data->user_id);
                if ($res->code != 0) {
                    return $this->respondJson($res->code, '审核失败', $res->msg);
                }
            }
        }
        $scheme = $this->pString('scheme', '');
        if (empty($scheme)) {
            return $this->respondJson(1, '建设方案不能为空');
        }
        $is_tenure = $this->pInt('is_tenure', 0);
        $quota = \Yii::$app->request->post('quota', null);
        if ($quota !== '' && $quota !== null) {
            $data->quota = round(floatval($quota), 2);
            if ($data->quota < 0) {
                $data->quota = 0;
            }
            // // 向IET同步数据
            // $url = IetSystemService::IET_URL['totalAmount_add'];
            // $user = BUser::find()->where(['id' => $data->user_id])->one();
            // $data_arr = ['phone' => $user->mobile, 'amount' => $data->quota];
            // $res_curl = IetSystemService::push($url, $data_arr);
            // if ($res_curl->code) {
            //     $transaction->rollBack();
            //     return $this->respondJson(1, 'IET数据同步失败', $res_curl->msg. $res_curl->content);
            // }
        } else {
            $data->quota = null;
        }
        $data->logo = $logo;
        $data->name = $name;
        $data->desc = $desc;
        $data->scheme = $scheme;
        $data->is_tenure = $is_tenure;
        $str = '编辑';
        if ($data->save()) {
            return $this->respondJson(0, $str.'成功');
        } else {
            return $this->respondJson(1, $str.'失败', $data->getFirstErrorText());
        }
    }

    public function actionCheckMobile()
    {
        $mobile = $this->pString('mobile');
        if (empty($mobile)) {
            return $this->respondJson(1, '手机不能为空');
        }
        if (!preg_match("/^1\d{10}$/", $mobile)) {
            return $this->respondJson(1, '手机格式不正确');
        }
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        //实名认证信息
        $identify = 0;
        if ($user) {
            $old_node = BNode::find()->where(['user_id' => $user->id])->one();
            if ($old_node) {
                return $this->respondJson(1, '此用户已有节点');
            }
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->one();
            if (!is_null($user_identify) && $user_identify->status == BUserIdentify::STATUS_ACTIVE) {
                $identify = 1;
            }
        }
        return $this->respondJson(0, '验证成功', ['is_identify' => $identify]);
    }

    //
    public function actionCheckNode()
    {
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $is_tenure = $this->pInt('is_tenure');

        $now_count = BNode::find()->where(['type_id' => $type_id, 'status' => BNode::STATUS_ON])->count();
        $setting = BNodeType::find()->where(['id' => $type_id])->one();

        if ($now_count >= $setting->max_candidate) {
            return $this->respondJson(1, $setting->name.'候选数量已达上限');
        }
        if ($is_tenure == BNotice::STATUS_ACTIVE) {
            $now_count = BNode::find()->where(['type_id' => $type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
            $setting = BNodeType::find()->where(['id' => $type_id])->one();
            if ($now_count >= $setting->tenure_num) {
                return $this->respondJson(1, $setting->name.'任职数量已达上限');
            }
        }
        return $this->respondJson(0, '验证成功');
    }

    public function actionCheckRecommend()
    {
        $mobile = $this->pString('mobile', '');
        $recommendMobile = $this->pString('recommendMobile', '');
        if ($mobile == $recommendMobile) {
            return $this->respondJson(1, '推荐人不能是自己');
        }
        $recommend_user = BUser::find()->where(['mobile' => $recommendMobile])->one();
        if (!$recommend_user) {
            return $this->respondJson(1, '推荐人用户不存在');
        }
        
        
        $node = BNode::find()->where(['user_id' => $recommend_user->id])->active()->one();
        if (!$node) {
            return $this->respondJson(1, '推荐人不是节点');
        }
        if ($node->type_id == 5) {
            return $this->respondJson(1, '推荐人不能是微店节点');
        }
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        
        if ($user) {
            $recommend_parent = BNodeRecommend::find()->where(['user_id' => $recommend_user->id])->one();
            if ($recommend_parent) {
                $parent_arr = explode(',', $recommend_parent->parent_list);
                if (in_array($user->id, $parent_arr)) {
                    return $this->respondJson(1, '推荐人不能是自己的下级');
                }
            }
        }
        return $this->respondJson(0, '验证成功');
    }
    // 添加节点
    public function actionCreateUser()
    {
        $mobile = $this->pString('mobile');
        if (empty($mobile)) {
            return $this->respondJson(1, '手机不能为空');
        }
        if (!preg_match("/^1\d{10}$/", $mobile)) {
            return $this->respondJson(1, '手机格式不正确');
        }
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $is_tenure = $this->pInt('is_tenure');
        // if ($is_tenure == BNotice::STATUS_ACTIVE) {
        //     $now_count = BNode::find()->where(['type_id' => $type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
        //     $setting = BNodeType::find()->where(['id' => $type_id])->one();
        //     if ($now_count >= $setting->tenure_num) {
        //         return $this->respondJson(1, '任职数量已达上限');
        //     }
        // }
        $grt = $this->pInt('grt', 0);

        $tt = $this->pInt('tt', 0);

        $bpt = $this->pInt('bpt', 0);

        $transaction = \Yii::$app->db->beginTransaction();
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        //实名认证信息
        $identify = 0;
        if ($user) {
            $old_node = BNode::find()->where(['user_id' => $user->id])->one();
            if ($old_node) {
                return $this->respondJson(1, '此用户已有节点');
            }
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->andWhere(['status' => BUserIdentify::STATUS_ACTIVE])->one();
            if ($user_identify) {
                $identify = 1;
            }
        } else {
            $user = new BUser();
            $user->mobile = $mobile;
            $user->username = $mobile;
            $recommend_code = UserService::generateRemmendCode(6);
            $user->recommend_code = $recommend_code;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '注册失败'.$user->getFirstErrorText());
            }
            $currency = BCurrency::find()->where(['status' => BCurrency::$CURRENCY_STATUS_ON, 'recharge_status' => BCurrency::$RECHARGE_STATUS_ON])->all();
            foreach ($currency as $v) {
                $returnInfo = RechargeService::getAddress($v['id'], $user->id);
                
                if ($returnInfo->code) {
                    $transaction->rollBack();
                    return $this->respondJson(1, $returnInfo->msg);
                }
            }
        }
        // $now_count = BNode::find()->where(['type_id' => $type_id, 'status' => BNode::STATUS_ON])->count();
        // $node_type = BNodeType::find()->where(['id' => $type_id])->one();
        // if ($now_count >= $node_type->max_candidate) {
        //     return $this->respondJson(1, '候选数量已达上限');
        // }
        // $node = new BNode();
        $old_upgrade = BNodeUpgrade::find()->where(['user_id' => $user->id, 'status' => BNodeUpgrade::STATUS_WAIT])->one();
        if ($old_upgrade) {
            $transaction->rollBack();
            return $this->respondJson(1, '此用户已有申请在审核中');
        }
        $node = new BNodeUpgrade();
        $node->old_type = 0;
        $node->user_id = $user->id;
        $node->type_id = $type_id;
        // $node->is_tenure = $is_tenure;
        $node->grt = $grt;
        $node->tt = $tt;
        $node->bpt = $bpt;
        $logo = $this->pImage('logo', '');
        if (empty($logo)) {
            return $this->respondJson(1, 'logo不能为空');
        }
        $name = $this->pString('name', '');
        if (empty($name)) {
            return $this->respondJson(1, '名称不能为空');
        }
        $desc = $this->pString('desc', '');
        if (empty($desc)) {
            return $this->respondJson(1, '简介不能为空');
        }

        $scheme = $this->pString('scheme', '');
        if (empty($scheme)) {
            return $this->respondJson(1, '建设方案不能为空');
        }
        $weixin = $this->pString('weixin', '');

        $grt_address = $this->pString('grt_address', '');
        $tt_address = $this->pString('tt_address', '');
        $bpt_address = $this->pString('bpt_address', '');
        $node->logo = $logo;
        $node->name = $name;
        $node->desc = $desc;
        $node->scheme = $scheme;
        $node->weixin = $weixin;
        $node->grt_address = $grt_address;
        $node->bpt_address = $bpt_address;
        $node->tt_address = $tt_address;
        //推荐相关
        $recommendMobile = $this->pString('recommendMobile', '');
        if ($recommendMobile != '') {
            $recommend_user = BUser::find()->where(['mobile' => $recommendMobile])->one();
            if (!$recommend_user) {
                return $this->respondJson(1, '注册失败,推荐用户不存在');
            }
            // UserService::checkNodeRecommend($user->id, $recommend_user->recommend_code);
            $node->parent_id = $recommend_user->id;
        }

        $node->status = BNodeUpgrade::STATUS_WAIT;
        // $node->examine_time = time();

        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败'.$node->getFirstErrorText());
        }




        // if ($bpt_address || $weixin || $grt_address || $tt_address) {
        //     // 添加个人其它信息
        //     $other = BUserOther::find()->where(['user_id' => $user->id])->one();
        //     if (empty($other)) {
        //         $other = new BUserOther();
        //         $other->user_id = $user->id;
        //     }
        //     $other->weixin = $weixin;

        //     $other->grt_address = $grt_address;
        //     $other->tt_address = $tt_address;
        //     $other->scenario = BUserOther::SCENARIO_APPLY;
        //     $other->bpt_address = $bpt_address;
        //     if (!$other->save()) {
        //         $transaction->rollBack();
        //         return $this->respondJson(1, '注册失败'.$other->getFirstErrorText());
        //     }
        // }



        // 推荐赠送
        // $res = NodeService::checkVoucher($user->id);
        // if ($res->code != 0) {
        //     $transaction->rollBack();
        //     return $this->respondJson(1, '注册失败', $res->msg);
        // }
        
        // // 补全充值冻结信息
        // $log = NodeService::addNodeMakeLogs($node);
        // if ($log->code != 0) {
        //     $transaction->rollBack();
        //     return $this->respondJson(1, '注册失败'.$log->content);
        // }
        // // 赠送gdt
        // $currencyDetail = new BUserCurrencyDetail();
        // $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
        // $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        // $currencyDetail->effect_time = NOW_TIME;
        // $currencyDetail->remark = '申请节点奖励';
        // $currencyDetail->user_id = $user->id;
        // $currencyDetail->relate_table = 'node';
        // $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
        // $currencyDetail->relate_id = $node->id;
        // $currencyDetail->amount = $node_type->gdt_reward;

        // if (!$currencyDetail->save()) {
        //     $transaction->rollBack();
        //     return $this->respondJson(1, '注册失败'.$currencyDetail->getFirstErrorText());
        // }
        // //重算gdt
        // UserService::resetCurrency($user->id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        // 实名认证信息
        $user_id = $user->id;
        
        if (!$identify) {
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->andWhere(['status' => BUserIdentify::STATUS_INACTIVE])->one();
            if ($user_identify) {
                $user_identify->delete();
            }
            $realname = $this->pString('realname');
            if (empty($realname)) {
                return $this->respondJson(1, '用户姓名不能为空');
            }
            $number = $this->pString('identify');
            if (empty($number)) {
                return $this->respondJson(1, '身份证号不能为空');
            }
            $pic_front = $this->pImage('pic_front');
            if (empty($pic_front)) {
                return $this->respondJson(1, '证件图片正面不能为空');
            }
            $pic_back = $this->pImage('pic_back');
            if (empty($pic_back)) {
                return $this->respondJson(1, '证件图片背面不能为空');
            }
            $identify = new BUserIdentify();
            $identify->user_id = $user_id;
            $identify->realname = $realname;
            $identify->number = (string)$number;
            $identify->pic_front = $pic_front;
            $identify->examine_time = time();
            $identify->audit_admin_id = $this->user_id;
            $identify->pic_back = $pic_back;
            $identify->type = 1;
            $identify->status = BNotice::STATUS_ACTIVE;
            if (!$identify->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '实名信息添加失败', $identify->getFirstErrorText());
            }
            $user->is_identified = BNotice::STATUS_ACTIVE;
            if (!$user->save()) {
                return $this->respondJson(1, '实名信息添加失败', $user->getFirstErrorText());
            }
        }


        $transaction->commit();
        return $this->respondJson(0, '添加成功');
    }


    // 删除记录
    public function actionDelOldData()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $res = BNode::updateAll(['status' => BNode::STATUS_DEL], ['and', ['in', 'id', $user_id], ['status' => BNode::STATUS_NO]]);
        if ($res) {
            return $this->respondJson(0, '删除成功');
        } else {
            return $this->respondJson(0, '删除失败');
        }
    }

    
    // 推荐记录
    public function actionRecommendList()
    {
        $find = BNodeRecommend::find()
        ->from(BNodeRecommend::tableName()." A")
        ->select(['B.mobile as p_moblie', 'F.realname as p_realname', 'D.type_id as p_type_id', 'C.mobile as u_mobile', 'G.realname as u_realname', 'E.type_id as u_type_id', 'A.amount', 'A.create_time'])
        ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')
        ->join('left join', BUser::tableName().' C', 'A.user_id = C.id')
        ->join('left join', BNode::tableName().' D', 'A.parent_id = D.user_id')
        ->join('left join', BUserIdentify::tableName().' F', 'A.parent_id = F.user_id && F.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BUserIdentify::tableName().' G', 'A.user_id = G.user_id && G.status = '.BUserIdentify::STATUS_ACTIVE)
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
        $find->groupBy('A.id');
        $count = $find->count();
        $page = $this->pInt('page', 0);
        $find->page($page);
        $data = $find->orderBy('A.create_time DESC')->asArray()->all();
        foreach ($data as &$v) {
            $v['p_type_id'] = BNodeType::GetName($v['p_type_id']);
            $v['u_type_id'] = BNodeType::GetName($v['u_type_id']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
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
        $find = BNodeRecommend::find()
        ->from(BNodeRecommend::tableName()." A")
        ->select(['B.mobile as p_mobile', 'F.realname as p_realname', 'D.type_id as p_type_id', 'C.mobile as u_mobile', 'G.realname as u_realname', 'E.type_id as u_type_id', 'A.amount', 'A.create_time'])
        ->join('left join', BUser::tableName().' B', 'A.parent_id = B.id')
        ->join('left join', BUser::tableName().' C', 'A.user_id = C.id')
        ->join('left join', BNode::tableName().' D', 'A.parent_id = D.user_id')
        ->join('left join', BUserIdentify::tableName().' F', 'A.parent_id = F.user_id && F.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BUserIdentify::tableName().' G', 'A.user_id = G.user_id && G.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BNode::tableName(). ' E', 'A.user_id = E.user_id');
        $id = $this->gString('id');
        if ($id != '') {
            $find->andWhere(['A.id' => explode(',', $id)]);
        }
        $searchName = $this->gString('searchName');
        if ($searchName != '') {
            $find->andWhere(['like', 'B.mobile', $searchName]);
        }
        $type = $this->gInt('type');
        if ($type != 0) {
            if ($type == 5) {
                $find->andWhere(['>', 'D.id', 0]);
            } elseif ($type <= 4) {
                $find->andWhere(['D.type_id' => $type]);
            } elseif ($type == 6) {
                $find->andWhere(['D.id' => null]);
            }
        }
        $strTime = $this->gString('strTime');
        if ($strTime != '') {
            $find->startTime($strTime, 'A.create_time');
        }
        $endTime = $this->gString('endTime');
        if ($endTime != '') {
            $find->endTime($endTime, 'A.create_time');
        }
        $data = $find->groupBy('A.id')->orderBy('A.create_time DESC')->asArray()->all();
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
