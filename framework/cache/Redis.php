<?php
namespace framework\cache;

use framework\interfaces\Cache;

use BadFunctionCallException;
use DateTime;
use Redis as PHPRedis;
/**
 * Redis缓存
 */
class Redis implements Cache
{
    protected $options = [
        'host'           => '127.0.0.1',
        'port'            => 6379,
        'password'   => '',
        'select'         => 0,
        'timeout'       => 0,
        'expire'         => 0,
        'persistent'   => false,
        'prefix'          => '',
    ];

    private $_redis;      //操作对象
    private static $_instance = NULL; //链接对象

    //构造函数
    private function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->getRedis();
    }

    //创建连接
    private function getRedis()
    {
            $this->_redis = new PHPRedis;
            $func = $this->options['persistent'] ? 'pconnect' : 'connect';
            $this->_redis->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

            if ('' != $this->options['password']) {
                    $this->_redis->auth($this->options['password']);
            }

            if (0 != $this->options['select']) {
                    $this->_redis->select($this->options['select']);
            }

    }

    //获取实例
    public static function init($config)
    {   
        //判断是否已存在示例对象
        if( !(static::$_instance instanceof static) ) {
            static::$_instance = new static($config);
        }
        return static::$_instance;
    }

    //获取实际的缓存标识
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . $name;
    }

    //判断缓存是否存在
    public function has($name)
    {
        return $this->_redis->get($this->getCacheKey($name)) ? true : false;
    }

    //读取缓存
    public function get($name, $default = false)
    {
        $value = $this->_redis->get($this->getCacheKey($name));
        if (is_null($value)) {
            return $default;
        }
        $jsonData = json_decode($value, true);
        return (null === $jsonData) ? $value : $jsonData;
    }

    //写入缓存
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
 
        $key = $this->getCacheKey($name);
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && $expire) {
            $result = $this->_redis->setex($key, $expire, $value);
        } else {
            $result = $this->_redis->set($key, $value);
        }

        return $result;
    }

    //删除缓存
    public function remove($name)
    {
        return $this->_redis->delete($this->getCacheKey($name));
    }

    //清除缓存
    public function clear()
    {
        return $this->_redis->flushDB();
    }

}
