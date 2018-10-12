<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


class MultiImageUploadWidget extends FormWidget{

    public $attachments = null;
    public $contentLen = 5;
    public $maxFiles = 10;
    public $useOss = true;
    public $showImgWidth = 80;
    public $showImgHeight = 80;
    public $showEditInputWidth = 80;
    public $diyInputPlaceholder = false;

    public function init() {
        parent::init();
        $this->id = md5($this->title);
    }


    public function run() {
        return $this->render('multi_image_upload');
    }

}