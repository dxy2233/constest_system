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
        ->select(['title', 'create_time', 'start_time', 'end_time', 'detail', 'desc', 'type'])
        ->active(BNotice::STATUS_ACTIVE)
        ->where(['id' => $noticeId])
        ->one();
        if (is_object($notice) && $notice->type === BNotice::TYPE_URL) {
            return $this->respondJson(0, '获取公告失败或者该公告为链接');
        }
        $data = ArrayHelper::toArray($notice);
        // 模型中格式化时间
        $data['create_time'] = $notice->createTimeText;
        $data['start_time'] = $notice->startTimeText;
        $data['end_time'] = $notice->endTimeText;
        return $this->respondJson(0, '获取成功', $data);
    }
}
