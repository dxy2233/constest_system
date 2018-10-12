<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;


use yii\base\Widget;

class BannerWidget extends FormWidget{

    public $imageUrl = null;
    public $local = false;
    public $useOss = true;
    public $disabled = false;
    public function init() {
        parent::init();
        $this->id = '{{property.id}}';

    }


    public function run() {
        if ($this->local) {
            return $this->render('banner_upload_local');
        } else {
            return $this->render('banner_upload');
        }
    }

}