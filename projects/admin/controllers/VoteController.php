<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BSetting;
use common\models\business\BVote;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BNotice;

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
            $find->andWhere(['or',['B.mobile','like',$searchName],['C.name','like', $searchName]]);
        }
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $count = $find->count();
        $page = $this->pInt('page', 1);
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionSetVote()
    {
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_VOTE])->all();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            $post_item = $this->pString($v->key, '');
            if ($post_item == '') {
                continue;
            }
            $v->value = $post_item;
            
            if (!$v->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, "操作失败", $v->getFirstErrorText());
            }
        }

        $transaction->commit();
        return $this->respondJson(0, "操作成功");
    }


    public function actionGetSettingList()
    {
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_VOTE])->asArray()->all();
        return $this->respondJson(0, "获取成功", $data);
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
        $page = $this->pInt('page', 1);
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
