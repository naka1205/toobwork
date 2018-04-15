<?php
namespace framework\lib;
/**
* 工厂类
* @AuthorHTL  naka1205
* @DateTime  2016-04-13T20:00:01+0800
*/
class Factory
{	
	private function __construct(){}

	private function __clone(){}
	
	/**
	 * 实例化单例模型对象
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T22:32:04+0800
	 * @param    string                  $model_name 模型名称
	 * @return     object 		返回单例模型对象		
	 */
	public static function M($model_name){
		//定义模型对象列表
		static $model_list = array();
		//判断实例化模型对象是否存在模型对象列表中
		if (!isset($model_list[$model_name])) {

			$model_class_name = $model_name;
			$model_name = strtolower($model_name);	//转换模型名称为小写
			$model_list[$model_name] = new $model_class_name($model_name);	//实例化模型对象	
		}
		//返回实例化模型对象
		return $model_list[$model_name];
	}
}