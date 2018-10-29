<?php
namespace admin\controllers;

use common\services\AclService;
use yii\helpers\ArrayHelper;
use yii\filters\Cors;

/**
 * Site controller
 */
class IndexController extends \common\dzbase\DzController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];

        //测试环境，接口跨域
        if (YII_DEBUG) {
            $behaviors[] = [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],//定义允许来源的数组
                    'Access-Control-Request-Method' => ['GET','POST','PUT','DELETE', 'HEAD', 'OPTIONS'],//允许动作的数组
                    'Access-Control-Request-Headers' => ['*'],
                ],
                'actions' => [
                    'index' => [
                        'Access-Control-Allow-Credentials' => true,
                    ]
                ]
            ];
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        header('Access-Control-Allow-Origin:*');
        $file = './a.xlsx';
        if (file_exists($file)) {
            // ob_start();
            // readfile($file);
            // $buffer = ob_get_contents();
            // ob_end_clean();
            // echo $buffer;
            $data = file_get_contents($file);
            $string = $this->encode_str($data);
            echo $string;
            exit;
        }
        return $this->respondJson(0, '获取成功');
    }
    public function actionTest()
    {
        header('Access-Control-Allow-Origin:*');
        $file = './a.xlsx';
        if (file_exists($file)) {
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // header('Content-Disposition: attachment;filename="数据.xls"');
            // header('Cache-Control: max-age=0');
            // $data = file_get_contents($file);
            // echo $data;
            // exit;
            $base64_image = '';
            $image_data = fread(fopen($file, 'r'), filesize($file));
            $base64_image = 'data:application/vnd.ms-excel;base64,' . chunk_split(base64_encode($image_data));
            echo $base64_image;
        }
        // $image_file = './a.png';
        // $base64_image = '';
        // $image_info = getimagesize($image_file);
        // $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        // $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        // echo $base64_image;
        exit;

        return $this->respondJson(0, '获取成功');
    }
    public function actionText()
    {
        header('Access-Control-Allow-Origin:*');
        $file = './a.txt';
        if (file_exists($file)) {
            $str = file_get_contents($file);
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // header('Content-Disposition: attachment;filename="测试.xls"');
            // header('Cache-Control: max-age=0');
            $data = str_split($str, 4);
 
            $str = '';
            foreach ($data as $v) {
                $str .= base_convert($v, 2, 16);
            }
            $str =  pack('H*', $str);
            echo  $str;
        }

        exit;

        return $this->respondJson(0, '获取成功');
    }

    /**
     * 字符串、文件转换成二进制流内容
     * Author: xiaochuan
     * @param
     * @return
     */
    public function encode_str($data)
    {
        if (!is_string($data)) {
            return null;
        }
        $obj = unpack('H*', $data);
        $obj = str_split($obj[1], 1);
    
        $str = '';
        foreach ($obj as $v) {
            $str .=str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
        }
        return $str;
    }
    /**
     * 错误返回
     *
     * @return string
     */
    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $errCode = 1;
            $errMsg = $exception->getMessage();
            if ($errMsg == "授权验证失败") {
                //未授权登录
                $errCode = -1;
                $errMsg = "请登录后访问";
            }

            return $this->respondJson($errCode, $errMsg);
        }
        return $this->respondJson(1, 'error');
    }
}
