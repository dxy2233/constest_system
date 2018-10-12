<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\Article */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="barticle-search row">

    <?php $form = ActiveForm::begin([
            'options'=>[
                'class '=>'form-horizontal'
            ],
            'fieldConfig' => [
                'template' => "<div class=\"col-sm-12\">{input}</div>\n",
                'labelOptions' => ['class' => 'col-sm-3 control-label'],  //修改label的样式
            ],
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

    <div class="col-sm-2">
        <?php echo $form->field($model, 'name')->textInput(['placeholder'=>'姓名']) ?>
    </div>
    <div class="col-sm-2">
        <?php echo $form->field($model, 'idcard')->textInput(['placeholder'=>'身份证号']) ?>
    </div>
    <div class="col-sm-2">
        <?php echo $form->field($model, 'mobile')->textInput(['placeholder'=>'手机']) ?>
    </div>
    <div class="col-sm-2">
        <?php echo $form->field($model, 'sex')
          ->dropDownList(
              array(1 => '男', 2 => '女'),
              ['prompt'=>'-- 性别 --','style'=>'width:120px']
          )  ?>
    </div>

    <div class="col-sm-3">
        <div class="form-group">
            <?php echo Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
            <?php echo Html::a('重置', Url::canonical(), ['class' => 'btn btn-danger']) ?>
        </div>
    </div>
    <div class="col-xs-12">
        <h4 class="page-header"></h4>
    </div>
    <?php ActiveForm::end(); ?>

</div>