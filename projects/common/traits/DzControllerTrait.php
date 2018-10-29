<?php
/*
 * @Author: jayden
 * @Date: 2018-05-22 15:34:21
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-05-22 15:35:10
 */

namespace common\traits;

use common\components\FuncHelper;
use moonland\phpexcel\Excel;
use Yii;

trait DzControllerTrait
{
    protected $respondData;
   
    final protected function download($list, $headers, $fileName = '')
    {
        // echo json_encode($list);
        // exit;
        // $string="";

        // $string .= implode(",", $headers)."\n";
        // foreach ($list as $key => $value) {
        //     $item = [];
        //     foreach ($headers as $k => $val) {
        //         $item[] = (string)$value[$k];
        //     }
        //     $string .= implode(",", $item)."\n"; //用英文逗号分开
        // }
        
        // echo $string;
        // exit;
        if ($fileName == '') {
            $fileName = time();
        }
        
        $columns = [];
        foreach ($headers as $key => $val) {
            $columns[] = $key;
        }
        Excel::export([
            'models'=>$list,
            'fileName'=>$fileName.'.xls',
            'columns'=>$columns,
            'headers'=>$headers,
            ]);
        
        exit;
    }

    /*
    * 安全参数
    */
    final protected function pFloat($name, $default = null)
    {
        $return = Yii::$app->request->post($name, $default);
        if (!empty($return)) {
            return floatval($return);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function pInt($name, $default = null)
    {
        $return = Yii::$app->request->post($name, $default);
        if (!empty($return)) {
            return intval($return);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function pString($name, $default = null)
    {
        $return = Yii::$app->request->post($name, $default);
        if (!empty($return)) {
            return trim($return);
        }
        if ($default !== null) {
            return $default;
        }
        return null;
    }

    final protected function pImage($name, $default = null)
    {
        $return = Yii::$app->request->post($name, $default);
        if (!empty($return)) {
            $return = str_replace(\Yii::$app->params['imgAddress'], '', $return);
            $return = str_replace('/'.\Yii::$app->params['oss']['project'], '', $return);
            $return = preg_replace('/!\d+_\d+/', '', $return);
            return trim($return);
        }
        if ($default !== null) {
            return $default;
        }
        return null;
    }

    final protected function gFloat($name, $default = null)
    {
        $return = Yii::$app->request->get($name, $default);
        if (!empty($return)) {
            return floatval($return);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function gInt($name, $default = null)
    {
        $return = Yii::$app->request->get($name, $default);
        if (!empty($return)) {
            return intval($return);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function gString($name, $default = null)
    {
        $return = Yii::$app->request->get($name, $default);
        if (!empty($return)) {
            return trim($return);
        }
        if ($default !== null) {
            return $default;
        }
        return null;
    }


    final protected function respondJson($errCode, $msg = '', $content = '', bool $camelize = true)
    {
        $post = \Yii::$app->request->post();
        $get = \Yii::$app->request->get();
        if (!empty($post['lang'])) {
            $language = $post['lang'];
        } elseif (!empty($post['lang'])) {
            $language = $get['lang'];
        }
        if (!empty($language) && in_array($language, \Yii::$app->params['languageList'])) {
            $msg = \Yii::t('app', $msg);
        } else {
            // 根据ip地址切换语言
        }
        $msg = $msg === null ? '' : $msg;
        // 将变量转换为驼峰命名法
        $content = $camelize && !is_bool($content)? FuncHelper::keyCamelize($content) : $content;
        $this->respondData = array('code' => $errCode, 'msg' => $msg, 'content' => $content);
        return json_encode($this->respondData, JSON_UNESCAPED_UNICODE);
    }



    final protected function pArray($name)
    {
        if (isset($_POST[$name]) && is_array($_POST[$name])) {
            return $_POST[$name];
        }

        return [];
    }


    final protected function gArray($name)
    {
        if (isset($_GET[$name]) && is_array($_GET[$name])) {
            return $_GET[$name];
        }

        return [];
    }
}
