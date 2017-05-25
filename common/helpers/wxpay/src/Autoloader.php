<?php
class Autoloader{
    public static function getBase(){
        return dirname(__FILE__);
    }
    public static function autoload($className){

        if(0 === substr_compare($className, 'wxpay', 0, 5)){
            $path = self::getBase() .
                    DIRECTORY_SEPARATOR .
                    str_replace('\\', '/', str_replace('wxpay\\', '', $className)) .
                    '.php';

            if(is_file($path)){
                require_once($path);
            }
        }elseif('QRcode' == $className){
            require_once self::getBase() . DIRECTORY_SEPARATOR . 'helpers/phpqrcode.php';
        }
    }
    public static function register(){
        spl_autoload_register(['Autoloader', 'autoload'], true, true);
    }
}
