<?php
namespace models;

class LoggerModel extends \AppModel
{
    public $tableName = "vacmodule_logger";

    /**
     * write users actions to logging table
     * @param int $userID | user's id
     * @param string $actionName | user's action
    */
    public function logAction($userID, $actionName)
    {
        $db = \AppDB::getDBConn();
        $timeNow = time();
        $db->makeSqlQuery("INSERT INTO $this->tableName (user, action, date_unix) VALUES ($userID, '$actionName', $timeNow)");
    }
}
