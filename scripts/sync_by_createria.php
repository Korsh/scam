<?php

if (isset($param[2])) {
    echo "Synchronize by '" . $param[2] . "'<br>'";
    $config = $admin_conf[0];
    
    echo 'Synced on ' . $admin_conf[$i]['dc'] . ': ' . $ui->syncUserInfo(trim($param[2])) . '<br>';
    echo "\n";
} else {
    echo 'need parameter: `param`';
}
exit;
