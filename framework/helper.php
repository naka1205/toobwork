<?php
use framework\lib\Request;

/**
 * 获取配置项参数
 * @AuthorHTL naka1205
 * @DateTime  2016-06-20T19:32:41+0800
 * @param     [type]                   $name 配置项名称
 * @param     boolean                $bool 应用配置或模块配置
 */
function config($name,$bool = false)
{
            $config = false;
            if ($bool) {
                    $file =  CONFIG_PATH . $name . '.php';
                    if ( is_file($file) ) {
                            $config = require $file;
                    }                     
            }
            else{
                    $file =  APP_PATH . Request::instance()->module() . '/config.php';
                    if ( is_file($file) ) {
                            $configs = require $file;
                            $config =  isset($configs[$name]) ? $configs[$name] : false;
                    }

            }
           return $config;
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
/**
 * 模版参数替换
 * @AuthorHTL
 * @DateTime  2017-08-24T13:02:21+0800
 * @param     [type]                   $strInput [description]
 * @param     [type]                   $smarty   [description]
 */
function prefiltered( $strInput, $smarty)
{
    return preg_replace("/__(.*)__/",'{$smarty.const.${0}}' , $strInput);
}

