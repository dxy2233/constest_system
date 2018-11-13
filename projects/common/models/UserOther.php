<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_other}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $consignee 收货人姓名
 * @property string $consignee_mobile 收货人电话
 * @property int $area_province_id 省级ID
 * @property int $area_city_id 市级ID
 * @property string $address 详细地址
 * @property int $zip_code 邮编
 * @property string $weixing 微信号
 * @property string $recommend_mobile 推荐人手机号
 * @property string $recommend_name 推荐人姓名
 * @property string $grt_address 贵人通地址
 * @property string $tt_address 茶通地址
 * @property string $bpt_address 美食通地址
 * @property int $create_time
 * @property int $update_time
 */
class UserOther extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_other}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'area_province_id', 'area_city_id', 'zip_code', 'create_time', 'update_time'], 'integer'],
            [['consignee'], 'string', 'max' => 64],
            [['consignee_mobile', 'recommend_mobile', 'recommend_name'], 'string', 'max' => 32],
            [['address', 'weixing'], 'string', 'max' => 128],
            [['grt_address', 'tt_address', 'bpt_address'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'consignee' => '收货人姓名',
            'consignee_mobile' => '收货人电话',
            'area_province_id' => '省级ID',
            'area_city_id' => '市级ID',
            'address' => '详细地址',
            'zip_code' => '邮编',
            'weixing' => '微信号',
            'recommend_mobile' => '推荐人手机号',
            'recommend_name' => '推荐人姓名',
            'grt_address' => '贵人通地址',
            'tt_address' => '茶通地址',
            'bpt_address' => '美食通地址',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
