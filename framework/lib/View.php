<?php
namespace framework\lib;
use Smarty;
/**
* 视图渲染类
* @AuthorHTL  naka1205
* @DateTime  2016-04-13T20:00:01+0800
*/
class View
{
	public $smarty;
	public $config;
	public function __construct($config)
	{	
		require EXT_PATH . 'smarty/Smarty.php';	
		$this->smarty = new Smarty();
		if (isset($config['viewpath']) && !empty($config['viewpath'])) {
			$this->viewPath($config['viewpath']);
		}
		if (isset($config['temppath']) && !empty($config['temppath'])) {
			$this->tempPath($config['temppath']);
		}
		if (isset($config['cachepath']) && !empty($config['cachepath'])) {
			$this->cachePath($config['cachepath']);
		}
		if (isset($config['cache']) && !empty($config['cache'])) {
			$this->cache($config['cache']);
		}
		foreach ($config['view_replace_str'] as $key => $value) {
			defined($key) or define($key, $value );
		}
		$this->smarty->registerFilter('pre', 'prefiltered');
	}
	
	/**
	 * 设置视图目录
	 */
	public function viewPath($path)
	{
		$this->smarty->setTemplateDir($path);
	}
	/**
	 * 设置编译目录
	 */
	public function tempPath($path)
	{
		$this->smarty->setCompileDir($path);
	}
	/**
	 * 设置缓存目录
	 */
	public function cachePath($path)
	{
		$this->smarty->cache_dir = $path;
	}
	/**
	 * 设置缓存
	 */
	public function cache($cache)
	{
		$this->smarty->caching = $cache;
	}
	/**
	 * 分配变量
	 */
	public function assign($name,$value)
	{
		$this->smarty->assign($name,$value);
	}
	/**
	 * 模板渲染
	 */
	public function display($tpl = '',$mrak='')
	{
		header("Content-type:text/html;charset=utf8");
		if (empty($tpl)) {
			$tpl = Request::instance()->action();
		}
		$tpl = $tpl . '.html';
		if (!empty($mrak)) {
			$this->smarty->display($tpl,$mrak);
		}
		else{
			$this->smarty->display($tpl);
		}
		
	}
}