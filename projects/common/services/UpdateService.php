<?php

namespace common\services;

use common\models\UploadForm;
use common\services\NodeService;
use common\models\business\BUpdateData;
use common\models\business\BUpdateLog;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BCurrency;
use common\models\business\BNodeUpgrade;
use common\models\business\BUserOther;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BNodeRecommend;
use common\models\business\BUserRecommend;
use common\models\business\BUserCurrencyDetail;
use Yii;

class UpdateService extends ServiceBase
{


    /**
     * 上传图片
     */
    public static function begin()
    {
        // 读取需要修改的数据
        $data = BUpdateData::find()->where(['status' => 0])->all();
        if (!$data) {
            $msg[] = '没有要更新的数据';
        }
        $currency = BCurrency::find()->all();
        $currency_arr = [];
        foreach ($currency  as $v) {
            $currency_arr[$v['code']] = $v['id'];
        }
        $currency_id = [];
        foreach ($currency  as $v) {
            $currency_id[$v['id']] = $v['code'];
        }
        
        $msg = [];
        foreach ($data as $v) {
            $user = BUser::find()->where(['mobile' => $v->mobile])->one();
            if (!$user) {
                $msg[] = '用户不存在';
                continue;
            }
            $node = BNode::find()->where(['user_id' => $user->id])->one();
            if (!$node) {
                $msg[] = '节点不存在';
                continue;
            }
            $withdraw = BUserRechargeWithdraw::find()->where(['user_id' => $user->id, 'remark' => '添加节点转入积分'])->all();
            if (!$withdraw) {
                $msg[] = '流水数据不存在';
                continue;
            }
            $transaction = \Yii::$app->db->beginTransaction();
            foreach ($withdraw as $val) {
                $detail = BUserCurrencyDetail::find()->where(['relate_table' => 'user_recharge_withdraw', 'currency_id' => $val->currency_id, 'relate_id' => $val->id])->one();
                if (!$detail) {
                    $msg[] = '明细数据不存在';
                    break;
                }
                $frozen = BUserCurrencyFrozen::find()->where(['type' => BUserCurrencyFrozen::$TYPE_ELECTION, 'currency_id' => $val->currency_id, 'relate_id' => $node->id])->one();
                if (!$frozen) {
                    $msg[] = '冻结数据不存在';
                    break;
                }
                $old_data = $val->amount;
                if ($val->currency_id == $currency_arr['grt']) {
                    $val->amount = $v->grt;
                    $detail->amount = $v->grt;
                    $frozen->amount = $v->grt;
                } elseif ($val->currency_id == $currency_arr['tt']) {
                    $val->amount = $v->tt;
                    $detail->amount = $v->tt;
                    $frozen->amount = $v->tt;
                } elseif ($val->currency_id == $currency_arr['bpt']) {
                    $val->amount = $v->bpt;
                    $detail->amount = $v->bpt;
                    $frozen->amount = $v->bpt;
                }
                if ($val->amount != 0) {
                    if (!$val->save()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $val->getFirstErrorText();
                        break;
                    }
                    $sql = '';
                    if (!self::addUpdateLogs('user_recharge_withdraw', 'amount', (string)$old_data, (string)$val->amount, $val->id, $sql)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                    if (!$detail->save()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $detail->getFirstErrorText();
                        break;
                    }
                    if (!self::addUpdateLogs('user_currency_detail', 'amount', (string)$old_data, (string)$val->amount, $detail->id, $sql)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                    if (!$frozen->save()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $frozen->getFirstErrorText();
                        break;
                    }
                    if (!self::addUpdateLogs('user_currency_frozen', 'amount', (string)$old_data, (string)$val->amount, $frozen->id, $sql)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                } else {
                    if (!$val->delete()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $val->getFirstErrorText();
                        break;
                    }
                    if (!self::addUpdateLogs('user_recharge_withdraw', 'all_data', (string)json_encode($val->toArray()), 'delete', $val->id)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                    if (!$detail->delete()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $detail->getFirstErrorText();
                        break;
                    }
                    if (!self::addUpdateLogs('user_currency_detail', 'all_data', (string)json_encode($detail->toArray()), 'delete', $detail->id)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                    if (!$frozen->delete()) {
                        $transaction->rollBack();
                        $msg[$user->id] = $frozen->getFirstErrorText();
                        break;
                    }
                    if (!self::addUpdateLogs('user_currency_frozen', 'all_data', (string)json_encode($frozen->toArray()), 'delete', $frozen->id)) {
                        $transaction->rollBack();
                        $msg[$user->id] = '日志写入失败';
                        break;
                    }
                }
            }
            // 旧数据中部分为0则新建数据
            if (count($withdraw) < 3) {
                $arr = [$currency_arr['bpt'], $currency_arr['grt'], $currency_arr['tt']];
                foreach ($withdraw as $val) {
                    foreach ($arr as $key => $value) {
                        if ($value == $val->currency_id) {
                            unset($arr[$key]);
                        }
                    }
                }
                foreach ($arr as $val) {
                    $name = $currency_id[$val];
                    if ($v->$name != 0) {
                        $res = NodeService::addCurrencyLogs($user->id, $val, $v->$name, $node->id);
                        $w = BUserRechargeWithdraw::find()->where(['user_id' => $user->id, 'remark' => '添加节点转入积分', 'currency_id' => $val])->one();
                        if (!self::addUpdateLogs('user_recharge_withdraw', 'all_data', (string)json_encode($w->toArray()), 'add', $w->id)) {
                            $transaction->rollBack();
                            $msg[$user->id] = '日志写入失败';
                            break;
                        }
                        $d = BUserCurrencyDetail::find()->where(['relate_table' => 'user_recharge_withdraw', 'currency_id' => $val, 'relate_id' => $w->id])->one();

                        if (!self::addUpdateLogs('user_currency_detail', 'all_data', (string)json_encode($d->toArray()), 'add', $d->id)) {
                            $transaction->rollBack();
                            $msg[$user->id] = '日志写入失败';
                            break;
                        }
                        $f = BUserCurrencyFrozen::find()->where(['type' => BUserCurrencyFrozen::$TYPE_ELECTION, 'currency_id' => $val, 'relate_id' => $node->id])->one();
                        if (!self::addUpdateLogs('user_currency_frozen', 'all_data', (string)json_encode($f->toArray()), 'add', $f->id)) {
                            $transaction->rollBack();
                            $msg[$user->id] = '日志写入失败';
                            break;
                        }
                        if ($res->code != 0) {
                            $transaction->rollBack();
                            $msg[$user->id] = '数据模拟失败';
                            break;
                        }
                    }
                }
            }
            if (empty($msg[$user->id])) {
                if (!self::addUpdateLogs('node', 'grt', (string)$node->grt, (string)$v->grt, $node->id)) {
                    $transaction->rollBack();
                    $msg[$user->id] = '日志写入失败';
                    continue;
                }
                if (!self::addUpdateLogs('node', 'tt', (string)$node->tt, (string)$v->tt, $node->id)) {
                    $transaction->rollBack();
                    $msg[$user->id] = '日志写入失败';
                    continue;
                }
                if (!self::addUpdateLogs('node', 'bpt', (string)$node->bpt, (string)$v->bpt, $node->id)) {
                    $transaction->rollBack();
                    $msg[$user->id] = '日志写入失败';
                    continue;
                }
                $node->grt = $v->grt;
                $node->tt = $v->tt;
                $node->bpt = $v->bpt;
                
                if (!$node->save()) {
                    $transaction->rollBack();
                    $msg[$user->id] = $node->getFirstErrorText();
                }
                
                $v->status = 1;
                if (!$v->save()) {
                    $transaction->rollBack();
                    $msg[$user->id] = $v->getFirstErrorText();
                }
                UserService::resetCurrency($user->id, $currency_arr['grt']);
                UserService::resetCurrency($user->id, $currency_arr['bpt']);
                UserService::resetCurrency($user->id, $currency_arr['tt']);
                $transaction->commit();
            }
        }
        
        if (count($msg) > 0) {
            var_dump($msg);
            Yii::error(json_encode($msg), 'update');
            return false;
        } else {
            echo '执行完成'.PHP_EOL;
            Yii::info('执行成功', 'update');
            return true;
        }
    }

    public static function addUpdateLogs($table_name, $field_name, $old_data, $new_data, $data_id, $sql = '')
    {
        $log = new BUpdateLog();
        $log->table_name = $table_name;
        $log->field_name = $field_name;
        $log->old_data = $old_data;
        $log->new_data = $new_data;
        $log->data_id = $data_id;
        // $log->sql = $sql;
        $log->create_time = time();
        $return =  $log->save();
        if (!$return) {
            echo $log->getFirstErrorText();
        }
        return $return;
    }

    public static function begin_other()
    {
        $data = BUpdateDataOther::find()->where(['status' => BUpdateDataOther::$STATUS_OFF])->all();
        foreach ($data as $v) {
            if ($v->type == 1) {
                // 清空用户推荐关系及相关数据
            }
        }
    }

    // 更改所有用户上级列表
    public static function update_recommend_begin()
    {
        echo '开始'.PHP_EOL;
        $sql = "UPDATE `gr_contest`.`gr_user_recommend` SET `parent_list` = ''";
        $connection=\Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rowCount=$command->execute();
        $all_data = BUserRecommend::find()->all();
        $all_arr = [];
        foreach ($all_data as $v) {
            $all_arr[$v['user_id']] = $v;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        $msg = [];
        foreach ($all_data as $v) {
            $parent = $all_arr[$v->parent_id] ?? false;
            if ($parent && $parent->parent_list != '') {
                $str = $parent->parent_list . ',' . $v->parent_id;
            } else {
                $str = $v->parent_id;
            }
            $user = $v;
            $user->parent_list = $str;
            if (!$user->save()) {
                $msg[] = $user->getFirstErrorText();
                break;
            }
            $sql = "UPDATE `gr_contest`.`gr_user_recommend` SET `parent_list` = CONCAT('".$str."',',',`parent_list`) where `parent_list` like '".$v->user_id.',%'."' || `parent_list` = $v->user_id";
            $connection=\Yii::$app->db;
            $command=$connection->createCommand($sql);
            $rowCount=$command->execute();
            echo '用户'.$v->user_id.'修改结束'.PHP_EOL;
        }
        if (count($msg) > 0) {
            $transaction->rollBack();
            var_dump($msg);
            Yii::error(json_encode($msg), 'update_recommend');
            return false;
        } else {
            $transaction->commit();
            echo '执行完成'.PHP_EOL;
            Yii::info('执行成功', 'update_recommend');
            return true;
        }
    }
    // 检查用户推荐关系是否循环
    public static function checkRecommend()
    {
        $all_data = BNodeRecommend::find()->all();
        $arr = [];
        foreach ($all_data as $v) {
            $parent = BUser::find()->where(['id' => $v->parent_id])->one();
            if ($parent->parent_list != '') {
                $str = $parent->parent_list . ',' . $v->parent_id;
            } else {
                $str = $v->parent_id;
            }
            $arr[$v->user_id] = $str;
            foreach ($arr as $key => $val) {
                if ($val == $v->user_id || substr($val, 0, strlen($v->user_id)+1) == $v->user_id.',') {
                    $arr[$key] = $str. ',' . $val;
                }
            }
        }
        foreach ($arr as $k => $v) {
            $this_arr = explode(',', $v);
            if (in_array($k, $this_arr)) {
                echo $k.',';
            }
        }
    }

    // 重建node_recommend数据
    public static function createNodeRecommend($type)
    {
        echo '开始'.PHP_EOL;
        $transaction = \Yii::$app->db->beginTransaction();
        $msg = [];
        //清空原数据并重新转移
        if ($type == 1) {
            $sql = "delete from gr_node_recommend";
            $connection=\Yii::$app->db;
            $command=$connection->createCommand($sql);
            $rowCount=$command->execute();
            echo '删除所有原始数据'.PHP_EOL;
            $all_data = BUserRecommend::find()->all();
            foreach ($all_data as $v) {
                if ($v->node_id == 0) {
                    echo '第'.$v->id.'数据用户非节点'.PHP_EOL;
                    continue;
                }
                $node = BNode::find()->where(['user_id' => $v->user_id])->one();
                if ($node->type_id == 1) {
                    echo '第'.$v->id.'数据用户是超级节点'.PHP_EOL;
                    continue;
                }
                $parent_node = BNode::find()->where(['user_id' => $v->parent_id])->one();
                if ($parent_node) {
                    $node_recommend = new BNodeRecommend();
                    $node_recommend->user_id = $v->user_id;
                    $node_recommend->parent_id = $v->parent_id;
                    $node_recommend->node_id = $v->node_id;
                    $node_recommend->amount = $v->amount;
                    $node_recommend->create_time = $v->create_time;
                    if (!$node_recommend->save()) {
                        echo '第'.$v->id.'数据转移出错'.PHP_EOL;
                        
                        $msg[$v->id] = $node_recommend->getFirstErrorText();
                    }
                    echo '第'.$v->id.'数据转移成功'.PHP_EOL;
                } else {
                    echo '第'.$v->id.'数据推荐人非节点'.PHP_EOL;
                }
            }
        }
        // 更新parent_list数据
        $sql = "UPDATE `gr_contest`.`gr_node_recommend` SET `parent_list` = ''";
        $connection=\Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rowCount=$command->execute();
        $all_data = BNodeRecommend::find()->all();
        $all_arr = [];
        foreach ($all_data as $v) {
            $all_arr[$v['user_id']] = $v;
        }
        foreach ($all_data as $v) {
            $parent = $all_arr[$v->parent_id] ?? false;
            if ($parent && $parent->parent_list != '') {
                $str = $parent->parent_list . ',' . $v->parent_id;
            } else {
                $str = $v->parent_id;
            }
            $user = $v;
            $user->parent_list = $str;
            if (!$user->save()) {
                $msg[] = $user->getFirstErrorText();
                break;
            }
            $sql = "UPDATE `gr_contest`.`gr_node_recommend` SET `parent_list` = CONCAT('".$str."',',',`parent_list`) where `parent_list` like '".$v->user_id.',%'."' || `parent_list` = $v->user_id";
            $connection=\Yii::$app->db;
            $command=$connection->createCommand($sql);
            $rowCount=$command->execute();
            echo '用户'.$v->user_id.'修改结束'.PHP_EOL;
        }
        if (count($msg) > 0) {
            var_dump($msg);
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'update');
            return false;
        } else {
            $transaction->commit();
            echo '执行完成'.PHP_EOL;
            Yii::info('执行成功', 'update');
            return true;
        }
    }

    // 补完历史节点申请数据
    public static function createOldUpgrade()
    {
        echo '开始'.PHP_EOL;
        $transaction = \Yii::$app->db->beginTransaction();
        $data = BNode::find()
        ->from(BNode::tableName()." A")
        ->select(['A.*', 'C.weixin', 'C.grt_address', 'C.tt_address', 'C.bpt_address'])
        ->join('left join', BNodeUpgrade::tableName().' B', 'A.user_id = B.user_id && B.status = '.BNodeUpgrade::STATUS_ACTIVE)
        ->join('left join', BUserOther::tableName().' C', 'A.user_id = C.user_id')
        ->where(['in', 'A.status', [0, 1, 2]])
        ->andWhere(['B.id' => null])
        ->asArray()->all();
        $msg = [];
        foreach ($data as $v) {
            $node_upgrade = new BNodeUpgrade();
            $node_upgrade->user_id = $v['user_id'];
            $node_upgrade->weixin = $v['weixin'] ?? '';
            $node_upgrade->grt_address = $v['grt_address'] ?? '';
            $node_upgrade->tt_address = $v['tt_address'] ?? '';
            $node_upgrade->bpt_address = $v['bpt_address'] ?? '';
            $node_upgrade->name = $v['name'];
            $node_upgrade->logo = $v['logo'];
            $node_upgrade->desc = $v['desc'];
            $node_upgrade->scheme = $v['scheme'];
            $node_upgrade->type_id = $v['type_id'];
            $node_upgrade->status = BNodeUpgrade::STATUS_ACTIVE;
            $node_upgrade->status_remark = '已通过';
            $node_upgrade->grt = $v['grt'];
            $node_upgrade->tt = $v['tt'];
            $node_upgrade->bpt = $v['bpt'];
            $node_upgrade->examine_time = $v['create_time'];
            $node_upgrade->old_type = 0;
            if (!$node_upgrade->save()) {
                echo '节点'.$v['name'].'数据填充错误'.PHP_EOL;
                $msg[$v->id] = $node_upgrade->getFirstErrorText();
                break;
            }
            echo '节点'.$v['name'].'数据填充完成'.PHP_EOL;
        }

        if (count($msg) > 0) {
            var_dump($msg);
            $transaction->rollBack();
            Yii::error(json_encode($msg), 'update');
            return false;
        } else {
            $transaction->commit();
            echo '执行完成'.PHP_EOL;
            Yii::info('执行成功', 'update');
            return true;
        }
    }
}
