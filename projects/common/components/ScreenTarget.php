<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 2016/11/8
 * Time: 下午7:29
 */

namespace common\components;


use yii\log\Logger;
use yii\log\Target;

class ScreenTarget extends Target {

    public $exportInterval = 1;

    /**
     * Writes log messages to a file.
     * @throws InvalidConfigException if unable to open the log file for writing
     */
    public function export() {
        foreach ($this->messages as &$msg) {
            if ($msg[1] == Logger::LEVEL_WARNING || $msg[1] == Logger::LEVEL_INFO) {
                $msg[4] = [];
            }
        }
        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";
        echo $text;
    }
}