<?php

namespace common\models\business;

use common\models\business\Traits\UserCurrencyTrait;
use yii\behaviors\TimestampBehavior;

class BUserCurrencyFrozen extends \common\models\UserCurrencyFrozen
{
    use UserCurrencyTrait;

    // 解冻
    const STATUS_THAW = 0;
    // 冻结
    const STATUS_FROZEN = 1;

    /**
     * 获取所有状态 类型
     *
     * @return void
     */
    public static function getStatus(int $key = null)
    {
        $arr = [
            static::STATUS_THAW => \Yii::t('app', '解冻'),
            static::STATUS_FROZEN => \Yii::t('app', '冻结'),
        ];
        if (!is_null($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        }

        return $arr;
    }

    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
