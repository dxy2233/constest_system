<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BCycle;

/**
 * Site controller
 */
class CycleController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'download',
            'finance-download',
            'frozen-download'
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    //获取未结束周期
    public function actionIndex()
    {
        $data = BCycle::find()->where(['>', 'tenure_end_time', time()])->orderBy('id desc')->asArray()->all();
        foreach ($data as &$v) {
            $v['cycle_start_time'] =  date('Y-m-d H:i:s', $v['cycle_start_time']);
            $v['cycle_end_time'] =  date('Y-m-d H:i:s', $v['cycle_end_time']);
            $v['tenure_start_time'] =  date('Y-m-d H:i:s', $v['tenure_start_time']);
            $v['tenure_end_time'] =  date('Y-m-d H:i:s', $v['tenure_end_time']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    // 获取已结束周期
    public function actionHistory()
    {
        $find = BCycle::find()->where(['<=', 'tenure_end_time', time()]);
        $count = $find->count();
        $page = $this->pInt('page', 1);
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->orderBy('id desc')->asArray()->all();
        foreach ($data as &$v) {
            $v['cycle_start_time'] =  date('Y-m-d H:i:s', $v['cycle_start_time']);
            $v['cycle_end_time'] =  date('Y-m-d H:i:s', $v['cycle_end_time']);
            $v['tenure_start_time'] =  date('Y-m-d H:i:s', $v['tenure_start_time']);
            $v['tenure_end_time'] =  date('Y-m-d H:i:s', $v['tenure_end_time']);
        }
        $return = ['list' => $data, 'count' => $count];
        return $this->respondJson(0, '获取成功', $return);
    }
    // 添加竞选周期
    public function actionCreateCycle()
    {
        $cycle_start_time = strtotime($this->pString('cycle_start_time'));
        if (empty($cycle_start_time)) {
            return $this->respondJson(1, '竞选开始时间不能为空');
        }
        $cycle_end_time = strtotime($this->pString('cycle_end_time'));
        if (empty($cycle_end_time)) {
            return $this->respondJson(1, '竞选结束时间不能为空');
        }
        $tenure_start_time = strtotime($this->pString('tenure_start_time'));
        if (empty($tenure_start_time)) {
            return $this->respondJson(1, '任职开始时间不能为空');
        }
        $tenure_end_time = strtotime($this->pString('tenure_end_time'));
        if (empty($tenure_end_time)) {
            return $this->respondJson(1, '任职结束时间不能为空');
        }
        if ($cycle_start_time > $cycle_end_time || $cycle_end_time > $tenure_start_time || $tenure_start_time > $tenure_end_time) {
            return $this->respondJson(1, '时间关系错误');
        }
        $cycle = new BCycle();
        $cycle->cycle_start_time = $cycle_start_time;
        $cycle->cycle_end_time = $cycle_end_time;
        $cycle->tenure_start_time = $tenure_start_time;
        $cycle->tenure_end_time = $tenure_end_time;
        if (!$cycle->save()) {
            return $this->respondJson(1, '添加失败', $cycle->getFirstErrorText());
        } else {
            return $this->respondJson(0, '添加成功');
        }
    }
    // 修改竞选周期
    public function actionUpdateCycle()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $cycle_start_time = strtotime($this->pString('cycle_start_time'));
        if (empty($cycle_start_time)) {
            return $this->respondJson(1, '竞选开始时间不能为空');
        }
        $cycle_end_time = strtotime($this->pString('cycle_end_time'));
        if (empty($cycle_end_time)) {
            return $this->respondJson(1, '竞选结束时间不能为空');
        }
        $tenure_start_time = strtotime($this->pString('tenure_start_time'));
        if (empty($tenure_start_time)) {
            return $this->respondJson(1, '任职开始时间不能为空');
        }
        $tenure_end_time = strtotime($this->pString('tenure_end_time'));
        if (empty($tenure_end_time)) {
            return $this->respondJson(1, '任职结束时间不能为空');
        }
        if ($cycle_start_time > $cycle_end_time || $cycle_end_time > $tenure_start_time || $tenure_start_time > $tenure_end_time) {
            return $this->respondJson(1, '时间关系错误');
        }
        $cycle = BCycle::find()->where(['id' => $id])->one();
        $cycle->cycle_start_time = $cycle_start_time;
        $cycle->cycle_end_time = $cycle_end_time;
        $cycle->tenure_start_time = $tenure_start_time;
        $cycle->tenure_end_time = $tenure_end_time;
        if (!$cycle->save()) {
            return $this->respondJson(1, '修改失败', $cycle->getFirstErrorText());
        } else {
            return $this->respondJson(0, '修改成功');
        }
    }

    public function actionDel()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $cycle = BCycle::find()->where(['id' => $id])->one();
        $cycle->delete();
        return $this->respondJson(0, '删除成功');
    }
}
