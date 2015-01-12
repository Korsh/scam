<?php

class UserActions
{
    
    private $gender;
    private $age;
    private $email;
    private $pass;
    private $site;
    private $location;
    private $country;
    var $ch;
    private $adminCh;
    private $adminLogin;
    private $adminPass;
    
    private $proxyAuth;
    private $proxyUrl;
    private $proxyPort;
    private $patternLocale = array(
            'deu' => 'ÜüäÖöẞß',
            'esp' => '¿¡áéíñóúÁÉÍÑÓÚ',
            'fra' => 'ÀàÂâÈèÉéÊêËëÎîÏïÔôÙùÛûŸ­ÿÆæŒœÇç',
            'nor' => 'ÆæØøÅå',
            'swe' => 'ÅåÄäÖö',
            'dnk' => 'ÆæØøÅå',
            'ita' => 'èéÈàáìíùúòó',
            'bra' => 'ãàáéêçíñõôóúÁÉÍÑÓÚ',
            'fin' => 'ŠšŽžÅåÄäÖö',
        );
        
    public function setAdminLoginPass($login, $pass)
    {
        $this->adminLogin = $login;
        $this->adminPass  = $pass;
    }
    
    public function setDefaultGender($gender)
    {
        $this->gender = $gender;
    }
    
    public function getDefaultGender()
    {
        return $this->gender;
    }
    
    public function setDefaultAge($age)
    {
        $this->age = $age;
    }
    
    public function getDefaultAge()
    {
        return $this->age;
    }
    
    public function setDefaultEmail($email)
    {
        $this->email = $email;
    }
    
    public function getDefaultEmail()
    {
        return $this->email;
    }
    
    public function setDefaultPass($pass)
    {
        $this->pass = $pass;
    }
    
    public function getDefaultPass()
    {
        return $this->pass;
    }
    
    public function setDefaultSite($site)
    {
        $this->site = $site;
    }
    
    public function getDefaultSite()
    {
        return $this->site;
    }
    
    public function setDefaultLocation($location)
    {
        $this->location = $location;
    }
    
    public function getDefaultLocation()
    {
        return $this->location;
    }
    
    function UserActions($DBH, $proxyConf)
    {
        $this->db         = $DBH;
        $this->proxyConf = $proxyConf;
    }
    
    public function setUpProxy($proxyAuth, $proxyUrl, $proxyPort = 3128)
    {
        if (!empty($proxyAuth)) {
            $this->proxyAuth = $proxyAuth;
        } else {
            return false;
        }
        if (!empty($proxyUrl)) {
            $this->proxyUrl = $proxyUrl;
        } else {
            return false;
        }
        $this->proxyPort = $proxyPort;
        return true;
    }
    
    public function registerUser($email, $site, $gender = 'male', $age = 21, $userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0", $platform = "web", $request_id = null, $referer = null, $password = '123123', $protocol = "www")
    {
        if (empty($email) || !(bool) is_valid_email_address($email) || empty($site)) {
            return false;
        }
        if ($age < 18) {
            $age = 18;
        }
        if (!empty($platform) && $platform == "mob") {
            $protocol = "m";
        }
        
        $referer = empty($referer) ? "https://" . $protocol . "." . $site : $referer;
        list($account, $domain) = split('@', $email);
        $unique_id = empty($request_id) ? time() : $request_id;
        $email     = $account . '+' . $unique_id . '@' . $domain;
        $this->ch  = curl_init();
        
        curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyPort);
        curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyUrl);
        curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
        curl_setopt($this->ch, CURLOPT_PROXYTYPE, 'HTTP');
        $location = $this->getLocation();
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
        
        curl_setopt($this->ch, CURLOPT_URL, $referer);
        
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($this->ch, CURLOPT_URL, $referer);
        curl_exec($this->ch);
        
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $protocol . "." . $site . "/user/register");
        
        $postArr = array(
            "UserForm" => array(
                "gender" => $gender,
                "day" => date('d', time()),
                "month" => date('m', time()),
                "year" => date('Y', time()) - $age,
                "email" => $email,
                "password" => $password,
                "location" => $location
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_exec($this->ch);
        return $email;
    }
    
    public function loginUserByAutologin($autologin, $platform = 'web', $protocol = 'www')
    {
        if (!empty($platform) && $platform == "mob") {
            $protocol = "m";
        }
        curl_setopt($this->ch, CURLOPT_POST, false);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_URL, 'https://' . $protocol . '.' . $autologin);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($this->ch);
    }
    
    public function adminLogin()
    {
        $this->adminCh = curl_init();
        $postArr       = array(
            "AdminLoginForm" => array(
                "login" => $this->adminLogin,
                "password" => $this->adminPass
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        
        curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/admin/base/login");
        curl_setopt($this->adminCh, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->adminCh, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->adminCh, CURLOPT_VERBOSE, 1);
        curl_setopt($this->adminCh, CURLOPT_POST, true);
        curl_setopt($this->adminCh, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 0);
        curl_exec($this->adminCh);
    }
    
    public function getUserActivity($userId)
    {
        $this->adminLogin();
        curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/user/chats?userId=" . $userId);
        curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 1);
        $out  = curl_exec($this->adminCh);
        $html = new nokogiri($out);
        if (sizeof($html->get(".empty")->toArray()) > 1) {
            $chatsInfo['chats'][0]['message'] = "empty";
            $chatsInfo['chats'][0]['user']    = "empty";

            return $chatsInfo;
        } else {
            if (sizeof($html->get(".pagination")->toArray()) < 1) {
                $pages = 1;
            } else {
                $page_arr = $html->get(".pagination")->toArray();
                $pages    = sizeof($page_arr[0]['ul']['li']) - 2;
            }
            $k = 0;
            for ($y = 1; $y <= $pages; $y++) {
                curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/user/chats?userId=" . $userId . "&page=" . $y);
                curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 1);
                $out  = curl_exec($this->adminCh);
                $html = new nokogiri($out);
                
                $elements = $html->get(".grid-view")->toArray();
                $element = $elements[0]['table'][0]['tbody'][0]['tr'];
                for ($i = 0; $i < sizeof($element); $i++) {
                    $profileLink = array_key_exists($i, $element) ? $element[$i]['td'][1]['a']['href'] : $element['td'][1]['a']['href'];
                    preg_match_all("/=([a-z0-9]*)/i", $profileLink, $matches);
                    $chatsInfo['chats'][$k]['user']['id'] = $matches[1][0];
                    $chatsInfo['chats'][$k]['user']    = $this->getUserInfo($chatsInfo['chats'][$k]['user']['id']);
                    $chatsInfo['chats'][$k]['message'] = $this->getChatInfo($userId, $chatsInfo['chats'][$k]['user']['id']);
                    if (!array_key_exists($i, $element)) {
                        break;
                    }
                    $k++;
                }
            }
            
        }
        $this->saveChats($userId, $chatsInfo);
        return $chatsInfo;
    }
    
    public function getChats($user, $day = false)
    {
        if ($day !== false) {
            $startDate = date("Y-m-d H:i:s", (strtotime($user['register']) + ($day * 86400)));
            $endDate   = date("Y-m-d H:i:s", (strtotime($user['register']) + ($day * 86400 + 86400)));
            try {
                $getChatsByUserIdQuery = $this->db->prepare("
                    SELECT 
                        `send_time`,
                        `sender_id`,
                        `is_99`,
                        `sender_birthday`,
                        `ll`,
                        `country`,
                        `address`,
                        `address_ll`,
                        `screenname`,
                        `message`
                    FROM
                        `chats`
                    WHERE 
                        `user_id` = :user_id 
                    AND 
                        `send_time` > :start_date 
                    AND 
                        `send_time` < :end_date 
                    AND
                        `is_99` != 0
                    ORDER BY `send_time` ASC
                    ;");
                $getChatsByUserIdQuery->bindValue(':start_date', $startDate);
                $getChatsByUserIdQuery->bindValue(':end_date', $endDate);
                $getChatsByUserIdQuery->bindValue(':user_id', $user['id']);
                $getChatsByUserIdQuery->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            }
        } else {
            try {
                $getChatsByUserIdQuery = $this->db->prepare("
                    SELECT 
                        `send_time`,
                        `sender_id`,
                        `is_99`,
                        `sender_birthday`,
                        `ll`,
                        `country`,
                        `address`,
                        `address_ll`,
                        `screenname`,
                        `message`
                    FROM
                        `chats`
                    WHERE `user_id` = :user_id
                    AND
                        `is_99` != 0
                    ORDER BY `send_time` ASC
                    ;");
                $getChatsByUserIdQuery->bindValue(':user_id', $user['id']);
                $getChatsByUserIdQuery->execute();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            }
        }
        $chatsInfo['count'] = 0;
        $pattern = array_key_exists($user['country'], $this->patternLocale) ? "/([^".$this->patternLocale[$user['country']]."A-Za-z0-9 …\&\.!+-`’'\*\?():;,\n\s]+)/" : "/([^A-Za-z0-9 …\&\.!+-`’'\*\?():;,\n\s]+)/";
        if ($getChatsByUserIdQuery->rowCount() > 0) {
            
            $regTime = strtotime($user['register']);
            $birthday = date('Y', $user['birthday']) - date('Y');
            $i        = 0;
            while ($row = $getChatsByUserIdQuery->fetch()) {
                list($senderLat, $senderLon) = split(',', $row['ll']);
                list($addressLat, $addressLon) = split(',', $row['address_ll']);
                list($userLat, $userLon) = split(',', $user['ll']);
                $sendTime = strtotime($row['send_time']) - $regTime;
                $age       = date('Y') - date('Y', strtotime($row['sender_birthday']));
                
                $days      = (date('d', $sendTime) - 1 != 0) ? date('d', $sendTime) - 1 . 'd, ' : false;
                $sendTime = $days . date('H:i:s', $sendTime) . ' (' . $row['send_time'] . ')';
                
                $chatsInfo['chats'][$i]['message']['time']         = $sendTime;
                $chatsInfo['chats'][$i]['user']['id']              = $row['sender_id'];
                $chatsInfo['chats'][$i]['user'][99]                = $row['is_99'];
                $chatsInfo['chats'][$i]['user']['screenname']      = $row['screenname'];
                $chatsInfo['chats'][$i]['user']['ll']              = $row['ll'];
                $chatsInfo['chats'][$i]['user']['age']             = $age;
                $chatsInfo['chats'][$i]['user']['country']         = strtoupper($row['country']);
                $chatsInfo['chats'][$i]['user']['birthday']        = $row['sender_birthday'];
                $chatsInfo['chats'][$i]['user']['distance']        = $this->calculateTheDistance($userLat, $userLon, $senderLat, $senderLon);
                $chatsInfo['chats'][$i]['user']['address_shift']   = $this->calculateTheDistance($senderLat, $senderLon, $addressLat, $addressLon);
                $chatsInfo['chats'][$i]['message']['text']    = $user['country'] != 'esp' || $user['country'] != 'fra' ? $row['message'] : $row['message'];
                $chatsInfo['count']                           = $chatsInfo['count'] + $row['is_99'];
                if ($chatsInfo['chats'][$i]['user'][99] == 1) {
                    $chatsInfo['chats'][$i]['user']['address_error']    = !empty($chatsInfo['chats'][$i]['user']['address_shift']) && $chatsInfo['chats'][$i]['user']['address_shift'] > 20 ? true : false;
                    $chatsInfo['chats'][$i]['user']['address'] = $row['address'];
                    $chatsInfo['chats'][$i]['user']['address_ll'] = $row['address_ll'];

                    $chatsInfo['chats'][$i]['user']['distance_error']   = $chatsInfo['chats'][$i]['user']['distance'] > 120 ? true : false;
                    $chatsInfo['chats'][$i]['message']['message_error'] = preg_match($pattern, $chatsInfo['chats'][$i]['message']['text']) == 1 ? true : false;
                }
                $i++;
            }
        } else {
            $chatsInfo['chats'][0]['message'] = 'empty';
            $chatsInfo['chats'][0]['user']    = 'empty';
        }

        return $chatsInfo;
    }
    
    public function calculateTheDistance($φA, $λA, $φB, $λB)
    {
        $earthRadius = 6372795;
        $lat1         = $φA * M_PI / 180;
        $lat2         = $φB * M_PI / 180;
        $long1        = $λA * M_PI / 180;
        $long2        = $λB * M_PI / 180;
        
        $cl1    = cos($lat1);
        $cl2    = cos($lat2);
        $sl1    = sin($lat1);
        $sl2    = sin($lat2);
        $delta  = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);
        
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
        
        $ad   = atan2($y, $x);
        $dist = round($ad * $earthRadius / 1000 / 1.608, 2);
        return $dist;
    }
    
    function getCoordinates($address)
    {
        $address = str_replace(" ", "+", $address);
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
        $response = file_get_contents($url);
        $json = json_decode($response,TRUE);
        return ($json['results'][0]['geometry']['location']['lat'].','.$json['results'][0]['geometry']['location']['lng']);
    }

    public function saveChats($userId, $chatsInfo)
    {
        for ($i = 0; $i < sizeof($chatsInfo['chats']); $i++) {
            for ($y = 0; $y < sizeof($chatsInfo['chats'][$i]['message']); $y++) {
                if ($chatsInfo['chats'][$i]['user']['id'] != '') {
                    $hash = md5($userId . $chatsInfo['chats'][$i]['message'][$y]['time'] . $chatsInfo['chats'][$i]['user']['id'] . $chatsInfo['chats'][$i]['message'][$y]['text'] . $y);
                    try {
                        $insertChatsInfoQuery = $this->db->prepare("   
                        INSERT INTO 
                            `chats`(
                                `user_id`,
                                `send_time`,
                                `sender_id`,
                                `is_99`,
                                `sender_birthday`,
                                `ll`,
                                `country`,
                                `address`,
                                `address_ll`,
                                `screenname`,
                                `message`,
                                `message_hash`
                            )
                            VALUES (
                                :user_id,            
                                :send_time,
                                :sender_id,
                                :is_99,
                                :sender_birthday,
                                :ll,
                                :country,
                                :address,
                                :address_ll,
                                :screenname,
                                :message,
                                :hash
                            )
                        ON DUPLICATE KEY UPDATE
                            `send_time` = :send_time2,
                            `ll` = :ll2,
                            `country` = :country2,
                            `address` = :address2,
                            `address_ll` = :address_ll2,
                            `is_99` = :is_992,
                            `sender_birthday` = :sender_birthday2,
                            `screenname` = :screenname2,
                            `message` = :message2
                        ;");
                        $paramArr               = array(
                            'user_id' => $userId,
                            'hash' => $hash,
                            'send_time' => $chatsInfo['chats'][$i]['message'][$y]['time'],
                            'send_time2' => $chatsInfo['chats'][$i]['message'][$y]['time'],
                            'sender_id' => $chatsInfo['chats'][$i]['user']['id'],
                            'is_99' => $chatsInfo['chats'][$i]['user'][99],
                            'is_992' => $chatsInfo['chats'][$i]['user'][99],
                            'screenname' => $chatsInfo['chats'][$i]['user']['screenname'],
                            'screenname2' => $chatsInfo['chats'][$i]['user']['screenname'],
                            'sender_birthday' => $chatsInfo['chats'][$i]['user']['birthday'],
                            'sender_birthday2' => $chatsInfo['chats'][$i]['user']['birthday'],
                            'll' => $chatsInfo['chats'][$i]['user']['ll'],
                            'll2' => $chatsInfo['chats'][$i]['user']['ll'],
                            'country' => $chatsInfo['chats'][$i]['user']['country'],
                            'country2' => $chatsInfo['chats'][$i]['user']['country'],
                            'address' => $chatsInfo['chats'][$i]['user']['address'],
                            'address_ll' => $chatsInfo['chats'][$i]['user']['address_ll'],
                            'address_ll2' => $chatsInfo['chats'][$i]['user']['address_ll'],
                            'address2' => $chatsInfo['chats'][$i]['user']['address'],
                            'message' => $chatsInfo['chats'][$i]['message'][$y]['text'],
                            'message2' => $chatsInfo['chats'][$i]['message'][$y]['text']
                        );
                        echo '<pre>'.print_r($paramArr, true).'</pre>';
                        $insertChatsInfoQuery->execute($paramArr);
                    }
                    catch (PDOException $e) {
                        echo $e->getMessage();
                        file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
                        return false;
                    }
                }
            }
        }
    }
    
    public function getChatInfo($userId, $withUserId, $country)
    {
        curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/user/chats?userId=$userId&withUserId=$withUserId");
        curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 1);
        $out      = curl_exec($this->adminCh);
        $html     = new nokogiri($out);
        $elements = $html->get(".chat-history")->toArray();
        if(!empty($elements)) {
            if (!empty($elements[0]['tbody']['tr'][0])) {
                for ($i = 0; $i < sizeof($elements[0]['tbody']['tr']); $i++) {
                    $messageInfo[$i]['time'] = $elements[0]['tbody']['tr'][$i]['td'][0]['#text'];
                    $messageInfo[$i]['text'] = $elements[0]['tbody']['tr'][$i]['td'][1]['#text'];
                    $timeElements            = $html->get("h4")->toArray();
                    $messageInfo[$i]['time'] = $timeElements[2]['#text'] . ' ' . $messageInfo[$i]['time'];
                    preg_match_all('/(\d+).(\d+).(\d+) (\d+):(\d+)/', $messageInfo[$i]['time'], $out, PREG_SET_ORDER);
                    $out                      = $out[0];
                    $messageInfo[$i]['time'] = date('Y-m-d H:i:s', mktime($out[4] - 1 + $this->proxyConf[$country]['timeShift'], $out[5], 0, $out[2], $out[1], $out[3]));
                }
            } else {
                for ($i = 0; $i < sizeof($elements); $i++) {
                    $messageInfo[$i]['time'] = $elements[$i]['tbody']['tr']['td'][0]['#text'];
                    $messageInfo[$i]['text'] = $elements[$i]['tbody']['tr']['td'][1]['#text'];
                    $timeElements            = $html->get("h4")->toArray();
                    $messageInfo[$i]['time'] = $timeElements[2]['#text'] . ' ' . $messageInfo[$i]['time'];
                    preg_match_all('/(\d+).(\d+).(\d+) (\d+):(\d+)/', $messageInfo[$i]['time'], $out, PREG_SET_ORDER);
                    $out                      = $out[0];
                    $messageInfo[$i]['time'] = date('Y-m-d H:i:s', mktime($out[4] - 1 + $this->proxyConf[$country]['timeShift'], $out[5], 0, $out[2], $out[1], $out[3]));
                }
            }
        } else {
            $messageInfo = array(
                0 => array(
                    'time' => null,
                    'text' => null,
                )
            );
        }
        return $messageInfo;
    }
    
    public function getUserInfo($userId)
    {
        curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/user/find?FindUserForm[user]=" . $userId);
        curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 1);
        $out                     = curl_exec($this->adminCh);
        $html                    = new nokogiri($out);
        $elements                = $html->get("#yw1")->toArray();
        if(!empty($elements)) {
            $userInfo['id']         = isset($elements[0]['tr'][0]['td'][0]['#text']) ? $elements[0]['tr'][0]['td'][0]['#text'] : null;
            $userInfo['mail']       = isset($elements[0]['tr'][2]['td'][0]['#text']) ? $elements[0]['tr'][2]['td'][0]['#text'] : null;
            $userInfo['screenname'] = isset($elements[0]['tr'][4]['td'][0]['#text']) ? $elements[0]['tr'][4]['td'][0]['#text'] : null;
            $userInfo['country']    = isset($elements[0]['tr'][13]['td'][0]['#text']) ? strtolower($elements[0]['tr'][13]['td'][0]['#text']) : null;
            $userInfo['birthday']   = isset($elements[0]['tr'][14]['td'][0]['#text']) ? strtolower($elements[0]['tr'][14]['td'][0]['#text']) : null;
            $traffic                = isset($elements[0]['tr'][34]['td'][0]['#text']) ? strtolower($elements[0]['tr'][34]['td'][0]['#text']) : null;
            if ($traffic == "unparsed" || strpos($userInfo['mail'], '@ufins.com') || strpos($userInfo['mail'], 'import') || strpos($userInfo['mail'], '_import') || strpos($userInfo['mail'], '@cupid.com')) {
                $userInfo['99'] = true;
            } else {
                $userInfo['99'] = false;
            }
            $userInfo['ll'] = isset($elements[0]['tr'][21]) && isset($elements[0]['tr'][22]) ? strtolower($elements[0]['tr'][21]['td'][0]['#text']) . "," . strtolower($elements[0]['tr'][22]['td'][0]['#text']) : "no data";
            curl_setopt($this->adminCh, CURLOPT_URL, "https://my.ufins.com/user/edit?user_id=" . $userId);
            curl_setopt($this->adminCh, CURLOPT_RETURNTRANSFER, 1);
            $out                     = curl_exec($this->adminCh);
            $html                    = new nokogiri($out);
            $elements                = $html->get("#location")->toArray();
            $userInfo['address'] = $elements[0]['value'];
            $userInfo['address_ll'] = $this->getCoordinates($userInfo['country'].', '.$userInfo['address']);
        } else {
            $userInfo['id']         = $userId;
            $userInfo['mail']       = null;
            $userInfo['screenname'] = null;
            $userInfo['country']   = null;
            $userInfo['birthday']   = null;
            $userInfo['99']         = true;
            $userInfo['address'] = null;
            $userInfo['address_ll'] = null;
            $userInfo['ll']         = "no data";
        }
        return $userInfo;
    }
    
    public function setScreenname($site, $platform = 'web', $protocol = 'www', $screenname = null)
    {
        if (empty($screenname)) {
            $characters = 'abcdefghijklmnoprstuvwxyz';
            $randstring = '';
            for ($i = 0; $i < 5; $i++) {
                $randstring .= $characters[rand(0, strlen($characters))];
            }
            $screenname = $randstring . substr(time(), -5);
        }
        
        $post = array(
            'userAttributes' => array(
                'screenname' => $screenname,
                'chatUpLine' => ''
            ),
            "YII_CSRF_TOKEN" => "",
            "yt0" => ""
        );
        if (!empty($platform) && $platform == "mob") {
            $protocol = "m";
        }
        echo "https://" . $protocol . '.' . $site . "/funnel";
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->ch, CURLOPT_REFERER, "https://" . $protocol . '.' . $site . "/funnel");
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $protocol . '.' . $site . "/funnel");
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json, text/javascript, */*; q=0.01',
            'X-Requested-With: XMLHttpRequest'
        ));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($this->ch);
    }
    
    public function loginUserByLoginPass($login, $password, $country = "GBR", $userAgent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0")
    {
    }
    
    public function logoutUser($site, $protocol)
    {
        curl_setopt($this->ch, CURLOPT_REFERER, "https://" . $protocol . '.' . $site . "/");
        curl_setopt($this->ch, CURLOPT_URL, "https://" . $protocol . '.' . $site . "/site/logout");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($this->ch);
    }
    
    public function logoutUserByAutologin($autologin)
    {
    }
    
    public function logoutUserBySession($ch)
    {
    }
    
    public function saveUser($email)
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
            $insertTmpUserInfoQuery->bindValue(':email', $email);
            
            $insertTmpUserInfoQuery->execute();
            return true;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            file_put_contents('../PDOErrors.txt', $e->getMessage(), FILE_APPEND);
            return false;
        }
    }
    
    public function getLocation()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
        curl_setopt($ch, CURLOPT_PROXY, $this->proxyUrl);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, "http://api.ipinfodb.com/v3/ip-city/?key=8cef7e56baa8c0bcfe5591b6624fe611bb4e58c5c8b481619dcb6a485f48aa44&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $jsonAnswer = curl_exec($ch);
        curl_close($ch);
        $jsonArray = json_decode($jsonAnswer);
        if (!empty($jsonArray)) {
            return $location = $jsonArray->{'cityName'} . ', ' . $jsonArray->{'zipCode'};
        } else {
            return false;
        }
    }
    
}
