<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use common\services\NodeService;
use yii\helpers\ArrayHelper;
use common\models\business\BNotice;
use common\models\business\BUserOther;
use common\models\business\BArea;
use common\models\business\BUser;
use common\models\business\BUserIdentify;
use common\models\business\BNode;
use common\models\business\BUserLog;
use common\models\business\BVote;
use common\components\IpUtil;

/**
 * Site controller
 */
class StatisticsController extends BaseController
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
    // 数据统计
    public function actionIndex()
    {
        $user_login_num = BUser::find()->where(['>=', 'last_login_time',strtotime(date('Y-m-d'))])->count();
        $user_all_num = BUser::find()->where(['status' => BUser::$STATUS_ON])->count();
        $repeat_login_num = BUserLog::find()->where(['type' => BUserLog::$TYPE_LOGIN])->andWhere(['>=', 'create_time',time()-3600*24*7])->groupBy(['user_id'])->having(['>', 'count(id)', 1])->count();
        $user_create_num = BUser::find()->where(['>=', 'create_time',strtotime(date('Y-m-d'))])->count();
        $vote = BVote::find()->select(['sum(vote_number) as num'])->where(['status' => BVote::STATUS_ACTIVE])->andWhere(['>=', 'create_time',strtotime(date('Y-m-d'))])->asArray()->one();
        $vote_num = $vote['num'] ? $vote['num'] : "0";
        $vote_all = BVote::find()->select(['sum(vote_number) as num'])->where(['status' => BVote::STATUS_ACTIVE])->asArray()->one();
        $vote_all_num = $vote_all['num'] ? $vote_all['num'] : "0";
        $identify_num = BUserIdentify::find()->where(['status' => BUserIdentify::STATUS_ACTIVE])->andWhere(['>=', 'examine_time', strtotime(date('Y-m-d'))])->count();
        $identify_all_num = BUserIdentify::find()->where(['status' => BUserIdentify::STATUS_ACTIVE])->count();
        $node_num = BNode::find()->where(['status' => BNode::STATUS_ON])->andWhere(['>=', 'examine_time', strtotime(date('Y-m-d'))])->count();
        $node_all_num = BNode::find()->where(['status' => BNode::STATUS_ON])->count();
        $return = [];
        $return['user_login_num'] = $user_login_num; // 今日用户登陆
        $return['user_all_num'] = $user_all_num;  // 用户总数
        $return['repeat_login_num'] = $repeat_login_num; // 7日重复登录人数
        $return['user_create_num'] = $user_create_num; // 新增用户数
        $return['vote_num'] = $vote_num; // 今日投票数
        $return['vote_all_num'] = $vote_all_num; // 合计投票量
        $return['identify_num'] = $identify_num; // 今日实名认证人数
        $return['identify_all_num'] = $identify_all_num; // 实名认证总人数
        $return['node_num'] = $node_num; // 今日新增节点数
        $return['node_all_num'] = $node_all_num; // 节点总数
        return $this->respondJson(0, "获取成功", $return);
    }

    public function actionGetStatisticsData()
    {
        $type = $this->pInt('type');
        if (empty($type)) {
            return $this->respondJson(1, 'type不能为空');
        }
        $group = $this->pInt('group');
        if (empty($group)) {
            return $this->respondJson(1, 'group不能为空');
        }
        $str_time = strtotime($this->pString('str_time'));
        if (empty($str_time)) {
            return $this->respondJson(1, 'str_time不能为空');
        }
        if ($str_time < time()-365*24*3600) {
            return $this->respondJson(1, '无法统计一年前数据');
        }
        $end_time = strtotime($this->pString('end_time'));
        if (empty($end_time)) {
            return $this->respondJson(1, 'end_time不能为空');
        }
        if ($end_time > time()) {
            return $this->respondJson(1, '结束时间不能大于当前时间');
        }
        $end_time += (24*3600-1);
        $group_arr = [1 => 'floor(create_time/(24*3600))', 2 => 'floor((create_time+4*3600*24)/(24*3600*7))', 3 => "FROM_UNIXTIME(create_time,'%Y-%m')"];
        if ($group == 1) {
            $select_field = 'floor(create_time/(24*3600))*24*3600 as date';
        } elseif ($group == 2) {
            $select_field = 'floor((create_time+4*3600*24)/(24*3600*7))*24*3600*7-3*3600*24 as date';
        } else {
            $select_field = $group_arr[$group].' as date';
        }
        if ($type == 1) {
            //登录人数
            $sql = "SELECT count(*) as count,$select_field FROM (select * from gr_user_log group by user_id, FROM_UNIXTIME(create_time, '%Y-%m-%d')) `a` WHERE (`create_time` >= $str_time) AND (`create_time` <= $end_time) GROUP BY $group_arr[$group]";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
        } elseif ($type == 2) {
            //新增用户数
            $sql = "SELECT count(*) as count,$select_field FROM gr_user WHERE (`create_time` >= $str_time) AND (`create_time` <= $end_time) GROUP BY $group_arr[$group]";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
        } elseif ($type == 3) {
            //新增投票量
            $sql = "SELECT sum(vote_number) as count,$select_field FROM gr_vote WHERE (`create_time` >= $str_time) AND (`create_time` <= $end_time) AND (status = ".BVote::STATUS_ACTIVE.") GROUP BY $group_arr[$group]";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
        } elseif ($type == 4) {
            //新增实名认证人数
            $group_by = str_replace('create_time', 'examine_time', $group_arr[$group]);
            $select_field = str_replace('create_time', 'examine_time', $select_field);
            $sql = "SELECT count(*) as count,$select_field FROM gr_user_identify WHERE (`examine_time` >= $str_time) AND (`examine_time` <= $end_time) AND (status = ".BUserIdentify::STATUS_ACTIVE.") GROUP BY $group_by";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
        } elseif ($type == 5) {
            //新增节点数量
            $group_by = str_replace('create_time', 'examine_time', $group_arr[$group]);
            $select_field = str_replace('create_time', 'examine_time', $select_field);
            $sql = "SELECT count(*) as count,$select_field FROM gr_node WHERE (`examine_time` >= $str_time) AND (`examine_time` <= $end_time) AND (status = ".BNode::STATUS_ON.") GROUP BY $group_by";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
        }

        $return = [];

        $date_arr = [];
        for ($i = $str_time; $i<=$end_time; $i += 3600*24) {
            if ($group == 1) {
                $m = date('Y-m-d', $i);
                if (empty($date_arr[$m])) {
                    $date_arr[$m] = 1;
                    $return['date'][] = $m;
                    $bool = false;
                    foreach ($data as $v) {
                        if (date('Y-m-d', $v['date']) == $m) {
                            $return['data'][] = $v['count'];
                            $bool = true;
                        }
                    }
                    if (!$bool) {
                        $return['data'][] = 0;
                    }
                }
            } elseif ($group == 2) {
                $m = date('Y-m-d', floor(($i+4*3600*24)/(24*3600*7))*24*3600*7-3*3600*24);
                if (empty($date_arr[$m])) {
                    $date_arr[$m] = 1;
                    $return['date'][] = $m;
                    $bool = false;
                    foreach ($data as $v) {
                        if (date('Y-m-d', $v['date']) == $m) {
                            $return['data'][] = $v['count'];
                            $bool = true;
                        }
                    }
                    if (!$bool) {
                        $return['data'][] = 0;
                    }
                }
            } elseif ($group == 3) {
                $m = date('Y-m', $i);
                if (empty($date_arr[$m])) {
                    $date_arr[$m] = 1;
                    $return['date'][] = $m;
                    $bool = false;
                    foreach ($data as $v) {
                        if ($v['date'] == $m) {
                            $return['data'][] = $v['count'];
                            $bool = true;
                        }
                    }
                    if (!$bool) {
                        $return['data'][] = 0;
                    }
                }
            }
        }
        if ($group == 2) {
            foreach ($return['date'] as $k => $v) {
                if ($k == 0) {
                    $return['date'][$k] = date('Y-m-d', $str_time);
                } else {
                    $return['date'][$k] = $v;
                }
                $return['date'][$k] .= ' - ';
                if (empty($return['date'][$k + 1])) {
                    $return['date'][$k] .= date('Y-m-d', $end_time);
                } else {
                    $return['date'][$k] .= date('Y-m-d', strtotime($return['date'][$k + 1])-3600*24);
                }
            }
            //var_dump($data);
        }
        return  $this->respondJson(0, '获取成功', $return);
    }

    public function actionGetAddressData()
    {
        $find_province = BUserOther::find()
      ->from(BUserOther::tableName()." A")
      
      ->join('left join', BArea::tableName().' B', 'A.area_province_id = B.id')
      ->join('left join', BUserIdentify::tableName().' C', 'A.user_id = C.user_id && C.status = '.BUserIdentify::STATUS_ACTIVE)
      ->join('left join', BNode::tableName().' D', 'A.user_id = D.user_id && D.status = '.BNode::STATUS_ON)
      ->select(['count(A.id) as count','B.id'])
      ->where(['>', 'A.area_province_id', 0]);
        $find_city = BUserOther::find()
      ->from(BUserOther::tableName()." A")
      ->join('left join', BArea::tableName().' B', 'A.area_city_id = B.id')
      ->join('left join', BUserIdentify::tableName().' C', 'A.user_id = C.user_id && C.status = '.BUserIdentify::STATUS_ACTIVE)
      ->join('left join', BNode::tableName().' D', 'A.user_id = D.user_id && D.status = '.BNode::STATUS_ON)
      ->select(['count(A.id) as count','B.id'])
      ->where(['>', 'A.area_city_id', 0]);
        $type = $this->pInt('type');
        if ($type == 2) {
            //实名认证用户
            $find_province->andWhere(['C.status' => BUserIdentify::STATUS_ACTIVE]);
            $find_city->andWhere(['C.status' => BUserIdentify::STATUS_ACTIVE]);
        } elseif ($type == 3) {
            // 实名非节点用户
            $find_province->andWhere(['C.status' => BUserIdentify::STATUS_ACTIVE]);
            $find_city->andWhere(['C.status' => BUserIdentify::STATUS_ACTIVE]);
            $find_province->andWhere(['!=', 'D.status', BNode::STATUS_ON]);
            $find_city->andWhere(['!=', 'D.status', BNode::STATUS_ON]);
        } elseif ($type == 4) {
            // 节点用户
            $find_province->andWhere(['D.status' => BNode::STATUS_ON]);
            $find_city->andWhere(['D.status' => BNode::STATUS_ON]);
        }
        $data_province = $find_province->groupBy(['A.area_province_id'])
      ->asArray()->all();
        $data_city = $find_city->groupBy(['A.area_city_id'])
      ->asArray()->all();
        $province_data = $city_data = $area_id =  [];
        $all_people = 0;
        foreach ($data_province as $v) {
            $province_data[$v['id']] = $v['count'];
            $all_people += $v['count'];
            $area_id[] = $v['id'];
        }
        foreach ($data_city as $v) {
            $city_data[$v['id']] = $v['count'];
            $area_id[] = $v['id'];
        }
        $area = BArea::find()->where(['or', ['in', 'id', $area_id], ['in', 'parentid', $area_id]])->orderBy('level')->all();
        $new_area = $region_area = [];
        foreach ($area as $v) {
            //省级
            if ($v->level == 1) {
                if (!empty($province_data[$v->id])) {
                    $new_area[$v->id]['count'] = $province_data[$v->id];
                } else {
                    continue;
                }
                $new_area[$v->id]['name'] = $v->areaname;
                $new_area[$v->id]['id'] = $v->id;
                
                $new_area[$v->id]['child'] = [];
            } elseif ($v->level == 2) {
                //市级
                if (empty($new_area[$v->parentid])) {
                    continue;
                }
                if (!empty($city_data[$v->id])) {
                    $new_area[$v->parentid]['child'][$v->id]['count'] = $city_data[$v->id];
                    foreach ($new_area[$v->parentid]['child'] as $k =>$val) {
                        if ($val['count'] == 0) {
                            unset($new_area[$v->parentid]['child'][$k]);
                        }
                        break;
                    }
                } else {
                    if (!empty($new_area[$v->parentid]) && $new_area[$v->parentid]['count'] > 0 && empty($new_area[$v->parentid]['child'])) {
                        $new_area[$v->parentid]['child'][$v->id]['count'] = 0;
                    } else {
                        continue;
                    }
                }
                $new_area[$v->parentid]['child'][$v->id]['name'] = $v->areaname;
                $new_area[$v->parentid]['child'][$v->id]['id'] = $v->id;
            } else {
                //区级
                if (!empty($city_data[$v->id])) {
                    if (empty($region_area[$v->parentid])) {
                        $region_area[$v->parentid] = [];
                    }
                    $region_area[$v->parentid][$v->id]['name'] = $v->areaname;
                    $region_area[$v->parentid][$v->id]['id'] = $v->id;
                    if (!empty($city_data[$v->id])) {
                        $region_area[$v->parentid][$v->id]['count'] = $city_data[$v->id];
                    } else {
                        $region_area[$v->parentid][$v->id]['count'] = 0;
                    }
                } else {
                    continue;
                }
            }
        }

        foreach ($region_area as $k => $v) {
            foreach ($new_area as &$val) {
                if (count($val['child']) > 1) {
                    continue;
                }
                foreach ($val['child'] as $key => $value) {
                    if ($k == $key) {
                        $val['child'] = $v;
                    }
                }
            }
        }
        $r = [];
        foreach ($new_area as $v) {
            if (!empty($v['child'])) {
                $it = [];
                foreach ($v['child'] as $val) {
                    $it[]=$val;
                }
                $v['child'] = $it;
            }
            
            $r[] = $v;
        }
        $return = [];
        $return['all_people'] = $all_people;
        $return['data'] = $r;
        return  $this->respondJson(0, '获取成功', $return);
    }

    public function actionTest()
    {
        $data = BUser::find()->all();
        foreach ($data as $v) {
            NodeService::checkVoucher($v->id);
        }
    }
}
