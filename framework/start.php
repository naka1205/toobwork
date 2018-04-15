<?php
/**
 * 应用启动
 * @AuthorHTL  naka1205
 * @DateTime  2016-04-13T20:00:01+0800
 */
//初始化配置
require __DIR__ . '/initialize.php';
//加载路由配置
require CONFIG_PATH . 'route.php';	

use framework\lib\Application;

//应用配置
Application::$configs = config('default',true);
//启动应用
Application::run();