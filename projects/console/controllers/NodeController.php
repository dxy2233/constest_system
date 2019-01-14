<?php
/**
 * Created by PhpStorm.
 * User: dazhengtech.com
 * Date: 2017/9/12
 * Time: 下午2:28
 */

namespace console\controllers;

use Yii;
use Exception;
use ErrorException;
use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BSetting;
use common\models\business\BNodeUpgrade;

class NodeController extends BaseController
{
    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    /**
     * 补录微店节点激活记录
     *
     * @return void
     */
    public function actionSupplementWei()
    {
        // 微店节点类型
        $weidianType = 5;
        $nodeUpgradeModel = BNodeUpgrade::find()
        ->select(['nu.user_id', 'nu.node_id', 'nu.type_id', 'u.mobile'])
        ->alias('nu')
        ->where([
            'nu.status' => BNodeUpgrade::STATUS_ACTIVE,
            'nu.old_type' => 0
        ])
        ->andWhere(['<>', 'nu.node_id', 0])
        ->andWhere(['<>', 'nu.type_id', $weidianType])
        ->joinWith(['user u' => function ($query) {
            $query->joinWith(['nodeExtend ne'], false);
        }], false)
        ->andWhere(['<>', 'ne.mobile', '']);
        $nodeupgradeData = $nodeUpgradeModel
        ->asArray()
        ->all();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($nodeupgradeData as $nodeUpgrade) {
                $upgradeModel = BNodeUpgrade::find()
                ->where(FuncHelper::arrayOnly($nodeUpgrade, ['user_id', 'node_id']));
                $existsOld = clone $upgradeModel;
                $existsOld->andWhere(['old_type' => $weidianType, 'status' => BNodeUpgrade::STATUS_ACTIVE]);
                if (!$existsOld->exists()) {
                    $updateUpgradeModel = $upgradeModel
                    ->andWhere(['status' => BNodeUpgrade::STATUS_ACTIVE])
                    ->orderBy(['id' => SORT_ASC])
                    ->one();
                    $updateUpgradeModel->old_type = $weidianType;
                    if (!$updateUpgradeModel->save()) {
                        throw new ErrorException($model->getFirstError());
                    }
                    $upgradeData = FuncHelper::arrayOnly($updateUpgradeModel->toArray(), ['user_id', 'node_id', 'name', 'create_time', 'update_time', 'examine_time']);
                    $model = new BNodeUpgrade();
                    $model->attributes = $upgradeData;
                    // 如果手动指定时间后会剔除自动添加时间的行为
                    $timeBehavior = 0;
                    if ($model->getBehavior($timeBehavior) instanceof \yii\behaviors\TimestampBehavior) {
                        // 删除指定  behavior 行为
                        $model->detachBehavior($timeBehavior);
                    }
                    $model->type_id = $weidianType;
                    $model->create_time = $upgradeData['create_time'] - 10;
                    $model->update_time = $upgradeData['update_time'] - 10;
                    $model->examine_time = $upgradeData['examine_time'] - 10;
                    $model->status = BNodeUpgrade::STATUS_ACTIVE;
                    $model->status_remark = '补充激活数据';
                    if (!$model->save()) {
                        throw new ErrorException($model->getFirstError());
                    }
                    echo 'ID：' . $updateUpgradeModel->id . ' 节点名称：' . $updateUpgradeModel->name . ' 补充成功' . PHP_EOL;
                } else {
                    $oldModel = $existsOld->one();
                    echo 'ID：' . $oldModel->id . ' 节点名称：' . $oldModel->name . ' 已存在' . PHP_EOL;
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            var_dump(1, $e->getMessage()) . PHP_EOL;
            $transaction->rollBack();
        }
    }
}
