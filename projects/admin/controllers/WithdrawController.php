<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\JingTumService;
use common\services\TicketService;
use common\services\WithdrawService;
use common\services\SettingService;
use yii\helpers\ArrayHelper;
use common\models\business\BSetting;
use common\models\business\BUser;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BCurrency;

/**
 * Site controller
 */
class WithdrawController extends BaseController
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
    // 修改设置
    public function actionSetVote()
    {
        $currency_id = $this->pString('currency_id');
        if (empty($currency_id)) {
            return $this->respondJson(1, '币种ID不能为空');
        }
        $withdraw_min_amount = $this->pFloat('withdraw_min_amount');
        if (empty($withdraw_min_amount)) {
            return $this->respondJson(1, '提现最小数量不能为空');
        }
        $withdraw_max_amount = $this->pFloat('withdraw_max_amount');
        if (empty($withdraw_max_amount)) {
            return $this->respondJson(1, '提现最大数量不能为空');
        }
        $withdraw_audit_amount = $this->pFloat('withdraw_audit_amount');
        if (empty($withdraw_audit_amount)) {
            return $this->respondJson(1, '提现审核大于数量不能为空');
        }
        $withdraw_day_amount = $this->pFloat('withdraw_day_amount');
        if (empty($withdraw_day_amount)) {
            return $this->respondJson(1, '提现日限制数量不能为空');
        }
        $is_identify = $this->pFloat('is_identify');
        $currency = BCurrency::find()->where(['id' => $currency_id])->one();
        if (empty($currency)) {
            return $this->respondJson(1, '币种不存在');
        }
        $currency->withdraw_min_amount = $withdraw_min_amount;
        $currency->withdraw_max_amount = $withdraw_max_amount;
        $currency->withdraw_audit_amount = $withdraw_audit_amount;
        $currency->withdraw_day_amount = $withdraw_day_amount;
        $setting = BSetting::find()->where(['key' => 'is_identify'])->one();
        $setting->value = $is_identify;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$setting->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, "操作失败", $setting->getFirstErrorText());
        }
        if (!$currency->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, "操作失败", $currency->getFirstErrorText());
        }
        SettingService::refresh();
        $transaction->commit();

        return $this->respondJson(0, "操作成功");
    }

    // 获取设置项
    public function actionGetSettingList()
    {
        $currency_id = $this->pString('currency_id');
        if (empty($currency_id)) {
            return $this->respondJson(1, '币种ID不能为空');
        }
        $currency = BCurrency::find()->where(['id' => $currency_id])->one();
        if (empty($currency)) {
            return $this->respondJson(1, '币种不存在');
        }
        $return  = [];
        $return['withdraw_min_amount'] = (float)$currency->withdraw_min_amount;
        $return['withdraw_max_amount'] = (float)$currency->withdraw_max_amount;
        $return['withdraw_audit_amount'] = (float)$currency->withdraw_audit_amount;
        $return['withdraw_day_amount'] = (float)$currency->withdraw_day_amount;
        $is_identify = BSetting::find()->where(['key' => 'is_identify'])->one();
        $return['is_identify'] = $is_identify->value;
        return $this->respondJson(0, "获取成功", $return, false);
    }

    public function actionIndex()
    {
        $status = $this->pInt('status');
        $searchName = $this->pString('searchName', '');
        $currency_id = $this->pInt('currency_id');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');

        $page = $this->pInt('page', 0);
        $find = BUserRechargeWithdraw::find()
        ->from(BUserRechargeWithdraw::tableName()." A")
        ->where(['A.status' => $status])
        ->select(['A.order_number','C.name','B.mobile','A.amount', 'A.type', 'A.status', 'A.remark', 'A.create_time', 'A.audit_time as examine_time', 'A.id', 'A.destination_address', 'A.status_remark'])
        ->andWhere(['>=', 'A.amount', 'C.withdraw_audit_amount'])
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BCurrency::tableName().' C', 'A.currency_id = C.id');
        if ($searchName != '') {
            $find->andWhere(['or',['like', 'B.mobile', $searchName], ['like', 'A.order_number', $searchName]]);
        }
        if ($currency_id != 0) {
            $find->andWhere(['A.currency_id' => $currency_id]);
        }
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $find->orderBy('A.create_time DESC');
        $count = $find->count();
        if ($page != 0) {
            $find->page($page);
        }

        $data = $find->asArray()->all();
        //echo $find->createCommand()->getRawSql();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] =  $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['type'] = BUserRechargeWithdraw::getType($v['type']);
            $v['status'] = BUserRechargeWithdraw::getStatus($v['status']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }

    // 审核失败
    public function actionExamineOff()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BUserRechargeWithdraw::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $return = WithdrawService::withdrawCurrencyAudit($id, BUserRechargeWithdraw::$STATUS_EFFECT_FAIL, $remark);
        if ($return->code == 0) {
            return $this->respondJson(0, '审核成功');
        } else {
            return $this->respondJson(1, '审核失败');
        }
    }

    // 审核成功
    public function actionExamineOn()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BUserRechargeWithdraw::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        $return = WithdrawService::withdrawCurrencyAudit($id, BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS);
        if ($return->code == 0) {
            return $this->respondJson(0, '审核成功');
        } else {
            return $this->respondJson(1, '审核失败');
        }
    }

    // 钱包资产信息
    public function actionWalletInfo()
    {
        $type = $this->pString('type');
        $currencyCode = $this->pString('currency_code');
        $currencyCode = $currencyCode ? strtoupper($currencyCode) : null;

        if ($type && isset(\Yii::$app->params['JTWallet'][$type])) {
            $walletList = [
                $type => \Yii::$app->params['JTWallet'][$type]
            ];
        } else {
            $walletList = \Yii::$app->params['JTWallet'];
        }
        $data = [];
        foreach ($walletList as $key => $wallet) {
            $res = JingTumService::getInstance()->queryBalance($wallet['address'], $currencyCode) ;

            if ($res->code != 0) {
                $res->content = "0.000000";
            }
            $data[$key] = $res->content;
        }

        return $this->respondJson(0, '获取成功', $data);
    }
}
