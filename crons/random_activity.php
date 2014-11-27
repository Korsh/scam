<?

define('INCLUDE_DIR', dirname(dirname(__FILE__)) . '/inc/');
define('MODULE_DIR', dirname(dirname(__FILE__)) . '/modules/');
define('CLASS_DIR', dirname(dirname(__FILE__)) . '/classes/');
define('LIB_DIR', dirname(dirname(__FILE__)) . '/libs/');

require_once(LIB_DIR . "rfc822/rfc822.php");
require_once(CLASS_DIR . 'UserInfo.class.php');
require_once(CLASS_DIR . 'UserActions.class.php');
require_once(INCLUDE_DIR . 'data.php');
require_once(LIB_DIR . 'nokogiri/nokogiri.php');

$ui          = new UserInfo($DBH);
$userActions = new UserActions($DBH);
echo dirname(dirname(__FILE__)) . '/classes/';
require_once(INCLUDE_DIR . 'sites_conf.php');
require_once(INCLUDE_DIR . 'proxy_conf.php');


$task_id = date('U');
$age     = 45;
$gender  = 'male';

$device = 'Firefox';

for ($i = 0; $i < 8; $i++) {
    $email = 'ide777spainbn2@gmail.com';
    $site  = $sites[rand(0, sizeof($sites) - 1)];
    foreach ($locale_conf[$site['locale']] as $key => $locales) {
        if (!$locales['enable']) {
            unset($locale_conf[$site['locale']][$key]);
        }
    }
    $locale_conf[$site['locale']] = array_values($locale_conf[$site['locale']]);
    $selected_locale              = !empty($locale_conf[$site['locale']]) ? $locale_conf[$site['locale']][rand(0, sizeof($locale_conf[$site['locale']]) - 1)] : $locale_conf[$site['locale']][0];
    $country                      = $selected_locale['proxy'];
    
    
    $script = "../scriptsJS/registerUser.js";
    echo $email = trim(shell_exec("../libs/PhantomJS/phantomjs --ignore-ssl-errors=true --proxy=" . $proxy[$country]['domain'] . ":" . $proxy[$country]['port'] . " --proxy-auth=andreya@ufins.com:srmlvpkk $script " . $site['domain'] . " $email $device $gender $age"));
    
    $ui->syncUserInfo($email);
    $response  = $ui->findByEmail($email);
    $script    = "../scriptsJS/confirmUser.js";
    $autologin = 'https://' . $response['data'][0]['site_domain'] . '/site/autologin/key/' . $response['data'][0]['key'];
    $output    = shell_exec("../libs/PhantomJS/phantomjs --ignore-ssl-errors=true --proxy=" . $proxy[$country]['domain'] . ":" . $proxy[$country]['port'] . " --proxy-auth=andreya@ufins.com:srmlvpkk $script $autologin");
    
    if (!empty($response['data'])) {
        $ui->saveUserTask($task_id, $response['data'][0]['key']);
        $response['result'] = true;
        echo json_encode($response);
    } else {
        $ui->saveTmpUser($email);
        echo json_encode(array(
            'result' => false
        ));
    }
}
