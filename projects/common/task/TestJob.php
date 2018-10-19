<?php
/*
 * @Author: jayden
 * @Date: 2018-05-18 16:43:03
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-05-18 16:44:07
 */
namespace common\task;

use Yii\base\BaseObject;
use common\services\JobService;
use common\models\business\BSetting;

class TestJob extends BaseObject implements \yii\queue\JobInterface
{
    public $url;
    public $file;
    
    public function execute($queue)
    {
        //JobService::beginPut();
        $authUrl = 'http://admin.contest_system.local/vote/now-reload';
        echo 'aaaa';
        $html = file_get_contents($authUrl);
        echo $html;
        // $curl = new Curl();
        // $response = $curl->get($authUrl);
    }
}

/**
 *
        \Yii::$app->queue->delay(10)->push(new TestJob([]));
 */
