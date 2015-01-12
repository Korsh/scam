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
        && isset($_REQUEST['site']) && $_REQUEST['email'] != "filatov") {
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
        $proxy[$country]['zipCode'] = str_replace(" ", "+", $proxy[$country]['zipCode']);
        $proxy[$country]['cityName'] = str_replace(" ", "+", $proxy[$country]['cityName']);
        $city = ($country == 'USA' || $country == 'USA2') ? $proxy[$country]['zipCode'] : ucfirst(strtolower($proxy[$country]['cityName'])).',+'.$proxy[$country]['zipCode'];

        $script = "scriptsJS/registerUser.js";
        $debug = false;
        if(isset($_GET['debug']) || $debug) {
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
        $script_result = trim(shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $site $email $device $gender $orientation $age " . $proxy[$country]['ipAddress'] . " $city ". $referer));
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
        $ui->syncUserInfo($email, $adminConf[1]);
        $response = $ui->findByEmail($email);
        if (!empty($response['data'])) {
            $response['request_id'] = $request_id;
            $script                 = "scriptsJS/confirmUser.js";
            $site = $response['data'][0]['siteDomain'];
            $autologin              = 'https://' . $response['data'][0]['siteDomain'] . '/site/autologin/key/' . $response['data'][0]['key'];
            $output                 = shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script $autologin " . $proxy[$country]['ipAddress'] . " $site");

            $ui->saveUserTask($task_id, $response['data'][0]['key']);
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
    } elseif (isset($_REQUEST['register']) && $_REQUEST['register'] 
        && isset($_REQUEST['email']) 
        && isset($_REQUEST['site']) && $_REQUEST['email'] == "filatov") {
            $email       = isset($_REQUEST['email']) ? $_REQUEST['email'] : false;
            $request_id  = isset($_REQUEST['request_id']) ? $_REQUEST['request_id'] : false;
            $task_id     = isset($_REQUEST['task_id']) ? $_REQUEST['task_id'] : time();
            $age         = isset($_REQUEST['age']) ? $_REQUEST['age'] : '21';
            $gender      = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : 'male';
            $orientation = isset($_REQUEST['orientation']) ? $_REQUEST['orientation'] : 'hetero';
            $country     = isset($_REQUEST['country']) ? $_REQUEST['country'] : false;
            $site        = isset($_REQUEST['site']) ? $_REQUEST['site'] : false;
            $device      = isset($_REQUEST['device']) ? $_REQUEST['device'] : false;
            $script      = "scriptsJS/registerUserTrunk.js";
            $mailDomains = array('mailinator.com','mailcatch.com','yopmail.com','yopmail.net','yopmail.fr','owlpic.com','dispostable.com','tempinbox.com','hmamail.com','trash2009.com','20minutemail.com','guerrillamailblock.com','jnxjn.com','klzlk.com','insorg-mail.info','nepwk.com','mailmetrash.com','dacoolest.com','fakeinbox.com','teleworm.com','teleworm.us','cool.fr.nf','nospam.ze.tc','sharklasers.com','rppkn.com','mailinator2.com','junkmail.com','24hourmail.com','nowmymail.com','tempthe.net','emailthe.net','hushmail.com','nomail.xl.cx','7tags.com','getairmail.com','rtrtr.com','dudmail.com','onewaymail.com','keepmymail.com','freemail.ms','privy-mail.de','vpnsmail.me','spamavert.com','meltmail.com','crapmail.org','dayrep.com','rmqkr.net','armyspy.com','notest.net','altaddress.com','yahoo.com.ph','mega.zik.dj','moncourrier.fr.nf','mmmmail.com','mailnesia.com','aaaaaaaaaaaaaaaaaaaaaa.aaa','courrieltemporaire.com','spamfree24.org','explodemail.com','mailinator.net','guerrillamail.net','hushmail.me','zebins.eu','mailHazard.com','mailHazard.us','mailHz.me','zebins.com','amail4.me','monemail.fr.nf','monmail.fr.nf','courriel.fr.nf','temporarioemail.com.br','sofimail.com','ypmail.webarnak.fr.eu.org','ojooo.com','guerrillamail.com','guerrillamail.biz','guerrillamail.org','guerrillamail.de','spam4.me','superrito.com','yahoo.com.my','yahoo.co.th','suioe.com','my10minutemail.com','leemail.me','jourrapide.com','inst1.com','einrot.com','drdrb.com','cuvox.de','consultant.com','anon.leemail.me','adzek.com','reallymymail.com','burstmail.info','dropjar.com','mailismagic.com','fleckens.hu','toiea.com','gustr.com','trbvm.com','6paq.com');

            $registeredMails = array();
            foreach($mailDomains as $mailDomain) {
                $mailToReg = $email.'@'.$mailDomain;
                echo $registerMail = trim(shell_exec("libs/PhantomJS/phantomjs --ignore-ssl-errors=true --ssl-protocol=any $script playcougar.phoenix.filatov.trunk-front.pmmedia.com.ua $mailToReg $device $gender $orientation $age " . $proxy[$country]['ipAddress']));
                if($registerMail == $mailToReg) {
                    $registeredMails[][$mailToReg] = true;
                } else {
                    $registeredMails[][$mailToReg] = false;
                }
                
            }
            var_dump($registeredMails);
            mail('arzhanov@ufins.com', 'scam domains', json_encode($registeredMails));
            mail('romanf@ufins.com', 'scam domains', json_encode($registeredMails));
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
