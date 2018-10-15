<?php

namespace common\models\business;

class BNodeType extends \common\models\NodeType
{



    /**
     * 节点类型下选举列表
     *  一对多
     * @return void
     */
    public function getNodes()
    {
        return $this->hasMany(BNode::className(), ['type_id' => 'id']);
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
