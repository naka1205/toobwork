<?php
namespace home\model;
/**
* 测试模型
*/
class Demo extends Common
{
	//自动写入时间戳，表字段名create_time、update_time
	public $autotime = true;

	//定义数据表名称，默认为类名
	public $name = 'profile';
		
}