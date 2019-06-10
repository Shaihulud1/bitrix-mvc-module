<?php
namespace controllers;

use models\UserModel;
use models\VacancyModel;
use models\TemplateModel;
use models\LoggerModel;

class AjaxController extends \AppController{

    /**
     * pages access
    */
    static $access = [
        'vacancycreate'  => [UserModel::REGIONAL_MANAGER, UserModel::HR],
        'gettemplate'    => [UserModel::REGIONAL_MANAGER, UserModel::HR],
        'usercreate'     => [UserModel::REGIONAL_MANAGER],
        'templatecreate' => [UserModel::REGIONAL_MANAGER],
        'choosecity'     => [UserModel::REGIONAL_MANAGER, UserModel::HR],
    ];

    /**
     * choose city modal
    */
    public function choosecity()
    {
        if($this->request->isPost()){

            $userModel = new UserModel;
            if(isset($_POST['serializeData'])){
                $arFormData = [];
                $arFormData = $this->request->getPost("serializeData");
                parse_str($arFormData, $arFormData);
                $arCities = array_map(function($n){return $n;}, $arFormData['CITY']);
                $userModel->setChoosenCities($arCities);
                $this->renderSuccess();
            }
            $arSelect = [
                'cities'        => $userModel->getActiveCities(),
                'choosenCities' => $userModel->getChoosenCities(),
            ];
            $this->ajaxRender('ajax-choose-city-form', ['arSelect' => $arSelect]);
        }
    }

    /**
     * ajax request return template text
    */
    public function gettemplate()
    {
        if($this->request->isPost() && $elemID = (int)$this->request->getPost("elemID"))
        {
            $templateModel = new TemplateModel;
            $templateData = $templateModel->getTemplateById($elemID);
            echo $templateData[0]['template_text'];
            die();
        }
    }

    /**
     * ajax request | create/update template
    */
    public function templatecreate()
    {
        $templateModel = new TemplateModel;
        $errors = false;
        $templateModel->id = (int)$this->request->getPost("elemID");
        if($this->request->isPost() && $this->request->getPost("serializeData")){
            $arFormData = [];
            parse_str($this->request->getPost("serializeData"), $arFormData);
            $actionType = $arFormData["actionType"];
            $templateModel->name = htmlspecialchars(trim($arFormData['NAME']));
            $templateModel->template_text = (trim($arFormData['TEXT']));
            $userModel = new UserModel();
            $loggerModel = new LoggerModel();
            switch($actionType){
                case 'add':
                    if(!$templateModel->isValid() || !$templateModel->save()){
                        $errors = $templateModel->getErrors();
                    }else{
                        $loggerModel->logAction($userModel->getID(), "NEW TEMPLATE (NAME = $templateModel->name)");
                        $this->renderSuccess();
                    }
                break;
                case 'update':
                    if(!$templateModel->isValid(true) || !$templateModel->update()){
                        $errors = $templateModel->getErrors();
                    }else{
                        $loggerModel->logAction($userModel->getID(), "UPDATED TEMPLATE (ID = $templateModel->id)");
                        $this->renderSuccess();
                    }
                break;
            }
            $arUserFormData = [];
            $arUserFormData = ['id' => $templateModel->id, 'name' => $templateModel->name, 'template_text' => $templateModel->template_text];
        }
        $formType = $this->request->getPost("form");
        if($formType == "update" && $templateModel->id){
            $updateFormData = $templateModel->getTemplateById($templateModel->id);
        }
        $this->ajaxRender('ajax-template-create-form', [
            'formType' => $formType,
            'errors'   => $errors,
            'updateFormData' => $arUserFormData ? $arUserFormData : $updateFormData[0],
        ]);
    }

    /**
     * ajax request | create/update user
    */
    public function usercreate()
    {
        $userModel = new UserModel;
        $errors = false;
        $userModel->id = (int)$this->request->getPost("elemID");
        if($this->request->isPost() && $this->request->getPost("serializeData")){
            $arFormData = [];
            parse_str($this->request->getPost("serializeData"), $arFormData);
            $actionType = $arFormData["actionType"];
            $cities = $arFormData['CITY'];
            $cities = array_map(function($n){return (int)$n;}, $cities);
            sort($cities);
            $userModel->city = $cities;
            $userModel->fullname = htmlspecialchars(trim($arFormData['NAME']));
            $userModel->phone = htmlspecialchars(trim($arFormData['PHONE']));
            $userModel->email = htmlspecialchars(trim($arFormData['EMAIL']));
            $userModel->pass = htmlspecialchars(trim($arFormData['PASSWRD']));
            $userModel->passRepeat = htmlspecialchars(trim($arFormData['PASSWRD_REPEAT']));
            $loggerModel = new LoggerModel;
            switch ($actionType) {
                case 'add':
                    if(!$userModel->isValid() || !$userModel->save()){
                        $errors = $userModel->getErrors();
                    }else{
                        $loggerModel->logAction($userModel->getID(), "NEW USER (NAME =  $userModel->fullname)");
                        $this->renderSuccess();
                    }
                break;
                case 'update':
                    if(!$userModel->isValid(true) || !$userModel->update()){
                        $errors = $userModel->getErrors();
                    }else{
                        $loggerModel->logAction($userModel->getID(), "UPDATED USER (ID =  $userModel->id)");
                        $this->renderSuccess();
                    }
                break;
            }
            $arUserFormData = [];
            $arUserFormData = ['id' => $userModel->id, 'fullname' => $userModel->fullname, 'phone' => $userModel->phone, 'email' => $userModel->email, 'city' => $userModel->city];

        }
        $arSelect = [
            'cities'    => $userModel->getActiveCities(),
        ];
        $formType = $this->request->getPost("form");
        $updateFormData = [];
        if($formType == "update" && $userModel->id){
            $updateFormData = $userModel->getUserById($userModel->id);
        }
        $this->ajaxRender('ajax-user-create-form', [
            'arSelect'       => $arSelect,
            'errors'         => $errors,
            'formType'       => $formType,
            'updateFormData' => $arUserFormData ? $arUserFormData : $updateFormData,
        ]);
    }

    /**
     * ajax request | create/update vacancy
    */
    public function vacancycreate()
    {
        $userModel = new UserModel;
        $userID = $userModel->getID();
        $loggerModel = new LoggerModel;
        $vacancyModel = new VacancyModel;
        $templateModel = new TemplateModel;
        $errors = false;
        $vacancyModel->id = (int)$this->request->getPost("elemID");
        if($this->request->isPost() && $this->request->getPost("serializeData")){
            $arFormData = [];
            parse_str($this->request->getPost("serializeData"), $arFormData);
            $actionType = $arFormData["actionType"];
            $vacancyModel->text = (trim($arFormData['TEXT']));
            $vacancyModel->name = htmlspecialchars(trim($arFormData['NAME']));
            $vacancyModel->city = (int)$arFormData['CITY'];
            $vacancyModel->hr = (int)$arFormData['HR'];
            $vacancyModel->template = (int)$arFormData['TEMPLATE'];
            switch ($actionType) {
                case 'add':
                    if(!$vacancyModel->isValid() || !$vacancyModel->save()){
                        $errors = $vacancyModel->getErrors();
                    }else{
                        $loggerModel->logAction($userID, "NEW VACANCY (NAME = $vacancyModel->name)");
                        $this->renderSuccess();
                    }
                break;
                case 'update':
                    if(!$vacancyModel->isValid(true) || !$vacancyModel->update()){
                        $errors = $vacancyModel->getErrors();
                    }else{
                        $loggerModel->logAction($userID, "UPDATED VACANCY (ID = $vacancyModel->id)");
                        $this->renderSuccess();
                    }
                break;
            }
            $vacancyModel->text = (trim($arFormData['TEXT']));
            $vacancyModel->name = htmlspecialchars(trim($arFormData['NAME']));
            $vacancyModel->city = (int)$arFormData['CITY'];
            $vacancyModel->hr = (int)$arFormData['HR'];
            $vacancyModel->template = (int)$arFormData['TEMPLATE'];
            $arUserFormData = [];
            $arUserFormData['VACANCIES'][] = ['ID' => $vacancyModel->id, 'DETAIL_TEXT' => $vacancyModel->text, 'NAME' => $vacancyModel->name, 'CITIES' => [$vacancyModel->city], 'HR_ID' => $vacancyModel->hr, 'TEMPLATE_ID' => $vacancyModel->template];
        }
        $uberMench = $userModel->isRolesAccess([UserModel::REGIONAL_MANAGER]);
        $userCity = $uberMench ? false : $userModel->getUserCity();
        $arSelect = [
            'users'     => $userModel->getActiveUsers($userCity),
            'cities'    => $userModel->getActiveCities($userCity),
            'templates' => $templateModel->getAllTemplates(),
        ];
        $formType = $this->request->getPost("form");
        $updateFormData = [];
        if($formType == "update" && $vacancyModel->id){
            $updateFormData = $vacancyModel->getVacanciesWithUser($userCity, false, $vacancyModel->id, $userID);
            $updateFormData = array_shift($updateFormData);
        }
        $this->ajaxRender('ajax-vacancy-create-form', [
            'uberMench'        => $uberMench,
            'arUsersVacancies' => $arUsersVacancies,
            'errors'           => $errors,
            'arSelect'         => $arSelect,
            'updateFormData'   => $arUserFormData ? $arUserFormData : $updateFormData,
            'formType'         => $formType,
        ]);
    }

}
