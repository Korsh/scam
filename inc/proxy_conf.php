<?php
    $locale_conf = array(
        'en' => array(
            0 => array(
                'proxy' => 'GBR',
                'enable' => true,
            ),
            1 => array(
                'proxy' => 'USA',
                'enable' => true,
            ),
            2 => array(
                'proxy' => 'CAN',
                'enable' => true,
            ),
            3 => array(
                'proxy' => 'USA',
                'enable' => true,
            ),
            4 => array(
                'proxy' => 'AUS',
                'enable' => true,
            ),
            5 => array(
                'proxy' => 'NZL',
                'enable' => true,
            ),
            6 => array(
                'proxy' => 'ZAF',
                'enable' => false,
            ),
            7 => array(
                'proxy' => 'IND',
                'enable' => false,
            ),
            8 => array(
                'proxy' => 'NZL',
                'enable' => true,
            ),
            9 => array(
                'proxy' => 'GBR2',
                'enable' => true,
            ),
            10 => array(
                'proxy' => 'CAN2',
                'enable' => true,
            ),
            11 => array(
                'proxy' => 'IRL',
                'enable' => true,
            ),
        ),
        'es' => array(
            0 => array(
                'proxy' => 'ESP',
                'enable' => true,
            ),
            1 => array(
                'proxy' => 'ARG',
                'enable' => false,
            ),
        ),
        'fr' => array(
            0 => array(
                'proxy' => 'FRA',
                'enable' => true,
            ),
        ),
        'it' => array(
            0 => array(
                'proxy' => 'ITA',
                'enable' => true,
            ),
        ),
        'de' => array(
            0 => array(
                'proxy' => 'DEU',
                'enable' => true,
            ),
        ),
        'no' => array(
            0 => array(
                'proxy' => 'NOR',
                'enable' => true,
            ),
        ),
        'dk' => array(
            0 => array(
                'proxy' => 'DNK',
                'enable' => true,
            ),
        ),
        'se' => array(
            0 => array(
                'proxy' => 'SWE',
                'enable' => true,
            ),
        ),
        'ot' => array(
            0 => array(
                'proxy' => 'TUR',
                'enable' => false,
            ),
            1 => array(
                'proxy' => 'AUT',
                'enable' => false,
            ),
            2 => array(
                'proxy' => 'BEL',
                'enable' => false,
            ),
            3 => array(
                'proxy' => 'CZE',
                'enable' => false,
            ),
            4 => array(
                'proxy' => 'NLD',
                'enable' => false,
            ),
            5 => array(
                'proxy' => 'PRT',
                'enable' => false,
            ),
            6 => array(
                'proxy' => 'CHE',
                'enable' => false,
            ),
            7 => array(
                'proxy' => 'CHN',
                'enable' => false,
            ),
            8 => array(
                'proxy' => 'IDN',
                'enable' => false,
            ),
            9 => array(
                'proxy' => 'JPN',
                'enable' => false,
            ),
            10 => array(
                'proxy' => 'MYS',
                'enable' => false,
            ),
            11 => array(
                'proxy' => 'PHL',
                'enable' => false,
            ),
            12 => array(
                'proxy' => 'MEX',
                'enable' => false,
            ),
            13 => array(
                'proxy' => 'BRA',
                'enable' => false,
            ),

        ),

    );
    $proxy = $ui->getProxyConfig();

    foreach($proxy as $key => $curr_proxy)
    {    
        if(!$proxy[$key]['enable'])
        {            
            unset($proxy[$key]);
        }
    }

    ksort($proxy);

