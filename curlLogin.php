<?php
        $mainSite = 'my.platformphoenix.com';
        $authLogin = 'andrey.arzhanov';
        $authPass = 'Anom8Aly89';
        $login = 'arzhanov';
        $pass = '96xaHjhu';
        $ch = curl_init();
        $headers = array('Authorization: Basic YW5kcmV5LmFyemhhbm92OkFub204QWx5ODk=');
        curl_setopt($ch, CURLOPT_URL, "https://my.platformphoenix.com/base/");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERPWD, $authLogin . ":" . $authPass);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        $postArr = array(
            "AdminLoginForm" => array(
                "login" => $login,
                "password" => $pass
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postArr));
        $out = curl_exec($ch);
exit;
        curl_setopt($ch, CURLOPT_URL, "https://my.platformphoenix.com/base/");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERPWD, $authLogin . ":" . $authPass);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
        $postArr = array(
            "AdminLoginForm" => array(
                "login" => $login,
                "password" => $pass
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        $out = curl_exec($ch);
        echo curl_errno($ch);
