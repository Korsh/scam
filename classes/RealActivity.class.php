<?php


class RealActivity
{
    var $db;
    var $ch;
    var $b2bCh;
    var $b2bLogin;
    var $b2bPass;
    var $b2bHttpLogin;
    var $b2bHttpPass;
    var $b2bSite;
    var $b2bLoginUrl;
    var $b2bFindUrl;
    var $mainSite;
    var $dcConf;
    var $dc;
    var $loginUrl;
    var $findUrl;
    var $type;
    var $login;
    var $pass;
    var $siteConf;
    
    function RealActivity($DBH)
    {
        $this->db        = $DBH;
        $this->adminUrl = 'my.ufins.com';
        $this->dc        = 'pc';
        $this->dcConf   = array(
            "pc",
            "lg"
        );
    }
    
    function getRealUsersIds($dc, $site = array(250), $country = array("GBR"), $platform = array("webSite"), $gender = array("male"), $dateFrom = false, $dateTo = false)
    {
        $this->setDc($dc);
        $this->adminLogin();
        
        $platform = $platform == "webSite" ? array(1) : array(2);
        $dateFrom     = !empty($dateFrom) ? $dateFrom : date('Y-m-d',strtotime(date('Y-m-d') . "-1 days"));
        $dateTo       = !empty($dateTo) ? $dateTo : date('Y-m-d');
        $postArr = array(
            "YII_CSRF_TOKEN" => "",
            "ReportUserInfoForm" => array(
                "site" => $site,
                "countryCode" => $country,
                "platform" => $platform,
                "gender" => $gender,
                "confDateFrom" => $dateFrom,
                "confDateTo" => $dateTo,
                "regDateFrom" => $dateFrom,
                "regDateTo" => $dateTo,
                "columns" => array(
                    0 => "userId",
                    1 => "source",
                    2 => "actionWay",
                    3 => "platform",
                    4 => "confDate",
                    5 => "scammerType",
                    6 => "paidStatus",
                    7 => "site",
                ),
                "excludeTester" => 1,
                "inputType" => "id",
                "userList" => ""
            ),
            "show" => ""
        );
        if($platform == 2) {
            unset($findArr['ReportUserInfoForm']['confDateFrom']);
            unset($findArr['ReportUserInfoForm']['confDateTo']);
        }

        curl_setopt($this->ch, CURLOPT_URL, "https://" . $this->mainSite . "/report/userInfo");
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        //echo '<pre>'.print_r(http_build_query($postArr), true).'</pre>';
        $out = curl_exec($this->ch);
        $html = new nokogiri($out);
        $elements = $html->get("#yw1")->toArray();
        $tdList = $elements[0]['table'][0]['tbody'][0]['tr'];
        for($i=1; $i<50; $i++) {
            $parentTd = $tdList[$i]['td'];
            $userInfo[$parentTd[0]['#text']] = array(
                'source'    => $parentTd[1]['#text'],
                'actionWay' => $parentTd[2]['#text'],
                'platform'  => $parentTd[3]['#text'],
                'confirm'   => $parentTd[4]['#text'],
                'scammer'   => $parentTd[5]['#text'],
                'paid'      => $parentTd[6]['#text'],
                'site'      => $parentTd[7]['#text'],
            );
        }
        return $userInfo;
    }

    public function checkActivity($userActivity, $b2bDc)
    {
        $this->b2bSetDc($b2bDc);
        $this->b2bAdminLogin();
        $userActivityString = implode(', ', array_keys($userActivity));
        curl_setopt($this->b2bCh, CURLOPT_URL, $this->b2bSite.$this->b2bFindUrl);
        $postArr = array(
            "ids" => $userActivityString,
            "dc_num" => 4,
            "action" => "Show user stat"
        );
        curl_setopt($this->b2bCh, CURLOPT_VERBOSE, 1);
        curl_setopt($this->b2bCh, CURLOPT_POST, true);
        curl_setopt($this->b2bCh, CURLOPT_POSTFIELDS, http_build_query($postArr));
        curl_setopt($this->b2bCh, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($this->b2bCh);
        $html = new nokogiri($out);
        $elements = $html->get('.b_right')->toArray();
        unset($elements[0]['table'][0]);
        unset($elements[0]['table'][1]['tr']['td'][0]['style']);
        unset($elements[0]['table'][1]['tr']['td'][0]['form']);
        unset($elements[0]['table'][1]['tr']['td'][0]['#text']);
        $userInfoListNotParsed     = $elements[0]['table'][1]['tr']['td'][0]['div'];
        $userActivityListNotParsed = $elements[0]['table'][1]['tr']['td'][0]['table'];

        foreach($userInfoListNotParsed as $infoKey => $infoValue) {
            $userInfoParse = explode(' ', $infoValue['#text'][0]);
            $userActivity[$userInfoParse[3]]['gender'] = $userInfoParse[7];
            $userActivity[$userInfoParse[3]]['year'] = $userInfoParse[10];
            $userActivity[$userInfoParse[3]]['country'] = $userInfoParse[12];
            $userActivity[$userInfoParse[3]]['language'] = $userInfoParse[14];
            foreach($userActivityListNotParsed[$infoKey]['tr'] as $activityKey => $activityValue) {
                if($activityKey == 0) continue;
                $td = !empty($userActivityListNotParsed[$infoKey]['tr'][$activityKey]['td']) ? $userActivityListNotParsed[$infoKey]['tr'][$activityKey]['td']: $userActivityListNotParsed[$infoKey]['tr'][$activityKey]['th'];
                echo $infoKey;
                    echo '<pre>'.print_r($userInfoParse, true).'</pre>';
                    echo '<pre>'.print_r($td, true).'</pre>';
                    echo '<pre>'.print_r($userInfoListNotParsed, true).'</pre>';
                    
                if(!empty($td['#text']) && $td['#text'] == 'Nothing found') {
                    $userActivity[$userInfoParse[3]]['messages'] = array(
                        0 => false,
                    );
                } else {

                    $userActivity[$userInfoParse[3]]['messages'][] = array(
                        'screenname'    => $td[1]['#text'],
                        'group'         => $td[2]['#text'],
                        'groupId'       => $td[3]['#text'],
                        'receiveTime'   => $td[4]['#text'],
                        'timeFromShift' => $td[5]['#text'],
                        'country'       => $td[6]['#text'],
                        'year'          => $td[8]['#text'],
                        'status'        => $td[9]['#text'],
                    );

                }
            }
        }
        echo '<pre>'.print_r($userActivity, true).'</pre>';
        return $userActivity;
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
    
    private function b2bAdminLogin()
    {
        $this->b2bCh = curl_init();
        curl_setopt($this->b2bCh, CURLOPT_URL, $this->b2bSite.$this->b2bLoginUrl);
        curl_setopt($this->b2bCh, CURLOPT_USERPWD, $this->b2bHttpLogin . ":" . $this->b2bHttpPass);
        curl_setopt($this->b2bCh, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($this->b2bCh, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($this->b2bCh, CURLOPT_RETURNTRANSFER, 1);
        $out = curl_exec($this->b2bCh);
        $html = new nokogiri($out);
        $token = $html->get("input[name=token]")->toArray();
        $token = $token[0]['value'];
        $postArr = array(
            "guid" => $this->b2bLogin,
            "pwd" => $this->b2bPass,
            "token" => $token,
            "login" => "Login",
        );
        curl_setopt($this->b2bCh, CURLOPT_URL, $this->b2bSite.$this->b2bLoginUrl);
        curl_setopt($this->b2bCh, CURLOPT_USERPWD, $this->b2bHttpLogin . ":" . $this->b2bHttpPass);
        curl_setopt($this->b2bCh, CURLOPT_POST, true);
        //echo "guid=" . $this->b2bLogin . "&pwd=" . $this->b2bPass . "&login=Login&token=" . $token;
        curl_setopt($this->b2bCh, CURLOPT_POSTFIELDS, "guid=" . $this->b2bLogin . "&pwd=" . urlencode($this->b2bPass) . "&login=Login&token=" . $token);
        curl_setopt($this->b2bCh, CURLOPT_VERBOSE, 1);
        curl_setopt($this->b2bCh, CURLOPT_RETURNTRANSFER, 0);
        $out = curl_exec($this->b2bCh);
    }
    
    private function b2bSetDc($config)
    {
        $this->b2bSite      = $config['site'];
        $this->b2bLoginUrl     = $config['loginUrl'];
        $this->b2bFindUrl   = $config['findUrl'];
        $this->b2bLogin     = $config['login'];
        $this->b2bPass      = $config['pass'];
        $this->b2bHttpLogin = $config['httpLogin'];
        $this->b2bHttpPass  = $config['httpPass'];
    }
    
    private function setDc($config)
    {
        $this->dc       = $config['dc'];
        $this->mainSite = $config['site'];
        $this->loginUrl = $config['loginUrl'];
        $this->findUrl  = $config['findUrl'];
        $this->type     = $config['type'];
        $this->login    = $config['login'];
        $this->pass     = $config['pass'];
    }

}
