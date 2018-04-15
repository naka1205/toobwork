<?php
namespace framework\lib;
// use framework\Request;
// use framework\Route;
/**
 * 应用处理类
 */
class Application
{

    /**
     * 调用的模块
    */
    protected static $module = '';
    protected static $controller = '';
    protected static $action = '';
    
    /**
     * 配置文件
    */
    public static $configs = array();    

    /**
     * 应用运行
     * @AuthorHTL naka1205
     * @DateTime  2016-06-24T22:35:41+0800
     * @return    [type]                   [description]
     */
    public static function run()
    {
            Route::dispatch();
    }   
    /**
     * 访问错误
     * @AuthorHTL naka1205
     * @DateTime  2016-06-24T23:39:13+0800
     * @return    [type]                   [description]
     */
    public static function error()
    {

        $controller = new Controller();
        $controller->error();
    }

    public static function module()
    {
        return self::$module;
    }

    /**
     * 执行命令
     * @AuthorHTL Naka1205
     * @DateTime  2017-09-13T20:26:40+0800
     */
    public static function command($argv)
    {
        $class = '\command\\' . $argv[1];
        $action = $argv[2];
        $controller = new $class();
        return $controller->$action();
    }

}