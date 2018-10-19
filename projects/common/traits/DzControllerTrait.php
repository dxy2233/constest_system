<?php
/*
 * @Author: jayden
 * @Date: 2018-05-22 15:34:21
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-05-22 15:35:10
 */

namespace common\traits;

use common\components\FuncHelper;

trait DzControllerTrait
{
    protected $respondData;
   
    /*
    * 安全参数
    */
    final protected function pFloat($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return floatval($_POST[$name]);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function pInt($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return intval($_POST[$name]);
        }
        if ($default !== null) {
            return $default;
        }
        return 0;
    }

    final protected function pString($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return trim($_POST[$name]);
        }
        if ($default !== null) {
            return $default;
        }
        return null;
    }

    final protected function gFloat($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return floatval($_GET[$name]);
        }
        if ($default !== null) {
            return $default;
        }

        return 0;
    }

    final protected function gInt($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return intval($_GET[$name]);
        }

        if ($default !== null) {
            return $default;
        }

        return 0;
    }

    final protected function gString($name, $default = null)
    {
        if (isset($_GET[$name]) && $_GET[$name]) {
            return trim($_GET[$name]);
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
