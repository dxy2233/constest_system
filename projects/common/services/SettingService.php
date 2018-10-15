<?php

namespace common\services;

use common\models\business\BSetting;

class SettingService extends ServiceBase
{
    public static function getInstance(): SettingService
    {
        self::$instance = new self();
        self::$instance->init();

        return self::$instance;
    }

    /**
     * 获取设置
     *
     * @param string $group 为空获取所有设置项
     * @return void
     */
    public static function get(string $group = null, string $key = null)
    {
        $query = BSetting::find()->filterWhere(['group' => $group, 'key' => $key]);
        if (!is_null($key)) {
            return $query->one() ?? (object) ['value' => null];
        }
        return $query->all();
    }
}
