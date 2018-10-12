<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/8/25
 * Time: 上午11:23
 */

namespace admin\widgets;

/**
 * 单图片上传
 *
 * Class SingleImageUploadWidget
 * @package admin\widgets
 */
class SingleFileUploadWidget extends FormWidget {

    //初始imageurl
    public $imageUrl = null;

    //上传方式 oss | local
    public $uploader = 'oss';

    //回调函数
    public $callback = null;

    //是否限定为图片
    public $imageOnly = true;

    //按钮样式
    public $buttonStyle = 'background-color:#4699ff; color:white;';

    //默认本地保存的图片名称
    public $fileName = null;

    //最多一次允许上传的数量
    public $maxAllowedCount = 1;


    protected $renderFile = 'single_file_upload';
    public function init() {
        parent::init();
        $this->id = md5(rand(1, 1000));
    }


    public function run() {
        if ($this->callback == null) {
            $this->callback = 'uploadProject_' . $this->id;
        }

        $url = sprintf('/uploader/default/file-uploader?context=%s&callback=%s&uploader=%s&imageOnly=%s&buttonStyle=%s&fileName=%s&maxAllowedCount=%s',
            $this->context,
            $this->callback,
            $this->uploader,
            $this->imageOnly,
            urlencode($this->buttonStyle),
            urlencode($this->fileName),
            $this->maxAllowedCount
        );

        return $this->render($this->renderFile, [
            'frameUrl' => $url
        ]);
    }

}