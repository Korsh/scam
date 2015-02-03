<?php

define('INCLUDE_DIR', dirname(dirname(__FILE__)) . '/inc/');
define('MODULE_DIR', dirname(dirname(__FILE__)) . '/modules/');
define('CLASS_DIR', dirname(dirname(__FILE__)) . '/classes/');
define('LIB_DIR', dirname(dirname(__FILE__)) . '/libs/');

require_once(INCLUDE_DIR . 'data.php');
require_once(CLASS_DIR . 'UserInfo.class.php');

$ui = new UserInfo($DBH);
require_once(INCLUDE_DIR . 'sites_conf.php');
require_once(INCLUDE_DIR . 'proxy_conf.php');

for ($i = 0; $i < 15; $i++) {
    
    $site[$i] = $sites[rand(0, sizeof($sites) - 1)];
    foreach ($locale_conf[$site[$i]['locale']] as $key => $locales) {
        if (!$locales['enable']) {
            
            unset($locale_conf[$site[$i]['locale']][$key]);
        }
        
    }
    
    $locale_conf[$site[$i]['locale']] = array_values($locale_conf[$site[$i]['locale']]);
    
    $rand = rand(0, sizeof($locale_conf[$site[$i]['locale']]) - 1);
    
    $site[$i]['locale'] = !empty($locale_conf[$site[$i]['locale']]) ? $locale_conf[$site[$i]['locale']][$rand] : $locale_conf[$site[$i]['locale']][0];
    
    $site_curr[$site[$i]['locale']['proxy']][] = $site[$i]['domain'];
}
var_dump($site_curr);

