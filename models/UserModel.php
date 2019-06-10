<?php
namespace models;

class UserModel extends \AppModel
{
    const HR = 8;
    const REGIONAL_MANAGER = 7;
    const RANG_DETAIL = [self::HR => "Менеджер по персоналу", self::REGIONAL_MANAGER => "Региональный менеджер"];
    const PASS_LENGHT = 6;
    protected $userObj;
    protected $arRoles;
    public $iblock;


    public $tableName = "vacmodule_users";
    public $city;
    public $email;
    public $pass;
    public $decodePass;
    public $role;
    public $id;
    public $fullname;
    public $passRepeat;


    public function __construct()
    {
        global $USER;
        $this->userObj = $USER;
        $this->iblock = new \CIBlockElement;
    }

    /**
     * get choosen cities from session
     * @return array
    */
    public function getChoosenCities()
    {
        return $_SESSION[self::MODULE_SESSION_NAME]["CHOOSEN_CITIES"];
    }

    /**
     * set choosen cities to session
     * @param array $arCities
    */
    public function setChoosenCities($arCities)
    {
        $_SESSION[self::MODULE_SESSION_NAME]["CHOOSEN_CITIES"] = $arCities;
    }


    /**
     * check is user login
     * @return boolean
    */
    public function isAuth()
    {
        session_start();
        return $_SESSION[self::MODULE_SESSION_NAME]["ID"] ? true : false;
    }

    public function getUserGroups($id)
    {
        return $this->userObj->GetUserGroup($id);
    }

    /**
     * check is user got access
     * @param array $requireRoles
     * @return boolean
    */
    public function isRolesAccess($requireRoles)
    {
        if(!$this->isAuth()){
            return false;
        }
        return in_array($this->getUserRole(), $requireRoles);
    }

    public function getUserRole($id = false)
    {
        $userID = $id ? $id : $this->getID();
        $db = \AppDB::getDBConn();
        $res = $db->makeSqlQuery("SELECT * FROM $this->tableName WHERE id = $userID", true);
        return $res[0]['role'];
    }

    /**
     * get user id
     * @return int
    */
    public function getID()
    {
        session_start();
        return $_SESSION[self::MODULE_SESSION_NAME]["ID"];
    }

    /**
     * @param $id | user id
     * @return array
    */
    public function getUserById($id)
    {
        $db = \AppDB::getDBConn();
        $userData = $db->makeSqlQuery("SELECT * FROM $this->tableName WHERE id = $id", true);
        if(!empty($userData[0]['city'])){
            $userData[0]['city'] = strpos($userData[0]['city'], ', ') ? explode(',', $userData[0]['city']) : [$userData[0]['city']];
            sort($userData[0]['city']);
        }
        return !empty($userData[0]) ? $userData[0] : false;
    }

    /**
     * @param array $userIds
     * @param int $roleOnly
     * @param string $searchHR
     * @return array
    */
    public function getUserListByIds($userIds, $roleOnly = false, $searchHR = false)
    {
        $db = \AppDB::getDBConn();
        $userIds = implode(', ', $userIds);
        $sql = "SELECT * FROM $this->tableName WHERE id in ($userIds)";
        if($roleOnly){
            $sql .= " AND role = $roleOnly";
        }
        if($searchHR){
            $sql .= " AND fullname LIKE '$searchHR%'";
        }
        $arUsers = $db->makeSqlQuery($sql, true);
        $arResult = [];
        if(!empty($arUsers)){
            foreach($arUsers as $user)
            {
                //print_R($user);
                // if(!empty($user['city'])){
                //     $city = strpos(',', $user['city']) ? implode(',' $user["city"]) : [$user["city"]];
                // }
                $arResult[$user["id"]] = [
                    "NAME"         => $user["fullname"],
                    "PHONE"        => $user["phone"],
                    "EMAIL"        => $user["email"],
                    "ROLE"         => $user["role"],
                    //"CITY"         => $city,
                ];
            }
        }
        return $arResult;
    }

    /**
    * get active users
    * @param array $arCity | city's ids
    * @return array
    */
    public function getActiveUsers($city = [])
    {
        $hrRole = self::HR;
        $sql = "SELECT * FROM $this->tableName WHERE role = $hrRole";
        if(!empty($city)){
            $sql .= " AND (";
            foreach($city as $key => $value){
                if($key > 0){
                    $sql .= " OR";
                }
                $sql .= " city LIKE '%$value' OR city LIKE '%$value,%'";
            }
            $sql .= ")";
        }
        $db = \AppDB::getDBConn();
        $userData = $db->makeSqlQuery($sql, true);
        $arUsers = [];
        $citiesIds = [];
        if(!empty($userData)){
            foreach($userData as $user){
                $arCities = strpos($user['city'], ',') ? explode(', ', $user['city']) : [$user['city']];
                $arUsers[] = [
                    "ID"    => $user['id'],
                    "NAME"  => $user['fullname'],
                    "PHONE" => $user['phone'],
                    "EMAIL" => $user['email'],
                    "ROLE"  => $user['role'],
                    "CITY"  => $arCities,
                ];
                if(!empty($arCities)){
                    $citiesIds =  !empty($citiesIds) ? array_merge($citiesIds, $arCities) : $arCities;
                }
            }
        }
        if(!empty($citiesIds)){
            $q = $this->iblock->getList([], ['ID' => $citiesIds], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
            while($r = $q -> fetch()){
                $citiesIds['DETAIL_CITY'][(int)$r['ID']] = $r['NAME'];
            }
            foreach($arUsers as $key => $user){
                if(!empty($user['CITY'])){
                    foreach($user['CITY'] as $id => $value){
                        if($citiesIds['DETAIL_CITY'][$value]){
                            $arUsers[$key]['CITY_DETAIL'][] = [
                                'ID' => $value,
                                'NAME' => $citiesIds['DETAIL_CITY'][$value],
                            ];
                        }
                    }
                }
            }
        }
        return $arUsers;
    }

    /**
     * get active cities by id
     * @param array $cityIds
     * @return array
    */
    public function getCitiesById($arIds)
    {
        $q = $this->iblock->getList([], ['ID' => $arIds], false, false, ["ID", "IBLOCK_ID", "NAME"]);
        $arResult = [];
        while($r = $q -> fetch()){
            $arResult[$r['ID']] = $r['NAME'];
        }
        return $arResult;
    }

    /**
    * get active cities
    * @param array $arCity | city's ids
    * @return array
    */
    public function getActiveCities($city = [])
    {
        $filter = ['IBLOCK_ID' => self::CITY_BLOCK, "ACTIVE" => "Y"];
        if(!empty($city)){
            $filter['ID'] = $city;
        }
        $q = $this->iblock->getList(["NAME"=>"ASC"], $filter, false, false, ["ID", "IBLOCK_ID", "NAME"]);
        $arCities = [];
        while($r = $q -> fetch())
        {
            $arCities[] = $r;
        }
        return $arCities;
    }

    /**
     * @return array
    */
    public function getUserCity()
    {
        $userID = $this->getID();
        if(!$userID) return false;
        $db = \AppDB::getDBConn();
        $cityArr = $db->makeSqlQuery("SELECT * FROM $this->tableName WHERE ID = $userID", true);
        return strpos($cityArr[0]['city'], ',') ? explode(',', $cityArr[0]['city']) : [$cityArr[0]['city']];
    }

    /**
     * is valid form data
     * @param boolean $update | is it update form
     * @return boolean
    */
    public function isValid($update = false)
    {
        if(!$this->fullname || !$this->city || !$this->email || !$this->phone || ($update && !$this->id) || (!$update && !$this->pass) || (!$update && !$this->passRepeat)){
            $this->addError('EMPTY_FIELD', 'Пустые поля');
            return false;
        }
        $userData = $this->getUserById($this->getID());
        if($userData['role'] != self::REGIONAL_MANAGER && $userData['city'] != $this->city){
            $this->addError('CITY_CANT_CHANGE', 'Только региональный менеджер может поменять город пользователя');
            return false;
        }
        $tempCity = $this->city;
        $checkCities = $this->getActiveCities($tempCity);
        $checkCities = array_column($checkCities, 'ID');
        foreach($tempCity as $key => $id){
            if(in_array($id, $checkCities)){
                unset($tempCity[$key]);
            }
        }
        if(count($tempCity) > 0){
            $this->addError('UNKNOWN_CITY', 'Город к которому привязывается менеджер не существует в базе');
            return false;
        }
        if($update){
            $userData = $this->getUserById($this->id);
        }
        if(!$update || ($update && $userData['email'] != $this->email))
        {
            $db = \AppDB::getDBConn();
            $checkUser = $db->makeSqlQuery("SELECT * FROM $this->tableName WHERE email = '$this->email'", true);
            if(!empty($checkUser[0])){
                $this->addError('ALREADY_SIGN_UP', "Пользователь с email $this->email уже существует");
                return false;
            }
        }
        if(!$update){
            if(strlen($this->pass) < self::PASS_LENGHT){
                $this->addError('PASS_LENGHT_ERROR', 'Минимальная длина пароля - '.self::PASS_LENGHT.' символов');
                return false;
            }
            if($this->pass != $this->passRepeat){
                $this->addError('PASS_REPEAT_ERROR', 'Пароли не совпадают');
                return false;
            }
        }
        return true;
    }

    /**
     * create new user
     * @return boolean
    */
    public function save()
    {
        $this->decodePass = $this->getEnCodePassword($this->pass);
        $this->city = implode(', ', $this->city);
        $this->role = self::HR;
        $db = \AppDB::getDBConn();
        $db->makeSqlQuery("INSERT INTO $this->tableName (email, phone, role, fullname, city, passwrd) VALUES ('$this->email', '$this->phone', $this->role, '$this->fullname', '$this->city', '$this->decodePass')");
        return true;
    }

    /**
     * update user
     * @return boolean
    */
    public function update()
    {
        $this->city = implode(', ', $this->city);
        $db = \AppDB::getDBConn();
        $db->makeSqlQuery("UPDATE $this->tableName SET fullname = '$this->fullname', city = '$this->city', email = '$this->email', phone = '$this->phone' WHERE id = $this->id");
        return true;
    }

    /**
     * check is login form is valid
     * @return boolean
    */
    public function isValidEnterForm()
    {
        if(empty($this->email) || empty($this->pass)){
            $this->addError('EMPTY_FIELD', 'Пустые поля');
            return false;
        }
        $this->decodePass = $this->getEnCodePassword($this->pass);
        $appDB = \AppDB::getDBConn();
        $dbData = $appDB->makeSqlQuery("SELECT * FROM $this->tableName WHERE email = '$this->email' AND passwrd = '$this->decodePass' LIMIT 1", true);
        if(!$dbData[0]['id']){
            $this->addError('WRONG_LOGIN', 'Неверный логин или пароль');
            return false;
        }
        $this->id = $dbData[0]['id'];
        $this->role = $dbData[0]['role'];
        return true;
    }

    /**
    * check user authorization
    * @return boolean
    */
    static function isUserSignIn()
    {
        session_start();
        return $_SESSION[self::MODULE_SESSION_NAME]['ID'] ? true : false;
    }

    static function isRoleAccessForUser($needleRoles)
    {
        session_start();
        return in_array($_SESSION[self::MODULE_SESSION_NAME]['ROLE'], $needleRoles) ? true : false;
    }

    /**
    * login user via sessions
    * @param int $id
    * @param string $role
    */
    public static function loginUser($id, $role)
    {
        session_start();
        $_SESSION[self::MODULE_SESSION_NAME]['ID'] = $id;
    }

    /**
    * encode password
    * @param string $rawPassword
    */
    public function getEnCodePassword($rawPassword)
    {
        return md5(md5(sha1($rawPassword)));
    }

    /**
    * destroy module session's
    */
    public function destroySessions()
    {
        unset($_SESSION[self::MODULE_SESSION_NAME]);
    }
}
