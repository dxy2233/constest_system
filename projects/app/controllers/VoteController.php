<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BVote;
use common\services\SettingService;

class VoteController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 需登录访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 贡献榜
     *
     * @return void
     */
    public function actionIndex()
    {
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $voteShowType = $this->pString('type', 'all');
        // 容器
        $data = [];
        
        // 刷新时间获取，更新时间
        $endUpdate = SettingService::get('vote', 'end_update_time');
        if (!is_object($endUpdate) && empty($endUpdate->value)) {
            return $this->respondJson(1, "投票更新时间未设定");
        }
        $updateTime = (int) $endUpdate->value;
        $data['countTime'] = FuncHelper::formateDate($updateTime);
        $voteModel = BVote::find()
        ->select(['user_id', 'node_id', 'create_time', 'type', 'status'])
        ->where(['<=', 'create_time', $updateTime])
        ->with('user')
        ->active();
        if ($voteShowType === 'pay') {
            $voteModel->where(['type' => BVote::TYPE_PAY]);
        }
        $voteModel->addSelect(['SUM(vote_number) as vote_number']);
        $voteModel->groupBy('user_id');
        $voteModel->orderBy(['vote_number' => SORT_ASC]);
        $data['count'] = $voteModel->count();
        $voteModel->page($page, $pageSize);
        $voteDataModel = $voteModel->all();
        
        if (!is_object(reset($voteDataModel))) {
            return $this->respondJson(0, '记录为空');
        }
        $voteData = [];
        foreach ($voteDataModel as $key => $vote) {
            $vote->create_time = $vote->createTimeText;
            $addData = [
                'type_str' => BVote::getType($vote->type),
                'status_str' => BVote::getStatus($vote->status),
                'mobile' => substr_replace($vote->user->mobile, '****', 3, 4),
            ];
            $vote = $vote->toArray();
            FuncHelper::arrayForget($vote, ['type', 'status', 'consume', 'user_id', 'node_id']);
            $voteData[$key] = array_merge($vote, $addData);
        }
        $data['list'] = $voteData;
        return $this->respondJson(0, '获取成功', $data);
    }
}
