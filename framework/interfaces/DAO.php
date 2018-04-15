<?php
namespace framework\interfaces;
/**
 * 数据库操作驱动
 */
interface DAO{
	public static function init($config);
	public function execute($config);
	public function fetchAll($config);
	public function fetchRow($config);
	public function escToStr($config);
	public function getError($config);
}