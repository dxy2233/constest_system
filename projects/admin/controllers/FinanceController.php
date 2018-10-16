<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BNotice;
use common\models\business\BCurrency;
use common\models\business\BUserCurrency;
use common\models\business\BUser;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;

/**
 * Site controller
 */
class FinanceController extends BaseController
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
    //资产管理
    public function actionIndex()
    {
        $find = BUserCurrency::find()
        ->from(BUserCurrency::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BUserWallet::tableName().' C', 'A.wallet_id = C.id')
        ->join('left join', BCurrency::tableName().' D', 'A.currency_id = C.id')
        ->select(['A.*','B.mobile','C.wallet','D.name']);
        $searchName = $this->pString('searchName', '');
        if ($searchName != '') {
            $find->andWhere(['B.mobile','like',$searchName]);
        }
        $currency_id = $this->pInt('currency_id', 0);
        if ($currency_id != 0) {
            $find->andWhere(['A.currency_id' => $currency_id]);
        }
        $type = $this->pInt('type');
        if ($type == 3) {
            $field = 'A.use_amount';
        } elseif ($type == 2) {
            $field = 'A.frozen_amount';
        } else {
            $field = 'A.position_amount';
        }
        $min = $this->pFloat('min', 0);
        $max = $this->pFloat('max', 0);
        if ($min != 0) {
            $find->andWhere(['>=', $field, $min]);
        }
        if ($max != 0) {
            $find->andWhere(['<=', $field, $max]);
        }

        $count = $find->count();

        $order = $this->pString('order');
        if ($order != '') {
            if ($type == 1) {
                $order = 'A.currency_id';
            } elseif ($type == 2) {
                $order = 'A.position_amount';
            } elseif ($order == 3) {
                $order = 'A.frozen_amount';
            } else {
                $order = 'A.use_amount';
            }
            $find->orderBy($order. ' desc');
        }


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
    
    // 币种列表

    public function actionGetCurrencyList()
    {
        $data = BCurrency::find()->active(BNotice::STATUS_ACTIVE)->asArray()->all();
        return $this->respondJson(0, "获取成功", $data);
    }

    // 锁仓记录
    public function actionGetFrozenList()
    {
        $find = BUserCurrencyFrozen::find()
        ->from(BUserCurrencyFrozen::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BCurrency::tableName().' D', 'A.currency_id = D.id')
        ->join('left join', BUserCurrency::tableName().' E', 'A.currency_id = E.currency_id && A.user_id = E.user_id')
        ->join('left join', BUserWallet::tableName().' F', 'E.wallet_id = F.id')
        ->select(['A.*','B.mobile','D.name','F.wallet']);
        $searchName = $this->pString('searchName', '');
        if ($searchName != '') {
            $find->andWhere(['B.mobile','like',$searchName]);
        }
        $currency_id = $this->pInt('currency_id', 0);
        if ($currency_id != 0) {
            $find->andWhere(['A.currency_id' => $currency_id]);
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
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }

    // 财务流水
    public function actionGetFinanceList()
    {
        $in_arr = array(3);
        $out_arr =array(1,2);
        $find = BUserCurrencyDetail::find()
        ->from(BUserCurrencyDetail::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->join('left join', BCurrency::tableName().' D', 'A.currency_id = D.id')
        ->join('left join', BUserCurrency::tableName().' E', 'A.currency_id = E.currency_id && A.user_id = E.user_id')
        ->join('left join', BUserWallet::tableName().' F', 'E.wallet_id = F.id')
        ->select(['A.*','B.mobile','D.name','F.wallet']);
        $searchName = $this->pString('searchName', '');
        if ($searchName != '') {
            $find->andWhere(['B.mobile','like',$searchName]);
        }
        $currency_id = $this->pInt('currency_id', 0);
        if ($currency_id != 0) {
            $find->andWhere(['A.currency_id' => $currency_id]);
        }
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $type = $this->pInt('type', 0);
        if ($type == 1) {
            $find->andWhere(['in', 'type', $in_arr]);
        } elseif ($type == 2) {
            $find->andWhere(['in', 'type', $out_arr]);
        }
        $count = $find->count();
        $page = $this->pInt('page', 1);
        if ($page != 0) {
            $find->page($page);
        }
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            if (in_array($v['type'], $in_arr)) {
                $v['type2'] = '收入';
            } else {
                $v['type2'] = '支出';
            }
            $v['type'] = BUserCurrencyDetail::getType($v['type']);
            $v['status'] = BUserCurrencyDetail::getStatus($v['status']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
}
