<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/3/7
 * Time: 下午12:11
 */

namespace common\components;


use yii\log\Target;

class ScreenLogger extends Target{

    public function export() {
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        foreach ($messages as $message) {
            echo $message . "\n";
        }
    }

}