<?php

$generalSitename = "phoenix";

$proxyLogin = 'proxyLogin';
$proxyPass  = 'proxyPass';

/*$proxy_login = 'sergeyse@ufins.com';
$proxy_pass  = 'agvmuuod';*/

$errorLevel =  E_ALL;
error_reporting($errorLevel);
try {
    $dbHost     = 'dbHost';
    $dbUser     = 'dbUser';
    $dbPassword = 'dbPass';
    $dbName     = 'dbName';
    
    $DBH = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
}
catch (PDOException $e) {
    echo $e->getMessage();
    file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
}

$DBH->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$scriptVersion = "1.1.3.2";

$dcConf = array(
    "pc",
    "lg"
);

$adminConf = array(
    0 => array(
        "dc" => "lg",
        "site" => "phoenixAdminUrl",
        "loginUrl" => "/admin/base/login",
        "findUrl" => "/user/find",
        "login" => "yourLogin",
        "pass" => "youPass",
        "type" => "phoenix",
        "authLogin" => "yourIPALogin",
        "authPass" => "yourIPAPass",
    )
);
$b2bAdminConf = array(
    "site" => "testerToolsUrl",
    "loginUrl" => "/base/login.php",
    "findUrl" => "/system/backend/profiles_tester_tools.php",
    "httpLogin" => "yourLogin",
    "httpPass" => "yourPass",
    "login" => "yourLogin",
    "pass" => "yourPass",
    "authLogin" => "yourIPALogin",
    "authPass" => "yourIPAPass",
);
