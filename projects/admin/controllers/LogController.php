<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BNotice;
use common\models\business\BUserOther;
use common\models\business\BArea;
use common\models\business\BUser;
use common\models\business\BUserIdentify;
use common\models\business\BAdminRule;
use common\models\business\BAdminLog;
use common\models\business\BAdminUser;

/**
 * Site controller
 */
class LogController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'download'
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    public function actionIndex()
    {
        $username = $this->pString('username');
        $str_time = $this->pString('strTime');
        $end_time = $this->pString('endTime');
        $page = $this->pInt('page', 1);
        $find = BAdminLog::find()
        ->from(BAdminLog::tableName()." A")
        ->join('left join', BAdminUser::tableName().' B', 'A.user_id = B.id');
        if ($username != '') {
            $find->andWhere(['or', ['like', 'B.real_name', $username], ['like', 'B.name', $username]]);
        }
        if ($str_time != '') {
            $find->startTime($str_time);
        }
        if ($end_time != '') {
            $find->endTime($end_time);
        }
        $find->page($page);
        $data = $find->select(['A.create_time', 'B.department', 'A.route', 'A.ip', 'B.real_name'])->orderBy('A.create_time desc')->asArray()->all();
        $rule = [];
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['route'] = substr($v['route'], 1);
            if (empty($rule[$v['route']])) {
                $this_rule = BAdminRule::find()->where(['like', 'url', $v['route']])->one();
                if ($this_rule) {
                    $p_rule = BAdminRule::find()->where(['id' => $this_rule->parent_id])->one();
                    $rule[$v['route']]['controller'] = $p_rule->name;
                    $rule[$v['route']]['action'] = $this_rule->name;
                } else {
                    $rule[$v['route']]['controller'] = $rule[$v['route']]['action'] = '-';
                }
            }
            $v['controller'] = $rule[$v['route']]['controller'];
            $v['action'] = $rule[$v['route']]['action'];
        }
        return $this->respondJson(0, "获取成功", $data);
    }

    public function actionDownload()
    {
        $username = $this->pString('username');
        $str_time = $this->pString('strTime');
        $end_time = $this->pString('endTime');
        $find = BAdminLog::find()
        ->from(BAdminLog::tableName()." A")
        ->join('left join', BAdminUser::tableName().' B', 'A.user_id = B.id');
        if ($username != '') {
            $find->andWhere(['or', ['like', 'B.real_name', $username], ['like', 'B.name', $username]]);
        }
        if ($str_time != '') {
            $find->startTime($str_time);
        }
        if ($end_time != '') {
            $find->endTime($end_time);
        }
        $data = $find->select(['A.create_time', 'B.department', 'A.route', 'A.ip', 'B.real_name'])->orderBy('A.create_time desc')->asArray()->all();
        $rule = [];
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['route'] = substr($v['route'], 1);
            if (empty($rule[$v['route']])) {
                $this_rule = BAdminRule::find()->where(['like', 'url', $v['route']])->one();
                if ($this_rule) {
                    $p_rule = BAdminRule::find()->where(['id' => $this_rule->parent_id])->one();
                    $rule[$v['route']]['controller'] = $p_rule->name;
                    $rule[$v['route']]['action'] = $this_rule->name;
                } else {
                    $rule[$v['route']]['controller'] = $rule[$v['route']]['action'] = '-';
                }
            }
            $v['controller'] = $rule[$v['route']]['controller'];
            $v['action'] = $rule[$v['route']]['action'];
        }
        $headers = ['create_time'=> '操作时间','real_name' => '操作人', 'department' => '部门', 'controller' => '操作模块', 'action' => '操作内容', 'ip' => '操作设备IP'];

        $down = $this->download($data, $headers, '日志列表'.date('YmdHis'));
        if (!$down) {
            exit('验证失败');
        }
        return;
    }
}
