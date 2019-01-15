<?php

namespace common\models\business;

class BNodeUpgrade extends \common\models\NodeUpgrade
{
    const STATUS_WAIT = 0; //待审核
    const STATUS_ACTIVE = 1; //审核通过
    const STATUS_FAIL = 2; //审核失败

    public static function getStatus($key = 0)
    {
        $arr = [
            self::STATUS_WAIT => \Yii::t('app', '待审核'),
            self::STATUS_ACTIVE => \Yii::t('app', '已审核'),
            self::STATUS_FAIL => \Yii::t('app', '审核失败'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
        }

        return $arr;
    }

    
    /**
     * 节点下类型关联
     *  一对多
     * @return void
     */
    public function getNodeType()
    {
        return $this->hasOne(BNodeType::className(), ['id' => 'type_id']);
    }
    /**
     * 申请下用户关联
     *  一对多
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
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
