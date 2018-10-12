<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\business\BRecord;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\Article */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Barticles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-info">
    <div class="box-body">
        <div class="barticle-index">
            <div class="box-header">



                <div class="pull-right box-tools">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="导出" id="export_excel_btn">
                        <i class="fa fa-download"></i>
                    </button>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="刷新" onclick="javascript:location.reload() ;">
                        <i class="fa fa-repeat"></i>
                    </button>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="返回" onclick="javascript :history.back(-1);">
                        <i class="fa fa-backward"></i>
                    </button>
                </div>
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>

            <div class="box-body">
                <?php
                echo GridView::widget([
        'dataProvider' => $dataProvider,

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name','name_pin','mobile','urgent_name','urgent_tel',[
                'attribute' => 'sex',
                'format' => 'html',
                'filter' => false,
                'value' => function ($data) {
                    return BRecord::getSex($data->sex);
                }
            ],'idcard',[
                'attribute' => 'shop_name',
                'format' => 'html',
                'filter' => false,
                'value' => function ($data) {
                    return BRecord::getShopName($data->shop_name, $data->my_identity);
                }
            ],[
                'attribute' => 'my_identity',
                'format' => 'html',
                'filter' => false,
                'value' => function ($data) {
                    return BRecord::getMyIdentity($data->my_identity);
                }
            ],[
                'attribute' => 'is_passport',
                'format' => 'html',
                'filter' => false,
                'value' => function ($data) {
                    return BRecord::getIsPassport($data->is_passport);
                }
            ],[
                'attribute' => 'created_at',
                'format' => 'html',
                'filter' => false,
                'value' => function ($data) {
                    return date("Y-m-d H:i:s", $data->created_at);
                }
            ],
            ['class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#export_excel_btn").click(function() {
        window.open('/welcome/download' + window.location.search);
    });
</script>