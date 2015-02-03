<?php 
require_once('requirer.php');

    $script = "../scriptsJS/proxySync.js";
    var_dump($proxy);
    foreach($proxy as $country)
    {
        $country['port'] = !empty($country['port']) ? $country['port'] : 3128;
        //var_dump(LIB_DIR."PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any --proxy=".$country['domain'].":".$country['port']." --proxy-auth=$proxy_login:$proxy_pass $script");
        $script_result = trim(shell_exec(LIB_DIR."PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any --proxy=".$country['domain'].":".$country['port']." --proxy-auth=$proxy_login:$proxy_pass $script"));

        $proxyConf = json_decode($script_result, true);
        if($proxyConf['statusCode'] == 'OK')
        {
            unset($proxyConf['statusCode']);
            unset($proxyConf['statusMessage']);
            $timeZone = explode(":", $proxyConf['timeZone']);
            $proxyConf['timeZone'] = $timeZone[0];
            $proxyConf['port'] = $country['port'];
            $proxyConf['domain'] = $country['domain'];
            $proxyConf['country'] = $country['country'];
            $proxyConf['enable'] = $country['enable'] == true ? 1 : 0;
            var_dump($proxyConf);
            $ui->updateProxy($proxyConf);
        }
    }
?>

