<?php

namespace app\controllers;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\services\UserService;
use common\services\VoteService;
use common\components\FuncHelper;
use common\models\business\BArea;
use common\models\business\BNode;
use common\models\business\BUser;
use common\services\SettingService;
use common\models\business\BVoucher;
use common\models\business\BUserRecommend;
use common\models\business\BUserOther;

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

        $checkRecomment = UserService::checkUserRecommend($userModel->id, $reCode);
        if ($checkRecomment->code) {
            return $this->respondJson($checkRecomment->code, $checkRecomment->msg);
        }
        
        return $this->respondJson(0, '设置成功');
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
        $recommendModel = $userModel->getParentUserRecommend()
        ->alias('r')
        ->select(['r.id', 'r.create_time', 'u.mobile'])
        ->joinWith(['user u'], false);
        $data['count'] = $recommendModel->count();
        $data['list'] = $recommendModel
        ->orderBy(['r.create_time' => SORT_DESC])
        ->page($page, $pageSize)
        ->asArray()->all();
        foreach ($data['list'] as &$recommend) {
            $recommend['create_time'] = FuncHelper::formateDate($recommend['create_time']);
            $recommend['mobile'] = substr_replace($recommend['mobile'], '****', 3, 4);
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

    /**
     * 收货地址
     *
     * @return void
     */
    public function actionAddressList()
    {
        $data = [];
        $userModel = $this->user;
        $otherModel = $userModel->userOther;
        if ($otherModel) {
            $address = '';
            $areaname = BArea::find()
            ->select(['areaname'])
            ->where(['id' => [$otherModel->area_province_id, $otherModel->area_city_id]])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();
            foreach ($areaname as $val) {
                $address .= $val['areaname'].' ';
            }
            $address .= $otherModel->address;
            $otherModel->address = $address;
            $list = [FuncHelper::arrayOnly($otherModel->toArray(), ['consignee', 'consignee_mobile', 'address'])];
            if (!$otherModel->consignee_mobile || !$otherModel->consignee) {
                $list = [];
            }
            $data['list'] = $list;
        } else {
            $data['list'] = [];
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 收货地址详情
     *
     * @return void
     */
    public function actionAddressInfo()
    {
        $data = [];
        $userModel = $this->user;
        if ($otherModel = $userModel->userOther) {
            $data = FuncHelper::arrayOnly($otherModel->toArray(), ['consignee', 'consignee_mobile', 'area_province_id', 'area_city_id', 'address', 'zip_code']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    /**
     * 收货地址详情
     *
     * @return void
     */
    public function actionAddressSave()
    {
        // $consignee = $this->pString('consignee');
        // $consigneeMobile = $this->pString('consignee_mobile');
        // $areaProvinceId = $this->pString('area_province_id');
        // $areaCityId = $this->pString('area_city_id');
        // $address = $this->pString('address');
        // $zipCode = $this->pString('zip_code');
        // if (!$consignee) {
        //     return $this->respondJson(1, '收货人不能为空');
        // }
        $userModel = $this->user;
        if (!$otherModel = $userModel->userOther) {
            $otherModel = new BUserOther();
            $otherModel->user_id = $userModel->id;
        }
        // 表单场景使用
        $otherModel->attributes = \Yii::$app->request->post();
        $otherModel->scenario = 'address';
        if ($otherModel->validate() && $otherModel->save()) {
            return $this->respondJson(0, '保存成功');
        }
        return $this->respondJson(1, $otherModel->getFirstErrorText());
    }

    public function actionRecommendInfo()
    {
        $userModel = $this->user;
        $mobile = $this->pString('mobile');
        if (!FuncHelper::validatMobile($mobile)) {
            return $this->respondJson(1, '手机号错误');
        }
        if (!$mobile) {
            return $this->respondJson(1, '手机号不能为空');
        }
        if ($mobile == $userModel->mobile) {
            return $this->respondJson(1, '推荐人不能是自己');
        }
        $recommendUser = BUser::find()->where(['mobile' => $mobile])->one();
        if (!$recommendUser) {
            return $this->respondJson(1, '推荐人用户不存在');
        }
        $recommendNodeModel = $recommendUser->getNode()
        ->active()
        ->one();
        if (!$recommendNodeModel) {
            return $this->respondJson(1, '推荐人不是节点');
        }
        $parent_arr = explode(',', $recommendUser->parent_list);
        if (in_array($userModel->id, $parent_arr)) {
            return $this->respondJson(1, '推荐人不能是自己的下级');
        }
        $data = [
            'mobile' => $mobile,
            'realname' => $recommendUser->identify->realname,
            'node_name' => $recommendNodeModel->name,
            'type_id' => $recommendNodeModel->type_id,
            'node_type' => $recommendNodeModel->nodeType->name,
        ];
        return $this->respondJson(0, '获取成功', $data);
    }
}
