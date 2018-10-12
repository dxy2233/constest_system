<?php
namespace app\controllers;

use Yii;
use common\models\Record;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\Models\RecordSearch;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class RecordController extends \common\dzbase\DzController
{
    public $defaultAction = 'index';
    // 禁止使用框架默认布局
    public $layout = true;

    /**
     * Lists all Record models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Record();

        if ($model->load(Yii::$app->request->post())) {
            $model->photo = UploadedFile::getInstances($model, 'photo');
            $model->photo2 = UploadedFile::getInstances($model, 'photo2');
            //

            $old_data = $model::find()->where(['idcard' => $model->idcard])->one();
            if (!empty($old_data)) {
                \Yii::$app->session->setFlash('error', '此身份证已经注册');
                return $this->redirect('index');
            }
            if ($model->validate()) {
                $upload = $model->upload();
                if (is_array($upload['photo'])) {
                    foreach ($upload['photo'] as $k => $v) {
                        $o = $model->photo[$k];
                        $o->name = $v;
                        $o->tempName = $v;
                    }
                }
                if (is_array($upload['photo2'])) {
                    foreach ($upload['photo2'] as $k => $v) {
                        $o = $model->photo2[$k];
                        $o->name = $v;
                        $o->tempName = $v;
                    }
                }
                if ($model->save()) {
                    \Yii::$app->session->setFlash('success', '提交成功');
                    return $this->redirect('index');
                } else {
                    \Yii::$app->session->setFlash('error', $model->getErrors());
                }
            } else {
                \Yii::$app->session->setFlash('error', $model->getErrors());
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
