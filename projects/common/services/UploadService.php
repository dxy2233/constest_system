<?php

namespace common\services;

use common\models\UploadForm;
use yii\web\UploadedFile;

class UploadService extends ServiceBase
{


    /**
     * 上传图片
     */
    public static function uploadImage($type = 'uploads', $local = false)
    {
        if (!empty(\Yii::$app->params['oss']['project'])) {
            $uploadFileName = \Yii::$app->params['oss']['project'].'/'.$type;
        } else {
            $uploadFileName = $type;
        }

        if (\Yii::$app->request->isPost) {
            if ($local) {
                //本地存储
                $model = new UploadForm();
                //重置为表单文件验证
                if (!empty($_FILES['image_file'])) {
                    $_FILES['UploadForm[imageFile]'] = $_FILES['image_file'];
                    unset($_FILES['image_file']);
                } else {
                    return new ReturnInfo(1, "参数不正确");
                }
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

                $fileName = uniqid(mt_rand(0, 99999)) . '.' . $model->imageFile->extension;
                $filePath =  '/'.$uploadFileName.'/'.date('Y/m/d').'/';
                $fileLocation =  ROOT.'web/home'.$filePath;
                if (!is_dir($fileLocation)) {
                    mkdir($fileLocation, 0777, true);
                }
                //$fileUri = $filePath . $fileName;
                $fileUri = '/'.$type. '/'.date('Y/m/d').'/'. $fileName;

                if ($res = $model->upload($fileLocation.$fileName)) {
                    // 文件上传成功
                    return new ReturnInfo(0, null, $fileUri);
                } else {
                    return new ReturnInfo(1, current($model->getFirstErrors()));
                }
            } else {
                //远程存储
                $file = $_FILES['image_file'];
                if (!$file) {
                    return new ReturnInfo(1, "参数不正确");
                }
                //文件格式检查
                $file['ext'] = pathinfo($file['name'], PATHINFO_EXTENSION);
                $ext = strtolower($file['ext']);
                if (in_array($ext, array('gif','jpg','jpeg','bmp','png','swf','xls','xlsx'))) {
                    $imgInfo = getimagesize($file['tmp_name']);
                    if (empty($imgInfo) || ($ext == 'gif' && empty($imgInfo['bits'])) || strpos($imgInfo['mime'], 'image' !== 0)) {
                        return new ReturnInfo(1, "上传失败,图像格式不正确");
                    }
                } else {
                    return new ReturnInfo(1, "上传失败,不允许的文件格式");
                }
                //文件大小检查,不大于10M
                if ($file['size'] > 10 * 1024 *1024) {
                    return new ReturnInfo(1, "上传失败,图像大小不正确");
                }

                $fileName = uniqid(mt_rand(0, 99999)) . '.' . $ext;
                $fileOss = $uploadFileName.'/'.date('Y/m/d').'/'. $fileName;

                
                $fileUri = '';

                //上传oss
                $ossClient = new \OSS\OssClient(\Yii::$app->params['oss']['AccessKeyId'], \Yii::$app->params['oss']['AccessKeySecret'], \Yii::$app->params['oss']['ossServer']);
                $doesExist = $ossClient->doesObjectExist(\Yii::$app->params['oss']['Bucket'], $fileOss);
                if (!$doesExist) {
                    $ossRes = $ossClient->uploadFile(\Yii::$app->params['oss']['Bucket'], $fileOss, $file['tmp_name']);

                    if (!empty($ossRes['info']['http_code']) && $ossRes['info']['http_code'] == 200) {
                        $fileUri = $ossRes['info']['url'];
                        $fileUriParams = parse_url($fileUri);
                        $fileUri = $fileUriParams['path'];
                        $fileUri = str_replace('/'.\Yii::$app->params['oss']['project'], '', $fileUri);
                        // 文件上传成功
                        return new ReturnInfo(0, null, $fileUri);
                    }
                }

                return new ReturnInfo(1, "上传失败");
            }
        }

        return new ReturnInfo(1, "参数不正确");
    }
}
