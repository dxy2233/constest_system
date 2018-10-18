<?php

namespace common\models\business;

use common\components\FuncHelper;

class BNode extends \common\models\Node
{
    // 定义首页默展示 10 个节点
    const INDEX_NUMBER = 10;


    /**
     * 节点下投票选举列表
     *  一对多
     * @return void
     */
    public function getNodeType()
    {
        return $this->hasOne(BNodeType::className(), ['id' => 'type_id']);
    }
    /**
     * 节点下投票选举列表
     *  一对多
     * @return void
     */
    public function getVotes()
    {
        return $this->hasMany(BVote::className(), ['node_id' => 'id']);
    }

    /**
     * 节点下投票选举列表
     *  一对一
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }
    /**
     * 转换LOGO链接
     *
     * @return void
     */
    public function getLogoText()
    {
        return FuncHelper::getImageUrl($this->logo);
    }
    /**
     * 转换 任职状态
     *
     * @return void
     */
    public function getIsTenureText()
    {
        return (bool) $this->is_tenure;
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
