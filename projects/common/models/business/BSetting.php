<?php

namespace common\models\business;

class BSetting extends \common\models\Setting
{
    public static $GROUP_NOTICE = 'notice'; // 公告
    public static $GROUP_NODE = 'node'; // 节点
    public static $GROUP_VOTE = 'vote'; // 投票
    public static $GROUP_USER = 'user'; // 用户



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
