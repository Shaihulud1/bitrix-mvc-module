<?php
namespace controllers;

use models\UserModel;
use models\LoggerModel;

class ProfileController extends \AppController{

    /**
     * pages access
    */
    static $access = [
        'selfsettings'   => [UserModel::REGIONAL_MANAGER, UserModel::HR],
        'users'          => [UserModel::REGIONAL_MANAGER],
        'exit'           => [UserModel::REGIONAL_MANAGER, UserModel::HR],
    ];

    /**
     * user's profile settings
    */
    public function selfsettings()
    {
        $userModel = new UserModel;
        $uberMench = $userModel->isRolesAccess([UserModel::REGIONAL_MANAGER]);
        $userCity = $uberMench ? false : $userModel->getUserCity();
        $errors = [];
        if($this->request->isPost()){
             $city = $this->request->getPost("CITY");
             sort($city);
             $city = array_map(function($n){return (int)$n;}, $city);
             $userModel->id = $userModel->getID();
             $userModel->city = $city;
             $userModel->email = htmlspecialchars(trim($this->request->getPost("EMAIL")));
             $userModel->phone = htmlspecialchars(trim($this->request->getPost("PHONE")));
             $userModel->fullname = htmlspecialchars(trim($this->request->getPost("NAME")));
             if(!$userModel->isValid(true) || !$userModel->update()){
                 $errors = $userModel->getErrors();
             }
        }
        $selfUserData = $userModel->getUserById($userModel->getID());
        $arSelect = [
            'cities'    => $uberMench ? $userModel->getActiveCities($userCity) : $userModel->getCitiesById($selfUserData['city']),
        ];

        $this->render('profile-selfsettings', [
            'arSelect'     => $arSelect,
            'selfUserData' => $selfUserData,
            'errors'       => $errors,
            'isAdmin'      => $uberMench,
        ]);
    }

    /**
     * users settings page
    */
    public function users()
    {
        $userModel = new UserModel;
        $arUsers = $userModel->getActiveUsers($userModel->getChoosenCities());
        if($this->request->isPost()){
            $actionType = $this->request->getPost("actionType");
            $userID = $userModel->getID();
            switch ($actionType) {
                case 'delete':
                    $userModel->id = (int)$this->request->getPost("elemID");
                    $userModel->delete($userModel->id);
                break;
            }
        }
        $this->render('profile-users', ['arUsers' => $arUsers]);
    }

    /**
     * exit page
    */
    public function exit()
    {
        $userModel = new UserModel;
        $userModel->destroySessions();
        $this->redirect('main', 'index');
    }
}
