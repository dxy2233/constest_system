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
     * 公共查询，判断当前时间是否在时间段内
     *
     * @param integer $start
     * @param integer $end
     * @return void
     */
    public function hasStartAndEndTime(int $time = NOW_TIME)
    {
        $query = $this->andOnCondition(['<=', 'start_time', $time]);
        return $query->andOnCondition(['>=', 'end_time', $time]);
    }

    /**
     * 公共分页
     *
     * @param integer $page
     * @param integer $pageSize
     * @return void
     */
    public function page(int $page = 1, int $pageSize = 20)
    {
        $query = $this->offset(($page-1)*$pageSize);
        $query = $this->Limit($pageSize);
        return $query;
    }

    /**
     * 公共查询，按照时间查询
     *
     * @param string $start
     * @param string $field
     * @return void
     */
    public function startTime(string $start, string $field)
    {
        $str_time = strtotime($start);
        $query = $this->andWhere(['>=', $field, $str_time]);
        return $query;
    }

    /**
     * 公共查询，按照时间查询
     *
     * @param string $end
     * @param string $field
     * @return void
     */
    public function endTime(string $end, string $field)
    {
        $end_time = strtotime($end);
        $query = $this->andWhere(['<=', $field, $end_time]);
        return $query;
    }
}
