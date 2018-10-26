<?php

namespace app\controllers;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\services\UserService;
use common\services\VoteService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BUser;
use common\services\SettingService;
use common\models\business\BVoucher;
use common\models\business\BUserRecommend;

class UserController extends BaseController
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
     * 用户中心
     *
     * @return void
     */
    public function actionIndex()
    {
        return $this->respondJson(0, '获取成功');
    }

    /**
     * 获取邀请码以及判断是否添加推荐人
     *
     * @return void
     */
    public function actionRecommendCode()
    {
        $userModel = $this->user;
        if (!(bool) $userModel->recommend_code) {
            $code = UserService::generateRemmendCode(6);
            $userModel->recommend_code = $code;
            if (!$userModel->save()) {
                return $this->respondJson(1, '推荐码生成失败', $userModel->getFirstErrors());
            }
        }
        $data['code'] = $userModel->recommend_code;
        $data['re_code'] = BUserRecommend::find()->where(['user_id' => $userModel->id])->exists();
        return $this->respondJson(0, '获取成功', $data);
    }
    /**
     * 添加邀请人邀请码
     *
     * @return void
     */
    public function actionAddRecommend()
    {
        $userModel = $this->user;
        $reCode = $this->pString('re_code', false);
        
        if (!preg_match('/^[a-zA-Z0-9]{6}$/i', $reCode)) {
            return $this->respondJson(1, '推荐码格式错误');
        }
        $reCode = strtoupper($reCode);
        $parentId = UserService::validateRemmendCode($reCode);
        if (!$parentId) {
            return $this->respondJson(1, '推荐人不存在');
        }
        if ($parentId === $userModel->id) {
            return $this->respondJson(1, '推荐人不能是自己');
        }
        if (BUserRecommend::find()->where(['user_id' => $userModel->id])->exists()) {
            return $this->respondJson(1, '已添加推荐人');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $nodeModel = $userModel->node;
            $recommendModel = new BUserRecommend();
            $recommendModel->parent_id = (int) $parentId;
            if (!is_null($nodeModel) && $nodeModel->status == BNode::STATUS_ON) {
                $multiple = (int) SettingService::get('vote', 'voucher_number')->value;
                // 指定货币类型的 * 设置倍数
                $voucherCount = $nodeModel->grt * $multiple;
                $recommendVoucher = (bool) SettingService::get('recommend', 'recommend_voucher')->value;
                if (BNode::find()->where(['user_id' => $parentId])->exists() && !$recommendVoucher) {
                    $voucherModel = new BVoucher();
                    $voucherModel->user_id = $parentId;
                    $voucherModel->node_id = $nodeModel->id;
                    $voucherModel->voucher_num = $voucherCount;
                    if (!$voucherModel->save()) {
                        throw new ErrorException('投票劵赠送失败');
                    }
                    $recommendModel->amount = $voucherCount;
                }
                $recommendModel->node_id = $nodeModel->id;
                // 重置用户投票券
                if (!UserService::resetVoucher($parentId)) {
                    throw new ErrorException('投票券资产更新失败');
                }
            }
            
            $recommendModel->link('user', $userModel);
            if (!$recommendModel->id) {
                throw new ErrorException('推荐人关联失败');
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->respondJson(1, $e->getMessage());
        }
        return $this->respondJson(0, '设置成功', $parentId);
    }

    /**
     * 获取推荐记录
     *
     * @return void
     */
    public function actionRecommend()
    {
        // 返回容器
        $data = [];
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        $recommendModel = $userModel->getUserRecommend()
        ->alias('r')
        ->select(['r.create_time', 'nt.name as type_name', 'r.node_id', 'u.mobile', 'r.parent_id'])
        ->joinWith(['node n' => function ($query) {
            $query->joinWith('nodeType nt', false);
        }, 'user u'], false);
        $data['count'] = $recommendModel->count();
        $data['list'] = $recommendModel
        ->page($page, $pageSize)
        ->asArray()->all();
        foreach ($data['list'] as &$recommend) {
            $recommend['create_time'] = FuncHelper::formateDate($recommend['create_time']);
            $recommend['mobile'] = substr_replace($recommend['mobile'], '****', 3, 4);
            $recommend['type_name'] = $recommend['type_name'] ?? '普通用户';
            unset($recommend['node']);
            unset($recommend['parent']);
            unset($recommend['node_id']);
            unset($recommend['parent_id']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }


    /**
     * 用户节点权益信息
     *
     * @return void
     */
    public function actionNodeRuleInfo()
    {
        // 返回容器
        $data = [];
        $userModel = $this->user;
        $nodeModel = $userModel->node;
        $nodeTypeModel = $nodeModel->nodeType;
        // 获取用户当前节点的排名
        $ranking = NodeService::getNodeRanking($nodeModel->type_id, $nodeModel->id);
        // 获取当前用户节点权益
        $nodeRule = NodeService::getNodeRule($nodeModel->id, $ranking);
        $rules = [];
        foreach ($nodeRule as $key => $rule) {
            $rules[$key]['name'] = $rule['name'];
            $rules[$key]['content'] = $rule['content'];
            $rules[$key]['is_tenure'] = (bool) $rule['is_tenure'];
        }
        $data['name'] = $nodeModel->name;
        $data['type_name'] = $nodeTypeModel->name;
        $data['type'] = $nodeModel->type_id;
        // $data['is_tenure'] = $nodeModel->isTenureText;
        $data['rules'] = $rules;
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 重置用户币种资金
     */
    public function actionResetCurrency()
    {
        $userId = $this->pInt('user_id', 0);
        $currencyId = $this->pInt('currency_id', 0);

        if ($userId === 0 || $currencyId === 0) {
            return $this->respondJson(1, 'params error.');
        }

        $sign = UserService::resetCurrency($userId, $currencyId);
        if ($sign !== false) {
            return $this->respondJson(0, 'reset currency success.');
        }

        return $this->respondJson(1, 'reset currency fail.');
    }
}
