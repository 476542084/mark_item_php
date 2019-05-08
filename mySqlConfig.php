<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Methods:OPTIONS, GET, POST'); // 允许option，get，post请求
    header('Access-Control-Max-Age:86400'); // 允许访问的有效期
    //定义常量，配置自己的数据库相关信息
    define('DB_HOST', 'localhost');  
    define('DB_USER', 'root');  
    define('DB_PWD', '');  
    define('DB_CHARSET', 'UTF8');  
    define('DB_DBNAME', 'test');
?>