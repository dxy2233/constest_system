<?php
use yii\helpers\Html;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>管理后台</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php echo \yii\helpers\Html::csrfMetaTags(); ?>
    <link rel="stylesheet" href="/adminlte/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/adminlte/dist/css/font-awesome.min.css">
    <link rel="stylesheet" href="/adminlte/dist/css/ionicons.min.css">
    <link rel="stylesheet" href="/adminlte/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="/adminlte/dist/css/skins/all-skins.min.css">
    <style type="text/css">
        html {
            overflow: hidden;
        }
    </style>
    <!--[if lt IE 9]>
    <script src="/adminlte/plugins/ie9/html5shiv.min.js"></script>
    <script src="/adminlte/plugins/ie9/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">

        <header class="main-header">
            <!-- Logo -->
            <a href="#" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">
                    <b>后台</b>
                </span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">
                    <b>管理后台</b>
                </span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img style="background: #ffffff;border: solid 1px #ffffff" src="/static/image/admin_user_avatar.png" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>
                            <?php echo \Yii::$app->user->identity->name ?>
                        </p>
                        <a href="#">
                            <i class="fa fa-circle text-success"></i> 在线</a>
                        <?= Html::a(
                        '退出',
                        ['/logout'],
                        ['data-method' => 'post', 'class' => '']
                    ) ?>
                    </div>
                </div>
                <!-- search form -->
                <form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="搜索菜单...">
                        <span class="input-group-btn">
                            <button type="button" name="search" id="search-btn" class="btn btn-flat" onclick="search_menu()">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </form>
                <!-- /.search form -->
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">

                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" id="content-wrapper" style="min-height: 421px;">
            <!--bootstrap tab风格 多标签页-->
            <div class="content-tabs">
                <button class="roll-nav roll-left tabLeft" onclick="scrollTabLeft()">
                    <i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs menuTabs tab-ui-menu" id="tab-menu">
                    <div class="page-tabs-content" style="margin-left: 0px;">

                    </div>
                </nav>
                <button class="roll-nav roll-right tabRight" onclick="scrollTabRight()">
                    <i class="fa fa-forward" style="margin-left: 3px;"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown tabClose" data-toggle="dropdown">
                        页签操作
                        <i class="fa fa-caret-down" style="padding-left: 3px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" style="min-width: 128px;">
                        <li>
                            <a class="tabReload" href="javascript:refreshTab();">刷新当前</a>
                        </li>
                        <li>
                            <a class="tabCloseCurrent" href="javascript:closeCurrentTab();">关闭当前</a>
                        </li>
                        <li>
                            <a class="tabCloseAll" href="javascript:closeOtherTabs(true);">全部关闭</a>
                        </li>
                        <li>
                            <a class="tabCloseOther" href="javascript:closeOtherTabs();">除此之外全部关闭</a>
                        </li>
                    </ul>
                </div>
                <button class="roll-nav roll-right fullscreen" onclick="App.handleFullScreen()">
                    <i class="fa fa-arrows-alt"></i>
                </button>
            </div>
            <div class="content-iframe " style="background-color: #ffffff; ">
                <div class="tab-content " id="tab-content">

                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2017
                <a href="#">贵人大数据区块链</a>.</strong> All rights reserved.
        </footer>


    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
    <script src="/adminlte/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="/adminlte/bootstrap/js/bootstrap.min.js"></script>
    <!-- Slimscroll -->
    <script src="/adminlte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="/adminlte/plugins/fastclick/fastclick.js"></script>
    <script src="/adminlte/dist/js/app.js"></script>
    <script src="/adminlte/dist/js/app_iframe.js"></script>

    <script type="text/javascript" src="/static/js/yii.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>


    <script type="text/javascript">
        /**
         * 本地搜索菜单
         */
        function search_menu() {
            //要搜索的值
            var text = $('input[name=q]').val();

            var $ul = $('.sidebar-menu');
            $ul.find("a.nav-link").each(function() {
                var $a = $(this).css("border", "");

                //判断是否含有要搜索的字符串
                if ($a.children("span.menu-text").text().indexOf(text) >= 0) {

                    //如果a标签的父级是隐藏的就展开
                    $ul = $a.parents("ul");

                    if ($ul.is(":hidden")) {
                        $a.parents("ul").prev().click();
                    }

                    //点击该菜单
                    $a.click().css("border", "1px solid");

                    //                return false;
                }
            });
        }



        $(function() {
            App.setGlobalImgPath("adminlte/dist/img/");

            addTabs({
                id: '10000',
                title: '首页',
                close: false,
                url: 'welcome',
                urlType: "relative"
            });

            App.fixIframeCotent();

            var menus = [{
                id: "10000",
                text: "首页",
                icon: "fa fa-dashboard",
                url: '/welcome/index',
                children: [{
                    id: "10010",
                    text: "首页",
                    targetType: "iframe-tab",
                    url: '/welcome/index',
                }]
            }];

            // 当前管理员所拥有的权限
            var actionStr = "<?php echo $actionStr;?>";

            // 所有权限
            if (actionStr === '*') {
                // 绘制菜单
                $('.sidebar-menu').sidebarMenu({
                    data: menus
                });
                return;
            }

            // 找菜单
            for (var i = 0; i < menus.length;) {
                // 存在子菜单
                if (menus[i]['children']) {

                } else {
                    // 不存在子菜单
                    // 父菜单下的子菜单被删除完，父菜单也应该被删除（url=''）
                    if (menus[i].url === '') {
                        // 删除该菜单
                        menus.splice(i, 1);
                    }
                    if (actionStr.indexOf(menus[i].url.substring(1)) < 0) {
                        // 未找到该权限
                        // 删除该菜单
                        menus.splice(i, 1);
                        continue;
                    }
                }
                i++;
            }

            // 绘制菜单
            $('.sidebar-menu').sidebarMenu({
                data: menus
            });

        });
    </script>

</body>

</html>