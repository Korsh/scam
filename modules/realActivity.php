<?php
define('CLASS_DIR', '../classes/');
define('INCLUDE_DIR', '../inc/');
define('LIB_DIR', '../libs/');
require_once(LIB_DIR . 'nokogiri/nokogiri.php');
require_once(INCLUDE_DIR . 'data.php');
require_once(CLASS_DIR . 'RealActivity.class.php');
require_once(CLASS_DIR . 'UserInfo.class.php');

$ui                = new UserInfo($DBH);
$realActivityClass = new RealActivity($DBH);

$site     = isset($_REQUEST['site']) ? findDictionaryIdInSitesConfig($_REQUEST['site'], $sites) : array('250');
$country[]  = isset($_REQUEST['country']) ? $_REQUEST['country'] : 'GBR';
$platform[] = isset($_REQUEST['platform']) ? $_REQUEST['platform'] : 'webSite';
$gender[]   = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : "male";
$dateFrom = isset($_REQUEST['dateFrom']) ? $_REQUEST['dateFrom'] : date('Y-m-d',strtotime(date('Y-m-d') . "-1 days"));
$dateTo   = isset($_REQUEST['dateTo']) ? $_REQUEST['dateTo'] : date('Y-m-d');

if(!empty($site) && !empty($country) && !empty($platform) && !empty($gender) && !empty($dateFrom) && !empty($dateTo)) {
    $userInfo     = $realActivityClass->getRealUsersIds($adminConf[0], $site, $country, $platform, $gender, $dateFrom, $dateTo);
    $userActivity = $realActivityClass->checkActivity($userInfo, $b2bAdminConf);
    echo '<pre>'.print_r($userActivity, true).'</pre>';
    if(!empty($_REQUEST['ajax'])) {
        echo json_encode($userActivity);
    } else {
        $smarty->assign('userActivity', $userActivity);
    }

}



