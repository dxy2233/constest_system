<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


class MultiFileUploadWidget extends SingleFileUploadWidget {

    public $attachments = null;

    public $maxAllowedCount = 5;

    public function init() {
        parent::init();
        $this->renderFile = 'multi_file_upload';
    }


}