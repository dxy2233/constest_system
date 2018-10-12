<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


use yii\base\Widget;

class SingleImageUploadWidget extends FormWidget{

    public $imageUrl = null;
    public $local = false;
    public $useOss = true;
    public $disabled = false;
    public $callback = null;
    public function init() {
        parent::init();
        $this->id = md5(rand(1,1000));
    }


    public function run() {
      
        if ($this->local) {
            return $this->render('single_image_upload_local');
        } else {
            return $this->render('single_image_upload');
        }
    }

}