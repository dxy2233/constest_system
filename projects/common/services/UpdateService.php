<?php

namespace common\services;

use common\models\UploadForm;
use common\services\NodeService;
use common\models\business\BUpdateData;
use common\models\business\BUpdateLog;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BCurrency;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BUserCurrencyFrozen;
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
            $withdraw = BUserRechargeWithdraw::find()->where(['user_id' => $user->id, 'remark' => '添加节点充币'])->all();
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
                        $w = BUserRechargeWithdraw::find()->where(['user_id' => $user->id, 'remark' => '添加节点充币', 'currency_id' => $val])->one();
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
            echo '执行完成';
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
}
