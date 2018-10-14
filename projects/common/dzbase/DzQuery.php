<?php

namespace common\dzbase;

use yii\db\ActiveQuery;

class DzQuery extends ActiveQuery
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

    /**
     * 公共查询，按照时间查询
     *
     * @param integer $start
     * @param integer $end
     * @return void
     */
    public function page(int $page = 1, int $pageSize = 20)
    {
        $query = $this->offset(($page-1)*$pageSize);
        $query = $this->Limit($pageSize);
        return $query;
    }
}
