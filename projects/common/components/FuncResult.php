<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/1/23
 * Time: 下午5:45
 */

namespace common\components;


class FuncResult {

    public $code, $msg, $content;

    public function __construct($errorCode, $msg='', $content = '') {
        $this->code = $errorCode;
        $this->msg = $msg;
        $this->content = $content;
    }

    public function respondeJson() {
        return json_encode(array('code' => $this->code, 'msg' => $this->msg, 'content' => $this->content));
    }

}