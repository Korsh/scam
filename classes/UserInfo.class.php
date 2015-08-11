<?php


class UserInfo
{
    var $db;
    var $ch;
    var $mainSite;
    var $dcConf;
    var $dc;
    var $loginUrl;
    var $findUrl;
    var $type;
    var $login;
    var $pass;
    var $siteConf;
    
    function UserInfo($DBH)
    {
        $this->db        = $DBH;
        $this->adminUrl = 'my.ufins.com/user/find';
        $this->dc        = 'pc';
        $this->dcConf   = array(
            "pc",
            "lg"
        );
    }
    
    function dashesToCamelCase($string, $capitalizeFirstCharacter = false) 
    {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
    
    function syncUserInfo($createria, $dc)
    {
        $this->setDc($dc);
        $this->adminLogin();
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . $this->findUrl . '/?FindUserForm[user]=' . urlencode($createria));
        $findArr = array(
            "YII_CSRF_TOKEN" => "",
            "FindUserForm" => array(
                "user" => $createria
            )
        );
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($this->ch);
        
        $html     = new nokogiri($out);
        $elements = $html->get(".grid-view")->toArray();
        $count  = 0;
        $idArr = array();
        $userInfo = array();
        for ($i = 0; $i < sizeof($elements); $i++) {
            if (isset($elements[$i]['table'][0]['tbody'][0]['tr']['td'])) {
                if (isset($elements[$i]['table'][0]['tbody'][0]['tr']['td'][4])) {
                    $autologinLink = $elements[$i]['table'][0]['tbody'][0]['tr']['td'][4]['a']['href'];
                    preg_match_all("/[\S]+userId=([0-9a-z]+)/i", $autologinLink, $matches);
                    if ($matches[1][0] != '') {
                        $idArr[] = trim($matches[1][0]);
                    }
                }
            } else {
                for ($y = 0; $y < sizeof($elements[$i]['table'][0]['tbody'][0]['tr']); $y++) {
                    if (isset($elements[$i]['table'][0]['tbody'][0]['tr'][$y]['td'][4])) {
                        $autologinLink = $elements[$i]['table'][0]['tbody'][0]['tr'][$y]['td'][4]['a']['href'];
                        preg_match_all("/[\S]+userId=([0-9a-z]+)/i", $autologinLink, $matches);
                        if (!empty($matches[1][0]) && $matches[1][0] != '') {
                            $idArr[] = trim($matches[1][0]);
                        }
                    }
                }
            }
            
        }
        for ($i = 0; $i < sizeof($idArr); $i++) {
            curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . $this->findUrl . '?user_id=' . $idArr[$i]);
            
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            $out                      = curl_exec($this->ch);
            $html                     = new nokogiri($out);
            $elements                 = $html->get("#yw1")->toArray();
            foreach($elements[0]['tr'] as $key => $value) {
                $userInfoArray[$this->dashesToCamelCase(str_replace(array(':', '(', ')'), '', $value['th'][0]['#text']))] = $value['td'][0]['#text'];
            }
            $userInfo = array(
                'id'          => $userInfoArray['id'],
                'mail'        => $userInfoArray['email'],
                'login'       => $userInfoArray['login'],
                'password'    => $userInfoArray['password'],
                'key'         => $userInfoArray['autologinKey'],
                'siteId'      => $userInfoArray['siteId'],
                'gender'      => $userInfoArray['gender'],
                'orientation' => $userInfoArray['sexualOrientation'],
                'fname'       => $userInfoArray['firstName'],
                'lname'       => $userInfoArray['lastName'],
                'country'     => $userInfoArray['countryCode'],
                'birthday'    => $userInfoArray['birthday'],
                'regTime'     => $userInfoArray['registeredTime'],
                'active'      => $userInfoArray['isDeleted'],
                'traffic'     => $userInfoArray['trafficSource'],
                'platform'    => $userInfoArray['platform'],
                'll'          => $userInfoArray['latitude'].':'.$userInfoArray['longitude'],
                'searchable'  => $userInfoArray['isSearchable'],
            );
            $userInfo['chatsCount']  = isset($elements[0]['tr'][28]['td'][0]['a'][0]['#text']) ? $elements[0]['tr'][28]['td'][0]['a'][0]['#text'] : null;
            
            preg_match_all("/([0-9]+)/", $userInfo['chatsCount'], $matches);
            $userInfo['chatsCount']  = trim($matches[1][0]);
            $elements                = $html->get(".user-block")->toArray();
            $userInfo['confirmed']   = !empty($elements[3]['h5'][0]['span'][0]['#text']) && strtolower($elements[3]['h5'][0]['span'][0]['#text']) == "confirmed" ? 1 : 0;
            curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . '/user/edit?user_id=' . $userInfo['id']);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            $out      = curl_exec($this->ch);
            $html     = new nokogiri($out);
            $elements = $html->get("#edit-user-form")->toArray();
            if (sizeof($elements) > 0) {
                $inputs = !empty($elements[0]['input']) ? $elements[0]['input'] : null;
                $spans  = !empty($elements[0]['span']) ? $elements[0]['span'] : null;
                
                $characters = 'abcdefghijklmnoprstuvwxyz';
                $randstring = '';
                for ($i = 0; $i < 5; $i++) {
                    $randstring .= $characters[rand(0, strlen($characters))];
                }
                $screenname = $randstring . substr(time(), -5);
                
                if (isset($elements[0])) {
                    $inputs = !empty($elements[0]['input']) ? $elements[0]['input'] : null;
                    for ($i = 0; $i < sizeof($inputs); $i++) {
                        if ($inputs[$i]['name'] == 'UserAdminForm[login]') {
                            $inputs[$i]['value'] = $screenname;
                            $userInfo['login']  = $screenname;
                        }
                        $arr[$inputs[$i]['name']] = $inputs[$i]['value'];
                    }
                }
                if (isset($elements[0])) {
                    $spans = !empty($elements[0]['span']) ? $elements[0]['span'] : null;
                    for ($i = 0; $i < sizeof($spans) - 1; $i++) {
                        $currSelect = $spans[$i]['select'][0];
                        for ($y = 0; $y < sizeof($currSelect['option']); $y++) {
                            if (!empty($currSelect['option'][$y]['selected'])) {
                                $arr[$currSelect['name']] = $currSelect['option'][$y]['value'];
                            }
                        }
                    }
                }
                $arr['UserAdminForm[location]'] = '';
                $arr['UserAdminForm[country]']  = '';
                error_log($userInfo['id'].'-'.$userInfo['mail'].'-'.$createria);
                curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . '/user/edit?user_id=' . $userInfo['id']);
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($this->ch, CURLOPT_REFERER, "https://" . $this->mainSite . '/user/edit?user_id=' . $userInfo['id']);
                
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($arr));
                $out = curl_exec($this->ch);
            }
            $this->saveSyncUser($userInfo);
        }
        return $userInfo;
    }
    
    private function setDc($config)
    {
        $this->dc        = $config['dc'];
        $this->mainSite = $config['site'];
        $this->loginUrl = $config['loginUrl'];
        $this->findUrl  = $config['findUrl'];
        $this->type      = $config['type'];
        $this->login     = $config['login'];
        $this->pass      = $config['pass'];
    }
    
    private function adminLogin()
    {
        $this->ch = curl_init();
        $postArr = array(
            "AdminLoginForm" => array(
                "login" => $this->login,
                "password" => $this->pass
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . $this->loginUrl);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($this->ch);
    }
    
    public function setTestUsers()
    {
        $this->setDc($this->config);
        $this->adminLogin();
        try {
            $getSetTestUsersQuery = $this->db->prepare("
                SELECT 
                    `id`
                FROM
                    `profile`
                ;");
            $getSetTestUsersQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
        if ($getSetTestUsersQuery->rowCount() > 0) {
            $i = 0;
            while ($row = $getSetTestUsersQuery->fetch()) {
                $users[$i]['id'] = $row['id'];
                curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . "/user/markTester?userId=" . $users[$i]['id']);
                curl_exec($this->ch);
            }
            return $users;
        } else {
            return false;
        }
    }
    
    public function saveUserTask($taskId, $key)
    {
        try {
            $updateTaskIdQuery = $this->db->prepare("
            INSERT INTO 
                `profile` (
                `key`,
                `task_id`
            )
            VALUES (
                :key,
                :taskId
            )
            ON DUPLICATE KEY UPDATE            
                `task_id` = :taskId2
            ;");
            $updateTaskIdQuery->bindValue(':taskId', $taskId);
            $updateTaskIdQuery->bindValue(':taskId2', $taskId);
            $updateTaskIdQuery->bindValue(':key', $key);
            $updateTaskIdQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    
    public function saveScheduleTask($days, $task_config, $time, $taskId = null)
    {
        try {
            $updateTaskIdQuery = $this->db->prepare("
            INSERT INTO 
                `launches` (
                `task_id`,
                `time_start`,
                `time_interval`,
                `mon`,
                `tue`,
                `wed`,
                `thu`,
                `fri`,
                `sat`,
                `sun`
            )
            VALUES (
                :taskId,
                NOW(),
                :time,
                :mon,
                :tue,
                :wed,
                :thu,
                :fri,
                :sat,
                :sun
            )
            ON DUPLICATE KEY UPDATE            
                `task_id` = :taskId2
            ;");
            $updateTaskIdQuery->bindValue(':taskId', $taskId);
            $updateTaskIdQuery->bindValue(':taskId2', $taskId);
            $updateTaskIdQuery->bindValue(':time', $time);
            $daysConfig = array(
                0 => 'mon',
                1 => 'tue',
                2 => 'wed',
                3 => 'thu',
                4 => 'fri',
                5 => 'sat',
                6 => 'sun'
            );
            for ($i = 0; $i < sizeof($days); $i++) {
                $updateTaskIdQuery->bindValue(':' . $daysConfig[$days[$i]], 1);
            }
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':time', $time);
            $updateTaskIdQuery->bindValue(':key', $key);
            $updateTaskIdQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    
    public function getUserById($userId)
    {
        if (!empty($userId)) {
            try {
                $getUserByIdQuery = $this->db->prepare("
                    SELECT 
                        `id`,
                        `mail`,
                        `login`,
                        `password`,
                        `key`,
                        `sites_config`.`site_name` as site,
                        `gender`,
                        `orientation`,
                        `fname`,
                        `lname`,
                        `country`,
                        `birthday`,
                        `reg_time`,
                        `active`,
                        `traffic`,
                        `platform`,
                        `ll`,
                        `site_id`,
                        `task_id`,
                        `location`,
                        `confirmed`,
                        `searchable`,
                        `chats`
                    FROM
                        `profile`
                    JOIN 
                        `sites_config`
                    ON
                        `profile`.`site_id` = `sites_config`.`site_id`
                    WHERE `id` = :id
                    LIMIT 1
                    ;");
                $getUserByIdQuery->bindValue(':id', $userId);
                $getUserByIdQuery->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            }
            if ($getUserByIdQuery->rowCount() > 0) {
                $row                 = $getUserByIdQuery->fetch(PDO::FETCH_ASSOC);
                $user['active']      = $row['active'];
                $user['password']    = $row['password'];
                $user['login']       = $row['login'];
                $user['orientation'] = $row['orientation'];
                $user['site']        = $row['site'];
                $user['country']     = $row['country'];
                $user['mail']        = $row['mail'];
                $user['gender']      = $row['gender'];
                $user['register']    = $row['reg_time'];
                $user['birthday']    = $row['birthday'];
                $user['age']         = date('Y') - date('Y', (int)strtotime($row['birthday']));
                $user['id']          = $row['id'];
                $user['ll']          = $row['ll'];
                $user['key']         = $row['key'];
                $user['chatsCount'] = $row['chats'];
                $user['searchable']  = $row['searchable'];
                $user['confirmed']   = $row['confirmed'];
                $user['platform']    = $row['platform'];
                $user['fname']       = $row['fname'];
                $user['lname']       = $row['lname'];
                $user['traffic']     = $row['traffic'];
                $user['siteId']     = $row['site_id'];
                $user['location']    = $row['location'];
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function getUserByTaskId($taskId, $site = false, $location = false, $status = false)
    {
        try {
            if (!empty($site)) {
                $getUserByTaskIdQuery = $this->db->prepare("
                    SELECT 
                        `sites_config`.`site_name` as site,
                        `country`,
                        `mail`,
                        `gender`,
                        `birthday`,
                        `reg_time`,
                        `id`,
                        `ll`,
                        `key`,
                        `searchable`,
                        `confirmed`,
                        `platform`,
                        `chats`
                    FROM
                        `profile`
                    JOIN 
                        `sites_config`
                    ON
                        `profile`.`site_id` = `sites_config`.`site_id`
                    WHERE `task_id` = :taskId3
                    AND `sites_config`.`site_name` in (:site)
                    ;");
                $getUserByTaskIdQuery->bindValue(':site', $site);
            } elseif (!empty($location)) {
                $getUserByTaskIdQuery = $this->db->prepare("
                    SELECT 
                        `sites_config`.`site_name` as site,
                        `country`,
                        `mail`,
                        `gender`,
                        `birthday`,
                        `reg_time`,
                        `id`,
                        `ll`,
                        `key`,
                        `searchable`,
                        `confirmed`,
                        `platform`,
                        `chats`
                    FROM
                        `profile`
                    JOIN 
                        `sites_config`
                    ON
                        `profile`.`site_id` = `sites_config`.`site_id`
                    WHERE `task_id` = :taskId3
                    AND `country` in (:location)
                    ;");
                $getUserByTaskIdQuery->bindValue(':location', $location);
            } elseif (!empty($status)) {
                if($status == 'null') {
                    $getUserByTaskIdQuery = $this->db->prepare("
                        SELECT 
                            `sites_config`.`site_name` as site,
                            `country`,
                            `mail`,
                            `gender`,
                            `birthday`,
                            `reg_time`,
                            `id`,
                            `ll`,
                            `key`,
                            `searchable`,
                            `confirmed`,
                            `platform`,
                            `chats`
                        FROM
                            `profile`
                        JOIN 
                            `sites_config`
                        ON
                            `profile`.`site_id` = `sites_config`.`site_id`
                        WHERE `task_id` = :taskId3
                        AND `chats` = 0
                        ;");
                } elseif ($status != 'null') {
                    $getUserByTaskIdQuery = $this->db->prepare("
                        SELECT 
                            `sites_config`.`site_name` as site,
                            `country`,
                            `mail`,
                            `gender`,
                            `birthday`,
                            `reg_time`,
                            `id`,
                            `ll`,
                            `key`,
                            `searchable`,
                            `confirmed`,
                            `platform`,
                            `chats`
                        FROM
                            `profile`
                        JOIN 
                            `sites_config`
                        ON
                            `profile`.`site_id` = `sites_config`.`site_id`
                        WHERE `task_id` = :taskId3
                        AND `chats` != 0
                        ;");
                }
            } else {            
                $getUserByTaskIdQuery = $this->db->prepare("
                    SELECT 
                        `sites_config`.`site_name` as site,
                        `country`,
                        `mail`,
                        `gender`,
                        `birthday`,
                        `reg_time`,
                        `id`,
                        `ll`,
                        `key`,
                        `searchable`,
                        `confirmed`,
                        `platform`,
                        `chats`
                    FROM
                        `profile`
                    JOIN 
                        `sites_config`
                    ON
                        `profile`.`site_id` = `sites_config`.`site_id`
                    WHERE `task_id` = :taskId3
                    ;");
            }
            $getUserByTaskIdQuery->bindValue(':taskId3', $taskId);
            $getUserByTaskIdQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
        if ($getUserByTaskIdQuery->rowCount() > 0) {
            $i = 0;
            while ($row = $getUserByTaskIdQuery->fetch()) {
                $users[$i]['site']        = $row['site'];
                $users[$i]['country']     = $row['country'];
                $users[$i]['mail']        = $row['mail'];
                $users[$i]['gender']      = $row['gender'];
                $users[$i]['register']    = $row['reg_time'];
                $users[$i]['birthday']    = $row['birthday'];
                $users[$i]['age']         = date('Y') - date('Y', strtotime($row['birthday']));
                $users[$i]['id']          = $row['id'];
                $users[$i]['ll']          = $row['ll'];
                $users[$i]['key']         = $row['key'];
                $users[$i]['chatsCount'] = $row['chats'];
                $users[$i]['searchable']  = $row['searchable'];
                $users[$i]['confirmed']   = $row['confirmed'];
                $users[$i]['platform']    = $row['platform'];
                $i++;
            }
            return $users;
        } else {
            return false;
        }
    }
    
    public function getTasksList()
    {
        try {
            $getTasksListQuery = $this->db->prepare("
                SELECT DISTINCT
                    `task_id` 
                FROM
                    `profile`
                WHERE `task_id` IS NOT NULL ORDER BY `task_id` DESC LIMIT 10
                ;");
            $getTasksListQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        if ($getTasksListQuery->rowCount() > 0) {
            $i    = 0;
            $rows = $getTasksListQuery->fetchAll();
            foreach ($rows as $row) {
                try {
                    $getTasksInfoListQuery = $this->db->prepare("
                        SELECT DISTINCT
                            `sites_config`.`site_name` as site, 
                            `country`
                        FROM
                            `profile`
                        INNER JOIN 
                            `sites_config`
                        ON
                            `profile`.`site_id` = `sites_config`.`site_id`
                        WHERE `task_id` = " . $row['task_id'] . "
                        ;");
                    $getTasksInfoListQuery->execute();
                }
                catch (PDOException $e) {
                    echo $e->getMessage();
                    file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                }
                if ($getTasksInfoListQuery->rowCount() > 0) {
                    $i         = 0;
                    $infoRows = $getTasksInfoListQuery->fetchAll();
                    foreach ($infoRows as $infoRow) {
                        $tasks[$row['task_id']][$infoRow['site']][] = $infoRow['country'];
                    }
                }
            }
            return $tasks;
        } else {
            return false;
        }
    }
    
    public function getTaskInfo($taskId)
    {
        try {
            $getTaskInfoQuery = $this->db->prepare("
                SELECT DISTINCT
                    `task_id`, 
                    `sites_config`.`site_name` as site, 
                    `country`
                FROM
                    `profile`
                JOIN
                    `sites_config`
                ON
                    `profile`.`site_id` = `sites_config`.`site_id`
                WHERE `task_id` = :taskId ORDER BY `task_id` DESC
                ;");
            $getTaskInfoQuery->bindValue(':taskId', $taskId);
            $getTaskInfoQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
        if ($getTaskInfoQuery->rowCount() > 0) {
            $i    = 0;
            $rows = $getTaskInfoQuery->fetchAll();
            foreach ($rows as $row) {
                $tasks[$row['task_id']][$row['site']][] = $row['country'];
            }
            
            return $tasks;
        } else {
            return false;
        }
    }
    
    function updateProxy($proxyConf)
    {
        try {
        $updateProxyConfigQuery = $this->db->prepare("
            INSERT INTO 
                `proxy` (
                `id`,
                `domain`,
                `port`,
                `ip_address`,
                `country_code`,
                `country_name`,
                `region_name`,
                `city_name`,
                `zip_code`,
                `latitude`,
                `longitude`,
                `time_zone`,
                `enable`,
                `country`
            )
            VALUES (
                default,
                :domain,
                :port,
                :ipAddress,
                :countryCode,
                :countryName,
                :regionName,
                :cityName,
                :zipCode,
                :latitude,
                :longitude,
                :timeZone,
                :enable,
                :country
            )
            ON DUPLICATE KEY UPDATE
                `domain` = :domain2,
                `port`= :port2,
                `ip_address` = :ipAddress2,
                `country_code` = :countryCode2,
                `country_name` = :countryName2,
                `region_name` = :regionName2,
                `city_name` = :cityName2,
                `zip_code` = :zipCode2,
                `latitude` = :latitude2,
                `longitude` = :longitude2,
                `time_zone` = :timeZone2,
                `enable` = :enable2,
                `country` = :country2
        ;");
        foreach ($proxyConf as $key => $item) {
            if ($key != 'id') {
                $this->bindMultiple($updateProxyConfigQuery, array(
                    $key,
                    $key . '2'
                ), $item);
            } else {
                $updateProxyConfigQuery->bindValue(':id', $item);
            }
        }
        $updateProxyConfigQuery->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            //file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    
    function getProxyConfig()
    {
        try {
            $usedEmailQuery = $this->db->query("
            SELECT 
                `id`,
                `ip_address`,
                `country_code`,
                `country_name`,
                `region_name`,
                `city_name`,
                `zip_code`,
                `latitude`,
                `longitude`,
                `time_zone`,
                `domain`,
                `port`,
                `country`,
                `enable`
        
        FROM
          `proxy`
        ;");
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        if ($usedEmailQuery->rowCount() > 0) {
            while ($row = $usedEmailQuery->fetch()) {
                $proxy[$row['country']]['id'] = $row['id'];
                $proxy[$row['country']]['ipAddress'] = $row['ip_address'];
                $proxy[$row['country']]['countryCode'] = $row['country_code'];
                $proxy[$row['country']]['countryName'] = $row['country_name'];
                $proxy[$row['country']]['regionName'] = $row['region_name'];
                $proxy[$row['country']]['cityName'] = $row['city_name'];
                $proxy[$row['country']]['zipCode'] = $row['zip_code'];
                $proxy[$row['country']]['latitude'] = $row['latitude'];
                $proxy[$row['country']]['longitude'] = $row['longitude'];
                $proxy[$row['country']]['timeShift'] = $row['time_zone'];
                $proxy[$row['country']]['domain'] = $row['domain'];
                $proxy[$row['country']]['port'] = $row['port'];
                $proxy[$row['country']]['enable'] = $row['enable'];
                $proxy[$row['country']]['country'] = $row['country'];
            }
            return $proxy;
        } else {
            return false;
        }
    }
    
    function syncSitesConfig($conf)
    {
        
        $this->setDc($conf);
        $this->adminLogin();
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . "/user/sites");
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($this->ch);

        $html     = new nokogiri($out);
        $elements = $html->get(".grid-view")->toArray();

        for ($i = 0; $i < count($elements); $i++) {
            for ($y = 0; $y < sizeof($elements[$i]['table'][0]['tbody'][0]['tr']); $y++) {
                $siteConf['siteId'] = $elements[$i]['table'][0]['tbody'][0]['tr'][$y]['td'][2]['#text'];

                curl_setopt($this->ch, CURLOPT_URL, 'https://' . $this->mainSite . '/admin/user/sites/view/' . $siteConf['siteId']);
                curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
                curl_setopt($this->ch, CURLOPT_VERBOSE, 0);
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                    'X-Requested-With: XMLHttpRequest'
                ));

                $jsonAnswer = json_decode(curl_exec($this->ch));

                $siteConf['site_name'] = strtolower($jsonAnswer->{$siteConf['siteId']}->{'siteName'});
                if (!is_object($jsonAnswer->{$siteConf['siteId']}->{'skin'})) {
                    $siteConf['skin'] = $jsonAnswer->{$siteConf['siteId']}->{'skin'};
                } elseif (!is_object($jsonAnswer->{$siteConf['siteId']}->{'skin'}->{'default'})) {
                    $siteConf['skin'] = $jsonAnswer->{$siteConf['siteId']}->{'skin'}->{'default'};
                } else {
                    $siteConf['skin'] = '';
                }
                $siteConf['company_name'] = $jsonAnswer->{$siteConf['siteId']}->{'companyName'};
                $siteConf['site_url']     = strtolower($jsonAnswer->{$siteConf['siteId']}->{'siteUrl'});
                $siteConf['site_domain']  = strtolower($jsonAnswer->{$siteConf['siteId']}->{'siteDomain'});

                $new_sites = array(
                    "nastyhookups.com",
                    "ulove.com",
                    "affairdating.com",
                    "seksueltforhold.dk",
                    "sexlugar.es",
                    "amissexy.com",
                    "annoncessexy.fr",
                    "copainssexy.com",
                );
                
                if (($siteConf['skin'] != 'lgw.vanilla' && $siteConf['skin'] != 'lgw.vermillion' && $siteConf['skin'] != 'lgw.turquoise')
                && (($siteConf['company_name'] == "Alcuda Limited" || $siteConf['company_name'] == "Cisca Services Ltd" || $siteConf['company_name'] == "Enedina Limited") || ($siteConf['company_name'] == "pmMedia"  && in_array($siteConf['site_domain'],$new_sites)))) {
                    try {
                        $updateSiteConfigQuery = $this->db->prepare("
                            INSERT INTO 
                                `sites_config` (
                                    `site_name`,
                                    `site_id`,
                                    `company_name`,
                                    `site_url`,
                                    `site_domain`,
                                    `skin`
                                )
                                VALUES (
                                    :site_name,
                                    :siteId,
                                    :company_name,
                                    :site_url,
                                    :site_domain,
                                    :skin
                                )
                            ON DUPLICATE KEY UPDATE            
                                `site_name` = :site_name2,
                                `company_name` = :company_name2,
                                `site_url` = :site_url2,
                                `site_domain` = :site_domain2,
                                `skin` = :skin2
                        ;");
                        
                        foreach ($siteConf as $key => $item) {
                            if ($key != 'siteId') {
                                $this->bindMultiple($updateSiteConfigQuery, array(
                                    $key,
                                    $key . '2'
                                ), $item);
                            } else {
                                $updateSiteConfigQuery->bindValue(':siteId', $item);
                            }
                        }
                        
                        
                        $updateSiteConfigQuery->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                    }
                }
            }
            
        }
        
    }
    
    function saveSyncUser($userInfo)
    {

        if (isset($userInfo)) {
            if ($userInfo['mail'] != 'adghcvnhtg@outlook.com' || $userInfo['mail'] != '') {
                try {
                    $insertUserInfoQuery = $this->db->prepare("
          INSERT INTO 
            `profile` (
                `id`,
                `mail`,
                `login`,
                `password`,
                `key`,
                `site_id`,
                `gender`,
                `orientation`,
                `fname`,
                `lname`,
                `country`,
                `birthday`,
                `reg_time`,
                `active`,
                `traffic`,
                `platform`,
                `ll`,
                `chats`,
                `searchable`,
                `confirmed`
            )
          VALUES (
                :id,
                :mail,
                :login,
                :password,
                :key,
                :siteId,
                :gender,
                :orientation,
                :fname,
                :lname,
                :country,
                :birthday,
                :regTime,
                :active,
                :traffic,
                :platform,
                :ll,
                :chatsCount,
                :searchable,
                :confirmed
          )
          ON DUPLICATE KEY UPDATE            
                `mail` = :mail2,
                `login` = :login2,
                `password` = :password2,
                `key` = :key2,
                `site_id` = :siteId2,
                `gender` = :gender2,
                `orientation` = :orientation2,
                `fname` = :fname2,
                `lname` = :lname2,
                `country` = :country2,
                `birthday` = :birthday2,
                `reg_time` = :regTime2,
                `active` = :active2,
                `traffic` = :traffic2,
                `platform` = :platform2,
                `ll` = :ll2,
                `chats` = :chatsCount2,
                `searchable` = :searchable2,
                `confirmed` = :confirmed2
          ;");
                    foreach ($userInfo as $key => $item) {
                        if ($key != 'id') {
                            $this->bindMultiple($insertUserInfoQuery, array(
                                $key,
                                $key . '2'
                            ), $item);
                        } else {
                            $insertUserInfoQuery->bindValue(':id', $item);
                        }
                    }
                    
                    
                    $insertUserInfoQuery->execute();
                    $this->syncDc($userInfo['mail']);
                    return true;
                }
                catch (PDOException $e) {
                    echo $e->getMessage();
                    file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                    return false;
                }
            }
        }
    }
    
    function bindMultiple($stmt, $params, $variable)
    {
        foreach ($params as $param) {
            $stmt->bindValue(':' . $param, $variable);
        }
    }
    
    function getSitesConfig()
    {
        try {
            $getSitesConfigQuery = $this->db->query("
        SELECT 
            `site_name`,
            `site_id`,
            `site_url`,
            `site_domain`,
            `company_name`,
            `locale`,
            `dictionary_id`
        FROM
          `sites_config`
    WHERE `skin` != 'lgw.vanilla'
    AND `enabled` = true;
        ;");
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
        if ($getSitesConfigQuery->rowCount() > 0) {
            while ($row = $getSitesConfigQuery->fetch()) {
                $sitesConfig[$row['site_id']]['live']         = $row['site_url'];
                $sitesConfig[$row['site_id']]['site_name']    = $row['site_name'];
                $sitesConfig[$row['site_id']]['site_id']    = $row['site_id'];
                $sitesConfig[$row['site_id']]['domain']       = $row['site_domain'];
                $sitesConfig[$row['site_id']]['company_name'] = $row['company_name'];
                $sitesConfig[$row['site_id']]['locale']       = $row['locale'];
                $sitesConfig[$row['site_id']]['dictionaryId']       = $row['dictionary_id'];
            }
            
            return $sitesConfig;
        } else {
            return false;
        }
    }
    
    function syncDc($param)
    {
        try {
            $dc_synced_deleteQuery_ph = $this->db->prepare("
                DELETE FROM 
                    `temp_profiles` 
                WHERE 
                    `email` = :curr_mail
            ;");
            $dc_synced_deleteQuery_ph->bindParam(':curr_mail', $param);
            $dc_synced_deleteQuery_ph->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            return false;
        }
    }
    
    public function findByEmail($email)
    {
        
        if (is_valid_email_address($email)) {
            try {
                $findByEmailQuery = $this->db->prepare("
                SELECT           
                    `profile`.`id`,
                    `profile`.`mail`,
                    `profile`.`login`,
                    `profile`.`password`,
                    `profile`.`key`,
                    `sites_config`.`site_name` as site,
                    `sites_config`.`site_domain`,
                    `profile`.`gender`,
                    `profile`.`orientation`,
                    `profile`.`fname`,
                    `profile`.`lname`,
                    `profile`.`country`,
                    `profile`.`birthday`,
                    `profile`.`reg_time`,
                    `profile`.`active`,
                    `profile`.`traffic`,
                    `profile`.`platform`,
                    `profile`.`ll`,
                    `profile`.`chats`,
                    `profile`.`site_id`
                FROM
                    `profile`
                INNER JOIN 
                    `sites_config`
                ON
                    `profile`.`site_id` = `sites_config`.`site_id`
                WHERE
                    `mail` = :mail
                LIMIT 1
            ;");
                $findByEmailQuery->bindParam(':mail', $email);
                $findByEmailQuery->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                file_put_contents('PDOErrors.txt', $e->getMessage() . '\r\n', FILE_APPEND);
                return false;
            }
            
            if ($findByEmailQuery->rowCount() > 0) {
                
                $i = 0;
                
                while ($row = $findByEmailQuery->fetch()) {
                    $answer['data'][$i]['site']        = $row['site'];
                    $answer['data'][$i]['gender']      = $row['gender'];
                    $answer['data'][$i]['country']     = $row['country'];
                    $answer['data'][$i]['key']         = $row['key'];
                    $answer['data'][$i]['regTime']    = $row['reg_time'];
                    $answer['data'][$i]['id']          = $row['id'];
                    $answer['data'][$i]['email']       = $row['mail'];
                    $answer['data'][$i]['password']    = $row['password'];
                    $answer['data'][$i]['traffic']     = $row['traffic'];
                    $answer['data'][$i]['login']       = $row['login'];
                    $answer['data'][$i]['orientation'] = $row['orientation'];
                    $answer['data'][$i]['fname']       = $row['fname'];
                    $answer['data'][$i]['lname']       = $row['lname'];
                    $answer['data'][$i]['birthday']    = $row['birthday'];
                    $answer['data'][$i]['active']      = $row['active'];
                    $answer['data'][$i]['platform']    = $row['platform'];
                    $answer['data'][$i]['ll']          = $row['ll'];
                    $answer['data'][$i]['chatsCount'] = $row['chats'];
                    $answer['data'][$i]['siteId']     = $row['site_id'];
                    $answer['data'][$i]['siteDomain'] = $row['site_domain'];
                    $answer['data'][$i]['splitGroup']  = hexdec(substr(md5($row['id']), 0, 4)) % 100;
                    $i++;
                }
                return $answer;
            } else {
                unset($STH);
                return false;
            }
        }
    }
    
    public function findById($id)
    {
        
        if (isset($id)) {
            try {
                $findByIdQuery = $this->db->prepare("
          SELECT           
                  `profile`.`id`,
                  `profile`.`mail`,
                  `profile`.`login`,
                  `profile`.`password`,
                  `profile`.`key`,
                  `sites_config`.`site_name` as site,
                  `profile`.`gender`,
                  `profile`.`orientation`,
                  `profile`.`fname`,
                  `profile`.`lname`,
                  `profile`.`country`,
                  `profile`.`birthday`,
                  `profile`.`reg_time`,
                  `profile`.`active`,
                  `profile`.`traffic`,
                  `profile`.`platform`,
                  `profile`.`ll`,
                  `profile`.`chats`,
                  `profile`.`site_id`,
                  `profile`.`searchable`,
                  `profile`.`confirmed`
          FROM
            `profile`
          JOIN 
            `sites_config`
          ON
            `profile`.`site_id` = `sites_config`.`site_id`
          WHERE
        `id` = :id
          LIMIT 1
        ;");
                $findByIdQuery->bindParam(':id', $id);
                $findByIdQuery->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                file_put_contents('PDOErrors.txt', $e->getMessage() . '\r\n', FILE_APPEND);
                return false;
            }
            
            if ($findByIdQuery->rowCount() > 0) {
                
                $i = 0;
                
                while ($row = $findByIdQuery->fetch()) {
                    $answer['data'][$i]['site']        = $row['site'];
                    $answer['data'][$i]['gender']      = $row['gender'];
                    $answer['data'][$i]['country']     = $row['country'];
                    $answer['data'][$i]['key']         = $row['key'];
                    $answer['data'][$i]['regTime']    = $row['reg_time'];
                    $answer['data'][$i]['id']          = $row['id'];
                    $answer['data'][$i]['email']       = $row['mail'];
                    $answer['data'][$i]['password']    = $row['password'];
                    $answer['data'][$i]['traffic']     = $row['traffic'];
                    $answer['data'][$i]['login']       = $row['login'];
                    $answer['data'][$i]['orientation'] = $row['orientation'];
                    $answer['data'][$i]['fname']       = $row['fname'];
                    $answer['data'][$i]['lname']       = $row['lname'];
                    $answer['data'][$i]['birthday']    = $row['birthday'];
                    $answer['data'][$i]['active']      = $row['active'];
                    $answer['data'][$i]['platform']    = $row['platform'];
                    $answer['data'][$i]['ll']          = $row['ll'];
                    $answer['data'][$i]['chatsCount'] = $row['chats'];
                    $answer['data'][$i]['searchable']  = $row['searchable'];
                    $answer['data'][$i]['confirmed']   = $row['confirmed'];
                    $answer['data'][$i]['siteId']     = $row['site_id'];
                    $i++;
                }
                return $answer;
            } else {
                unset($STH);
                return false;
            }
        }
    }
    
    function saveTmpUser($userInfo)
    {
        try {
            $insertTmpUserInfoQuery = $this->db->prepare("
        INSERT INTO 
          `temp_profiles`(
            email) 
        VALUES (
            :email
        )
      ;");
            $insertTmpUserInfoQuery->bindValue(':email', $userInfo);
            
            $insertTmpUserInfoQuery->execute();
            return true;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            return false;
        }
    }
            /*
        try {

            $valueString = substr($valueString, 0, -2);
            $saveTaskConfigQuery = $this->db->prepare("
            INSERT INTO 
                `task_config`(
                    `task_id`,
                    `country`,
                    `site_id`,
                    `count`,
                    `device`,
                    `gender`,
                    `referer`,
                    `email`,
                    `age`
                ) 
            VALUES $valuesString 
      ;");
            $saveTaskConfigQuery->bindValue(':email', $userInfo);
            
            $saveTaskConfigQuery->execute();
            return true;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            return false;
        }
    
    }
    */
    /*--- not actual
    
    

    
    function getUsersForSync($dc)
    {
        try {
            $usedEmailQuery = $this->db->query("
        SELECT 
          `email`
        FROM
          `temp_profiles`
        LIMIT 25
        ;");
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        if ($usedEmailQuery->rowCount() > 0) {
            
            while ($row = $usedEmailQuery->fetch()) {
                $users[] = $row['email'];
            }
            return $users;
        } else {
            return false;
        }
        
    }
    
    function getUsersForUpdate()
    {
        $date = date('Y-m-d H:i:s', strtotime('-12 hours'));
        try {
            $usedEmailForUpdQuery = $this->db->prepare("
        SELECT 
          DISTINCT `mail`
        FROM
          `profile`
    WHERE `location` is NULL
        LIMIT 50       
        ;");
            
            $usedEmailForUpdQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
        if ($usedEmailForUpdQuery->rowCount() > 0) {
            while ($row = $usedEmailForUpdQuery->fetch()) {
                $users[] = $row['mail'];
            }
            return $users;
        } else {
            return false;
        }
    }
    
    function getUsersForSyncDate($dc)
    {
        try {
            $usedEmailQuery = $this->db->prepare("
        SELECT 
          `id`
        FROM
          `profile`
        WHERE
          `dc` = :dc
          AND
          `reg_time` = '0000-00-00 00:00:00'

        ;");
            $usedEmailQuery->bindValue(':dc', $dc);
            $usedEmailQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        if ($usedEmailQuery->rowCount() > 0) {
            while ($row = $usedEmailQuery->fetch()) {
                $users[] = $row['id'];
            }
            return $users;
        } else {
            return false;
        }
    }
    
    function getCreateriaList($createria)
    {
        try {
            $listQuery = $this->db->prepare("
      SELECT 
        DISTINCT $createria
      FROM
        `profile`
      ORDER BY $createria ASC
      ;");
            $listQuery->execute();
            $list = array();
            while ($row = $listQuery->fetch()) {
                $list[] = $row[$createria];
            }
            return $list;
        }
        catch (PDOException $e) {
            echo $e->getMessage() . '\r\n';
            file_put_contents('PDOErrors.txt', $e->getMessage() . '\r\n', FILE_APPEND);
            return false;
        }
    }
    
    function syncDates($userId, $site, $config)
    {
        
        $this->setDc($config);
        $this->adminLogin();
        
        curl_setopt($this->ch, CURLOPT_URL, "https://www." . $this->mainSite . ".com/profiles/search.php?pid=" . $userId . "&site" . $this->siteConf[$site]['id']);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, "pid=" . $userId . "&chk=&action=ajax_profile_details");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'X-Requested-With: XMLHttpRequest'
        ));
        
        $out = curl_exec($this->ch);
        curl_close($this->ch);
        $html     = new nokogiri($out);
        $elements = $html->get("tr")->toArray();
        
        $reg_str = implode('|', $elements[2]['td'][2]['table'][0]['tr'][4]['td'][3]);
        preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/i", $reg_str, $matches);
        $reg = $matches[0];
        
        $conf_str = implode('|', $elements[2]['td'][2]['table'][0]['tr'][5]['td'][3]);
        preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/i", $conf_str, $matches);
        $conf = $matches[0];
        
        $traff_str = implode('|', $elements[2]['td'][2]['table'][0]['tr'][6]['td'][3]);
        preg_match("/([\-a-zA-Z]*)/i", $traff_str, $matches);
        $traff = $matches[0];
        try {
            $dcSyncedUpdateQuery = $this->db->prepare("
          UPDATE 
            `profile` 
          SET 
            `reg_time` = :regTime,
            `conf_time` = :confTime, 
            `traffic` = :traffic,
            `last_sync` = NOW()
          WHERE 
            `id` = :id
        ;");
            
            $dcSyncedUpdateQuery->bindParam(':id', $userId);
            $dcSyncedUpdateQuery->bindParam(':regTime', $reg);
            $dcSyncedUpdateQuery->bindParam(':confTime', $conf);
            $dcSyncedUpdateQuery->bindParam(':traffic', $traff);
            
            $dcSyncedUpdateQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        }
        
    }
    
    function findByCreaterias($createrias, $sort_by = 'reg_time', $sort = 'DESC', $page = 1)
    {
        $createriasText = $createrias;
        $createriasText .= " ORDER BY `" . $sort_by . "` " . $sort;
        try {
            $count = 20;
            
            $limitTo   = $page * $count;
            $limitFrom = $limitTo - $count;
            
            $findByCreateriaCount = $this->db->prepare("
        SELECT
          count(*)
        FROM
          `profile`
        $createriasText
      ;");
            $findByCreateriaCount->execute();
            $count_result = $findByCreateriaCount->fetch();
            
            $findByCreateriaQuery = $this->db->prepare("
        SELECT           
                `id`,
                `mail`,
                `login`,
                `password`,
                `key`,
                `sites_config`.`site_name` as site,
                `gender`,
                `orientation`,
                `fname`,
                `lname`,
                `country`,
                `birthday`,
                `reg_time`,
                `active`,
                `traffic`,
                `platform`,
                `ll`,
                `chats`,
                `site_id`
        FROM
          `profile`
        JOIN 
            `sites_config`
        ON
            `profile`.`site_id` = `sites_config`.`site_id`
        $createriasText
        LIMIT $limitFrom, $limitTo
        ;");
            
            $findByCreateriaQuery->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('PDOErrors.txt', $e->getMessage() . '\r\n', FILE_APPEND);
            return false;
        }
        
        if ($findByCreateriaQuery->columnCount() > 0) {
            $i = 0;
            while ($row = $findByCreateriaQuery->fetch()) {
                $answer['data'][$i]['site']        = $row['site'];
                $answer['data'][$i]['gender']      = $row['gender'];
                $answer['data'][$i]['country']     = $row['country'];
                $answer['data'][$i]['key']         = $row['key'];
                $answer['data'][$i]['regTime']    = $row['reg_time'];
                $answer['data'][$i]['id']          = $row['id'];
                $answer['data'][$i]['mail']        = $row['mail'];
                $answer['data'][$i]['password']    = $row['password'];
                $answer['data'][$i]['traffic']     = $row['traffic'];
                $answer['data'][$i]['login']       = $row['login'];
                $answer['data'][$i]['orientation'] = $row['orientation'];
                $answer['data'][$i]['fname']       = $row['fname'];
                $answer['data'][$i]['lname']       = $row['lname'];
                $answer['data'][$i]['birthday']    = $row['birthday'];
                $answer['data'][$i]['active']      = $row['active'];
                $answer['data'][$i]['platform']    = $row['platform'];
                $answer['data'][$i]['ll']          = $row['ll'];
                $answer['data'][$i]['chatsCount'] = $row['chats'];
                $answer['data'][$i]['siteId']     = $row['site_id'];
                $i++;
            }
            $answer['count']        = $count_result[0];
            $answer['sites']        = $sites;
            $answer['sortElement'] = $sort_by;
            $answer['sort']         = $sort;
            return $answer;
        } else {
            unset($STH);
            return false;
        }
    }
    

    


    

    
    */
}
