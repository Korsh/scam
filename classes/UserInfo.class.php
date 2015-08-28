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
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . $this->findUrl . '/?FindUserForm[user]=' . urlencode($createria).'&json=true');
            
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            $out            = curl_exec($this->ch);

            $userInfoArray  = array_shift(array_shift(json_decode($out,true)));
            
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
                'country'     => $userInfoArray['country'],
                'birthday'    => $userInfoArray['birthday'],
                'regTime'     => $userInfoArray['registeredAt'],
                'firstLogin'  => $userInfoArray['firstLoginAt'],
                'active'      => $userInfoArray['isActive'],
                'traffic'     => $userInfoArray['trafficSource'],
                'platform'    => $userInfoArray['registrationPlatform'],
                'll'          => $userInfoArray['latitude'].':'.$userInfoArray['longitude'],
                'searchable'  => $userInfoArray['isSearchable'],
                'confirmed'   => $userInfoArray['isConfirmed'],
            );
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
            if(!empty($userInfo['id'])) {
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
                $user = array(
                    'active'      => $row['active'],
                    'password'    => $row['password'],
                    'login'       => $row['login'],
                    'orientation' => $row['orientation'],
                    'site'        => $row['site'],
                    'country'     => $row['country'],
                    'mail'        => $row['mail'],
                    'gender'      => $row['gender'],
                    'register'    => $row['reg_time'],
                    'birthday'    => $row['birthday'],
                    'age'         => date('Y') - date('Y', (int)strtotime($row['birthday'])),
                    'id'          => $row['id'],
                    'll'          => $row['ll'],
                    'key'         => $row['key'],
                    'chatsCount'  => $row['chats'],
                    'searchable'  => $row['searchable'],
                    'confirmed'   => $row['confirmed'],
                    'platform'    => $row['platform'],
                    'fname'       => $row['fname'],
                    'lname'       => $row['lname'],
                    'traffic'     => $row['traffic'],
                    'siteId'      => $row['site_id'],
                    'location'    => $row['location'],
                );
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
                $users[$i] = array(
                    'site'        => $row['site'],
                    'country'     => $row['country'],
                    'mail'        => $row['mail'],
                    'gender'      => $row['gender'],
                    'register'    => $row['reg_time'],
                    'birthday'    => $row['birthday'],
                    'age'         => date('Y') - date('Y', strtotime($row['birthday'])),
                    'id'          => $row['id'],
                    'll'          => $row['ll'],
                    'key'         => $row['key'],
                    'chatsCount'  => $row['chats'],
                    'searchable'  => $row['searchable'],
                    'confirmed'   => $row['confirmed'],
                    'platform'    => $row['platform'],
                );
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
                WHERE `task_id` IS NOT NULL ORDER BY `task_id` DESC LIMIT 50
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
                $proxy[$row['country']] = array(
                        'id'            => $row['id'],
                        'ipAddress'     => $row['ip_address'],
                        'countryCode'   => $row['country_code'],
                        'countryName'   => $row['country_name'],
                        'regionName'    => $row['region_name'],
                        'cityName'      => $row['city_name'],
                        'zipCode'       => $row['zip_code'],
                        'latitude'      => $row['latitude'],
                        'longitude'     => $row['longitude'],
                        'timeShift'     => $row['time_zone'],
                        'domain'        => $row['domain'],
                        'port'          => $row['port'],
                        'enable'        => $row['enable'],
                        'country'       => $row['country'],
                );
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
            if ($userInfo['mail'] != 'adghcvnhtg@outlook.com') {
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
                                    `first_login`,
                                    `active`,
                                    `traffic`,
                                    `platform`,
                                    `ll`,
                                    `searchable`,
                                    `confirmed`,
                                    `test`
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
                                    :firstLogin,
                                    :active,
                                    :traffic,
                                    :platform,
                                    :ll,
                                    :searchable,
                                    :confirmed,
                                    1
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
                                `first_login` = :firstLogin2,
                                `active` = :active2,
                                `traffic` = :traffic2,
                                `platform` = :platform2,
                                `ll` = :ll2,
                                `searchable` = :searchable2,
                                `confirmed` = :confirmed2,
                                `test` = 1
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
            $sitesConfig = array();
            while ($row = $getSitesConfigQuery->fetch()) {
                $sitesConfig[$row['site_id']] = array(
                        'live'          => $row['site_url'],
                        'site_name'     => $row['site_name'],
                        'site_id'       => $row['site_id'],
                        'domain'        => $row['site_domain'],
                        'company_name'  => $row['company_name'],
                        'locale'        => $row['locale'],
                        'dictionaryId'  => $row['dictionary_id'],
                );
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
                    `profile`.`site_id`,
                    `profile`.`searchable`
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
                    $answer['data'][$i] = array(
                        'site'          => $row['site'],
                        'gender'        => $row['gender'],
                        'country'       => $row['country'],
                        'key'           => $row['key'],
                        'regTime'       => $row['reg_time'],
                        'id'            => $row['id'],
                        'email'         => $row['mail'],
                        'password'      => $row['password'],
                        'traffic'       => $row['traffic'],
                        'login'         => $row['login'],
                        'orientation'   => $row['orientation'],
                        'fname'         => $row['fname'],
                        'lname'         => $row['lname'],
                        'birthday'      => $row['birthday'],
                        'active'        => $row['active'],
                        'platform'      => $row['platform'],
                        'll'            => $row['ll'],
                        'chatsCount'    => $row['chats'],
                        'siteId'        => $row['site_id'],
                        'siteDomain'    => $row['site_domain'],
                        'searchable'    => $row['searchable'],
                        'splitGroup'    => hexdec(substr(md5($row['id']), 0, 4)) % 100,
                    );
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
                    $answer['data'][$i] = array(
                        'site'          => $row['site'],
                        'gender'        => $row['gender'],
                        'country'       => $row['country'],
                        'key'           => $row['key'],
                        'regTime'       => $row['reg_time'],
                        'id'            => $row['id'],
                        'email'         => $row['mail'],
                        'password'      => $row['password'],
                        'traffic'       => $row['traffic'],
                        'login'         => $row['login'],
                        'orientation'   => $row['orientation'],
                        'fname'         => $row['fname'],
                        'lname'         => $row['lname'],
                        'birthday'      => $row['birthday'],
                        'active'        => $row['active'],
                        'platform'      => $row['platform'],
                        'll'            => $row['ll'],
                        'chatsCount'    => $row['chats'],
                        'searchable'    => $row['searchable'],
                        'confirmed'     => $row['confirmed'],
                        'siteId'        => $row['site_id'],
                    );
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
}
