<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/11/13
 * Time: 下午3:39
 */

// 此文件放置所有的第三方服务配置信息, 开发环境和正式环境一样,只是参数不同, 会合并到 Yii::$app->params里面去
return [
    // 密码加密/解密串
    'authCodeKey' => '2QST9d46soiQpf2Hug8igL0ja0RdOYTk',

    'imgAddress' => 'http://static.vguiren.com',

    'oss'=> [
        'ossServer' => 'http://oss-cn-hangzhou.aliyuncs.com', //服务器外网地址，深圳为 http://oss-cn-shenzhen.aliyuncs.com
        'ossServerInternal' => 'http://oss-cn-hangzhou-internal.aliyuncs.com', //服务器内网地址，深圳为 http://oss-cn-shenzhen-internal.aliyuncs.com
        'AccessKeyId' => 'LTAIv0R9FpkaZA7k', //阿里云给的AccessKeyId
        'AccessKeySecret' => 'khia0Pp7dzRqybJEXAtuUJk7gmwGj4', //阿里云给的AccessKeySecret
        'Bucket' => 'atshuyi', //创建的空间名
        'project' => 'contest_system'
    ],

    //短信通道
    'sendSms'   => false,
    'smsType' => 'cl', //短信平台类型：cl(253短信平台，默认值),aliyun
    'smsKey' => 'CI4037761',
    'smsSecret' => '2NIUGshyBv96b6',
    'smsSig' => '共生态',

    // 图形验证码验证开关
    'imageCaptcha' => false,

    // （钱包业务）测试地址：http://47.92.80.205:3002/v2
    // （钱包业务）正式地址：https://grtchain.daguobrand.com/v2/
    'JTBusinessUrl'=>'http://47.92.80.205:3002/v2',//井通钱包业务URL
    'JTAccountUrl'=>'',//井通账号体系URL 暂时未用该参数
    'JTAddress'=>'',//公钥  jpFWhXZCEcifZQftfWBPyQmmxTnucRvTbB 测试
    'JTKey'=>'',//私钥  snmhqnej9YopAieT7A8C4uroQupm3 测试
    'JTIssuer'=>'jh195JTSbws2owFT4HW6tvNTxBcygU5jAs',//银关
    'JTWalletActiveAmount'=>0.000001,//激活钱包GRT金额
    'JTWallet'=> [
        //激活账户
        'active' => [
            'address' => 'jPjnUjv7ARzTukcr2h2vYuGZmgPnj6EvYz', //公钥
            'key' => 'snXfp7p6nHHZ7LXKEHXsgpdxsHyPK', //私钥
        ],
        //转入账户
        'receipt' => [
            'address' => 'jpFWhXZCEcifZQftfWBPyQmmxTnucRvTbB', //公钥
            'key' => '', //私钥
        ],
        //付款账户
        'payment' => [
            'address' => 'jEnDkuaFSTZn2Ck3TLpu219QW7C5gUuBBQ', //公钥
            'key' => 'shMBVGrwWp5MHPMbHcvxxxhmUi4wS', //私钥
        ],
    ],


    // 货币显示精度 0.000012 小数点位数
    'wallet_precision' => 6,

    // 平台url
    'home_platform_url' => 'http://contest.vguiren.com',
];
