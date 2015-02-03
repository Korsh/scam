<?php
require_once('requirer.php');

$users = $ui->getUsersForUpdate();

echo '<pre>' . print_r($users, true) . '</pre>';

for ($y = 0; $y < sizeof($users); $y++) {
    $ui->syncUserInfo($users[$y], $adminConf[0]);
}
