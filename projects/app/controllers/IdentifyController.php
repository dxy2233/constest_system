<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\services\SettingService;
use common\models\business\BUserIdentify;

class IdentifyController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 无需需登录访问 为空则所有相关接口都需登录后访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 实名认证结果展示
     *
     * @return void
     */
    public function actionIndex()
    {
        // 返回容器
        $data = [];
        $userModel = $this->user;
        
        $identify = $userModel->identify;
        if (is_null($identify)) {
            return $this->respondJson(0, "未实名认证");
        }
        
        $data['status'] = $identify->status;
        $data['status_remark'] = $identify->status_remark;
        // 前端查看状态
        // $data['status_list'] = BUserIdentify::getStatus();
        switch ($identify->status) {
            case BUserIdentify::STATUS_FAIL:
                $data['remark'] = $identify->remark;
                break;
            case BUserIdentify::STATUS_INACTIVE:
                break;
            case BUserIdentify::STATUS_ACTIVE:
                $data['realname'] = $identify->realname;
                $data['number'] = substr_replace($identify->number, '*************', 3, 13);
                break;
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 实名认证提交
     *
     * @return void
     */
    public function actionSubmit()
    {
        $userModel = $this->user;
        $identify = $userModel->identify;
        $isOpen = SettingService::get('user', 'is_open')->value;
        if (is_null($isOpen) || !(bool) $isOpen) {
            return $this->respondJson(1, "实名认证未启用");
        }
        if (is_null($identify)) {
            $identify = new BUserIdentify();
        } elseif ($identify->status == BUserIdentify::STATUS_FAIL) {
            $identify = $userModel->getIdentify()->one();
        } else {
            return $this->respondJson(1, '不能再次提交');
        }
        $postData = \Yii::$app->request->post();
        $postData['pic_front'] = $this->pImage('pic_front');
        $postData['pic_back'] = $this->pImage('pic_back');
        $identify->load(['BUserIdentify' => $postData]);
        if (!$identify->validate()) {
            return $this->respondJson(1, $identify->getFirstError());
        }
        $identify->status = (int) SettingService::get('user', 'has_identify')->value;
        $identify->status_remark = BUserIdentify::getStatus($identify->status);
        $identify->user_id = $userModel->id;
        if (!$identify->save()) {
            return $this->respondJson(1, $identify->getFirstError());
        }
        $identify->pic_front = $identify->picFrontText;
        $identify->pic_back = $identify->picBackText;
        $identify = $identify->toArray();
        // 不能回传的数据排除
        FuncHelper::arrayForget($identify, ['update_time', 'create_time', 'audit_time', 'audit_admin_id', 'status_remark', 'id', 'status']);
        return $this->respondJson(0, '提交成功', $identify);
    }
}
