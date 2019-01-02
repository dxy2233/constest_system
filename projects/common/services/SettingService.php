<?php

namespace common\services;

use yii\helpers\ArrayHelper;
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

    /** (暂无用不删除，防止出错后复用)
     * 获取设置
     *
     * @param string $group 为空获取所有设置项
     * @return void
     */
    public static function get(string $group = null, string $key = null)
    {
        $cache = \Yii::$app->cache;
        $query = BSetting::find()->filterWhere(['group' => $group, 'key' => $key])->cache(15);
        if (!is_null($key)) {
            return $query->one() ?? (object) ['value' => null];
        }
        return $query->all();
    }

    /**
     * 改进 获取 设置 key 值
     *
     * @param string $group
     * @param string $key
     * @return void
     */
    public static function getNew(string $group = null, string $key = null)
    {
        $cache = \Yii::$app->cache;
        if ($cache->exists(self::CACHE_KEY)) {
            $settingCache = $cache->get(self::CACHE_KEY);
        } else {
            $settingCache = self::cacheSetting();
        }
        $settingGroup = ArrayHelper::map($settingCache, 'key', 'value', 'group');
        $data = [];
        if (is_null($group)) {
            $data = $settingGroup;
        }
        if (!is_null($key)) {
            $value = isset($settingGroup[$group][$key]) ? $settingGroup[$group][$key] : null;
            $data = ['value' => json_decode($value, true)];
        }
        return (object) $data;
    }
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

    /**
     * 刷新设置缓存
     *
     * @return void
     */
    public static function refresh()
    {
        $cache = \Yii::$app->cache;
        $cache->delete(self::CACHE_KEY);
        return self::cacheSetting();
    }
}
