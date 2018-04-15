<?php
/**
* 模块配置
*/
return [

	'def_modul' =>'home',
	'def_controller' =>'index',
	'def_action' =>'index',
	'log_path' => LOG_PATH . 'home' . DS,
	//配置模板引擎
	'view' => [
		'viewpath' => APP_PATH . 'home/view',
		'temppath' => TEMP_PATH . 'home',
		'cachepath' => CACHE_PATH . 'home',
		'cache' => false,
		'view_replace_str' => [
		        '__CSS__'   	 => '/home/css',
		        '__IMG__' 	 => '/home/images',
		        '__LAY__' 	 => '/home/layui',
		        '__MOD__' 	 => '/home/mods',
		        '__JS__'     	 => '/home/js',
		]
	],
	//分页配置
	'page' => [
		'p' => 'p',
		'roll' => 5,
		'config' => [
			'prev' => '<sapn class="%STYLE%"><a  href="%HREF%">%TEXT%</a></sapn>',
			'next' => '<sapn class="%STYLE%"><a  href="%HREF%">%TEXT%</a></sapn>',
			'header' => '',
			'prev'   => '上一页',
			'next'   => '下一页',
			// 'theme'  => '%HEADER% %LINK_PAGE% %DOWN_PAGE%',
			],
		'template'  => [
			'num'   	=> '<a  href="%HREF%">%TEXT%</a>',
			'prev'   	=> '<a  class="laypage-prev" href="%HREF%">%TEXT%</a>',
			'next'   	=> '<a  class="laypage-next" href="%HREF%">%TEXT%</a>',
			'first'   		=> '<a  class="laypage-first" href="%HREF%">首页</a>',
			'last'   		=> '<a  class="laypage-last" href="%HREF%">尾页</a>',
			'current' 	=> '<sapn class="laypage-curr">%TEXT%</sapn>',
			]		
	],
	

];