<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BVoucher;

class VoucherService extends ServiceBase
{
    public static function createNewVoucher($user_id, $node_id, $voucher_num)
    {
        $voucher = new BVoucher();
        $voucher->user_id = $user_id;
        $voucher->node_id = $node_id;
        $voucher->voucher_num = $voucher_num;
        if (!$voucher->save()) {
            $transaction->rollBack();
            return new FuncResult(1, $voucher->getFirstErrorText());
        }
        UserService::resetVoucher($user_id);
        return new FuncResult(0, '成功', $voucher);
    }
}
