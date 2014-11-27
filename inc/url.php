<?php
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, "?"))
    $url = substr($url, 0, strpos($url, "?"));
$param = explode("/", $url);

$i = 0;
if (isset($param[1]) && ($param[1] != "")) {
    $part = $param[1];
} else {
    $part     = "main";
    $param[1] = "main";
}

$temp = $_SERVER['REQUEST_URI'];
if (strpos($temp, "?")) {
    $temp    = substr($temp, strpos($temp, "?") + 1);
    $temp_ar = explode("&", $temp);
    $i       = 0;
    while (isset($temp_ar[$i])) {
        if (strpos($temp_ar[$i], "=")) {
            $key             = substr($temp_ar[$i], 0, strpos($temp_ar[$i], "="));
            $value           = substr($temp_ar[$i], strpos($temp_ar[$i], "=") + 1);
            $GET_param[$key] = urldecode($value);
        } else {
            unset($GET_param);
            break;
        }
        $i++;
    }
}
