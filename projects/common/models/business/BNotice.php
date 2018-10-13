<?php

namespace common\models\business;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\services\SettingService;

class BNotice extends \common\models\Notice
{
    // 下架
    const STATUS_INACTIVE = 0;
    // 上架
    const STATUS_ACTIVE = 1;

    public static function getStatus($key = '')
    {
        $arr = [
            self::STATUS_INACTIVE => \Yii::t('app', '下架'),
            self::STATUS_ACTIVE => \Yii::t('app', '上架'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
        }

        return $arr;
    }

    /**
     * 自定义查询规则
     * 公用定义查询类
     * @return void
     */
    public static function find()
    {
        return new BaseQuery(get_called_class());
    }

    public static function getAppNoticeList(bool $isIndex = true, int $page = 1, int $pageSize = 15)
    {
        $isOpen = SettingService::get('notice', 'is_open');
        if (is_null($isOpen)) {
            return new FuncResult(1, "公告关闭");
        }
        if ((bool) $isOpen->value) {
            return new FuncResult(1, "公告未启用");
        }

        $setting = SettingService::get('notice', 'show_count');
        $query = self::find()
        ->select(['title', 'desc', 'type', 'image', 'url', 'click', 'sort', 'create_time'])
        ->active()
        ->startAndEndTime();
        if ($isIndex) {
            $query->limit($setting->value);
        } else {
            $count = $query->count();
            $query->limit($pageSize)->offset(($page - 1) * $pageSize);
        }
        
        // ->createCommand()->getRawSql();
        // ->orderBy([
        //     'sort' => SORT_ASC,
        //     'create_time' => SORT_DESC,
        // ])
        
        $noticeList = $query->asArray()->all();
        foreach ($noticeList as $key => &$notice) {
            $notice['image'] = FuncHelper::getImageUrl($notice['image']);
        }
        ArrayHelper::multisort($noticeList, ['sort', 'create_time'], [SORT_ASC, SORT_DESC]);
        if (!$isIndex) {
            $noticeList = [
                'list' => $noticeList,
                'count' => $count,
            ];
        }
        return new FuncResult(0, "获取成功", $noticeList);
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
