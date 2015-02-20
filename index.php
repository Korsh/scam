<?php

define('INCLUDE_DIR', 'inc/');
define('MODULE_DIR', 'modules/');
define('CLASS_DIR', 'classes/');
define('IMG_DIR', 'img/');
define('JS_DIR', 'js/');
define('SCRIPT_DIR', 'scripts/');
define('LIB_DIR', 'libs/');
define('CRON_DIR', 'crons/');

require_once(LIB_DIR . "rfc822/rfc822.php");
require_once(LIB_DIR . 'nokogiri/nokogiri.php');
require_once(LIB_DIR . 'fPDF/fpdf.php');
require_once(INCLUDE_DIR . 'data.php');
require_once(INCLUDE_DIR . 'url.php');
require_once(INCLUDE_DIR . 'user_agents_config.php');
require_once(CLASS_DIR . 'UserInfo.class.php');
require_once(CLASS_DIR . 'UserActions.class.php');

define('SMARTY_DIR', LIB_DIR . 'Smarty3/');
require_once(SMARTY_DIR . 'Smarty.class.php');
define('SMARTY_TEMPLATE_DIR', 'templates/');
define('SMARTY_TEMPLATE_С_DIR', SMARTY_TEMPLATE_DIR . 'templates_c/');


$ui = new UserInfo($DBH);
require_once(INCLUDE_DIR . 'sites_conf.php');
require_once(INCLUDE_DIR . 'proxy_conf.php');
$userActions = new UserActions($DBH, $proxy);

$smarty                = new Smarty;
$smarty->compile_check = true;
$smarty->debugging     = false;
$smarty->template_dir  = SMARTY_TEMPLATE_DIR;
$smarty->compile_dir   = SMARTY_TEMPLATE_С_DIR;

$display_page = 'index.tpl';

if ($param[1] == "repeat") {
    $display_page = 'repeat.tpl';
    require_once(MODULE_DIR . 'repeat_task.php');
    switch ($param[2]) {
        case "active":
            $smarty->assign('repeat', "active");
            break;
        case "edit":
            $smarty->assign('repeat', "edit");
            break;
        case "add":
            $smarty->assign('repeat', "add");
            break;
        case "view":
            $smarty->assign('repeat', "view");
            break;
        case "history":
            $smarty->assign('repeat', "history");
            break;
        default:
            $smarty->assign('repeat', "history");
            break;
    }
} else if ($param[1] == "once") {
    $display_page = 'once.tpl';
    require_once(MODULE_DIR . 'once_task.php');
    switch ($param[2]) {
        case "add":
            $smarty->assign('once', "add");
            break;
        case "view":
            $smarty->assign('once', "view");
            break;
        case "history":
            $smarty->assign('once', "history");
            break;
        default:
            $smarty->assign('once', "history");
            break;
    }
} else if ($param[1] == "monitor") {
    $display_page = 'monitor.tpl';
    switch ($param[2]) {
        case "showStat":
            require_once(MODULE_DIR . 'realActivity.php');
            break;
    }
} else {
    switch ($param[1]) {
        case "register":
            require_once(MODULE_DIR . 'registerUser.php');
            $display_page = 'registerUser.tpl';
            break;
        case "sync_by_createria":
            require_once(SCRIPT_DIR . 'sync_by_createria.php');
            break;
        case "update":
            require_once(CRON_DIR . 'update_all.php');
            break;
        case "task":
            require_once(MODULE_DIR . 'registerUser.php');
            $display_page = 'taskReg.tpl';
            break;
        case "test":
            require_once(SCRIPT_DIR . 'testUser.php');
            break;
        case "sites":
            $display_page = 'sites_config.tpl';
            require_once(MODULE_DIR . 'sites_config.php');
            break;
        default:
            $display_page = 'index.tpl';
            break;
    }
}

$smarty->assign('sites_conf', $sites);
$smarty->assign('proxy_conf', $proxy);
$smarty->assign('user_agents_conf', $user_agents);
$smarty->display($display_page);
