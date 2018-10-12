# 项目说明文件

拉取开发版本时需复制projects/debug_example.php文件更名为debug.php文件

projects/debug.php 文件说明：
1. 如文件存在则为开发版本
2. 文件的配置会覆盖对应应用的配置

projects/params.php 文件说明：
1. 该文件为服务器使用配置参数文件
2. 修改前需和团队其他人员做沟通，谨慎修改

projects/common/config/main.php 文件说明：
1. 该文件为总配置文件，可以放置一些公共的配置文件
2. 该文件可以被应用文件覆盖

