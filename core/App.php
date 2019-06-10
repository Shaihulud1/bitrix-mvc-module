<?php
abstract class App
{
    const BASE_VACANCY_PATH = "/vacancy/superuser/";
    const VACANCY_BLOCK = IBLOCK_ID_VACANCY;
    const CITY_BLOCK = IBLOCK_ID_CITY;
    const MODULE_SESSION_NAME = "VACMODULE";
    const TEMPLATES_BLOCK = IBLOCK_ID_VACANCY_TEMPLATES;

    public $arErrors;

    /**
     * add error to error array
     * @param string $code
     * @param string $text
    */
    public function addError($code, $text)
    {
        $this->arErrors[$code] = $text;
    }

    /**
     * get errors from error array
     * @return string
    */
    public function getErrors()
    {
        if(empty($this->arErrors)) return false;
        $result = false;
        foreach($this->arErrors as $erCode => $errorText){
            $result .= $errorText."</br>";
        }
        return $result;
    }

    /**
     * return url to page
     * @param string $controller | controller name
     * @param string $action | controller's method name
     * @return string
    */
    static function getUrl($controller, $action)
    {
        return self::BASE_VACANCY_PATH."$controller/$action/";
    }

    /**
     * return url to page
     * @param string $controller | controller name
     * @param string $action | controller's method name
     * @return string
    */
    static function redirect($controller, $action)
    {
        LocalRedirect(self::getUrl($controller, $action));
    }

    /**
     * return 404 error page
    */
    static function unknownPage()
    {
        self::redirect('main', 'index');
    }
}
