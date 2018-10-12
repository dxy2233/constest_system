<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/7/29
 * Time: 下午2:04
 */

namespace common\components;


use common\services\AliyunSlsService;
use yii\log\Target;

class SlsTarget extends Target {

    public function init() {
        parent::init();
        $this->exportInterval = 10;
    }

    private function getTypeText($typeNumber) {
        if ($typeNumber == 1) {
            return 'error';
        }

        if ($typeNumber == 4) {
            return 'info';
        }

        if ($typeNumber == 2) {
            return 'warning';
        }
    }

    /**
     * Writes log messages to a file.
     * @throws InvalidConfigException if unable to open the log file for writing
     */
    public function export() {
        $items = [];
        foreach ($this->messages as $msg) {
            $contents = [
                'type' => $this->getTypeText($msg[1]),
                'content' => $msg[0],
                'time' => date('Y-m-d H:i:s', $msg[3]),
                'stack' => var_export($msg[4], true),
                'uid' => \Yii::$app->user ? \Yii::$app->user->id : 0
            ];
            $logItem = new \Aliyun_Log_Models_LogItem();
            $logItem->setTime(time());
            $logItem->setContents($contents);
            if (!isset($items[$msg[2]])) {
                $items[$msg[2]] = [];
            }
            $items[$msg[2]][] = $logItem;
        }

        foreach ($items as $category => $itemArray) {
            AliyunSlsService::getInstance()->sendLog(APP_NAME, $category, $itemArray);
        }
    }
}