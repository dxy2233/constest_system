<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= Html::encode($this->title) ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php echo \yii\helpers\Html::csrfMetaTags(); ?>
    <link rel="stylesheet" href="/adminlte//bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/adminlte//dist/css/font-awesome.min.css">
    <link rel="stylesheet" href="/adminlte//dist/css/ionicons.min.css">
    <link rel="stylesheet" href="/adminlte//dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/adminlte//dist/css/skins/all-skins.min.css">

    <link rel="stylesheet" href="/static/css/common.css">
    <style type="text/css">
        html {
            /*overflow: hidden;*/
        }
    </style>
    <!--[if lt IE 9]>
    <script src="/adminlte//plugins/ie9/html5shiv.min.js"></script>
    <script src="/adminlte//plugins/ie9/respond.min.js"></script>
    <![endif]-->

    <script src="/adminlte//plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="/adminlte//bootstrap/js/bootstrap.min.js"></script>
    <script src="/adminlte//plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/adminlte//plugins/fastclick/fastclick.js"></script>
    <script src="/adminlte//dist/js/app.js"></script>
    <script src="/adminlte//dist/js/app_iframe.js"></script>
    
    <script type="text/javascript" src="/static/js/common.js"></script>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>

<?=$content?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>