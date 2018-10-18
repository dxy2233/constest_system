<?php

namespace common\services;

use shmilyzxt\queue\base\JobHandler;
use common\models\business\BSetting;

class JobService extends JobHandler
{
    public function handle($job, $data)
    {
        if ($job->getAttempts() > 3) {
            $this->failed($job);
        }

        $setting = new BSetting();
        $setting->group = 'test';
        $setting->key = json_code($job);
        $setting->value = json_code($data);
        $setting->save();
        exit;

        //$payload即任务的数据，你拿到任务数据后就可以执行发邮件了
        // TODO 发邮件
    }

    public function failed($job, $data)
    {
        die("发了3次都失败了，算了");
    }
}
