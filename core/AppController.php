<?php
use models\UserModel;
use Bitrix\Main\Application;

abstract class AppController extends App{

    protected $isAuthOnly;
    protected $rolesAccess = [];
    protected $user = false;
    protected $request;

    public function __construct()
    {
        parent::__construct();
        $this->request = Application::getInstance()->getContext()->getRequest();
    }

    /**
     * render view page
     * @param string $page | page name
     * @param array $params
    */
    public function render($page, $params = [])
    {
        if(!empty($params)){
            extract($params);
        }
        require_once(__DIR__.'/../header.php');
        require_once(__DIR__.'/../views/'.$page.'.php');
        require_once(__DIR__.'/../footer.php');
    }

    /**
     * render page for ajax request without header and footer
     * @param string $page | page name
     * @param array $params
    */
    public function ajaxRender($page, $params = [])
    {
        if(!empty($params)){
            extract($params);
        }
        require_once(__DIR__.'/../views/'.$page.'.php');
    }
    

    /**
     * display success and die needed for js
    */
    public function renderSuccess()
    {
        die('SUCCESS');
    }
}
