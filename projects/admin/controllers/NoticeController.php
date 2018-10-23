<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BSetting;
use common\models\business\BNotice;
use common\components\FuncHelper;

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
        $type = $this->pInt('type');
        $find = BNotice::find();
        if ($type != 0) {
            if ($type == 2) {
                $type = 0 ;
            }
            $find->andWhere(['status' => $type]);
        } else {
            $find->andWhere(['!=', 'status', BNotice::STATUS_DELETE]);
        }
        $count = $find->count();
        $page = $this->pInt('page', 1);
        $find->page($page);
        echo $find->createCommand()->getRawSql();
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
            $v['image'] = FuncHelper::getImageUrl($v['image']);
        }
        $return = [];
        $return['list'] = $data;
        $return['count'] = $count;
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

    // 删除
    public function actionDelNotice()
    {
        $id = $this->pString('id');
        $notice_id = explode(',', $id);
        $notices = BNotice::find()->where(['in','id',$notice_id])->all();
        if (empty($notices)) {
            return $this->respondJson(1, '不存在的公告');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($notices as $notice) {
            $notice->status = BNotice::STATUS_DELETE;
            if (!$notice->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '删除失败', $notice->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '删除成功');
    }

    // 上架
    public function actionOnShelf()
    {
        $id = $this->pString('id');
        $notice_id = explode(',', $id);
        $notices = BNotice::find()->where(['in','id',$notice_id])->all();
        if (empty($notices)) {
            return $this->respondJson(1, '不存在的公告');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($notices as $notice) {
            $notice->status = BNotice::STATUS_ACTIVE;
            if (!$notice->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '上架失败', $notice->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '上架成功');
    }

    // 下架
    public function actionOffShelf()
    {
        $id = $this->pString('id');
        $notice_id = explode(',', $id);
        $notices = BNotice::find()->where(['in','id',$notice_id])->all();
        if (empty($notices)) {
            return $this->respondJson(1, '不存在的公告');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($notices as $notice) {
            $notice->status = BNotice::STATUS_INACTIVE;
            if (!$notice->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '下架失败', $notice->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '下架成功');
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
        $notice['image'] = FuncHelper::getImageUrl($notice['image']);
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
            $notice->url = $url;
        } else {
            $detail = $this->pString('detail');
            if (empty($detail)) {
                return $this->respondJson(1, '正文不能为空');
            }
            $notice->detail = $detail;
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
        $data = BSetting::find()->active(BNotice::STATUS_ACTIVE)->where(['group' => BSetting::$GROUP_NOTICE])->orderBy('sort')->asArray()->all();
        foreach ($data as &$v) {
            $v['initialize'] = json_decode($v['initialize'], true);
        }
        return $this->respondJson(0, "获取成功", $data, false);
    }
}
