<?php

namespace admin\controllers;

use common\models\search\SRecord;
use Yii;
use moonland\phpexcel\Excel;
use common\models\business\BRecord;

/**
 * UserScoreLogController implements the CRUD actions for BUserScoreLog model.
 */
class WelcomeController extends BaseController
{

    /**
     * Lists all BUserScoreLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SRecord();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel,'dataProvider' => $dataProvider,]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete($id)
    {
        // $model = $this->findModel($id);
        // $model->status = 0;
        // $model->save();
        // return $this->redirect(['index']);
    
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = SRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // 数据导出
    public function actionDownload()
    {
        $searchModel = new SRecord();
        $searchModel -> pageSize = 0; //获取全部数据
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $data = $dataProvider->getModels();

        $data_arr = [];
        if (count($data) > 0) {
            foreach ($data as $v) {
                $col = $v->GetAttributes();

                $col['sex'] =  BRecord::getSex($col['sex']);
                $col['is_passport'] =  BRecord::getIsPassport($col['is_passport']);
                $col['photo'] = BRecord::getPhotoToString($col['photo']);
                $col['photo2'] = BRecord::getPhotoToString($col['photo2']);
                $col['idcard'] = (string)$col['idcard'].' ';
                $col['urgent_tel'] = (string)$col['urgent_tel'].' ';
                $col['mobile'] = (string)$col['mobile'].' ';
                $col['shop_name'] = BRecord::getShopName($col['shop_name'], $col['my_identity']);
                $col['my_identity'] = BRecord::getMyIdentity($col['my_identity']);
                $col['created_at'] = date("Y-m-d H:i:s", $col['created_at']);
                $data_arr[] = $col;
            }
        }
        Excel::export([
        'models'=>$data_arr,
        'fileName'=>time(),
        'columns'=>['id','name','name_pin','sex','idcard','mobile','is_passport','photo','shop_name','my_identity','wallet_number','wallet_address','photo2','urgent_name','urgent_tel','created_at'],
        'headers'=>[
                'id'=>'id',
                'name' => '用户名',
                'name_pin' => '姓名拼音',
                'wallet_number' => '钱包账号',
                'wallet_address' => '充值地址',
                'mobile' => '手机号码',
                'urgent_name' => '紧急联系人',
                'urgent_tel' => '紧急联系人方式',
                'sex' => '性别',
                'idcard' => '身份证号',
                'my_identity' => '身份',
                'shop_name' => '店铺名',
                
                'is_passport' => '有无护照',
                'photo' => '证件照',
                'photo2' => '划拨贵人通凭证',
                'created_at' => '添加时间'
                ],
        ]);
    }
}
