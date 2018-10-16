<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BSetting;
use common\models\business\BNotice;

/**
 * Site controller
 */
class NoticeController extends BaseController
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

    public function actionIndex()
    {
        $type = $this->pInt('type', 1);
        $find = BNotice::find();
        if ($type != 0) {
            $find->andWhere(['status' => $type]);
        }
        $page = $this->pInt('page', 1);
        $find->page($page);
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    // 置顶
    public function actionSetTop()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $notice = BNotice::find()->where(['id' => $id])->one();
        if (empty($notice)) {
            return $this->respondJson(1, '文章不存在');
        }
        $notice->is_top = BNotice::STATUS_ACTIVE;
        if ($notice->save()) {
            return $this->respondJson(0, '操作成功');
        } else {
            return $this->respondJson(1, '操作失败', $notice->getFirstErrorText());
        }
    }

    // 取消置顶
    public function actionSetUnTop()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $notice = BNotice::find()->where(['id' => $id])->one();
        if (empty($notice)) {
            return $this->respondJson(1, '文章不存在');
        }
        $notice->is_top = BNotice::STATUS_INACTIVE;
        if ($notice->save()) {
            return $this->respondJson(0, '操作成功');
        } else {
            return $this->respondJson(1, '操作失败', $notice->getFirstErrorText());
        }
    }


    // 获取文章详细信息
    public function actionGetDetail()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $notice = BNotice::find()->where(['id' => $id])->one();
        if (empty($notice)) {
            return $this->respondJson(1, '文章不存在');
        }
        $notice['create_time'] = date('Y-m-d H:i:s', $notice['create_time']);
        $notice['update_time'] = date('Y-m-d H:i:s', $notice['update_time']);
        return $this->respondJson(0, '获取成功', $notice);
    }

    public function actionEdit()
    {
        $id = $this->pInt('id');

        $notice = BNotice::find()->where(['id' => $id])->one();
        if (empty($notice)) {
            return $this->respondJson(1, '文章不存在');
        }
        $image = $this->pString('image');
        if (!empty($image)) {
            $notice->image = $image;
        }
        $title = $this->pString('title');
        if (empty($title)) {
            return $this->respondJson(1, '标题不能为空');
        }
        $type = $this->pString('type', 0);
        $notice->type = $type;
        $notice->title = $title;
        if ($type == BNotice::TYPE_URL) {
            $url = $this->pString('url');
            if (empty($url)) {
                return $this->respondJson(1, '链接地址不能为空');
            }
        } else {
            $detail = $this->pString('detail');
            if (empty($detail)) {
                return $this->respondJson(1, '正文不能为空');
            }
        }
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $notice->start_time = $str_time;
        $notice->end_time = $end_time;
        $status = $this->pInt('status');
        $notice->status = $status;
        if (!$notice->save()) {
            return $this->respondJson(1, '修改失败', $notice->getFirstErrorText());
        }
        
        return $this->respondJson(0, '修改成功');
    }


    public function actionCreate()
    {
        $notice = new BNotice();

        $image = $this->pString('image');
        if (empty($image)) {
            return $this->respondJson(1, 'LOGO不能为空');
        }
        $notice->image = $image;
        $title = $this->pString('title');
        if (empty($title)) {
            return $this->respondJson(1, '标题不能为空');
        }
        $type = $this->pString('type', 0);
        $notice->type = $type;
        $notice->title = $title;
        if ($type == BNotice::TYPE_URL) {
            $url = $this->pString('url');
            if (empty($url)) {
                return $this->respondJson(1, '链接地址不能为空');
            }
        } else {
            $detail = $this->pString('detail');
            if (empty($detail)) {
                return $this->respondJson(1, '正文不能为空');
            }
        }
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $notice->start_time = $str_time;
        $notice->end_time = $end_time;
        $status = $this->pInt('status');
        $notice->status = $status;
        if (!$notice->save()) {
            return $this->respondJson(1, '添加失败', $notice->getFirstErrorText());
        }
  
        return $this->respondJson(0, '添加成功');
    }


    public function actionSetNotice()
    {
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_NOTICE])->all();
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            $post_item = $this->pString($v->key, '');
            if ($post_item == '') {
                continue;
            }
            $v->value = $post_item;
            
            if (!$v->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, "操作失败", $v->getFirstErrorText());
            }
        }

        $transaction->commit();
        return $this->respondJson(0, "操作成功");
    }


    public function actionGetSettingList()
    {
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_NOTICE])->asArray()->all();
        return $this->respondJson(0, "获取成功", $data);
    }
}
