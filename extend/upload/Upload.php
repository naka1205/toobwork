<?php
namespace extend\upload;
use Finfo;
/**
* 文件上传类
*/
class Upload
{
	
	private $_ext_list = array('.jpeg','.jpg','.png','.gif');	//允许上传文件类型
	private $_max_size = 2097152;	//允许上传大小 1048576 = 1M ;2097152 = 2M
	private $_upload_path = './upload/';	//文件上传路径
	private $_prefix = '';		//文件名前缀

	private $_error_info = '';	//上传错误信息
	/**
	 * 获取上传错误信息
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:54:22+0800
	 * @return    string                  返回当前上传文件的错误信息
	 */
	public function getErrorInfo()
	{
		return $this->_error_info;
	}
	/**
	 * 设置文件上传允许类型
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:55:07+0800
	 * @param     array                    $ext_list 允许上传的文件后缀名数组
	 */
	public function setExtList($ext_list = array())
	{
		$this->_ext_list = $ext_list;
	}
	/**
	 * 设置允许上传文件的大小
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:56:05+0800
	 * @param     int                  $max_size 允许文件的的最大值
	 */
	public function setMaxSize($max_size = 0)
	{
		$this->_max_size = $max_size;
	}
	/**
	 * 设置文件上传的路径
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:57:11+0800
	 * @param     string                   $upload_path 默认为'./'目录根
	 */
	public function setUploadPath($upload_path)
	{	
		if (is_dir($upload_path)) {
			$this->_upload_path = $upload_path;
		}		
	}
	/**
	 * 设置文件保存的文件名前缀
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:58:02+0800
	 * @param     string                   $prefix  保存文件名的前缀字符
	 */
	public function setPrefix($prefix = '')
	{
		$this->_prefix = $prefix;
	}
	/**
	 * 文件上传
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:45:06+0800
	 * @param     array                    $file_info 上传文件信息
	 * @return    mix                  上传成功返回保存文件的路径地址，失败返回false        
	 */
	public function upLoadFile($file_info=array())
	{
		//print_r($file_info);die;
		if ($file_info['error'] ==  1) {
			$this->_error_info = "文件超过系统环境配置最大值";
			return false;
		}
		if ($file_info['error'] ==  2) {
			$this->_error_info = "文件超过允许最大值";
			return false;
		}
		if ($file_info['error'] ==  4) {
			$this->_error_info = "未发现上传文件";
			return false;
		}
		if ($file_info['error'] ==  6) {
			$this->_error_info = "系统未找到临时目录";
			return false;
		}
		if ($file_info['error'] ==  7) {
			$this->_error_info = "文件保存临时目录失败";
			return false;
		}
		if ($file_info['error'] !=  0) {
			$this->_error_info = "文件上传错误";
			return false;
		}
		//获取文件后缀名
		$file_ext = strrchr($file_info['name'], '.');
		//判断文件后缀是否允许上传
		if (! in_array(strtolower($file_ext), $this->_ext_list)) {
			$this->_error_info = "文件后缀错误";
			return false;
		}
		//获取文件类型
		$mime_list = $this->_ext2Mime($this->_ext_list);
		//判断文件MIME类型是否允许上传
		if (! in_array($file_info['type'], $mime_list)) {
			$this->_error_info = '文件类型错误';
			return false;
		}
		//实例化PHP Finfo扩展类，获取上传文件的MIME类型 PHP.INI 开启php_fileinfo.dll
		$finfo = new Finfo(FILEINFO_MIME_TYPE);	
		//获取临时文件的真实类型
		$real_mime = $finfo->file($file_info['tmp_name']);
		if (! in_array($real_mime, $mime_list)) {
			$this->_error_info = '文件MIME类型错误';
			return false;
		}
		//判断文件上传大小
		if ($file_info['size'] > $this->_max_size) {
			$this->_error_info = '文件大小错误';
			return false;
		}
		//判断临时文件是否为上传文件
		//var_dump($file_info['tmp_name']);die;
		if (! is_uploaded_file($file_info['tmp_name'])) {
			$this->_error_info = '上传文件损坏';
			return false;
		}
		//生成文件名称
		$file_name = uniqid($this->_prefix,false) . $file_ext;
		//设置上传路径	
		$sub_dir = date('Ymd') . '/';	//子目录，日期为文件夹名
		$save_path = $this->_upload_path . $sub_dir;
		if (! is_dir($save_path)) {
			mkdir($save_path);
		}
		//文件地址
		$file_path = $save_path . $file_name;
		//将临时文件移动到上传目录
		$bool = move_uploaded_file($file_info['tmp_name'], $file_path);
		if ($bool) {
			$result = $file_path;
		}else{
			$this->_error_info = '保存文件失败';
			$result = false;
		}
		//上传成功返回保存文件的路径地址，失败返回false
		return $result;
	}
	/**
	 * 将文件后缀转换MIME类
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T17:49:49+0800
	 * @param     array                    $ext_list 需要转换的文件后缀名数组
	 * @return    array                      返回对应的MIME类型数组
	 */
	private function _ext2Mime($ext_list=array())
	{
		$mime_list = array();
		//获取映射列表
		$ext2mime_list = require dirname(__FILE__) . '/ext2mime.php';
		//遍历文件后缀名数组
		foreach ($ext_list as $v) {
			//通过映射列表，获取对应的MIME类型
			$mime_list[] = $ext2mime_list[$v];
		}
		return $mime_list;
	}
}