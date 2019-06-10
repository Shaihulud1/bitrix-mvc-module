<?php
namespace controllers;

use Bitrix\Main\Application;
use models\UserModel;

class MainController extends \AppController{
    static $access = [
        'index'   => ["ALL"],
    ];

    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        //"INSERT INTO vacmodule_users (email, phone, role, fullname, city, passwrd) VALUES ('admin@mail.ru', '+7999999999', '7', 'Региональный менеджер', '1', 'cc0ab764c30699d0a73566517ab3cb6e')"
        //"INSERT INTO vacmodule_users (email, phone, role, fullname, city, passwrd) VALUES ('admin2@mail.ru', '+7999999999', '7', 'Региональный менеджер', '1', '8d9425c54d90de046631e99fa9fc17df')"
        $pass = $this->userModel->getEnCodePassword("321sfsfdsf4sjgjg");
        print_r($pass);
        die();
        if($this->userModel->isAuth()){
            $this->redirect('admin', 'vacancy');
        }
    }
    /**
     * login page
    */
    public function index()
    {
        $errors = false;
        if($this->request->isPost()){
            $this->userModel->email = htmlspecialchars(trim($this->request->getPost("USER_EMAIL")));
            $this->userModel->pass = htmlspecialchars(trim($this->request->getPost("USER_PASS")));
            if(!$this->userModel->isValidEnterForm()){
                $errors = $this->userModel->getErrors();
            }else{/*login user*/
                $this->userModel::loginUser($this->userModel->id, $this->userModel->role);
                $this->redirect('admin', 'vacancy');
            }
        }
        $this->render('index', ['errors' => $errors]);
    }

}
