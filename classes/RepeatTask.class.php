<?php

class RepeatTask
{

    private $db;
    
    public function RepeatTask($DBH)
    {
        $this->db = $DBH;
    }
    
    function saveTask($taskParameters)
    {
        try {
            $valuesArray = array();
            var_dump($taskParameters['config']);
            for ($i = 0; $i < count($taskParameters['config']); $i++) {
                $valuesString .= "(:taskId$i, :country$i, (select site_id from sites_config where site_domain = :site$i), :count$i, :device$i, :gender$i, :referer$i, :email$i, :age$i), ";
                foreach($taskParameters['config'][$i] as $key => $value) {
                        $valueArray[$key.$i] = $value;
                }
                $valueArray['taskId'.$i] = $taskParameters['taskId'];
            }
            $valuesString = substr($valuesString, 0, -2);
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
                    `age`) 
                VALUES 
                    $valuesString
            ;");
            echo "
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
                    `age`) 
                VALUES 
                    $valuesString
            ;";
            $this->bindArray($saveTaskConfigQuery, $valueArray);
            $saveTaskConfigQuery->execute();
            try {
                for($i=0; $i<count($taskParameters['days']); $i++) {
                    $activeDays .= $taskParameters['days'][$i].',';
                }
                $activeDays = substr($activeDays, 0, -1);
                $saveTaskQuery = $this->db->prepare("
                    INSERT INTO 
                      `tasks`(
                        `id`,
                        `last_launch`,
                        `period`,
                        `report_period`,
                        `active_days`,
                        `is_reported`) 
                    VALUES (
                        :taskId,
                        '0000-00-00 00:00:00',
                        :period,
                        :reportPeriod,
                        (:activeDays),
                        0
                    )
                ;");
                echo $activeDays;
                $saveTaskQuery->bindValue(':taskId', $taskParameters['taskId']);
                $saveTaskQuery->bindValue(':period', $taskParameters['time']);
                $saveTaskQuery->bindValue(':reportPeriod', $taskParameters['report']);
                $saveTaskQuery->bindValue(':activeDays', $activeDays);
                $saveTaskQuery->execute();
                return true;
            } catch (PDOException $e) {
                echo $e->getMessage();
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    function bindArray($stmt2, $array)
    {
        foreach ($array as $key => $value) {
            $stmt2->bindValue(':' . $key, $value);
        }
    }
    
    public function getTaskParameters($taskId)
    {
        /*
            task_id
            country
            site_id
            count
            device
            gender
            referer
            email
            age
        */
        try {
            $getTaskConfigQuery = $this->db->prepare("
                SELECT
                    `task_config`.`task_id`,
                    `task_config`.`country`,
                    `sites_config`.`site_domain` as site,
                    `task_config`.`count`,
                    `task_config`.`device`,
                    `task_config`.`gender`,
                    `task_config`.`referer`,
                    `task_config`.`email`,
                    `task_config`.`age`
                FROM
                    `task_config`
                INNER JOIN
                    `sites_config`
                ON
                    `task_config`.`site_id` = `sites_config`.`site_id`
                WHERE
                    `task_config`.`task_id` = :taskId
            ;");
            $getTaskConfigQuery->bindValue(':taskId', $taskId);
            $getTaskConfigQuery->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        if ($getTaskConfigQuery->rowCount() > 0) {
            while ($row = $getTaskConfigQuery->fetch()) {
                $parameters['taskId'] = $row['task_id'];
                $parameters['country'] = $row['country'];
                $parameters['site'] = $row['site'];
                $parameters['count'] = $row['count'];
                $parameters['device'] = $row['device'];
                $parameters['gender'] = $row['gender'];
                $parameters['referer'] = $row['referer'];
                $parameters['email'] = $row['email'];
                $parameters['age'] = $row['age'];
                $taskConfig[] = $parameters;
            }
            
            return $taskConfig;
        } else {
            return false;
        }
    }
    
    public function getTasksToLaunch()
    {
        try {
            $getTaskToLaunchQuery = $this->db->query("
                SELECT
                    `id`
                FROM
                    `tasks`
                WHERE
                    period < UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_launch)
                AND
                    FIND_IN_SET(date_format(curdate(), '%w'), active_days);
            ");
            $getTaskToLaunchQuery->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        if ($getTaskToLaunchQuery->rowCount() > 0) {
            while ($row = $getTaskToLaunchQuery->fetch()) {
                $tasksList[] = $row['id'];
            }
            
            return $tasksList;
        } else {
            return false;
        }
    
    }
}
