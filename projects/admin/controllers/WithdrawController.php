<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BNotice;
use common\models\business\BSetting;
use common\models\business\BUserCurrency;
use common\models\business\BUser;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;

/**
 * Site controller
 */
class WithdrawController extends BaseController
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
    // 修改设置
    public function actionSetNotice()
    {
        $currency_code = $this->pString('currencyCode');
        if (empty($currency_code)) {
            return $this->respondJson(1, '币种代码不能为空');
        }
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_CURRENCY.'_'.$currency_code])->all();
        if (empty($data)) {
            return $this->respondJson(1, '币种代码不存在');
        }
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

    // 获取设置项
    public function actionGetSettingList()
    {
        $currency_code = $this->pString('currencyCode');
        if (empty($currency_code)) {
            return $this->respondJson(1, '币种代码不能为空');
        }
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_CURRENCY.'_'.$currency_code])->orderBy('sort')->asArray()->all();
        if (empty($data)) {
            return $this->respondJson(1, '币种代码不存在');
        }
        foreach ($data as &$v) {
            $v['initialize'] = json_decode($v['initialize'], true);
        }
        return $this->respondJson(0, "获取成功", $data, false);
    }

    public function actionIndex()
    {
        $status = $this->pInt('status');
        $searchName = $this->pString('searchName', '');
        $currency_id = $this->pInt('currencyId');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $setting = BSetting::find()->where(['key' => 'max_amount'])->one();
        $min = $setting->value;
        $page = $this->pInt('page', 1);
        $data = UserRechargeWithdraw::find()
        ->from(UserRechargeWithdraw::tableName()." A")
        ->where(['A.status' => $status])
        ->andWhere(['>=', 'A.amount', $min])
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id');
        if ($searchName != '') {
            $find->andWhere(['like', 'B.mobile', $searchName]);
        }
        if ($currency_id != 0) {
            $find->andWhere(['currency_id' => $currency_id]);
        }
        if ($str_time != '') {
            $find->startTime($str_time, 'A.create_time');
        }
        
        if ($end_time != '') {
            $find->endTime($end_time, 'A.create_time');
        }
        $count = $find->count();
        $find->page($page);

        $data = $find->asArray()->all();
        //echo $find->createCommand()->getRawSql();
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
    // 审核失败
    public function actionExamineOff()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = UserRechargeWithdraw::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data->status = UserRechargeWithdraw::STATUS_NO;
        $data->status_remark = $remark;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }

    // 审核成功
    public function actionExamineOn()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = UserRechargeWithdraw::find()->where(['id' => $id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '数据不存在');
        }
        $data->status = UserRechargeWithdraw::STATUS_ON;
        $data->status_remark = '已成功';
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
}
