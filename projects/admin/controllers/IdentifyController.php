<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BUserIdentify;
use common\models\business\BUserWallet;
use common\models\business\BUserVoucher;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserRecommend;
use common\components\FuncHelper;

/**
 * Site controller
 */
class IdentifyController extends BaseController
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
        $find = BUser::find()
        ->from(BUser::tableName()." A")
        ->join('left join', BUserIdentify::tableName().' B', 'B.user_id = A.id');
        $page = $this->pInt('page');
        $status = $this->pInt('status', 0);
        $find->andWhere(['B.status' => $status]);
        $find->select(['A.mobile','B.realname','B.number','B.status','B.create_time','A.id']);
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.username',$searchName],['like', 'A.mobile', $searchName],['like', 'B.number', $searchName]]);
        }
        $count = $find->count();
        $list = $find->page($page)->asArray()->all();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['status'] = BUserIdentify::getStatus($v['status']);
        }
        $return = ['count' => $count, 'list' => $list];
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionDetail()
    {
        $user_id = $this->pInt('user_id');
        if (empty($user_id)) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        $data = BUserIdentify::find()->where(['user_id' => $user_id])->asArray()->one();
        if (empty($data)) {
            return $this->respondJson(1, '此用户没有实名信息');
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    public function actionCheckNo()
    {
    }
}
