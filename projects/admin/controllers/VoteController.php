<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BSetting;

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
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionSetVote()
    {
        $payment_number = (string)$this->pFloat('payment_number');
        if (empty($payment_number)) {
            return $this->respondJson(1, "支付投票价格必须大于0");
        }
        $ordinary_number = (string)$this->pFloat('ordinary_number');
        if (empty($ordinary_number)) {
            return $this->respondJson(1, "普通投票价格必须大于0");
        }
        $vote_open = (string)$this->pInt('vote_open');
        $transaction = \Yii::$app->db->beginTransaction();
        $setting = BSetting::find()->where(['name' => BSetting::$PAYMENT_NUMBER])->one();
        if (!$setting) {
            $setting = new BSetting();
            $setting->name = BSetting::$PAYMENT_NUMBER;
            $setting->remark = '支付投票价';
            $setting->create_time = time();
        }
        $setting->value = $payment_number;
        if (!$setting->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, "操作失败", $setting->getFirstErrorText());
        }
        $setting = BSetting::find()->where(['name' => BSetting::$ORDINARY_NUMBER])->one();
        if (!$setting) {
            $setting = new BSetting();
            $setting->name = BSetting::$ORDINARY_NUMBER;
            $setting->remark = '普通投票价';
            $setting->create_time = time();
        }
        $setting->value = $ordinary_number;
        if (!$setting->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, "操作失败", $setting->getFirstErrorText());
        }
        $setting = BSetting::find()->where(['name' => BSetting::$VOTE_OPEN])->one();
        if (!$setting) {
            $setting = new BSetting();
            $setting->name = BSetting::$VOTE_OPEN;
            $setting->remark = '投票启动状态';
            $setting->create_time = time();
        }
        $setting->value = $vote_open;
        if (!$setting->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, "操作失败", $setting->getFirstErrorText());
        } else {
            $transaction->commit();
            return $this->respondJson(0, "操作成功");
        }
    }

    public function actionVoteList()
    {
        $search_name = $this->pString('search_name');
        $str_time = $this->pString('str_time');
        $end_time = $this->pString('end_time');
        $find = BVote::find()
        ->from(BVote::tableName()." A")
        ->join('inner join', 'gr_user B', 'A.user_id = B.id')
        ->join('inner join', 'gr_node B', 'A.node_id = B.id')
        ->select(['A.*','B.username','C.'])
        ->where(['A.tag_id' => $id]);
        if (!empty($search_name)) {
            $find->andWhere(['like','']);
        }
    }
}
