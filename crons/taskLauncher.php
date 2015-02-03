<?php
require_once('requirer.php');
require_once(CLASS_DIR . 'RepeatTask.class.php');
$repeatTaskClass = new RepeatTask($DBH);
$tasksList = $repeatTaskClass->getTasksToLaunch();
for($i=0; $i<count($tasksList); $i++) {
    $taskParameters = $repeatTaskClass->getTaskParameters($tasksList[$i]);
    for($y=0; $y<count($taskParameters); $y++) {
        while($taskParameters[$y]['count'] > 0) {
            //register
            echo 'register';
            //save
            echo 'save';
            $taskParameters[$y]['count']--;
        }
    }
}
//$ui->launchTask();
