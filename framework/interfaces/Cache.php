<?php
namespace framework\interfaces;
/**
 * 缓存操作驱动
 */
interface Cache{
	public static function init($config);
	public function has($name);
	public function get($name,$default);
	public function set($name,$value, $expire);
	public function remove($name);
	public function clear();
	public function getCacheKey($name);
}