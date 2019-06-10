<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);

Class VitaVacancies extends CModule
{

    var $MODULE_ID = "VitaVacancies";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    public $path;

    function __construct()
    {
        $arModuleVersion = array();

        $this->path = str_replace("\\", "/", __FILE__);
        $this->path = substr($this->path, 0, strlen($this->path) - strlen("/index.php"));
        include($this->path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = 'Вакансии';
        $this->MODULE_DESCRIPTION = 'Модуль для админки вакансий';
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        $this->createDB();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля ".$this->MODULE_NAME, $DOCUMENT_ROOT."/local/modules/VitaVacancies/install/step.php");
        return true;
    }
    function InstallFiles($arParams = array())
    {
        /*
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."local/modules/VitaVacancies/install/copypage/index.php", $_SERVER["DOCUMENT_ROOT"]."vacancy/superuser/index.php", true, true);
        return true;
        */
    }

    function createDB()
    {
        global $DB;
        $sqlQueries = [];
        $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `vacmodule_users` (
                          `id` int(11) NOT NULL AUTO_INCREMENT ,
                           PRIMARY KEY (`id`),
                          `email` varchar(255) NOT NULL,
                          `phone` varchar(255) NOT NULL,
                          `role` varchar(255) NOT NULL DEFAULT 'HR',
                          `fullname` varchar(255) NULL,
                          `city` varchar(455) NOT NULL,
                          `passwrd` varchar(255) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `vacmodule_logger`(
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                         PRIMARY KEY (`id`),
                        `user` int(11) NOT NULL,
                        `action` varchar(255) NOT NULL,
                        `date_unix` int(11) NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        /*
        $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `vacmodule_vacancies_templates` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                         PRIMARY KEY (`id`),
                        `name` varchar(255) DEFAULT NULL,
                        `template_text` text DEFAULT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        */
        foreach($sqlQueries as $sql){
            $r = $DB->Query($sql);
        }
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        if(!isset($_GET['delete_tables'])){
            $APPLICATION->IncludeAdminFile("Деинсталляция модуля ".$this->MODULE_NAME, $DOCUMENT_ROOT."/local/modules/VitaVacancies/install/unstep1.php");
            die();
        }
        if($_GET['delete_tables'] == "Y"){
            $this->deleteDB();
        }
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля ".$this->MODULE_NAME, $DOCUMENT_ROOT."/local/modules/VitaVacancies/install/unstep.php");
        return true;
    }
    function UnInstallFiles()
    {
        /*
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"]."vacancy/superuser");
        return true;
        */
    }
    function deleteDB()
    {
        global $DB;
        $tables = ['vacmodule_users', 'vacmodule_logger', 'vacmodule_vacancies_templates', 'vacmodule_vacancies'];
        foreach($tables as $table){
            $sql = "DROP TABLE IF EXISTS `$table`";
            $r = $DB->Query($sql);
        }
    }
}
