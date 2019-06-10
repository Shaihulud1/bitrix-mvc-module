<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Application;
use models\UserModel;
/*list of the core files here*/
$coreFilesInclude = [
    "App",
    "AppDB",
    "AppModel",
    "AppController",
];
foreach($coreFilesInclude as $coreFile){
    include "core/$coreFile.php";
}
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    include "$class.php";
});
$controller = empty($_GET) ? 'main' : trim(htmlspecialchars($_GET['controller']));
$action = empty($_GET) ? 'index' : trim(htmlspecialchars($_GET['action']));
$id = empty($_GET['id']) ? false : (int)$_GET['id'];
$user = new UserModel;
$controller = "controllers\\".ucfirst($controller)."Controller";
$controllerClass = new $controller;
if(!$controllerClass || !method_exists($controllerClass, $action)){
    $controllerClass::unknownPage();
}
if(!empty($controllerClass::$access[$action]) && !in_array("ALL", $controllerClass::$access[$action])){
    $user = new UserModel;
    if(!$user->isRolesAccess($controllerClass::$access[$action])){
        $controllerClass::unknownPage();
    }
}
$controllerClass->$action();
