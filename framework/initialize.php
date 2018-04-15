<?php
/**
 * 框架初始化
 * @AuthorHTL  naka1205
 * @DateTime  2016-04-13T20:00:01+0800
 */
date_default_timezone_set('Asia/Chongqing');
// 系统常量
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('WEB_PATH') or define('WEB_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('ROOM_PATH') or define('ROOM_PATH', realpath(dirname(WEB_PATH)) . DS);

defined('FRAME_PATH') or define('FRAME_PATH', ROOM_PATH .'framework' . DS);
defined('LIB_PATH') or define('LIB_PATH', FRAME_PATH .'lib' . DS);
defined('EXT_PATH') or define('EXT_PATH', ROOM_PATH .'extend' . DS);
defined('APP_PATH') or define('APP_PATH', ROOM_PATH .'application' . DS);
defined('CONFIG_PATH') or define('CONFIG_PATH', ROOM_PATH .'config' . DS);
defined('PUBLIC_PATH') or define('PUBLIC_PATH',ROOM_PATH .'public' . DS);
defined('HTML_PATH') or define('HTML_PATH',ROOM_PATH .'html' . DS);
defined('RUN_PATH') or define('RUN_PATH',ROOM_PATH .'run' . DS);
defined('COM_PATH') or define('COM_PATH', ROOM_PATH . 'command' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUN_PATH . 'log' . DS);
defined('CACHE_PATH') or define('CACHE_PATH', RUN_PATH . 'cache' . DS);
defined('TEMP_PATH') or define('TEMP_PATH', RUN_PATH . 'temp' . DS);

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

defined('MBSTRING') or define('MBSTRING', function_exists('mb_get_info'));
defined('RESOURCE_CHAR_SET') or define('RESOURCE_CHAR_SET', MBSTRING ? 'UTF-8' : 'ISO-8859-1');
defined('RESOURCE_DATE_FORMAT') or define('RESOURCE_DATE_FORMAT', '%b %e, %Y');

//加载框架助手函数
require FRAME_PATH . 'helper.php';
require LIB_PATH . 'Loader.php';
require APP_PATH . 'common.php';

//应用配置
$configs = config('default',true);
if ( $configs['debug'] == true ) {
	ini_set("display_errors", "On");
}

//注册自动加载
\framework\lib\Loader::register();