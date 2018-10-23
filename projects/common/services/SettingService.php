<?php

namespace common\services;

use common\models\business\BSetting;

class SettingService extends ServiceBase
{
    const CACHE_KEY = 'setting';
    const EXPIRE_TIME = 10;
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
        $cache = \Yii::$app->cache;
        $query = BSetting::find()->filterWhere(['group' => $group, 'key' => $key]);
        if (!is_null($key)) {
            return $query->one() ?? (object) ['value' => null];
        }
        return $query->all();
    }

    // public static function getKey(string $group = null, string $key = null)
    // {
    //     $cache = \Yii::$app->cache;
    //     if ($cache->exists(self::CACHE_KEY)) {
    //         $settingCache = $cache->get(self::CACHE_KEY);
    //     }
    //     // ArrayHelper::s
    // }
    /**
     * 缓存所有设置数据
     *
     * @return void
     */
    public static function cacheSetting()
    {
        $cache = \Yii::$app->cache;

        $settingCache = $cache->getOrSet(self::CACHE_KEY, function ($cache) {
            return BSetting::find()->asArray()->all();
        }, 10000);
        return $settingCache;
    }

    public static function refresh()
    {
        $cache = \Yii::$app->cache;
        $cache->delete(self::CACHE_KEY);
        return self::getKey();
    }
}
