<?php

namespace common\models\business;

class BUserRecommend extends \common\models\UserRecommend
{

    /**
     * 对应用户
     *  一对多
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    /**
     * 对应推荐节点
     *  一对多
     * @return void
     */
    public function getParent()
    {
        return $this->hasOne(BUser::className(), ['id' => 'parent_id']);
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
