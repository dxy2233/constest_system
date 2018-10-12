<?php
spl_autoload_register(function ($class)  {
    if (strpos($class, 'malkusch\\lock')  !== false) {
        $classInfo = explode('\\', $class);
        require __DIR__ . '/classes/' . $classInfo[2] . '/' . $classInfo[3] . '.php';
    }

}, true);
