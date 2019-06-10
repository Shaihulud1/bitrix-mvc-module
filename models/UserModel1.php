<?php
namespace models;

class UserModel extends \AppModel
{
    const HR = 8;
    const REGIONAL_MANAGER = 7;
    protected $userObj;
    protected $arRoles;
    public $iblock;
    const RANG_DETAIL = [self::HR => "Менеджер по персоналу", self::REGIONAL_MANAGER => "Региональный менеджер"];


    public $tableName = "vacmodule_users";
    public $email;
    public $pass;
    public $decodePass;
    public $role;
    public $id;


    public function __construct()
    {
        global $USER;
        $this->userObj = $USER;
        $this->iblock = new \CIBlockElement;
    }

    /**
     * get active users
     * @return array
    */
    public function getActiveUsers()
    {
        $userBy = "id";
        $userOrder = "desc";
        $userFilter = array(
            'ACTIVE' => 'Y',
            'GROUPS_ID' => [self::HR]
        );
        $userParams = array(
            'SELECT' => array(
                "UF_HR_CITY"
            ),
            'FIELDS' => array(
                'ID',
                'NAME',
                'GROUPS_ID',
                'EMAIL',
                'PERSONAL_PHONE',
            ),
        );

        $q = $this->userObj->GetList(
            $userBy,
            $userOrder,
            $userFilter,
            $userParams
        );
        $arUsers = [];
        $arCities = [];
        while($r = $q->fetch())
        {
            if(!empty($r['UF_HR_CITY'])){
                $arCities = !empty($arCities) ? array_merge($arCities, $r['UF_HR_CITY']) : $r['UF_HR_CITY'];
            }
            $arUsers[$r['ID']] = $r;
            //$arUsers[$r['ID']]['GROUPS'] = $this->getUserGroups($r['ID']);
        }
        if(!empty($arCities)){
            $q = $this->iblock->getList([], ["IBLOCK_ID" => self::CITY_BLOCK, "ID" => $arCities], false, false, ["ID", "IBLOCK_ID", "NAME"]);
            while($r = $q -> fetch())
            {
                $arCities['DETAIL_CITY'][$r['ID']]['NAME'] = $r['NAME'];
            }
        }
        foreach($arUsers as $key => $userData){
            if(empty($userData['UF_HR_CITY'])) continue;
            foreach($userData['UF_HR_CITY'] as $cityID){
                if($arCities['DETAIL_CITY'][$cityID]){
                    $arUsers[$key]["DETAIL_CITY"][$cityID] = $arCities['DETAIL_CITY'][$cityID]['NAME'];
                }
            }
        }
        return $arUsers;
    }

    /**
     * check is user login
     * @return boolean
    */
    public function isAuth()
    {
        return $this->userObj->IsAuthorized();
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
        $userRoles = $this->userObj->GetUserGroupArray();
        if(!empty($userRoles)){
            return array_intersect($userRoles, $requireRoles) ? true : false;
        }
        return false;
    }

    /**
     * get user id
     * @return int
    */
    public function getID()
    {
        return $this->userObj->GetID();
    }

    /**
     * @param $id | user id
     * @return array
    */
    public function getUserById($id)
    {
        $userBy = "id";
        $userOrder = "desc";
        $userFilter = array(
            'ID' => $id,
            'ACTIVE' => 'Y'
        );
        $userParams = array(
            'SELECT' => array(
                "UF_HR_CITY"
            ),
            'FIELDS' => array(
                'ID',
                'NAME',
            ),
        );
        $q = $this->userObj->GetList(
            $userBy,
            $userOrder,
            $userFilter,
            $userParams
        );
        if($r = $q->fetch())
        {
            return $r;
        }
        return false;
    }

    /**
     * @return array
    */
    public function getUserCity()
    {
        $userBy = "id";
        $userOrder = "desc";
        $userFilter = array(
            'ID' => $this->getID(),
            'ACTIVE' => 'Y'
        );
        $userParams = array(
            'SELECT' => array(
                "UF_HR_CITY"
            ),
            'FIELDS' => array(
                'ID',
                'NAME',
            ),
        );

        $q = $this->userObj->GetList(
            $userBy,
            $userOrder,
            $userFilter,
            $userParams
        );
        if($r = $q->fetch()){
            return $r['UF_HR_CITY'];
        }
    }

    /**
     * @param array $userIds
     * @return array
    */
    public function getUserListByIds($userIds)
    {
        $userBy = "id";
        $userOrder = "desc";
        $userFilter = array(
            'ID' => implode(" | ", $userIds),
            'ACTIVE' => 'Y'
        );
        $userParams = array(
            'SELECT' => array(
            ),
            'FIELDS' => array(
                'ID',
                'NAME',
                'LAST_NAME',
                'SECOND_NAME',
                'EMAIL',
            ),
        );

        $q = $this->userObj->GetList(
            $userBy,
            $userOrder,
            $userFilter,
            $userParams
        );
        $arResult = [];
        while($r = $q -> fetch()){
            $arResult[$r["ID"]] = [
                "NAME"        => $r["NAME"],
                "LAST_NAME"   => $r["LAST_NAME"],
                "SECOND_NAME" => $r["SECOND_NAME"],
                "EMAIL"       => $r["EMAIL"],
            ];
        }
        return $arResult;
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
        $dbData = $appDB->makeSqlQuery("SELECT * FROM $this->tableName WHERE email = 'mail@mail.ru' AND passwrd = '$this->decodePass' LIMIT 1", true);
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
        return $_SESSION['VACMODULE']['ID'] ? true : false;
    }

    static function isRoleAccessForUser($needleRoles)
    {
        session_start();
        return in_array($_SESSION['VACMODULE']['ROLE'], $needleRoles) ? true : false;
    }

    /**
     * login user via sessions
     * @param int $id
     * @param string $role
    */
    public static function loginUser($id, $role)
    {
        session_start();
        $_SESSION['VACMODULE']['ID'] = $id;
        $_SESSION['VACMODULE']['ROLE'] = $role;
    }

    /**
     * encode password
     * @param string $rawPassword
    */
    protected function getEnCodePassword($rawPassword)
    {
        return md5(md5(sha1($rawPassword)));
    }

}
