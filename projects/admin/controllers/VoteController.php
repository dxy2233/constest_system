<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use common\services\UserService;
use common\services\SettingService;
use common\services\JobService;
use yii\helpers\ArrayHelper;
use common\models\business\BSetting;
use common\models\business\BVote;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BNotice;
use common\task\TestJob;

/**
 * Site controller
 */
class VoteController extends BaseController
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
        $find = BVote::find()
        ->from(BVote::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BNode::tableName().' C', 'A.node_id = C.id')
        ->select(['A.*','B.mobile','C.name']);
        $searchName = $this->pString('searchName', '');
        if ($searchName != '') {
            $find->andWhere(['or',['like', 'B.mobile',$searchName],['like','C.name', $searchName]]);
        }
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.vote_number', 2 => 'A.type', 3 => 'A.create_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $find->orderBy($order. ' DESC');
        $count = $find->count();
        $page = $this->pInt('page', 0);
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['type'] = BVote::getType($v['type']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
    public function actionDownload()
    {
        $find = BVote::find()
        ->from(BVote::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BNode::tableName().' C', 'A.node_id = C.id')
        ->select(['A.*','B.mobile','C.name']);
        $searchName = $this->gString('searchName', '');
        if ($searchName != '') {
            $find->andWhere(['or',['like', 'B.mobile',$searchName],['like','C.name', $searchName]]);
        }
        $str_time = $this->gString('str_time', '');
        $end_time = $this->gString('end_time', '');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $order = $this->gString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.vote_number', 2 => 'A.type', 3 => 'A.create_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $find->orderBy($order. ' DESC');

        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['type'] = BVote::getType($v['type']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        $headers = ['mobile'=> '投票用户', 'name' => '投票节点名称', 'vote_number' => '投出票数', 'type' => '投票方式', 'create_time' => '投票时间'];
        $this->download($data, $headers);
        return;
    }
    /**
     * Displays homepage.
     *  修改配置
     * @return string
     */
    public function actionSetVote()
    {
        $time = BSetting::find()->where(['key' => 'count_time'])->one();
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_VOTE])->all();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            $post_item = $this->pString($v->key, '');
            if ($post_item == '') {
                continue;
            }
            if (strstr($v->key, 'time')) {
                $post_item = strtotime($post_item);
            }

            $v->value = $post_item;
            
            if (!$v->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, "操作失败", $v->getFirstErrorText());
            }
        }
        SettingService::refresh();
        $transaction->commit();
        return $this->respondJson(0, "操作成功");
    }

    public function actionNowReload()
    {
        $bool = JobService::beginPut(1);
        if ($bool) {
            return $this->respondJson(0, "操作成功");
        } else {
            return $this->respondJson(1, "操作失败");
        }
    }
    public function actionGetSettingList()
    {
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_VOTE])->orderBy('sort')->asArray()->all();
        foreach ($data as &$v) {
            $v['initialize'] = json_decode($v['initialize'], true);
            if (strstr($v['key'], 'time')) {
                $v['value'] = date('Y-m-d H:i:s', (int)$v['value']);
            } else {
                $v['value'] = float($v['value']);
            }
        }
        return $this->respondJson(0, "获取成功", $data, false);
    }

    public function actionGetVoteOrder()
    {
        $type = $this->pInt('type', 0);
        $find = BVote::find()
        ->from(BVote::tableName()." A")
        ->select(['B.mobile','sum(A.vote_number) as num','A.create_time'])
        ->where(['A.status' => BNotice::STATUS_ACTIVE])
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->groupBy(['A.user_id'])
        ->orderBy('sum(A.vote_number) desc');
        if ($type != 0) {
            $find->andWhere(['A.type' =>$type]);
        }
        $end_time = $this->pString('end_time', '');
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $count = $find->count();
        $page = $this->pInt('page', 0);
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
}
