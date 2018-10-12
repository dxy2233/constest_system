<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


class MultiImageUploadWidgetWithoutName extends FormWidget{

    public $attachments = null;
    public $contentLen = 5;
    public $maxFiles = 10;
    public $renderUI = true;
    public $renderCallback = null;
    public $useOss = true;

    public function init() {
        parent::init();
        if (!$this->id){
            $this->id = md5($this->title);
        }

    }


    public function run() {
        return $this->render('multi_image_upload_without_name');
    }

}