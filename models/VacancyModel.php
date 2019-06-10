<?php
namespace models;

use models\UserModel;

class VacancyModel extends \AppModel
{
    /*fields*/
    public $id;
    public $name;
    public $hr;
    public $text;
    public $city;
    public $template;
    /**/
    public $iblock;

    public function __construct()
    {
        $this->iblock = new \CIBlockElement;
    }

    /**
     * deactivate/activate vacancy's iblock element
     * @param boolean
    */
    public function archive($toArchive = true)
    {
        if(!$this->id) return;
        $active = $toArchive ? "Y" : "N";
        $this->iblock->Update($this->id, ["ACTIVE" => $active]);
    }

    /**
     * check is valid form fields
     * @return boolean
    */
    public function isValid($update = false)
    {
        if(!$this->hr || !$this->text || !$this->city || !$this->template || !$this->name || ($update && !$this->id)){
            $this->addError('EMPTY_FIELD', 'Пустые поля');
            return false;
        }
        $userModel = new UserModel;
        $uberMench = $userModel->isRolesAccess([UserModel::REGIONAL_MANAGER]);
        $userData = $userModel->getUserById($this->hr);
        if(!$uberMench){
            $cities = $userModel->getUserCity();
            if(empty($cities) || !in_array($this->city, $cities)){
                $this->addError('WRONG_CITY', 'Пользователь не может создать вакансию не своего города');
                return false;
            }
            if(!in_array($this->city, $userData['city'])){
                $this->addError('WRONG_USER_CITY', 'Пользователь не привязан к городу добавления');
                return false;
            }
        }
        $c = false;
        $c = $this->iblock->getList([], ['IBLOCK_ID' => self::CITY_BLOCK, 'ID' => $this->city], [], false, []);
        if($c < 1){
            $this->addError('UNKNOWN_CITY', 'Указанный город не найден');
            return false;
        }
        if(!$userData || $userData['role'] != $userModel::HR){
            $this->addError('UNKNOWN_USER', 'Указанный пользователь не найден');
            return false;
        }
        $c = false;
        if($update){
            $c = $this->iblock->getList([], ["ACTIVE" => "Y", "IBLOCK_ID" => self::VACANCY_BLOCK, "ID" => $this->id], [], false, []);
            if($c < 1){
                $this->addError('UNKNOWN_VACANCY', 'Указанная вакансия не найдена');
                return false;
            }
        }
        return true;
    }

    /**
     * add new vacancy to iblock
     * @return boolean
    */
    public function save()
    {
        $r = $this->iblock->add([
            "ACTIVE"          => "Y",
            "IBLOCK_ID"       => self::VACANCY_BLOCK,
            "NAME"            => $this->name,
            "CODE"            => \Cutil::translit($this->name, "ru"),
            "DETAIL_TEXT"     => $this->text,
            "PROPERTY_VALUES" => [
                "HR"       => $this->hr,
                "REGION"   => $this->city,
                "TEMPLATE" => $this->template,
            ],
        ]);
        if(!$r){
            $this->addError('BITRIX_ERROR', $this->iblock->LAST_ERROR);
            return false;
        }
        return true;
    }

    /**
     * update vacancy
     * @param boolean
    */
    public function update()
    {
        $r = $this->iblock->update($this->id, [
            "ACTIVE"      => "Y",
            "IBLOCK_ID"   => self::VACANCY_BLOCK,
            "NAME"        => $this->name,
            "CODE"        => \Cutil::translit($this->name, "ru"),
            "DETAIL_TEXT" => $this->text,
        ]);
        if(!$r){
            $this->addError('BITRIX_ERROR', $this->iblock->LAST_ERROR);
            return false;
        }
        $this->iblock->SetPropertyValuesEx($this->id, self::VACANCY_BLOCK, [
            "HR"       => $this->hr,
            "REGION"   => $this->city,
            "TEMPLATE" => $this->template,
        ]);
        return true;
    }

    /**
     * get array of the vacancies with user's ids as keys
     * @param int $city | city id
     * @param boolean $archived | get deactive or active vacancies
     * @param int $vacancyID | get only one vancancy with param id
     * @param int $userID | id of the user
     * @param array $choosenCities | array of the cities ids
     * @return array
     */
    public function getVacanciesWithUser($city = false, $archived = false, $vacancyID = false, $userID = false, $choosenCities = false)
    {
        $arFilter = ["IBLOCK_ID" => self::VACANCY_BLOCK, '!PROPERTY_HR' => false];
        $arFilter['ACTIVE'] = $archived ? "N" : "Y";
        if($city){
            $arFilter[] = ["LOGIC" => "OR", ["PROPERTY_REGION" => $city], ["PROPERTY_HR" => $userID]];
        }
        if($vacancyID){
            $arFilter['ID'] = $vacancyID;
        }
        $q = $this->iblock->getList(["NAME" => "ASC"], $arFilter, false, false, ["NAME", "ID", "IBLOCK_ID", "PROPERTY_HR", "PROPERTY_REGION", "DETAIL_TEXT", "PROPERTY_TEMPLATE"]);
        $arResult = [];
        $cities = [];
        while($r = $q->fetch()){
            if(!empty($choosenCities) && !array_intersect($r['PROPERTY_REGION_VALUE'], $choosenCities)){
                continue;
            }
            if(!empty($r['PROPERTY_REGION_VALUE'])){
                $cities = !empty($cities) ? array_merge($cities, $r['PROPERTY_REGION_VALUE']) : $r['PROPERTY_REGION_VALUE'];
            }
            $arResult[$r['PROPERTY_HR_VALUE']]['VACANCIES'][] = [
                'NAME'        => $r['NAME'],
                'ID'          => $r['ID'],
                'CITIES'      => $r['PROPERTY_REGION_VALUE'],
                'DETAIL_TEXT' => $r['DETAIL_TEXT'],
                'HR_ID'       => $r['PROPERTY_HR_VALUE'],
                'TEMPLATE_ID' => $r['PROPERTY_TEMPLATE_VALUE'],
            ];
        }
        if(!empty($cities)){
            $q  = $this->iblock->getList([], ["IBLOCK_ID" => self::CITY_BLOCK, "ID" => $cities], false, false, ["ID", "IBLOCK_ID", "NAME"]);
            while($r = $q -> fetch()){
                $cities['CITIES_DETAIL'][$r['ID']] = $r;
            }
            foreach($arResult as $key => $hrData){
                if(!empty($hrData['VACANCIES'])){
                    foreach($hrData['VACANCIES'] as $keyVac => $vacData){
                        if(!empty($vacData['CITIES'])){
                            foreach($vacData['CITIES'] as $cityKey => $cityData){
                                if(!empty($cities['CITIES_DETAIL'][$cityData])){
                                    $arResult[$key]['VACANCIES'][$keyVac]['CITIES_DETAIL'][] = $cities['CITIES_DETAIL'][$cityData]['NAME'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arResult;
    }
}
