<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/5/11
 * Time: 下午4:14
 */

namespace common\services;

use common\components\NetUtil;
use common\models\business\BAdminLog;
use yii\helpers\Url;

class AdminLogService extends ServiceBase
{
    public static function update($event)
    {
        //未登录，不记录日志
        if (empty(\Yii::$app->user->identity)) {
            return;
        }

        // 具体要记录什么东西，自己来优化$description
        if (!empty($event->changedAttributes)) {
            $desc = '';
            foreach ($event->changedAttributes as $name => $value) {
                $desc .= $name . ':' . $value . '=>' . $event->sender->getAttribute($name) . ', ';
            }
            $desc = substr($desc, 0, -1);
            $description = \Yii::$app->user->identity->name . '修改了' . $event->sender->className() . '(主键'.$event->sender->primaryKey()[0].':' . $event->sender->getAttribute($event->sender->primaryKey()[0]) . '):' . $desc;
            $route = Url::to();
            $userId = \Yii::$app->user->id;
            $data = [
                'route' => $route,
                'content' => $description,
                'ip'=> NetUtil::getIp(),
                'create_time'=> time(),
                'user_id' => $userId
            ];
            $model = new BAdminLog();
            $model->setAttributes($data);
            $model->save();
        }
    }

    public static function delete($event)
    {
        //未登录，不记录日志
        if (empty(\Yii::$app->user->identity)) {
            return;
        }

        $str = '';
        foreach ($event->sender->getAttributes() as $name => $value) {
            $str .= $name . '=>' . $value .', ';
        }
        $description = \Yii::$app->user->identity->name . '删除了' . $event->sender->className() . '(主键'.$event->sender->primaryKey()[0].':' . $event->sender->getAttribute($event->sender->primaryKey()[0]) . '):' .$str;
        $route = Url::to();
        $userId = \Yii::$app->user->id;
        $data = [
            'route' => $route,
            'content' => $description,
            'ip'=> NetUtil::getIp(),
            'create_time'=> time(),
            'user_id' => $userId
        ];
        $model = new BAdminLog();
        $model->setAttributes($data);
        $model->save();
    }


    public static function add($event)
    {
        //未登录，不记录日志
        if (empty(\Yii::$app->user->identity)) {
            return;
        }

        if ($event->sender->className() != 'common\models\business\BAdminLog') {
            $str = '';
            foreach ($event->sender->getAttributes() as $name => $value) {
                $str .= $name . '=>' . $value .', ';
            }
            $description = \Yii::$app->user->identity->name . '插入了' . $event->sender->className() . '(主键'.$event->sender->primaryKey()[0] . '):' .$str;

            $route = Url::to();
            $userId = \Yii::$app->user->id;
            $data = [
                'route' => $route,
                'content' => $description,
                'ip'=> NetUtil::getIp(),
                'create_time'=> time(),
                'user_id' => $userId
            ];
            $model = new BAdminLog();
            $model->setAttributes($data);
            $model->save();
        }
    }
}
