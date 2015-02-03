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
