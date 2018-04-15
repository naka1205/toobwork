<?php
namespace framework\lib;
/**
* 基础控制类
* @AuthorHTL  naka1205
* @DateTime  2016-04-13T20:00:01+0800
*/
class Controller
{
	public $view;
	function __construct()
	{		
		session_start();
		$config = config('view');
		$this->view = new View($config);
	}
	/**
	 * 分配变量
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-08-28T14:43:44+0800
	 * @param     [type]                   $name [description]
	 * @param     [type]                   $data [description]
	 */
	public function assign($name,$data)
	{
		$this->view->assign($name,$data);
	}
	/**
	 * 渲染模板
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-08-28T14:43:59+0800
	 * @return    [type]                   [description]
	 */
	public function display($name = '')
	{
		$this->view->display($name);
	}	
	/**
	 * 立即跳转
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-14T22:53:17+0800
	 * @param     string                   $url 跳转地址
	 */
	protected function _jumpNow($url='')
	{
		header('Location:'.$url);
		die;
	}
	/**
	 * 提示跳转
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-14T22:53:47+0800
	 * @param     string                   $url    跳转地址
	 * @param     string                   $message 提示信息
	 * @param     integer                  $wait    等待时间，默认3秒
	 */
	protected function _jumpWait($url='',$message='',$wait=3)
	{
		header("Refresh: $wait ; URL=$url");
		echo $message;
		die;
	}

	public function json($result)
	{
		header('Content-type:application/json;charset=utf8');
		echo json_encode($result);
		die;
	}

	public  function error()
	{
		header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
		echo '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>404</h1><p> 页面丢失</p><span style="font-size:22px;">点击<a href="/" target="toobooo">返回首页</a></span></div>';
		die;
	}
}