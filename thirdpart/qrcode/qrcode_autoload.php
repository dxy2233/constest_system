<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/7/18
 * Time: 下午12:28
 */

spl_autoload_register(function ($class)  {
   if (strpos( $class,'Endroid\\QrCode\\')  !== false) {
       $path = str_replace('Endroid\\QrCode\\', '', $class);
       $pathInfo = explode('\\', $path, 2);
       if (count($pathInfo) == 1) {
           require __DIR__ . '/' . $pathInfo[0] . '.php';
       } else {
           require __DIR__ . '/' . $pathInfo[0] . '/' . $pathInfo[1] . '.php';
       }
   }

}, true);

