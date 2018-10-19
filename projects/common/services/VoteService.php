<?php

namespace common\services;

use common\components\FuncResult;
use common\models\business\BUser;
use common\models\business\BVote;

class VoteService extends ServiceBase
{
    public static function getInstance(): VoteService
    {
        self::$instance = new self();
        self::$instance->init();

        return self::$instance;
    }

    /**
     * 校验投票是否能被赎回
     *
     * @param BUser $userModel
     * @param integer $id
     * @return boolean
     */
    public static function hasRevoke(BUser $userModel, int $voteId)
    {
        $voteModel = $userModel->getVotes()
        ->active()
        ->where(['type' => BVote::TYPE_ORDINARY, 'id' => $voteId]);
        $vote = $voteModel->one();
        if (is_null($vote)) {
            return new FuncResult(0, '该投票不存在或不能赎回', false);
        }
        $historyModelExists = $vote->getHistorys()
        ->select('id')
        ->andWhere(['>', 'create_time', $vote->create_time])
        ->orderBy(['update_number' => SORT_DESC])
        ->exists();
        return new FuncResult(0, '校验结果', $historyModelExists);
    }
}
