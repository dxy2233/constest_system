<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\business\BArticle */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Barticles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">
  <div class="box-body">
    <div class="barticle-view">
      <div class="box-header">
        <p>

          &nbsp;
        </p>


        <div class="pull-right box-tools">
          <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="刷新" onclick="javascript:location.reload() ;">
            <i class="fa fa-repeat"></i>
          </button>
          <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="返回" onclick="javascript :history.back(-1);">
            <i class="fa fa-backward"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name','name_pin',[
              'attribute' => 'sex',
              'label' =>'性别',
              'format' => 'html',
              'filter' =>false,
              'value' =>function ($data) {
                  return \common\models\business\BRecord::getSex($data->sex);
              }
          ],'idcard','mobile',[
            'attribute' => 'is_passport',
            'label' =>'有无护照',
            'format' => 'html',
            'filter' =>false,
            'value' =>function ($data) {
                return \common\models\business\BRecord::getIsPassport($data->is_passport);
            }
        ],[
          'attribute' => 'photo',
          'label' =>'证件照',
          'format' => 'html',
          'filter' =>false,
          'value' =>function ($data) {
              return \common\models\business\BRecord::getPhoto($data->photo);
          }
      ],[
        'attribute' => 'shop_name',
        'format' => 'html',
        'filter' => false,
        'value' => function ($data) {
            return \common\models\business\BRecord::getShopName($data->shop_name, $data->my_identity);
        }
      ],[
            'attribute' => 'my_identity',
            'label' =>'我的身份',
            'format' => 'html',
            'filter' =>false,
            'value' =>function ($data) {
                return \common\models\business\BRecord::getMyIdentity($data->my_identity);
            }
        ],'wallet_number','wallet_address',[
          'attribute' => 'photo2',
          'label' =>'划拨贵人通凭证（您的邮轮舱位预订金）',
          'format' => 'html',
          'filter' =>false,
          'value' =>function ($data) {
              return \common\models\business\BRecord::getPhoto($data->photo2);
          }
      ],'urgent_name','urgent_tel',[
            'attribute' => 'created_at',
            'format' => 'html',
            'filter' => false,
            'value' => function ($data) {
                return date("Y-m-d H:i:s", $data->created_at);
            }
        ]
        ],
    ]) ?>
      </div>
    </div>
  </div>
</div>