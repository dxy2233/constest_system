<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11 0011
 * Time: 14:56
 */

namespace common\components;


class TcpCommunication
{

    public static $transUnit = 1;
    public static $transResult = 2;
    public static $cancelUnit = 3;
    public static $cancelResult = 4;
    public static $requestResult = 5;


    /**
     * @param array $data
     * @return string
     * FunctionInfo：交易单元集合包--提交用户订单
     */
    public static function assembleTransUnitPacketSubmitOrder( $data = [ ] ) {

        $res = $dataPack = "";
        $res .= pack('h', '1'); //类型

        //数据包
        $dataPack .= pack('V', $data["user_id"]);
        $dataPack .= pack('V', $data["bid_id"]);
        $dataPack .= pack('V', $data["entrust_time"]);

        $dataPack .= pack('d', $data["entrust_price"]);
        $dataPack .= pack('Q', $data["id"]);
        $dataPack .= pack('C', $data["type"]);
        $dataPack .= pack('d', $data["entrust_amount"]);//F 浮點數，V整數

        $res .= pack('V', strlen($dataPack));//长度
        $res = $res.$dataPack;
        return $res;
    }

    /**
     * @param array $data
     * @return string
     * FunctionInfo：交易单元集合包--撤销用户订单
     */
    public static function assembleTransUnitPacketCancelOrder( $data = [ ] ) {

        $res = $dataPack = "";
        $res .= pack('h', '3'); //类型(p_type)

        //数据包
        $dataPack .= pack('V', $data["bid_id"]);

        $dataPack .= pack('Q', $data["id"]);
        $dataPack .= pack('C', $data["type"]);

        $res .= pack('V', strlen($dataPack));//长度
        $res = $res.$dataPack;
        return $res;
    }


    public static function communication( $input ){

        // 定义在params_debug
        $address = \Yii::$app->params['matchAddress'];
        $servicePort = \Yii::$app->params['matchPort'];

        // 创建socket
        $socket = self::createSocket( $address, $servicePort );
        // 连接socket
        self::connectSocket( $socket, $address, $servicePort );

        // 向socket服务器发送消息
        socket_write($socket, $input, strlen($input));
//        echo  "Client send success \n";
//        echo "Reading response:\n";
        // 读取socket服务器发送的消息
        $ptype = self::readSocket( $socket );

        $content = self::goBackContent( $socket );

        // 返回信息
        if($ptype == self::$requestResult) {
            if($content) {
                $sign = true;
            } else {
                $sign = false;
            }
        } else {
            $sign = false;
        }

        socket_close($socket); // 关闭socket连接
        return $sign;
    }

    // 创建并返回一个套接字
    public static function createSocket( $address, $servicePort ){
        // 创建并返回一个套接字（通讯节点）
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            exit();
        }

        return $socket;
    }

    // 连接socket
    public static function connectSocket( $socket, $address, $servicePort ){
        // 发起socket连接请求
        $result = @socket_connect($socket, $address, $servicePort);
        if($result === false) {
            throw new \Exception("socket connect error");
        } else {
        }
    }

    //  读取socket服务器发送的消息
    public static function readSocket( $socket ){
        // 读取socket服务器发送的消息
        //返回类型
        $ptype = socket_read($socket, 1 );
//        var_dump($ptype);
        $ptype = unpack('h', $ptype);
//        var_dump($ptype);
        $ptype = array_shift($ptype);
//        echo $ptype."\n";

        return $ptype;
    }

    // 返回消息
    public static function goBackContent( $socket ){
        //返回内容长度
        $len = socket_read($socket, 4);
//        var_dump($len);
        $len = unpack('V', $len);
//        var_dump($len);
        $len = array_shift($len);
//        echo $len."\n";

        //返回内容
        $content = socket_read($socket, $len);
//        var_dump($content);
        $content = unpack('h', $content);
//        var_dump($content);
        $content = array_shift($content);
//        echo $content."\n";

        return $content;
    }
}