<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;

class BUserCurrency extends \common\models\UserCurrency
{
    public function behaviors()
    {
        return [
            [
                // 自动添加时间
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    self::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    # 修改之前
                    self::EVENT_BEFORE_UPDATE => ['update_time']
                ],
                #设置默认值
                'value' => NOW_TIME
            ]
        ];
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
