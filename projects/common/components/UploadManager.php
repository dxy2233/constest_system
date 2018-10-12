<?php
namespace common\components;

use common\services\QiniuService;

/**
 *
 * User: dazhengtech.com
 * Date: 15/4/30
 * Time: 下午7:55
 */

class UploadManager {

    private $key;
    private $secret;
    private $bucket;
    private $localDest = '/data/upload';


    public function __construct() {
        $this->key = \Yii::$app->params['qiniuKey'];
        $this->secret = \Yii::$app->params['qiniuSecret'];
        $this->bucket = \Yii::$app->params['qiniuBucket'];
    }

    public function uploadImage($context, $callback, $localUploader = true) {


            $config = array(
                'savePath' => $this->getSavePath($context),
                'saveName' => $this->getSaveName(),
                'saveLocal' => $localUploader,
                "maxSize" => 50000 ,
                "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp", '.pdf', '.xls', '.xlsx'),

                'imageDomain' => Yii::app()->params['domain']['image'],
                "localDest" => $this->localDest,

                'accessKey' => $this->key,
                'accessSecret' => $this->secret,
                'bucket' => $this->bucket,
                'host' => 'http://qidoudev.qiniudn.com'
            );
            $uploader = new FileUploader('file', $config);

            $info = $uploader->getFileInfo();
            if ($callback) {
                echo '<script>' . $callback . '(' . json_encode($info) . ')</script>';
            } else {
                echo json_encode($info);
            }

    }

    public function getImageUploadToken($context) {
        $qiniu = new QiniuService(null, $this->key, $this->secret);
        $key = $this->getSavePath($context) . '/' . $this->getSaveName();
        return array($key, $qiniu->getUploadToken($this->bucket, $key));
    }

    private function getSavePath($context) {
        return $context . '/' . date( "Ymd" );
    }

    private function getSaveName($ext = 'jpg') {
        return time() . rand( 1 , 10000 ) . '.' . $ext;
    }

    public function getUploadToken($context, $ext) {
        $qiniu = new QiniuService(null, $this->key, $this->secret);
        $key = $this->getSavePath($context) . '/' . $this->getSaveName($ext);
        return array($key, $qiniu->getUploadToken($this->bucket, $key));
    }



} 