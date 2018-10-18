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
    // 删除
    const STATUS_DELETE = 2;

    public static function getStatus($key = '')
    {
        $arr = [
            self::STATUS_INACTIVE => \Yii::t('app', '下架'),
            self::STATUS_ACTIVE => \Yii::t('app', '上架'),
            self::STATUS_DELETE => \Yii::t('app', '删除'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
        }

        return $arr;
    }
    // 跳转
    const TYPE_URL = 0;
    // 文章页
    const TYPE_ARTICLE = 1;

    public static function getType($key = '')
    {
        $arr = [
            self::TYPE_URL => \Yii::t('app', '跳转'),
            self::TYPE_ARTICLE => \Yii::t('app', '文章页'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
        }

        return $arr;
    }

    public static function getAppNoticeList(bool $isIndex = true, int $page = 1, int $pageSize = 15)
    {
        $isOpen = SettingService::get('notice', 'is_open');
        if (is_null($isOpen)) {
            return new FuncResult(1, "公告关闭");
        }
        if (!(bool) $isOpen->value) {
            return new FuncResult(1, "公告未启用");
        }

        $setting = SettingService::get('notice', 'show_count');
        $query = self::find()
        ->select(['id', 'title', 'desc', 'type', 'image', 'url', 'click', 'sort', 'create_time'])
        ->active()
        ->hasStartAndEndTime();
        if ($isIndex) {
            $query->limit($setting->value);
        } else {
            $count = $query->count();
            $query->page($page, $pageSize);
        }
        
        // ->createCommand()->getRawSql();
        // ->orderBy([
        //     'sort' => SORT_ASC,
        //     'create_time' => SORT_DESC,
        // ])
        
        $noticeList = $query->asArray()->all();
        ArrayHelper::multisort($noticeList, ['sort', 'create_time'], [SORT_ASC, SORT_DESC]);
        foreach ($noticeList as $key => &$notice) {
            $notice['image'] = FuncHelper::getImageUrl($notice['image']);
            unset($notice['create_time']);
        }
        if (!$isIndex) {
            $noticeList = [
                'list' => $noticeList,
                'count' => $count,
            ];
        }
        
        return new FuncResult(0, "获取成功", $noticeList);
    }

    /**
     * 输出时间时自动格式化
     *
     * @return void
     */
    public function getStartTimeText()
    {
        // return \Yii::$app->formatter->asDatetime($this->create_time);
        return date('Y-m-d H:i:s', $this->start_time);
    }
    /**
     * 输出时间时自动格式化
     *
     * @return void
     */
    public function getEndTimeText()
    {
        // return \Yii::$app->formatter->asDatetime($this->create_time);
        return date('Y-m-d H:i:s', $this->end_time);
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
