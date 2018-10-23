<?php

namespace app\controllers;

use common\models\business\BUserRechargeAddress;
use common\models\business\BUserRechargeWithdraw;
use common\services\RechargeService;
use common\services\ValidationCodeSmsService;
use common\services\WithdrawService;
use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\services\JingTumService;
use common\services\SettingService;
use common\models\business\BSmsAuth;
use common\models\business\BUserLog;
use common\models\business\BCurrency;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrency;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;

class WalletController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 无需需登录访问 为空则所有相关接口都需登录后访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 钱包
     *
     * @return void
     */
    public function actionIndex()
    {
//        var_dump(FuncHelper::sendSocketMsg('msg','single', 'user', 35, ['id'=>11, 'status'=>"ssss"]));
//        var_dump(WithdrawService::withdrawCurrencyAudit(24,2,'身份不正确')) ;
//        var_dump( JingTumService::getInstance()->queryPayments("jGXcJRazVUC1iqNbHDiTkMk4hvybWPPzYY" , "1", "10")) ;
//        var_dump( JingTumService::getInstance()->addUserBalanceFormMain("jNnCJNbvrctbriJRSfwGg2BydGph2tmDuu","Trans20181018002",0.3,"test",JingTumService::ASSETS_TYPE_GRT)) ;
//        var_dump( JingTumService::getInstance()->queryBalance("j3q98BEzVKGp6deZM5RdPoXREDtNuoJw7H")) ;
//        $resJingTum = JingTumService::getInstance()->queryPayments("j4oRzJ88L37Qnig8ftGtDrmbKxyaXR7G1d" , 1, 10);
//        var_dump($resJingTum);
//        var_dump( JingTumService::getInstance()->mainBalance()) ;
//        UserService::resetCurrency(51, 1);
    }

    /**
     * 钱包下币种列表
     *
     * @return void
     */
    public function actionCurrency()
    {
        $userId = $this->user->id;
        $userCurrencys = BCurrency::find()
            ->from(BCurrency::tableName().' c')
            ->select(['c.name', 'c.code', 'c.id', 'uc.position_amount', 'uc.frozen_amount', 'uc.use_amount'])
            ->leftJoin(
                BUserCurrency::tableName().' uc',
                'c.id = uc.currency_id '
                .' and uc.user_id = '.$userId
            )
            ->where(['c.status' => BCurrency::STATUS_ACTIVE])
            ->orderBy('c.sort desc, c.id asc')
            ->asArray()
//             ->createCommand()->getRawSql();var_dump($userCurrencys);return;
            ->all();

        foreach ($userCurrencys as $key => &$currency) {
            $currency['code'] = $currency['code'];
            $currency['name'] = $currency['name'];
            $currency['position_amount'] = FuncHelper::formatAmount($currency['position_amount']);
            $currency['frozen_amount'] = FuncHelper::formatAmount($currency['frozen_amount']);
            $currency['use_amount'] = FuncHelper::formatAmount($currency['use_amount']);
        }
        return $this->respondJson(0, '获取成功', $userCurrencys);
    }

    public function actionCurrencyInfo()
    {
        $currencyId = $this->pInt('id', false);
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }
        $userId = $this->user->id;

        $userCurrency = BCurrency::find()
            ->from(BCurrency::tableName().' c')
            ->select(['c.name', 'c.code', 'c.id',
                'c.recharge_status',
                'c.withdraw_status',
                'c.withdraw_min_amount',
                'c.withdraw_max_amount',
                'c.withdraw_audit_amount',
                'c.withdraw_day_amount',
                'c.withdraw_amount_precision',
                'uc.position_amount', 'uc.frozen_amount', 'uc.use_amount'])
            ->leftJoin(
                BUserCurrency::tableName().' uc',
                'c.id = uc.currency_id '
                .' and uc.user_id = '.$userId
            )
            ->where(['c.status' => BCurrency::STATUS_ACTIVE, 'c.id' => $currencyId])
            ->orderBy('c.sort desc, c.id asc')
            ->asArray()
//             ->createCommand()->getRawSql();var_dump($userCurrencys);return;
            ->limit(1)
            ->one();

        if(!$userCurrency) {
            return $this->respondJson(1, '货币不存在');
        }

        $userCurrency['position_amount'] = FuncHelper::formatAmount($userCurrency['position_amount']);
        $userCurrency['frozen_amount'] = FuncHelper::formatAmount($userCurrency['frozen_amount']);
        $userCurrency['use_amount'] = FuncHelper::formatAmount($userCurrency['use_amount']);

        return $this->respondJson(0, '获取成功', $userCurrency);
    }

    // public function actionCurrency

    /**
     * 货币收入明细
     *
     * @return void
     */
    public function actionCurrencyDetail()
    {
        $currencyId = $this->pInt('id', false);
        $type = $this->pInt('type', 0);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $data = [
            'page' => $page,
            'page_size' => $pageSize
        ];
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }
        $userId = $this->user->id;
        // 获取收入 类型ID // 获取是否或者支出的 id 集
        $detailType = (bool) $type ? BUserCurrencyDetail::getTypeRevenue() : BUserCurrencyDetail::getTypePay();
        
        $currencyModel = BUserCurrencyDetail::find()
        ->select(['amount', 'remark', 'effect_time', 'status'])
        ->where(['user_id' => $userId, 'currency_id' => $currencyId, 'type' => $detailType])
        ->active();
        // var_dump($currencyModel->createCommand()->getRawSql());exit;
        $data['count'] = $currencyModel->count();
        $data['list'] = $currencyModel->page($page, $pageSize)->orderBy('create_time desc, id desc')->asArray()->all();
        foreach ($data['list'] as &$val) {
            $val['amount'] = FuncHelper::formatAmount($val['amount'], 0, true);
            $val['status_str'] = BUserCurrencyDetail::getStatus($val['status'], 0, true);
            $val['effect_time'] = FuncHelper::formateDate($val['effect_time']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    /**
     * 货币锁仓明细
     *
     * @return void
     */
    public function actionCurrencyFrozen()
    {
        $currencyId = $this->pInt('id', false);
        $status = $this->pInt('type', false);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $data = [
            'page' => $page,
            'page_size' => $pageSize
        ];
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }
        $userId = $this->user->id;
        // 获取收入 类型ID
        $detailType = BUserCurrencyFrozen::getTypeFrozen();
        $currencyModel = BUserCurrencyFrozen::find()
        ->select(['amount', 'remark', 'unfrozen_time', 'create_time', 'status'])
        ->where(['user_id' => $userId, 'currency_id' => $currencyId, 'type' => $detailType]);
        if (is_int($status)) {
            $currencyModel->active($status);
        }
        $data['count'] = $currencyModel->count();
        $data['list'] = $currencyModel->page($page, $pageSize)->orderBy('create_time desc, id desc')->asArray()->all();
        foreach ($data['list'] as &$val) {
            if ($val['status'] == BUserCurrencyFrozen::STATUS_FROZEN) {
                $val['amount'] = FuncHelper::formatAmount($val['amount'], 0, true);
            } else {
                $val['amount'] = FuncHelper::formatAmount($val['amount'] * -1, 0, true);
            }
            $val['unfrozen_time'] = FuncHelper::formateDate($val['unfrozen_time']);
            $val['create_time'] = FuncHelper::formateDate($val['create_time']);
            $val['status_str'] = $val['remark'].BUserCurrencyFrozen::getStatus($val['status']);
            $val['status'] = (bool) $val['status'];
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * @return string
     * 充值地址
     */
    public function actionRechargeAddress()
    {
        $currencyId = $this->pInt('id', 0);
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }

        $currency = BCurrency::find()->where(['id' => $currencyId])->one();
        if (empty($currency) || $currency->recharge_status == BCurrency::$RECHARGE_STATUS_OFF) {
            return $this->respondJson(1, "此货币不可充币");
        }

        $userId = $this->user->id;
        $returnInfo = RechargeService::getAddress($currencyId, $userId);
        if ($returnInfo->code) {
            return $this->respondJson(1, $returnInfo->msg);
        }
        $respondContent = [
            'address' => $returnInfo->content['address']
        ];
        return $this->respondJson(0, "获取成功", $respondContent);
    }

    /**
     * @return string
     * 充值刷新
     */
    public function actionRechargeRefresh()
    {
        $currencyId = $this->pInt('id', 0);
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }

        $userId = $this->user->id;
        $rechargeAddress = BUserRechargeAddress::find()
            ->where(['user_id' => $userId, 'currency_id' => $currencyId])
            ->limit(1)
            ->one();
        if(!$rechargeAddress) {
            return $this->respondJson(1, '钱包地址不存在');
        }
        $address = $rechargeAddress->address;
        $isRefresh = false;

        //井通下的货币
        $currencyJingtum = BCurrency::getJingtumCurrency();
        //井通下的货币充值刷新
        if(in_array($currencyId, $currencyJingtum)) {
            $page = 1;
            $pageSize = 10;
            $isUpdate = true; // 拉取交易记录自动更新交易数据
            $record = JingTumService::getInstance()->pullTransRecord($address, $page, $pageSize, $isUpdate);
            if($record['new_record']) {
                $isRefresh = true;
            }
        }

        return $this->respondJson(0, '刷新成功', ['is_refresh' => $isRefresh]);
    }


    public function actionTransfer()
    {
        $currencyId = $this->pInt('id');
        $userModel = $this->user;
        if (!$currencyId) {
            return $this->respondJson(1, '转出货币不能为空');
        }
        $amount = $this->pFloat('amount', 0);
        if ($amount <= 0) {
            return $this->respondJson(1, '转出数量必须大于0');
        }
        $address = $this->pString('address');
        if (!$address) {
            return $this->respondJson(1, '转出地址不能为空');
        }
        $remark = $this->pString('remark', '');

        // 获取设置中相关设置
        $settingCurrency = 'currency';
        $isIdentify = (bool) SettingService::get($settingCurrency, 'is_identify')->value;
        if ($isIdentify && !(bool) $userModel->is_identified) {
            return $this->respondJson(1, '未实名认证');
        }

        $currency = BCurrency::find()->where(['id' => $currencyId])->one();
        if (!$currency) {
            return $this->respondJson(1, '转出货币不存在');
        }

        //货币状态
        if ($currency->status != BCurrency::$CURRENCY_STATUS_ON) {
            return $this->respondJson(1, '该货币已下架');
        }
        //货币提现状态
        if ($currency->withdraw_status == BCurrency::$RECHARGE_STATUS_OFF) {
            return $this->respondJson(1, '该货币暂不支持提币');
        }
        // 单笔最小数量
        $minAmount = $currency->withdraw_min_amount;
        if ($amount < $minAmount) {
            return $this->respondJson(1, '单笔最小转账数量'.floatval($minAmount));
        }
        // 单笔最大数量
        $maxAmount = $currency->withdraw_max_amount;
        if ($amount > $maxAmount) {
            return $this->respondJson(1, '单笔最大转账数量'.floatval($maxAmount));
        }

        // 重算用户持仓
        UserService::resetCurrency($userModel->id, $currencyId);
        $userCurrencyModel = BUserCurrency::findOne(['user_id' => $userModel->id, 'currency_id' => $currencyId]);
        if (!$userCurrencyModel) {
            return $this->respondJson(1, '用户资产不足');
        }

        $use_amount = $userCurrencyModel->use_amount;
        if ($amount > $use_amount) {
            return $this->respondJson(1, '转出数量不能大于可用数量'.floatval($use_amount));
        }
        // 每日累计转账数量
        $beginToday = strtotime(date("Y-m-d"));
        $endToday = $beginToday + 86399;
        $withdrawDay = BUserRechargeWithdraw::find()
            ->where(['currency_id' => $currencyId, 'user_id' => $userModel->id, 'type' => BUserRechargeWithdraw::$TYPE_WITHDRAW])
            ->andWhere(['<>', 'status', BUserRechargeWithdraw::$STATUS_EFFECT_FAIL])
            ->andWhere(['<=', 'create_time', $endToday])
            ->andWhere(['>=', 'create_time', $beginToday])
            ->sum('amount');

        $dayMax = $currency->withdraw_day_amount;
        if ($dayMax > 0) {
            if (round($withdrawDay+$amount,8) > $dayMax) {
                return $this->respondJson(1, '今日已转账'.floatval($withdrawDay).'，每日累计转账限制数量为'.floatval($dayMax));
            }
        }

        // 验证地址
        $addressCheck = WithdrawService::withdrawAddressCheck($address, $currencyId);
        if ($addressCheck === false) {
            return $this->respondJson(1, "转出地址不正确");
        }
        $rechargeAddress = BUserRechargeAddress::find()
            ->where(['user_id' => $userModel->id, 'currency_id' => $currencyId])
            ->limit(1)
            ->one();
        if(!empty($rechargeAddress) && $rechargeAddress->address == $address) {
            return $this->respondJson(1, '转出地址不能为自己钱包地址');
        }

        // 短信验证码
        $vcode = $this->pString('vcode');
        if (!$vcode) {
            return $this->respondJson(1, '验证码不能为空');
        }
        //手机验证码是否正确, 有效期只有5分钟
        $returnInfo = ValidationCodeSmsService::checkValidateCode(
            $userModel->mobile,
            $vcode,
            BSmsAuth::$TYPE_TRANSFER_GET
        );
        if ($returnInfo->code != 0) {
            return $this->respondJson(1, $returnInfo->msg);
        }

        //支付密码验证
        $pass = $this->pString('pass');
        if (!$pass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        if (!$userModel->trans_password) {
            return $this->respondJson(1, '未设置支付密码');
        }
        $passCheck = UserService::validateTransPwd($userModel, $pass);
        if (!$passCheck) {
            return $this->respondJson(1, "支付密码不正确");
        }

        //转账操作
        $time = time();
        $poundage = 0;
        $data = [
            'currency_id' => $currencyId,
            'user_id' => $userModel->id,
            'amount' => $amount, // 总数量
            'poundage' => $poundage, // 手续费
            'destination_address' => $address, // 接收方地址
            'tag' => "", // 地址标签
            'remark' => $remark,
            'status' => BUserRechargeWithdraw::$STATUS_EFFECT_WAIT,
            'create_time' => $time,
            'update_time' => $time,
        ];

        $res = WithdrawService::withdrawCurrencyApply($data);
        if ($res->code != 0) {
            return $this->respondJson(1, '提交失败');
        }
        $withdrawId = $res->content;

        //日累计金额小于限制金额自动审核
        $auditMax = $currency->withdraw_audit_amount;
        if (round($withdrawDay+$amount,8) <= $auditMax) {
            //审核
            WithdrawService::withdrawCurrencyAudit($withdrawId, BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS,'', 0);
        }


        return $this->respondJson(0, '提交成功');
    }
}
