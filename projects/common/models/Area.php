<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%area}}".
 *
 * @property int $id ID
 * @property string $areaname 栏目名
 * @property int $parentid 父栏目
 * @property string $shortname
 * @property string $lng
 * @property string $lat
 * @property int $level 1.省 2.市 3.区 4.镇
 * @property string $position
 * @property int $sort 排序
 *
 * @property Area $parent
 * @property Area[] $areas
 * @property UserCity[] $userCities
 * @property UserProvince[] $userProvinces
 * @property UserRegion[] $userRegions
 */
class Area extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%area}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['areaname'], 'required'],
            [['areaname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'areaname' => '区域名',
            'parentid' => '父区域',
            'shortname' => 'Shortname',
            'lng' => 'Lng',
            'lat' => 'Lat',
            'level' => '1.省 2.市 3.区 4.镇',
            'position' => 'Position',
            'sort' => '排序',
        ];
    }
}
