<?php

namespace common\models\business;

class BUserOther extends \common\models\UserOther
{
    // 节点申请
    const SCENARIO_APPLY = 'apply';
    // 地址编辑
    const SCENARIO_ADDRESS = 'address';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // 验证节点申请
        $scenarios[self::SCENARIO_APPLY] = ['grt_address', 'tt_address', 'bpt_address', 'weixing', 'recommend_mobile', 'recommend_name'];
        // 验证地址添加
        $scenarios[self::SCENARIO_ADDRESS] = ['consignee', 'consignee_mobile', 'area_province_id', 'area_city_id', 'address', 'zip_code'];
        return $scenarios;
    }

    /**
    * 自定义 label
    * @return array
    */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['consignee_mobile', 'recommend_mobile'], 'match', 'pattern' => '/^1\d{10}$/'],
            [['user_id', 'grt_address', 'tt_address', 'bpt_address', 'weixing', 'area_province_id', 'area_city_id', 'address', 'consignee_mobile', 'consignee'], 'required']
        ]);
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
