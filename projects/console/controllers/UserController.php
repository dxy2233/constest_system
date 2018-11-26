<?php

namespace console\controllers;

use Yii;
use common\services\UserService;
use common\models\business\BUserCurrency;

class UserController extends BaseController
{

    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    /**
     * 重置用户币种资金
     */
    public function actionResetCurrency($userId, $currencyId)
    {
        echo 'reset currency start.'.PHP_EOL;

        if (!$userId || !$currencyId) {
            echo 'params error.'.PHP_EOL;
        }

        $sign = UserService::resetCurrency($userId, $currencyId);
        if ($sign !== false) {
            echo 'success.'.PHP_EOL;
        } else {
            echo 'fail.'.PHP_EOL;
        }

        echo 'reset currency end.'.PHP_EOL;
    }

    /**
     * 重算所有用户及其币种资产
     *
     * @return void
     */
    public function actionResetUserCurrency()
    {
        $date = date('Y-m-d H:i:s', time());
        echo $date . 'reset user currency start.'.PHP_EOL;
        $userCurrencyModel = BUserCurrency::find()->orderBy(['user_id' => SORT_DESC])->all();
        foreach ($userCurrencyModel as $userCurrency) {
            $result = '';
            $result .= '用户ID：' . $userCurrency->user_id . '  用户积分ID：' . $userCurrency->currency_id . ' reset ';
            if ($sign = UserService::resetCurrency($userCurrency->user_id, $userCurrency->currency_id)) {
                $result .= 'success';
            } else {
                $result .= 'fail';
            }
            Yii::info($result, 'userCurrency');
            echo $result.PHP_EOL;
        }
        
        $date = date('Y-m-d H:i:s', time());
        echo $date . 'reset user currency end.'.PHP_EOL;
    }
}
