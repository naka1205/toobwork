<?php
namespace framework\lib;
/**
 * 自动加载类
 */
class Loader
{

    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class)
    {

        $_class = str_replace('\\', '/', $class);

        $file = ROOM_PATH . $_class . '.php';
       
        if (is_file($file)) {
            require $file;
            return;
        }

        $file = EXT_PATH . $_class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }

        $file = COM_PATH . $_class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }

        $file = APP_PATH . $_class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }

        return;
    }

}