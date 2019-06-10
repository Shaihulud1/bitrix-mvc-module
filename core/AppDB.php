<?php
class AppDB
{
    static $db;
    static $init = false;

    protected function __construct(){}

    /**
     * singletone obj return
     * @return object
    */
    static function getDBConn()
    {
        if(!self::$init){
            global $DB;
            self::$db = $DB;
            self::$init = new self();
        }
        return self::$init;
    }

    /**
    * do a db query
    * @param string $sql
    * @param boolean $getAssoc
    */
    public function makeSqlQuery($sql, $getAssoc = false)
    {
        $q = self::$db->query($sql);
        if($getAssoc){
            $result = [];
            while($r = $q->fetch()){
                $result[] = $r;
            }
        }
        return $result;
    }
}
