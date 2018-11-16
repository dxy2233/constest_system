<?php

namespace common\services;

use common\models\UploadForm;
use common\models\business\BUpdateData;
use common\models\business\BUpdateLog;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserCurrencyDetail;

class UpdateService extends ServiceBase
{


    /**
     * 上传图片
     */
    public static function begin()
    {
        // 读取需要修改的数据
        $data = BUpdateData::find()->where(['status' => 0])->all();
        $transaction = \Yii::$app->db->beginTransaction();
        $msg = [];
        foreach ($data as $v) {
            $user = BUser::find()->where(['mobile' => $data->mobile])->one();
            if (!$user) {
                $msg[] = '用户不存在';
                continue;
            }
        }
        if (count($msg) > 0) {
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'history');
            return false;
        } else {
            $transaction->commit();
            Yii::info('执行成功', 'history');
            return true;
        }
    }
}
