<?php
require_once(CLASS_DIR . 'RepeatTask.class.php');
if (isset($_REQUEST)) {
    if (isset($_REQUEST['ajax'])) {
        if (isset($_REQUEST['saveTask']) && isset($_REQUEST['config']) && sizeof($_REQUEST['config']) > 0 && isset($_REQUEST['days']) && sizeof($_REQUEST['days']) > 0 && isset($_REQUEST['timeIntervalHour']) && isset($_REQUEST['timeIntervalMinute']) && isset($_REQUEST['reportIntervalHour']) && isset($_REQUEST['reportIntervalMinute']) && isset($_REQUEST['taskId'])) {
            $timeInterval = $_REQUEST['timeIntervalHour'] * 60 + $_REQUEST['timeIntervalMinute'];
            $reportInterval = $_REQUEST['reportIntervalHour'] * 60 + $_REQUEST['reportIntervalMinute'];
            $taskParameters = array(
                'taskId' => $_REQUEST['taskId'],
                'days' => $_REQUEST['days'],
                'config' => $_REQUEST['config'],
                'time' => $timeInterval,
                'report' => $reportInterval,
            );
            $repeatTaskClass = new RepeatTask($DBH);
            var_dump($taskParameters);
            $repeatTaskClass->saveTask($taskParameters);
        }
    }
}
//var_dump($_REQUEST);
