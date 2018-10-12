<?php
use yii\helpers\Html;

$this->title = '登陆';
?>
<style>
    .help-block-error{color:orangered}
    .login-box-body{background: #eeeeee}
</style>
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>&nbsp;</b></a>
    </div>
    <!-- /.login-logo -->
<div class="login-box-body">
    <p class="login-box-msg">
        <b>登录</b>
    </p>
    <form class="" action="/login" method="post">
        <div class="form-group  has-feedback">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input type="text" class="form-control" name="username" placeholder="用户名">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback ">
            <input type="password" class="form-control" name="password" placeholder="密码">
            <span class="glyphicon glyphicon-eye-close form-control-feedback"></span>
        </div>

        <div style="text-align: center">
            <?php if ($this->context->errorMsg) {
    ?>
            <p class="help-block help-block-error">
                <?php echo $this->context->errorMsg; ?>
            </p>
            <?php
} ?>
        </div>
        <div>
            <?= Html::submitButton('登录', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
        </div>
    </form>
</div>
<!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script>
    function changeVode() {
        $.get('/vcode?key=adminLogin&n=' + Math.random(999999), null, function(data) {
            $('#vcode').attr('src', data);
        });
    }
</script>