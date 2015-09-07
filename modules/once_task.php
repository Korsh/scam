<?php

$tasks_list = $ui->getTasksList();
$smarty->assign('tasks_list', $tasks_list);

if (isset($param[3])) {
    $site      = isset($_REQUEST['site']) ? $_REQUEST['site'] : false;
    $location  = isset($_REQUEST['location']) ? $_REQUEST['location'] : false;
    $status  = isset($_REQUEST['status']) ? $_REQUEST['status'] : false;
    $day       = isset($_REQUEST['day']) ? $_REQUEST['day'] : false;
    $task_id   = $param[3];
    $response  = $ui->getUserByTaskId($task_id, $site, $location, $status);
    $task_info = $ui->getTaskInfo($task_id);
    if (isset($response)) {
        $userActions->setAdminLoginPass($adminConf[0]['login'], $adminConf[0]['pass']);
        $userActions->adminLogin();
        for ($i = 0; $i < sizeof($response); $i++) {
            if (isset($_REQUEST['activity'])) {
                $response[$i] = array_merge($response[$i], $userActions->getUserActivity($response[$i]['id']));
            }
            $response[$i] = array_merge($response[$i], $userActions->getChats($response[$i], $day));
        }

        $smarty->assign('users_info', $response);
    }
    if (isset($param[4]) && $param[4] == 'csv') {
        require_once('csv.php');
    }
    $smarty->assign('task_id', $task_id);
    $smarty->assign('task_info', $task_info);
}
if (isset($_REQUEST['ajax'])) {
    if (isset($_REQUEST['register']) && $_REQUEST['register'] 
        && isset($_REQUEST['email']) 
        && is_valid_email_address($_REQUEST['email']) 
        && isset($_REQUEST['site'])) {
        $email       = isset($_REQUEST['email']) ? $_REQUEST['email'] : false;
        $request_id  = isset($_REQUEST['request_id']) ? $_REQUEST['request_id'] : false;
        $task_id     = isset($_REQUEST['task_id']) ? $_REQUEST['task_id'] : time();
        $age         = isset($_REQUEST['age']) ? $_REQUEST['age'] : false;
        $gender      = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : false;
        $orientation = isset($_REQUEST['orientation']) ? $_REQUEST['orientation'] : 'hetero';
        $country     = isset($_REQUEST['country']) ? $_REQUEST['country'] : false;
        $site        = isset($_REQUEST['site']) ? $_REQUEST['site'] : false;
        $device      = isset($_REQUEST['device']) ? $_REQUEST['device'] : false;
        $referer     = isset($_REQUEST['referer']) ? $_REQUEST['referer'] : false;
        $payFor      = isset($_REQUEST['paid']) ? $_REQUEST['paid'] : 0;
        $proxy[$country]['zipCode'] = str_replace(" ", "+", $proxy[$country]['zipCode']);
        $proxy[$country]['cityName'] = str_replace(" ", "+", $proxy[$country]['cityName']);
        $city = ($country == 'USA' || $country == 'USA2') ? $proxy[$country]['zipCode'] : ucfirst(strtolower($proxy[$country]['cityName'])).',+'.$proxy[$country]['zipCode'];
        $script = "scriptsJS/registerUser.js";
        $debug = false;
        if(isset($_GET['debug']) || $debug) {
                /*if($country == "GBR") {
            $cityArr = array('Blackrod, BL6', 'Coppull, PR7', 'Rushden, NN10', 'Huntingdon, PE28', 'Holmpton, HU19', 'Winestead, HU12', 'Stretton, WA4', 'Thorpe Bay, SS1');
        } elseif ($country == "AUS") {
            $cityArr = array('Mawson, 7151', 'Belfield, 2191', 'Turrella, 2205', 'Sandringham, 2219', 'Jabiru, 0886', 'Gunyangara, 0880', 'York Town, 7270', 'Woollahra, 2025');
        } elseif ($country == "CAN") {
            $cityArr = array('Westwold, V0E 3B0', 'Walhachin, V0K 2P0', 'Ulukhaktok, X0E 0S0', 'Woodbridge, L4L 9T8', 'Wilmot Station, B0P 1W0', 'Paradise, B0S 1R0', 'Wheatley River, C1E 0T4', 'Yarmouth, B5A 4W3');
        }

        $city = $cityArr[rand(0,7)];*/
                echo trim("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $site $email $device $gender $orientation $age " . $proxy[$country]['ipAddress'] . " $city ". $referer);
                $script                 = "scriptsJS/confirmUser.js";
                $autologin              = "https://quierorollo.com/site/autologin/key/76822422ba1924be5c478c17ae0b7702";
                //echo "libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $autologin " . $proxy[$country]['ipAddress'] . " $site";
            $response = array(
                'result' => false,
                'request_id' => $request_id,
            );
            //echo $referer;
            echo json_encode($response);
            exit;
        }
        /*--proxy=".$proxy[$country]['domain'].":".$proxy[$country]['port']." --proxy-auth=andreya@ufins.com:srmlvpkk*/
        /*if($country == "GBR") {
            $cityArr = array('Blackrod,+BL6', 'Coppull,+PR7', 'Rushden,+NN10', 'Huntingdon,+PE28', 'Holmpton,+HU19', 'Winestead,+HU12', 'Stretton,+WA4', 'Thorpe Bay,+SS1');
        } elseif ($country == "AUS") {
            $cityArr = array('Mawson,+7151', 'Belfield,+2191', 'Turrella,+2205', 'Sandringham,+2219', 'Jabiru,+0886', 'Gunyangara,+0880', 'York Town,+7270', 'Woollahra,+2025');
        } elseif ($country == "CAN") {
            $cityArr = array('Westwold,+V0E+3B0', 'Walhachin,+V0K+2P0', 'Ulukhaktok,+X0E+0S0', 'Woodbridge,+L4L+9T8', 'Wilmot Station,+B0P+1W0', 'Paradise,+B0S+1R0', 'Wheatley River,+C1E+0T4', 'Yarmouth,+B5A+4W3');
        }
        $city = $cityArr[rand(0,7)];*/
        $script_result = trim(shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $site $email $device $gender $orientation $age " . $proxy[$country]['ipAddress'] . " $city $request_id $referer"));
        $email = is_valid_email_address($script_result) ? $script_result : array('response' => $script_result  , 'result' => 'false');
        if(is_array($email)) {
            preg_match_all("/([A-Za-z.0-9+]*[@]{1}[A-Za-z.0-9]*)/i", $email['response'], $matches);
            $email = is_valid_email_address($matches[0][0]) ? $matches[0][0] : false;
            if(!$email) {
                $response = array(
                    'result' => false,
                    'request_id' => $request_id,
                );
                echo json_encode($response);
                exit;
            }
        }
        $ui->syncUserInfo($email, $adminConf[0]);
        $response = $ui->findByEmail($email);
        if (!empty($response['data'])) {
            $platform = $response['data'][0]['platform'] == 'webSite' ? 'www' : 'm';
            $script                 = "scriptsJS/confirmUser.js";
            $site = $response['data'][0]['siteDomain'];
            $autologin              = $response['data'][0]['siteDomain'] . '/site/autologin/key/' . $response['data'][0]['key'];
            if(isset($_GET['debug']) || $debug) {
                echo $autologin.'<br>';
                echo "libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $autologin " . $proxy[$country]['ipAddress'] . " $site $payFor $device $request_id";
                exit;
            }
            $output                 = shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $autologin " . $proxy[$country]['ipAddress'] . " $site $payFor $device $request_id");
            $ui->saveUserTask($task_id, $response['data'][0]['key']);
            $ui->syncUserInfo($email, $adminConf[0]);
            $response = $ui->findByEmail($email);
            $response['request_id'] = $request_id;
            $response['result'] = true;            
        } else {
            $ui->saveTmpUser($email);
            $response = array(
                'result' => false,
                'request_id' => $request_id,
            );
        }
        echo json_encode($response);
        exit;
    } elseif (isset($_REQUEST['activity']) && $_REQUEST['activity'] && isset($_REQUEST['userId'])) {
        $userId = isset($_REQUEST['userId']) ? $_REQUEST['userId'] : false;
        $userActions->setAdminLoginPass($adminConf[0]['login'], $adminConf[0]['pass']);
        $userActions->adminLogin();
        $activity = $userActions->getUserActivity($_REQUEST['userId']);
        echo json_encode($activity);
        exit;
    } elseif (isset($_REQUEST['get_user_info']) && isset($_REQUEST['userId'])) {
        $userId   = isset($_REQUEST['userId']) ? $_REQUEST['userId'] : false;
        $user_info = $ui->findById($userId);
        echo json_encode($user_info);
        exit;
    } elseif (isset($_REQUEST['id']) && $_REQUEST["activity"]) {
        $ui->syncUserInfo($_REQUEST['id'], $adminConf[0]);
        $userActions->setAdminLoginPass($adminConf[0]['login'], $adminConf[0]['pass']);
        $userActions->adminLogin();
        $userActions->getUserActivity($_REQUEST['id']);
        echo '{result:true}';
        exit;
    } else {
        exit;
    }
    if (isset($_REQUEST['id']) && $param[2] == "activity") {
        $ui->syncUserInfo($_REQUEST['id'], $adminConf[0]);
        $userActions->setAdminLoginPass($adminConf[0]['login'], $adminConf[0]['pass']);
        $userActions->adminLogin();
        $userActions->getUserActivity($_REQUEST['id']);
        exit;
    }
}
