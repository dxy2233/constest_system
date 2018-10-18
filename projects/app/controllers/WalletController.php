<?php

namespace app\controllers;

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
     * 钱包列表
     *
     * @return void
     */
    public function actionIndex()
    {
        // 返回容器
        $data = [];
        $walletId = $this->pInt('id', false);
        $userModel = $this->user;
        $userWallet = $userModel->getUserWallet();
        // 全局控制钱包开关
        $isOpen = (bool) SettingService::get('wallet', 'is_open')->value;
        if (!$isOpen) {
            return $this->respondJson(1, "钱包未开启");
        }
        $userWallet->select(['id', 'name', 'address', 'is_main'])
        ->orderBy(['is_main' => SORT_DESC]);
        if ((bool) $walletId) {
            $wallet = $userWallet->filterWhere(['id' => $walletId])->one();
            if (!is_null($wallet)) {
                $wallet->is_main = $wallet->isMainText;
            } else {
                return $this->respondJson(1, '获取失败');
            }
            return $this->respondJson(0, '获取成功', $wallet);
        }
        // 全局设置显示主钱包
        if ((bool) SettingService::get('wallet', 'only_main')->value) {
            $data[] = $userWallet->one();
        } else {
            $data = $userWallet->all();
        }
        
        foreach ($data as $key => &$wallet) {
            if (!is_object($wallet)) {
                $wallet = [];
                continue;
            }
            $wallet->is_main = $wallet->isMainText;
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 钱包下币种列表
     *
     * @return void
     */
    public function actionCurrency()
    {
        $walletId = $this->pInt('id', false);
        if (!$walletId) {
            return $this->respondJson(1, '钱包不能为空');
        }
        $userId = $this->user->id;
        $userCurrencys = BUserCurrency::find()
        ->select(['c.name', 'c.code', 'uc.currency_id', 'uc.position_amount', 'uc.frozen_amount', 'uc.use_amount'])
        ->alias('uc')
        ->joinWith(['currency c'])
        ->where(['uc.user_id' => $userId, 'uc.wallet_id' => $walletId])
        ->active(BUserCurrency::STATUS_ACTIVE, 'c.')
        ->orderBy('c.sort')
        ->asArray()
        // ->createCommand()->getRawSql();
        ->all();
        foreach ($userCurrencys as $key => &$currency) {
            $currency['code'] = strtoupper($currency['code']);
            $currency['position_amount'] = FuncHelper::formatAmount($currency['position_amount']);
            $currency['frozen_amount'] = FuncHelper::formatAmount($currency['frozen_amount']);
            $currency['use_amount'] = FuncHelper::formatAmount($currency['use_amount']);
            unset($currency['currency']);
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
        $userCurrency = BUserCurrency::find()
        ->select(['c.name', 'c.code', 'uc.currency_id', 'uc.position_amount', 'uc.frozen_amount', 'uc.use_amount'])
        ->alias('uc')
        ->joinWith(['currency c'])
        ->where(['uc.user_id' => $userId, 'uc.currency_id' => $currencyId])
        ->active(BUserCurrency::STATUS_ACTIVE, 'c.')
        ->orderBy('c.sort')
        ->asArray()
        ->one();
        
        $userCurrency['position_amount'] = FuncHelper::formatAmount($userCurrency['position_amount']);
        $userCurrency['frozen_amount'] = FuncHelper::formatAmount($userCurrency['frozen_amount']);
        $userCurrency['use_amount'] = FuncHelper::formatAmount($userCurrency['use_amount']);
        $userCurrency['code'] = strtoupper($userCurrency['code']);
        FuncHelper::arrayForget($userCurrency, 'currency');
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
        $type = $this->pInt('type', 1);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        if (!$currencyId) {
            return $this->respondJson(1, '货币不能为空');
        }
        $userId = $this->user->id;
        // 获取收入 类型ID
        $detailType = (bool) $type ? BUserCurrencyDetail::getTypeRevenue() : BUserCurrencyDetail::getTypePay();
        $currencyModel = BUserCurrencyDetail::find()
        ->select(['amount', 'remark', 'effect_time', 'status'])
        ->where(['user_id' => $userId, 'currency_id' => $currencyId, 'type' => $detailType])
        ->active();
        $data['count'] = $currencyModel->count();
        $data['list'] = $currencyModel->page($page, $pageSize)->asArray()->all();
        foreach ($data['list'] as &$val) {
            $val['amount'] = FuncHelper::formatAmount($val['amount']);
            $val['effect_time'] = FuncHelper::formateDate($val['effect_time']);
            $val['status_str'] = BUserCurrencyDetail::getStatus($val['status']);
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
        $data['list'] = $currencyModel->page($page, $pageSize)->asArray()->all();
        foreach ($data['list'] as &$val) {
            if ($val['status'] == BUserCurrencyFrozen::STATUS_FROZEN) {
                $val['amount'] = FuncHelper::formatAmount($val['amount'] * -1);
            } else {
                $val['amount'] = FuncHelper::formatAmount($val['amount']);
            }
            $val['unfrozen_time'] = FuncHelper::formateDate($val['unfrozen_time']);
            $val['create_time'] = FuncHelper::formateDate($val['create_time']);
            $val['status_str'] = $val['remark'].BUserCurrencyFrozen::getStatus($val['status']);
            $val['status'] = (bool) $val['status'];
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 创建钱包
     *
     * @return void
     */
    public function actionCreate()
    {
        $userModel = $this->user;
        if (empty($userModel->trans_password)) {
            return $this->respondJson(1, '支付密码不存在');
        }
        $defaultWallet = FuncHelper::getDefaultWallet();
        $walletModel = $userModel->getUserWallet()
        ->where(['wallet' => $defaultWallet['code']]);
        if ($walletModel->exists()) {
            return $this->respondJson(1, '钱包已存在');
        }
        $jinTumService = JingTumService::getInstance();
        $wallet = $jinTumService->createNewWallet()->content;
        if (empty($wallet)) {
            return $this->respondJson(1, '创建失败');
        }
        // 激活钱包
        // $initWallet = $jinTumService->initializeWallet($wallet['address']);
        // 查询钱包资产 暂不使用，原因：激活后不能及时查看钱包资产 需要 15-10秒左右时间
        // $currencyList = $jinTumService->queryBalance($wallet['address']);
        
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            // 钱包激活
            $jinTumService->initializeWallet($wallet['address']);
            // if ((bool) $initWallet->code) {
            //     throw new \Exception('init wallet fail');
            // }
            // 钱包入库
            $userWallet = new BUserWallet();
            $userWallet->is_main = $defaultWallet['default'];
            $userWallet->name = $defaultWallet['name'];
            $userWallet->wallet = $defaultWallet['code'];
            $userWallet->address = $wallet['address'];
            $userWallet->secret = $wallet['secret'];
            $userWallet->link('user', $userModel);
            if (!(bool) $userWallet->id) {
                throw new \Exception('钱包添加失败');
            }
            foreach ($defaultWallet['balances'] as $balances) {
                $currencyModel = BCurrency::find()->where(['code' => strtolower($balances['currency'])]);
                if ($currencyModel->exists()) {
                    $currencyModel = $currencyModel->select('id')->one();
                } else {
                    $currencyModel = new BCurrency();
                    $currencyModel->code = strtolower($balances['currency']);
                    $currencyModel->save(false);
                }
                $userCurrencyModel = new BUserCurrency();
                $userCurrencyModel->user_id = $userModel->id;
                $userCurrencyModel->wallet_id = $userWallet->id;
                $userCurrencyModel->link('currency', $currencyModel);
            }
            $transaction->commit();
            return $this->respondJson(0, '钱包创建成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->respondJson(1, '钱包创建失败', $e->getMessage());
        }
    }

    public function actionTransfer()
    {
        $currencyId = $this->pInt('id', false);
        $userModel = $this->user;
        if (!$currencyId) {
            return $this->respondJson(1, '转出货币不能为空');
        }
        $amount = $this->pFloat('amount', 0);
        if (!$amount) {
            return $this->respondJson(1, '转出数量不能为空');
        }
        $receive = $this->pString('receive', false);
        if (!$receive) {
            return $this->respondJson(1, '转出地址不能为空');
        }
        $remark = $this->pString('remark', '');
        $userCurrencyModel = BUserCurrency::findOne(['user_id' => $userModel->id, 'currency_id' => $currencyId]);
        // 获取设置中币种相关设置
        $code = strtolower($userCurrencyModel->currency->code);
        $settingCurrency = 'currency_'.$code;
        $isIdentify = (bool) SettingService::get($settingCurrency, 'is_identify')->value;
        if ($isIdentify && !(bool) $userModel->is_identified) {
            return $this->respondJson(1, '未实名认证');
        }
        // 单笔最小数量
        $minAmount = (float) SettingService::get($settingCurrency, 'min_amount')->value;
        if ($amount < $minAmount) {
            return $this->respondJson(1, '单笔最小转账数量 '.$minAmount);
        }
        // 每日累计转账数量
        $secondMax = (float) SettingService::get($settingCurrency, 'one_second_max')->value;
        if ($amount > $secondMax) {
            return $this->respondJson(1, '每日单次最高转账数量 '.$secondMax);
        }
        $use_amount = floatval($userCurrencyModel->use_amount);
        if ($amount > $use_amount) {
            return $this->respondJson(1, '转出数量不能大于可用数量');
        }
        // 每日累计转账数量
        $dayMax = (float) SettingService::get($settingCurrency, 'one_day_max')->value;
        if ($dayMax > 0) {
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
            $currencyDetail = BUserCurrencyDetail::find()
            ->where(['currency_id' => $currencyId, 'user_id' => $userModel->id, 'type' => BUserCurrencyDetail::$TYPE_WITHDRAW])
            ->andWhere(['<=', 'create_time', $endToday])
            ->andWhere(['>=', 'create_time', $beginToday])
            ->sum('amount');
            if ((float) $currencyDetail > $dayMax) {
                return $this->respondJson(1, '每日累计转账数量 '.$dayMax);
            }
        }
        
        // 短信验证码
        if (\Yii::$app->params['sendSms']) {
            $vcode = $this->pString('vcode');

            //手机验证码是否正确, 有效期只有5分钟
            $returnInfo = ValidationCodeSmsService::checkValidateCode(
                $userModel->mobile,
                $vcode,
                BSmsAuth::$TYPE_TRANSFER_GET
            );
            if ($returnInfo->code != 0) {
                return $this->respondJson(1, $returnInfo->msg);
            }
        }
        // 后续补充转账操作

        return $this->respondJson(0, '转账成功');
    }
}
