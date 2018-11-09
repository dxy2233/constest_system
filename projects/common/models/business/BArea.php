<?php

namespace common\models\business;

class BArea extends \common\models\Area
{





    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }

    public static function getChild($id)
    {
        $data = self::find()->where(['parentid' => $id])->asArray()->all();
        $child_arr = [];
        foreach ($data as $v) {
            $child_arr[] = $v['areaname'];
        }
        return implode($child_arr, ',');
    }

    public static function getAreaName($id)
    {
        $data = self::find()->where(['id' => $id])->one();
        if ($data->parentid != 0) {
            return self::getAreaName($data->parentid). '->'. $data->areaname;
        } else {
            return $data->areaname;
        }
    }

    public static function getAreaOneName($id, $type)
    {
        $data = self::find()->where(['id' => $id])->one();
        if ($data->level == $type) {
            return $data->areaname;
        } elseif ($type == 2) {
            //最末级不用向上查
            return $data->areaname;
        } else {
            if ($data->parentid != 0) {
                return self::getAreaOneName($data->parentid, $type);
            } else {
                return '没有找到对应名称';
            }
        }
    }

    public static function getAllName($id)
    {
        $data = self::find()->where(['id' => $id])->one();
        $arr_level = array(
            0 => 'region',
            1 => 'province',
            2 => 'city',
            3 => 'qu'
        );

        if ($data->level == 0) {
            return array($arr_level[$data->level] => $data->id);
        } else {
            return array_merge(array($arr_level[$data->level] => $data->id), self::getAllName($data->parentid));
        }
    }
}
