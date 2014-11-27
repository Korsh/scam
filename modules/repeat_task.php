<?php

if (isset($_REQUEST)) {
    if (isset($_REQUEST['ajax'])) {
        if (isset($_REQUEST['save_task']) && isset($_REQUEST['config']) && sizeof($_REQUEST['config']) > 0 && isset($_REQUEST['days']) && sizeof($_REQUEST['days']) > 0 && isset($_REQUEST['time_interval_hour']) && isset($_REQUEST['time_interval_minute']) && isset($_REQUEST['task_id'])) {
            $time_interval = $_REQUEST['time_interval_hour'] * 60 + $_REQUEST['time_interval_minute'];
            $ui->saveTask($_REQUEST['days'], $_REQUEST['config'], $time_interval);
        }
    }
}
var_dump($_REQUEST);
