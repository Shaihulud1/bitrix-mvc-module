<?php
namespace models;

class TemplateModel extends \AppModel
{
    public $tableName = "vacmodule_vacancies_templates";
    public $name;
    public $template_text;
    public $id;

    public function __construct()
    {
        $this->iblock = new \CIBlockElement;
    }

    /**
     * get all templates from template table
     * @return array
    */
    public function getAllTemplates()
    {
        $arResult = [];
        $q = $this->iblock->getList([], ["IBLOCK_ID" => self::TEMPLATES_BLOCK, "ACTIVE" => "Y"], false, false, ['ID', 'IBLOCK_ID', 'DETAIL_TEXT', 'NAME']);
        while($r = $q -> fetch()){
            $arResult[] = [
                'id'            => $r['ID'],
                'name'          => $r['NAME'],
                'template_text' => $r['DETAIL_TEXT'],
            ];
        }
        return $arResult;
    }

    /**
     * get template data by ID
     * @param int $id | template's id
     * @return array
    */
    public function getTemplateById($id)
    {
        if(!$id){
            return false;
        }
        $arResult = [];
        $q = $this->iblock->getList([], [ "ACTIVE" => "Y", "IBLOCK_ID" => self::TEMPLATES_BLOCK, "ID" => $id], false, false, ['ID', 'IBLOCK_ID', 'DETAIL_TEXT', 'NAME']);
        if ($r = $q->fetch()) {
            $arResult[] = [
                'id'            => $r['ID'],
                'name'          => $r['NAME'],
                'template_text' => $r['DETAIL_TEXT'],
            ];
        }
        return $arResult;
    }

    /**
     * is valid form
     * @param boolean $update | is a update form
     * @return boolean
    */
    public function isValid($update = false)
    {
        if(!$this->name || !$this->template_text || ($update && !$this->id)){
            $this->addError('EMPTY_FIELD', 'Пустые поля');
            return false;
        }
        if($update){
            $updateID = $this->getTemplateById($this->id);
            if(!$updateID[0]['id']){
                $this->addError('UNKNOWN_ID', 'Записи с таким шаблоном нет в базе данных');
                return false;
            }
        }
        return true;
    }

    /**
     * save new template to template table
    */
    public function save()
    {
        $r = $this->iblock->add([
            "ACTIVE"          => "Y",
            "IBLOCK_ID"       => self::TEMPLATES_BLOCK,
            "NAME"            => $this->name,
            "CODE"            => \Cutil::translit($this->name, "ru"),
            "DETAIL_TEXT"     => $this->template_text,
        ]);
        if (!$r) {
            $this->addError('BITRIX_ERROR', $this->iblock->LAST_ERROR);
            return false;
        }
        return true;
    }

    /**
     * update template
    */
    public function update()
    {
        $r = $this->iblock->update($this->id, [
            "ACTIVE"          => "Y",
            "IBLOCK_ID"       => self::TEMPLATES_BLOCK,
            "NAME"            => $this->name,
            "CODE"            => \Cutil::translit($this->name, "ru"),
            "DETAIL_TEXT"     => $this->template_text,
        ]);
        if (!$r) {
            $this->addError('BITRIX_ERROR', $this->iblock->LAST_ERROR);
            return false;
        }
        return true;
    }


    /**
     * delete template item
     */
    public function delete($id)
    {
        if(!$id){return;}
        $this->iblock->delete($id);
    }

}
