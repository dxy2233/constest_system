<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午3:39
 */

// 此文件放置所有的第三方服务配置信息, 开发环境和正式环境一样,只是参数不同, 会合并到 Yii::$app->params里面去
return [
  // 'imgAddress' => 'http://gr-app.oss-cn-hangzhou.aliyuncs.com',
  'imgAddress' => 'http://home.record.local/',


    // （钱包业务）测试地址：http://47.92.80.205:3002/v2
    // （钱包业务）正式地址：https://grtchain.daguobrand.com/v2/
    'JTBusinessUrl'=>'http://47.92.80.205:3002/v2',//井通钱包业务URL
    'JTAccountUrl'=>'',//井通账号体系URL 暂时未用该参数
    'JTAddress'=>'jpFWhXZCEcifZQftfWBPyQmmxTnucRvTbB',//公钥  jHDkGBYN17aXd6xm42cpcYFvLc3oWckxKx 正式；jpFWhXZCEcifZQftfWBPyQmmxTnucRvTbB 测试
    'JTKey'=>'snmhqnej9YopAieT7A8C4uroQupm3',//私钥  shbhRq3KBqbB78nMzNS7NzDgzse1Z 正式； snmhqnej9YopAieT7A8C4uroQupm3 测试
    'JTIssuer'=>'jh195JTSbws2owFT4HW6tvNTxBcygU5jAs',//银关
    'JTWalletActiveAmount'=>0.000001,//激活钱包GRT金额
];
