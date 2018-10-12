<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/8/11
 * Time: 下午7:46
 */

namespace common\traits;

use common\models\GlobalConf;

/**
 * Controller的基础 trait
 * Class BaseControllerTrait
 * @package common\traits
 */

trait BaseControllerTrait
{

    /*
     * 安全参数
     */
    public function pFloat($name)
    {
        if (isset($_POST[$name])) {
            return floatval($_POST[$name]);
        }

        return 0;
    }

    public function pInt($name)
    {
        if (isset($_POST[$name])) {
            return intval($_POST[$name]);
        }

        return 0;
    }

    public function pString($name)
    {
        if (isset($_POST[$name])) {
            return trim($_POST[$name]);
        }

        return null;
    }

    public function gFloat($name)
    {
        if (isset($_GET[$name])) {
            return floatval($_GET[$name]);
        }

        return 0;
    }

    public function gInt($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return intval($_GET[$name]);
        }

        if ($default !== null) {
            return $default;
        }

        return 0;
    }

    public function gString($name, $default = null)
    {
        if (isset($_GET[$name]) && $_GET[$name]) {
            return trim($_GET[$name]);
        }

        if ($default !== null) {
            return $default;
        }

        return null;
    }

    public function respondJson($errCode, $msg = '', $content = '')
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
        return json_encode(array('code' => $errCode, 'msg' => $msg, 'content' => $content));
    }



    /*
     *
     */
    public function addCssFile($file)
    {
        if (is_array($file)) {
            $this->cssFiles = array_merge($this->cssFiles, $file);
        } else {
            $this->cssFiles[] = $file;
        }
    }

    public function addJsFile($file)
    {
        if (is_array($file)) {
            $this->jsFiles = array_merge($this->jsFiles, $file);
        } else {
            $this->jsFiles[] = $file;
        }
    }

    public function pArray($name)
    {
        if (isset($_POST[$name]) && is_array($_POST[$name])) {
            return $_POST[$name];
        }

        return array();
    }



    /**
     * 把model的验证错误放到一个数组里面
     * @param $errors
     * @return array
     */
    public function getModelErrorString($errors)
    {
        $returnError = [];
        if (!is_array($errors)) {
            return $returnError;
        }

        foreach ($errors as $attributeName => $errors) {
            if (is_array($errors)) {
                $returnError = array_merge($returnError, array_values($errors));
            } else {
                $returnError[] = $errors;
            }
        }

        return implode('<br/>', $returnError);
    }

    public function addError($error)
    {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    public function addInfo($info)
    {
        $this->infos[] = $info;
    }


    /**
     * 把所有的错误从数组变成用换行符隔开的文本
     * @return string
     */
    public function getErrorsAsText()
    {
        $text = '';
        foreach ($this->errors as $error) {
            $text .= $error . '<br/>';
        }

        return $text;
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }


    public function isGet()
    {
        return !\Yii::app()->request->getIsPostRequest();
    }

    public function isPost()
    {
        return \Yii::app()->request->getIsPostRequest();
    }

    public function getPageTitle()
    {
        if (!empty($this->pageTitle)) {
            return $this->pageTitle;
        }

        return null;
    }

    public function errorPage($msg = '没有找到相应页面', $url = null)
    {
        $data['msg'] = $msg;
        $data['type'] = 'error';
        if ($url == null) {
            $url = \Yii::$app->request->referrer;
        }
        $data['jumpUrl'] = $url;
        return $this->render('//tip/tip', $data);
    }

    public function successPage($msg, $url = null)
    {
        $data['msg'] = $msg;
        $data['type'] = 'ok';
        if ($url == null) {
            $url = \Yii::$app->request->referrer;
        }
        $data['jumpUrl'] = $url;
        return $this->render('//tip/tip', $data);
    }
}
