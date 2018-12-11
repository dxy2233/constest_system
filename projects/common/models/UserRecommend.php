<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_recommend}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $parent_id 推荐人ID
 * @property int $create_time 推荐时间
 */
class UserRecommend extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_recommend}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'parent_id'], 'required'],
            [['user_id', 'parent_id', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'parent_id' => Yii::t('app', '推荐人ID'),
            'amount' => Yii::t('app', '投票数量'),
            'create_time' => Yii::t('app', '推荐时间'),
        ];
    }
}
