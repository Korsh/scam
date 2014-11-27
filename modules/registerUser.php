<?php
$userActions = new UserActions($DBH);
$tasks_list  = $ui->getTasksList();
$smarty->assign('tasks_list', $tasks_list);
if (isset($_GET['task_id'])) {
    $task_id  = $_GET['task_id'];
    $response = $ui->getUserByTaskId($task_id);
    
    if (!empty($response)) {
        $userActions->setAdminLoginPass($admin_conf[0]['login'], $admin_conf[0]['pass']);
        $userActions->adminLogin();
        for ($i = 0; $i < sizeof($response); $i++) {
            //$response[$i] = array_merge($response[$i], $userActions->getUserActivity($response[$i]['id']));
        }
        //var_dump($response);
        $smarty->assign('users_info', $response);
    }
}
if (isset($_REQUEST['ajax'])) {
    if (isset($_REQUEST['register']) && $_REQUEST['register'] && isset($_REQUEST['email']) && is_valid_email_address($_REQUEST['email']) && isset($_REQUEST['site'])) {
        
        
        $email      = isset($_REQUEST['email']) ? $_REQUEST['email'] : false;
        $task_id    = isset($_REQUEST['task_id']) ? $_REQUEST['task_id'] : time();
        $password   = isset($_REQUEST['password']) ? $_REQUEST['password'] : false;
        $age        = isset($_REQUEST['age']) ? $_REQUEST['age'] : false;
        $gender     = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : false;
        $country    = isset($_REQUEST['country']) ? $_REQUEST['country'] : false;
        $site       = isset($_REQUEST['site']) ? $_REQUEST['site'] : false;
        $device     = isset($_REQUEST['device']) ? $_REQUEST['device'] : false;
        $request_id = isset($_REQUEST['request_id']) ? $_REQUEST['request_id'] : false;
        $referer    = isset($_REQUEST['referer']) ? $_REQUEST['referer'] : false;
        echo $proxy_login . ':' . $proxy_pass;
        $userActions->setUpProxy($proxy_login . ':' . $proxy_pass, $proxy[$country]['domain'], $proxy[$country]['port']);
        $response['request_id'] = $request_id;
        $response['email']      = $userActions->registerUser($email, $site, $gender, $age, $password, $user_agents[$device], $request_id);
        
        //$ui->syncUserInfo($response['email'], $admin_conf[0]);
        $email_info = $ui->findByEmail($response['email']);
        $response   = is_array($email_info) ? array_merge($response, $email_info) : $response;
        
        if (!empty($response['data'])) {
            $userActions->loginUserByAutologin($site . '/site/autologin/key/' . $response['data'][0]['key']);
            $ui->saveUserTask($task_id, $response['data'][0]['key']);
            //$userActions->setScreenname($site);
            //$userActions->logoutUser($response['email']);
            $response['result'] = true;
            echo json_encode($response);
        } else {
            echo json_encode(array(
                'result' => false
            ));
        }
        exit;
    } else if (isset($_REQUEST['activity']) && $_REQUEST['activity'] && isset($_REQUEST['user_id'])) {
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : false;
        $userActions->setAdminLoginPass($admin_conf[0]['login'], $admin_conf[0]['pass']);
        $userActions->adminLogin();
        $activity = $userActions->getUserActivity($_REQUEST['user_id']);
        
        echo json_encode($activity);
        exit;
    } else if (isset($_REQUEST['get_user_info']) && isset($_REQUEST['user_id'])) {
        $user_id   = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : false;
        $user_info = $ui->findById($user_id);
        echo json_encode($user_info);
        exit;
    } else {
        exit;
    }
}

else if (isset($_REQUEST['email'])) {
    $user_info = $ui->findByEmail($_REQUEST['email']);
    echo '<pre>' . print_r($user_info, true) . '</pre>';
}

