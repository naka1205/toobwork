<?php
namespace framework\lib;

class Cache
{
    protected static $instance = [];
    public static $readTimes   = 0;
    public static $writeTimes  = 0;

    //操作句柄
    protected static $handler;

    //连接缓存
    public static function connect(array $options = [] )
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';
        $class =  'framework\\cache\\' . ucwords($type);
        return $class::init($options);
    }

    //自动初始化缓存
    public static function init(array $options = [])
    {

        if (is_null(self::$handler)) {

            // 自动初始化缓存
            if (!empty($options)) {
                $connect = self::connect($options);
            }  else {
                $module_config = config('cache') ;
                $config = $module_config ? $module_config :  config('cache',true);
                $connect = self::connect($config);
            }
            self::$handler = $connect;
        }
        return self::$handler;
    }

    //判断缓存是否存在
    public static function has($name)
    {
        self::$readTimes++;
        return self::init()->has($name);
    }

    //获取缓存
    public static function get($name, $default = false)
    {
        self::$readTimes++;
        return self::init()->get($name, $default);
    }

    //设置缓存
    public static function set($name, $value, $expire = null)
    {
        self::$writeTimes++;
        return self::init()->set($name, $value, $expire);
    }

    //删除缓存
    public static function remove($name)
    {
        self::$writeTimes++;
        return self::init()->remove($name);
    }

    //清空缓存
    public static function clear($name = null)
    {
        self::$writeTimes++;
        return self::init()->clear($name);
    }


}
