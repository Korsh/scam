<?

define('INCLUDE_DIR', '../inc/');
define('MODULE_DIR', '../modules/');
define('CLASS_DIR', '../classes/');
define('IMG_DIR', '../img/');
define('JS_DIR', '../js/');
define('SCRIPT_DIR', '../scripts/');
define('LIB_DIR', '../libs/');
define('CRON_DIR', '../crons/');

require_once(LIB_DIR . "rfc822/rfc822.php");
require_once(LIB_DIR . "nokogiri/nokogiri.php");
require_once(INCLUDE_DIR . 'data.php');
require_once(INCLUDE_DIR . 'user_agents_config.php');
require_once(CLASS_DIR . 'UserInfo.class.php');
require_once(CLASS_DIR . 'UserActions.class.php');

$ui = new UserInfo($DBH);
require_once(INCLUDE_DIR . 'sites_conf.php');
require_once(INCLUDE_DIR . 'proxy_conf.php');

$userActions        = new UserActions();
$webBrowserFF29     = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0";
$webBrowserIE9      = "Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C)";
$webBrowserChrome16 = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.14 (KHTML, like Gecko) Chrome/16.0.0 Safari/534.14";
$mobileIphone5      = "Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3";
$mobileNexus4       = "Mozilla/5.0 (Linux; U; Android 4.2; en-us; Nexus One Build/ERD62) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17";

echo $email = 'gidtidgspa5@gmail.com';
echo $password = '123123';
echo $age = 21;
echo $gender = 'male';
echo $country = 'AUS';
echo $site = 'hornyasia.com';
echo $device = 'Firefox';
echo $request_id = 14018663862;

$userActions->setUpProxy($proxy_login . ':' . $proxy_pass, $proxy[$country]['domain'], $proxy[$country]['port']);
$response['request_id'] = $request_id;
echo $response['email'] = $userActions->registerUser($email, $site, $gender, $age, $user_agents[$device]['user_agent'], $request_id);
$ui->syncUserInfo($response['email'], $admin_conf[0]);
echo '<pre>' . print_r($response, true) . '</pre>';
$email_info = $ui->findByEmail($response['email']);
$response   = is_array($email_info) ? array_merge($response, $email_info) : $response;
var_dump($email_info);
if (!empty($response['data'])) {
    $userActions->loginUserByAutologin($site . '/site/autologin/key/' . $response['data'][0]['key']);
    $userActions->setScreenname($site, $user_agents[$device]['platform']);
    $userActions->logoutUser($site, $user_agents[$device]['platform']);
    $response['result'] = true;
    echo json_encode($response);
} else {
    echo json_encode(array(
        'result' => false
    ));
}
exit;
