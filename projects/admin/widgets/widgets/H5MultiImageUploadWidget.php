<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


class H5MultiImageUploadWidget extends FormWidget{

    public $attachments = null;
    public $contentLen = 5;
    public $params = false;
    public $maxFiles = 10;
    public $useOss = true;
    public $callBackUrl = null;
    public $callBackFun = false;
    public function init() {
        parent::init();
        $this->id = md5($this->title);
    }


    public function run() {
        return $this->render('h5_multi_image_upload');
    }

}