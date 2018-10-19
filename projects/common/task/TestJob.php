<?php
/*
 * @Author: jayden
 * @Date: 2018-05-18 16:43:03
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-05-18 16:44:07
 */
namespace common\task;

use Yii\base\BaseObject;

class TestJob extends BaseObject implements \yii\queue\JobInterface
{
    public $url;
    public $file;
    
    public function execute($queue)
    {
        file_put_contents($this->file, file_get_contents($this->url));
    }
}

/**
 *
        \Yii::$app->queue->delay(30)->push(new TestJob([
            'url' => 'http://www.auslinkshop.com/themes/xyx/images/login-img.gif',
            'file' => PROJECTS_ROOT . '/runtime/temp/login-img.gif',
        ]));
 */
