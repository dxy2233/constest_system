<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BNotice;
use common\services\SettingService;

class NoticeController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 需登录访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    /**
     * 公告列表
     *
     * @return void
     */
    public function actionIndex()
    {
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $result = BNotice::getAppNoticeList(false, $page, $pageSize);
        return $this->respondJson($result->code, $result->msg, $result->content);
    }

    public function actionInfo()
    {
        $noticeId = $this->pInt('id', 0);
        $notice = BNotice::find()
        // ->select(['title', 'create_time', 'start_time', 'end_time', 'detail', 'desc', 'type', 'url', 'click'])
        ->active(BNotice::STATUS_ACTIVE)
        // ->hasStartAndEndTime()
        ->where(['id' => $noticeId])
        ->cache(15)
        ->one();
        if (!is_object($notice)) {
            return $this->respondJson(1, '获取公告失败');
        }
        $notice->updateCounters(['click' => 1]);
        $data = ArrayHelper::toArray($notice);
        $data['type_str'] = BNotice::getType($notice->type);
        // 模型中格式化时间
        $data['create_Time'] = $notice->createTimeText;
        $data['update_Time'] = $notice->updateTimeText;
        $data['start_Time'] = $notice->startTimeText;
        $data['end_time'] = $notice->endTimeText;
        unset($data['sort']);
        // 转换驼峰命名
        return $this->respondJson(0, '获取成功', $data);
    }
}
