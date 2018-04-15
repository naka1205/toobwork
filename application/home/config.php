<?php
/**
* 模块配置
* @AuthorHTL  naka1205
* @DateTime  2016-04-13T20:00:01+0800
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
		 	'__CSS__'    => '/home/css',
	        '__JS__'     => '/home/js',
	        '__IMG__' 	 => '/home/images',
	        '__LAY__' 	 => '/layui'
		]
	],
	//分页配置
	'page' => [
		'p' => 'p',
		'roll' => 5,
		'config' => [
			'header' => '',
	        'prev'   => '上一页',
	        'next'   => '下一页',
	        'theme'  => '%HEADER% %LINK_PAGE% %DOWN_PAGE%',
        ],
        'style'  => [
	        'prev'   	=> 'prev-page',
	        'next'   	=> 'next-page',
	        'first' 	=> 'first-page',
	        'last'   	=> 'next-page',
	        'current'   => 'active',
        ]
	],

];