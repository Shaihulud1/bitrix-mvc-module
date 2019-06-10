<?php
abstract class AppModel extends App{
    public $tableName;

    /**
     * return mysql's table name of the model
     * @return string
    */
    public function getTableName(){
        return $this->tableName;
    }

    /**
     * delete entity from SQL
     * @param int $id | entity id
    */
    public function delete($id)
    {
        if(!$id) return;
        $db = \AppDB::getDBConn();
        $db->makeSqlQuery("DELETE FROM $this->tableName WHERE id = $id");
    }
}
