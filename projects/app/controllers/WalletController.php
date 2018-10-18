<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\services\SettingService;
use common\models\business\BSmsAuth;
use common\models\business\BUserLog;
use common\models\business\BUserCurrency;

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
        FuncHelper::arrayForget($userCurrency, 'currency');
        return $this->respondJson(0, '获取成功', $userCurrency);
    }

    // public function actionCurrency

    /**
     * 创建支付密码
     *
     * @return void
     */
    public function actionCreate()
    {
        $userModel = $this->user;
        if (empty($userModel->trans_password)) {
            return $this->respondJson(1, '支付密码不存在');
        }
        return $this->respondJson(0, '支付密码设置成功');
    }
}
