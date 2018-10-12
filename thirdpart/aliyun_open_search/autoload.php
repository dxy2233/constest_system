<?php
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/7/25
 * Time: 下午9:50
 */

spl_autoload_register(function ($class) {
    if (strpos($class, 'Cloudsearch') !== false) {
        require __DIR__ . '/' .$class . '.php';
    }

}, true);

