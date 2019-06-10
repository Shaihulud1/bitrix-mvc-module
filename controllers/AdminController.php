<?php
namespace controllers;

use models\UserModel;
use models\TemplateModel;
use models\VacancyModel;
use models\LoggerModel;


class AdminController extends \AppController{

    /**
     * pages access
    */
    static $access = [
        'vacancy'   => [UserModel::REGIONAL_MANAGER, UserModel::HR],
        'archive'   => [UserModel::REGIONAL_MANAGER, UserModel::HR],
        'templates' => [UserModel::REGIONAL_MANAGER],
    ];

    /**
     * vacancies page
    */
    public function vacancy()
    {
        $userModel = new UserModel;
        $userID = $userModel->getID();
        $loggerModel = new LoggerModel;
        $vacancyModel = new VacancyModel;
        $templateModel = new TemplateModel;
        $errors = false;
        if($this->request->isPost()){
            $actionType = $this->request->getPost("actionType");
            switch ($actionType) {
                case 'archive':
                    $vacancyModel->id = (int)$this->request->getPost("elemID");
                    $loggerModel->logAction($userID, "ARCHIVED VACANCY (ID = $vacancyModel->id)");
                    $vacancyModel->archive(false);
                break;
            }
        }
        $uberMench = $userModel->isRolesAccess([UserModel::REGIONAL_MANAGER]);
        $userCity = $uberMench ? false : $userModel->getUserCity();
        $arSelect = [
            'users'     => $userModel->getActiveUsers($userCity),
            'cities'    => $userModel->getActiveCities($userCity),
            'templates' => $templateModel->getAllTemplates(),
        ];
        $searchHR = $_GET['search_hr'] ? htmlspecialchars(trim($_GET['search_hr'])) : false;
        $arUsersVacancies = $vacancyModel->getVacanciesWithUser($userCity ? $userCity : false, false, false, $userID, $userModel->getChoosenCities());
        if(!empty($arUsersVacancies)){
            $arUserData = $userModel->getUserListByIds(array_keys($arUsersVacancies), $userModel::HR, $searchHR);
            foreach($arUsersVacancies as $key => $userData){
                if(!$arUserData[$key]){
                    unset($arUsersVacancies[$key]);
                    continue;
                }
                $arUsersVacancies[$key]["NAME"] = $arUserData[$key]["NAME"];
                $arUsersVacancies[$key]["PHONE"] = $arUserData[$key]["PHONE"];
                $arUsersVacancies[$key]["EMAIL"] = $arUserData[$key]["EMAIL"];
                $arUsersVacancies[$key]["ROLE"] = $arUserData[$key]["ROLE"];
                $arUsersVacancies[$key]["CITY"] = $arUserData[$key]["CITY"];
            }
        }
        $this->render('admin-vacancy', [
            'uberMench' => $uberMench,
            'arUsersVacancies' => $arUsersVacancies,
            'errors' => $errors,
            'arSelect' => $arSelect,
        ]);
    }

    /**
     * templates page
    */
    public function templates()
    {
        $templateModel = new TemplateModel();
        if($this->request->isPost()){
            $actionType = $this->request->getPost("actionType");
            $errors = false;
            $loggerModel = new LoggerModel();
            $userModel = new UserModel();
            $userID = $userModel->getID();
            switch ($actionType) {
                case 'delete':
                    $templateModel->id = (int)$this->request->getPost("elemID");
                    $loggerModel->logAction($userID, "DELETED TEMPLATE (ID = $templateModel->id)");
                    $templateModel->delete($templateModel->id);
                break;
            }
        }
        $arTemplates = $templateModel->getAllTemplates();
        $this->render('admin-templates', ['arTemplates' => $arTemplates, 'errors' => $errors]);
    }

    /**
     * archive page
    */
    public function archive()
    {
        $userModel = new UserModel;
        $userID = $userModel->getID();
        $loggerModel = new LoggerModel;
        $vacancyModel = new VacancyModel;
        $errors = false;
        if($this->request->isPost()){
            $actionType = $this->request->getPost("actionType");
            switch ($actionType) {
                case 'unarchive':
                    $vacancyModel->id = (int)$this->request->getPost("elemID");
                    $loggerModel->logAction($userID, "UNARCHIVED VACANCY (ID = $vacancyModel->id)");
                    $vacancyModel->archive(true);
                break;
            }
        }

        $uberMench = $userModel->isRolesAccess([UserModel::REGIONAL_MANAGER]);
        $userCity = $uberMench ? false : $userModel->getUserCity();
        $arUsersVacancies = $vacancyModel->getVacanciesWithUser($userCity ? $userCity : false, true, false, $userID, $userModel->getChoosenCities());
        if(!empty($arUsersVacancies)){
            $arUserData = $userModel->getUserListByIds(array_keys($arUsersVacancies), $userModel::HR);
            foreach($arUsersVacancies as $key => $userData){
                if(!$arUserData[$key]){
                    unset($arUsersVacancies[$key]);
                    continue;
                }
                $arUsersVacancies[$key]["NAME"] = $arUserData[$key]["NAME"];
                $arUsersVacancies[$key]["PHONE"] = $arUserData[$key]["PHONE"];
                $arUsersVacancies[$key]["EMAIL"] = $arUserData[$key]["EMAIL"];
                $arUsersVacancies[$key]["ROLE"] = $arUserData[$key]["ROLE"];
                $arUsersVacancies[$key]["CITY"] = $arUserData[$key]["CITY"];
            }
        }
        $this->render('admin-archive', ['uberMench' => $uberMench, 'arUsersVacancies' => $arUsersVacancies, 'errors' => $errors]);
    }

}
