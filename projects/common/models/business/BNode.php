<?php

namespace common\models\business;

use common\components\FuncHelper;

class BNode extends \common\models\Node
{
    // 定义首页默展示 10 个节点
    const INDEX_NUMBER = 10;

    const STATUS_OFF = 0; //停用
    const STATUS_ON = 1; //正常
    const STATUS_WAIT = 2; //待审核
    const STATUS_UNDO = 3; //撤回
    const STATUS_NO = 4; //未通过

    public static function getStatus($key = 0)
    {
        $arr = [
            self::STATUS_OFF => \Yii::t('app', '停用'),
            self::STATUS_ON => \Yii::t('app', '已审核'),
            self::STATUS_WAIT => \Yii::t('app', '待审核'),
            self::STATUS_UNDO => \Yii::t('app', '撤回'),
            self::STATUS_NO => \Yii::t('app', '未通过'),
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
     * 节点下投票关联
     *  一对多
     * @return void
     */
    public function getVotes()
    {
        return $this->hasMany(BVote::className(), ['node_id' => 'id']);
    }

    /**
     * 节点下用户关联
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
