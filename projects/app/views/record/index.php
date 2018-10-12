<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use phpDocumentor\Reflection\Types\Nullable;
use common\models\business\BRecord;

$this->title = '贵生态千人豪游之旅报名系统';

/* @var $this yii\web\View */
/* @var $searchModel app\Models\RecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
// $this->params['breadcrumbs'][] = $this->title;
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <meta name="keywords" content="<?= Html::encode($this->title) ?>" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.0.min.js"></script>
    <!-- Custom Theme files -->
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <!-- Custom Theme files -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
</head>

<body>
    <!--container-->
    <div class="container">
        <?php
            if (\Yii::$app->session->hasFlash('success')) {
                echo '<div class="alert alert-success" role="alert">'.\Yii::$app->session->getFlash('success').'</div>';
            }
            if (\Yii::$app->session->hasFlash('error')) {
                echo '<div style="color:red" class="alert alert-success" role="alert">'.\Yii::$app->session->getFlash('error').'</div>';
            }
        ?>
        <!--main-content-->
        <!--top-header-->
        <!--inner-content-->
        <div class="inner-content">
            <!--web-forms-->
            <div class="web-forms">
                <!--first-one-->
                <div class="col-md-12 first-one">
                    <div class="first-one-inner">
                        

                        <?php if (1) {
            ?>
            <h3 class="tittle">贵生态千人豪游之旅<br/><br/>报名已结束</h3>
            <div class="message">
                        咨询服务热线：177-8546-3627<br/>
                        <img style="max-width:100%;" src="/images/wrecode.jpg" /><br/>
                        </div>
                        <?php
        } else {
            ?>
                        <h3 class="tittle">贵生态千人豪游之旅报名系统</h3>
                        <?php $form = ActiveForm::begin(['options'=> [
                            'enctype' => 'multipart/form-data',
                            'class '=>'record'
                        ]]); ?>

                        <span style="color:red">*</span>姓名（如 张三）：
                        <?= $form->field($model, 'name')->label(false)->textInput(['maxlength' => true]) ?>

                        <span style="color:red">*</span>姓名拼音 大写（如 张三 ZHANG SAN 李四浩 LI SI HAO 每个拼音中加一个空格）：
                        <?= $form->field($model, 'name_pin')->label(false)->textInput(['maxlength' => true]) ?>
                        <span style="color:red">*</span>性别：
                        <div class="clearfix"></div>
                        <div style="line-height:64px;" class="record-redio">
                            <?php $model->sex = '1' ?>
                            <?= $form->field($model, 'sex')->radioList([
                                '1' => '男',
                                '2' => '女'
                            ])->label(false) ?>
                        </div>
                        <div class="clearfix"></div>
                        <span style="color:red">*</span>身份证号：
                        <?= $form->field($model, 'idcard')->label(false)->textInput(['maxlength' => true]) ?>
                        <span style="color:red">*</span>手机号：
                        <?= $form->field($model, 'mobile')->label(false)->textInput(['maxlength' => true]) ?>
                        <span style="color:red">*</span>有无护照（若无护照请尽快自行前往当地出入境管理局办理，并于9月25日前联系我方客服（客服电话、微信同号：177-8546-3627）提交身份证、联系方式、护照（或者护照回执单）三合一的照片。注意护照有效期须在2019年5月1日之后，并确保护照上有2页以上的空白签证页用于敲盖签证章。）：

                        <div class="record-redio" style="line-height:64px;">
                            <?php $model->is_passport = '0' ?>
                            <?= $form->field($model, 'is_passport')->radioList([
                                '0' => '无',
                                '1' => '有'
                            ])->label(false) ?>
                        </div>
                        <span style="color:red">*</span>证件照(请按照示例要求提交清晰的身份证、联系方式及护照三合一的照片，若无护照，请尽快办理，并且先提交身份证正面照，后续我们将联系您收集如示例要求的照片。)
                        <a style="color:blue;" href="javascript:;" data-toggle="modal" data-target="#myModal" class="translate_a">点击查看示例</a> ：
                        <?=$form->field($model, 'photo[]')->label(false)->fileInput(['accept' => 'image/*']) ?>
                        
                        <span style="color:red">*</span>我的身份：
                        <?php $model->my_identity = '5' ?>
                        <?= $form->field($model, 'my_identity')->label(false)
                        ->dropDownList(
                            BRecord::getMyIdentity(),
                            ['prompt'=>'--请选择--','style'=>'width:140px']
                        )  ?>
                        <span style="color:red">*</span>（<span id="shop_remark" style="color:blue"></span>）：
                        <div class="form-group field-record-shop_name required">
                        <input type="text" id="record-shop_name" class="form-control" name="Record[shop_name]" aria-required="true">
                        <div class="help-block"></div>
                        </div>
                        <span style="color:red">*</span>您的贵生态钱包帐号（贵生态APP帐号）：
                        <?= $form->field($model, 'wallet_number')->label(false)->textInput(['maxlength' => true]) ?>
                        <span style="color:red">*</span>您的贵生态钱包地址：<br/>
                        <?= $form->field($model, 'wallet_address')->label(false)->textInput(['maxlength' => true]) ?>

                        贵生态豪游行贵人通收款钱包二维码及地址：
                        <img style="max-width:100%;" src="/images/qrcode.jpg" /><br/>
                        j3hX9Fv4HePZd3UgcVLqAtFkkShDzbyBzR<br/><br/>

                        <span style="color:red">*</span>贵人通转账截图（您的邮轮舱位预订金）
                        <?=$form->field($model, 'photo2[]')->label(false)->fileInput(['accept' => 'image/*']) ?>
                        <span style="color:red">*</span>紧急联系人：
                        <?= $form->field($model, 'urgent_name')->label(false)->textInput(['maxlength' => true]) ?>
                        <span style="color:red">*</span>紧急联系人手机号码：
                        <?= $form->field($model, 'urgent_tel')->label(false)->textInput(['maxlength' => true]) ?>
                        <br/>
                        <div class="message">
                        若您已提交过报名，需要修改信息请通过下方的二维码或者电话联系客服！<br/>
                        咨询服务热线：177-8546-3627<br/>
                        <img style="max-width:100%;" src="/images/wrecode.jpg" /><br/>
                        </div>
                        <div class="submit">
                            <?= Html::submitButton('提交') ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php ActiveForm::end(); ?>
                        <?php
        }?>
                    </div>
                </div>

            </div>
            <!--//web-forms-->

        </div>
        <!--main-content-->

    </div>
    <!--//container-->
</body>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div style="height:50%;width:80%;margin:10% auto;text-align:center;">
        <img style="max-width:90%;" src="/images/demo.jpg" />
    </div>
</div>

</html>
<script>
    $(".record").submit(function(e) {
        if ($.trim($("#record-name").val()) == "") {
            alert("姓名不能为空");
            $("#record-name").focus();
            return false;
        }

        if ($.trim($("#record-name_pin").val()) == "") {
            alert("姓名拼音不能为空");
            $("#record-name_pin").focus();
            return false;
        }
        if ($.trim($("#record-idcard").val()) == "") {
            alert("身份证号不能为空");
            $("#record-idcard").focus();
            return false;
        }
        if ($.trim($("#record-photo").val()) == "") {
            alert("身份证正面照不能为空");
            return false;
        }
        if ($.trim($("#record-mobile").val()) == "") {
            alert("手机号不能为空");
            $("#record-mobile").focus();
            return false;
        }
        if ($.trim($("#record-shop_name").val()) == "") {
            alert("店铺名不能为空");
            $("#record-shop_name").focus();
            return false;
        }
        if ($.trim($("#record-urgent_name").val()) == "") {
            alert("紧急联系人不能为空");
            $("#record-urgent_name").focus();
            return false;
        }
        if ($.trim($("#record-urgent_tel").val()) == "") {
            alert("紧急联系人手机号码不能为空");
            $("#record-urgent_tel").focus();
            return false;
        }
        if ($("#record-name").val().length > 20) {
            alert("姓名长度不能超过20");
            $("#record-name").focus();
            return false;
        }
        if ($("#record-name_pin").val().length > 20) {
            alert("姓名拼音长度不能超过20");
            $("#record-name_pin").focus();
            return false;
        }
        if ($("#record-idcard").val().length > 18) {
            alert("身份证不能超过18");
            $("#record-idcard").focus();
            return false;
        }
        if ($("#record-idcard").val().length < 15) {
            alert("身份证不能少于15");
            $("#record-idcard").focus();
            return false;
        }
        if ($("#record-mobile").val().length > 20) {
            alert("手机号不能超过20");
            $("#record-mobile").focus();
            return false;
        }
        if ($("#record-shop_name").val().length > 20) {
            alert("店铺名称不能超过20");
            $("#record-shop_name").focus();
            return false;
        }
        if ($("#record-urgent_name").val().length > 20) {
            alert("紧急联系人不能超过20");
            $("#record-urgent_name").focus();
            return false;
        }
        if ($("#record-urgent_tel").val().length > 20) {
            alert("紧急联系人手机号码长度不能超过20");
            $("#record-urgent_tel").focus();
            return false;
        }
        $("button[type='submit']").attr('disabled', 'disabled');
        alert('提交中，请稍等')
    });
    function change_type(){
        let html_v = '';
            switch ($('select[name="Record[my_identity]"]').val()) {
                case '5':
                    html_v = 'XXX区级服务中心'
                    break;
            
                case '6':
                    html_v = 'XXX省级服务中心'
                    break;
            
                case '7':
                    html_v = 'XXX省XXX市级服务中心'
                    break;
            
                case '4':
                    html_v = 'XXX家属'
                    break;
            
                default:
                    html_v = '×××省×××市×××店'
                    break;
            }
            $('#shop_remark').html(html_v);
    }
    $(function(){
        $('select[name="Record[my_identity]"]').change(function(){
            change_type();
        })
        change_type();
    })
</script>
<script src="/other/bootstrap/js/bootstrap.min.js"></script>
<script src="/other/dist/js/app.js"></script>
<script src="/other/dist/js/demo.js"></script>
<script src="/other/dist/js/app_iframe.js"></script>