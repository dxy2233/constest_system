<?php

namespace common\models\business;

use yii\db\ActiveQuery;

class BaseQuery extends ActiveQuery
{
    public function init()
    {
        parent::init();
    }
    /**
     * 公共查询状态
     *
     * @param integer $status 自定义状态
     * @return void
     */
    public function active(int $status = 1)
    {
        return $this->andOnCondition(['status' => $status]);
    }

    /**
     * 公共查询，按照时间查询
     *
     * @param integer $start
     * @param integer $end
     * @return void
     */
    public function startAndEndTime(int $time = NOW_TIME)
    {
        $query = $this->andOnCondition(['<=', 'start_time', $time]);
        return $query->andOnCondition(['>=', 'end_time', $time]);
    }
}
