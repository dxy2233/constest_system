<?php

namespace common\models\business;

class BNodeRecommend extends \common\models\NodeRecommend
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

    /** 已无用
     * 对应推荐节点
     *  一对多
     * @return void
     */
    public function getNode()
    {
        return $this->hasOne(BNode::className(), ['id' => 'node_id']);
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
