<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;

/**
 * Site controller
 */
class UserController extends BaseController
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
        $find = BUser::find();
        $pageSize = $this->pInt('pageSize');
        $page = $this->pInt('page');
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['like','username',$searchName]);
        }
        $str_time = $this->pString('str_time');
        if ($str_time != '') {
            $find->startTime($str_time, 'create_time');
        }
        $end_time = $this->pString('end_time');
        if ($end_time != '') {
            $find->endTime($end_time, 'create_time');
        }
        $count = $find->count();
        $list = $find->page($page)->asArray()->all();
        foreach ($list as &$v) {
            $node = BNode::find()
            ->from(BNode::tableName()." A")
            ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
            ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $v['id']])->one();
            if ($node) {
                $v['userType'] = $node->name;
                $v['nodeName'] = $node->nodeName;
            } else {
                $v['userType'] = '普通用户';
                $v['nodeName'] = '——';
            }
        }
        $return = ['count' => $count, 'list' => $list];
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionGetOneUser()
    {
        $userId = $this->pInt('userId');
        $user = BUser::find()->where(['id' => $userId])->one();
        if (empty($user)) {
            return $this->respondJson(1, '不存在的用户');
        }
        $info = [];
        $info['username'] = $user->username;
        $info['mobile'] = $user->mobile;
        $node = BNode::find()
        ->from(BNode::tableName()." A")
        ->join('inner join', 'gr_node_type B', 'A.type_id = B.id')
        ->select(['B.name', 'A.name as nodeName'])->where(['A.user_id' => $userId])->one();
        if ($node) {
            $info['userType'] = $node->name;
            $info['nodeName'] = $node->nodeName;
        } else {
            $info['userType'] = '普通用户';
            $info['nodeName'] = '——';
        }
    }
}
