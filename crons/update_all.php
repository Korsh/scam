<?php

define('INCLUDE_DIR', dirname(dirname(__FILE__)) . '/inc/');
define('CLASS_DIR', dirname(dirname(__FILE__)) . '/classes/');

require_once(INCLUDE_DIR . 'data.php');
require_once(INCLUDE_DIR . 'sites_conf.php');
require_once(CLASS_DIR . 'UserInfo.class.php');

$ui = new UserInfo($DBH, $sites);

$users = $ui->getUsersForUpdate();

echo '<pre>' . print_r($users, true) . '</pre>';

for ($y = 0; $y < sizeof($users); $y++) {
    $ui->syncUserInfo($users[$y], $adminConf[0]);
}
