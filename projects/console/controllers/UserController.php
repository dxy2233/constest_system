<?php

namespace console\controllers;

use common\services\UserService;

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
}
