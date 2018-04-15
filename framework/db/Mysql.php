<?php
namespace framework\db;
use framework\interfaces\DAO;
/**
 * 数据库操作类
 * @AuthorHTL  naka1205
 * @DateTime  2016-04-13T20:00:01+0800
 */
class Mysql implements DAO
{
	private $_host;		//主机地址
	private $_port;		//端口号	
	private $_uname;	//用户名
	private $_pwd;		//密码
	private $_charset;	//字符集
	private $_dbname;	//数据库名

	private $_link;		//连接资源

	const SELECTSQL = "SELECT|SHOW|DESC";

	private static $_instance=NULL;	//数据库操作对象
	/**
	 * 数据库操作构造函数
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T20:39:50+0800
	 * @param     array                    $config 数据库配置信息
	 */
	private function __construct($config=array())
	{			
		$this->_host = isset($config['host']) ? $config['host'] : 'localhost';
		$this->_port = isset($config['port']) ? $config['port'] : '3306';
		$this->_uname = isset($config['uname']) ? $config['uname'] : '';
		$this->_pwd = isset($config['pwd']) ? $config['pwd'] : '';
		$this->_charset = isset($config['charset']) ? $config['charset'] : 'UTF8';
		$this->_dbname = isset($config['dbname']) ? $config['dbname'] : 'test';
		$this->_linkDB();
		$this->_setCharset();
		$this->_selectDB();
	}
	private function __clone(){}

	/**
	 * 初始化数据库对象
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T21:00:06+0800
	 * @param     array                  $config 参数：主机地址[host]，端口号[port=3306]，用户名[uname]，密码[pwd]，字符集[charset=UTF8]，数据库名[charset=test]
	 * @return    object              数据库操作对象      
	 */
	public static function init($config)
	{	
		//判断是否已存在示例对象
		if( !(static::$_instance instanceof static) ) {
			static::$_instance = new static($config);
		}
		return	static::$_instance;
	}
	/**
	 * 数据库连接
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T20:43:20+0800
	 */
	private function _linkDB()
	{	
		//使用配置信息连接数据库
		$result = mysql_connect("$this->_host:$this->_port",$this->_uname,$this->_pwd);
		//判断连接状态
		if ($result) {
			//将连接资源赋值给成员属性
			$this->_link = $result;
		}else{
			echo "数据库连接失败，请检查数据库配置信息！";
			die;
		}
	}
	/**
	 * 设置字符集
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T21:26:39+0800
	 */
	private function _setCharset()
	{
		$sql="SET NAMES $this->_charset";
		$this->execute($sql);
	}
	private function _selectDB(){
		$sql="USE `$this->_dbname`";
		$this->execute($sql);
	}
	/**
	 * 检测SQL语句
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T01:52:46+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function checkSQL($sql)
	{
		$sql = ltrim($sql);
		$arr = explode ( " " ,  $sql );
		if ( strpos( self::SELECTSQL, strtoupper($arr[0])) !== false) {
			return true;
		}
		return false;
	}

	/**
	 * 执行SQL语句
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:51:56+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function execute($sql)
	{
		//检测SQL语句
		$bool = $this->checkSQL($sql);
		$result = mysql_query($sql,$this->_link);
		if ($result === false) {
			$this->getError($sql);
		}
 		if ( !$bool) {
 			return mysql_affected_rows($this->_link);
 		}
		return $result;
	}

	/**
	 * 查询所有信息
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-02T21:19:10+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function fetchAll($sql){
		$result = $this->execute($sql);
		$list = array();
		while ($rows=mysql_fetch_assoc($result)) {
			$list[] = $rows;
		}
		mysql_free_result($result);
		return $list;
	}
	/**
	 * 查询一条信息
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-02T21:19:32+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function fetchRow($sql){
		$result = $this->execute($sql);
		$list = mysql_fetch_assoc($result);
		mysql_free_result($result);
		return $list;
	}

	public function getInsertId()
	{
		return mysql_insert_id($this->_link);
	}
	/**
	 * 获取错误信息
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-02T21:33:42+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function getError($sql)
	{
		//输出错误信息
		echo "SQL语句执行失败：" , "<br>";
		echo "错误的SQL语句：", $sql , "<br>";
		echo "错误的消息为：", mysql_error($this->_link) , "<br>";
		die;
	}
	/**
	 * SQL语句转译
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T20:30:12+0800
	 * @param     string                   $str 需要转译的字符串
	 * @return      string                    转译后的字符串
	 */
	public function escToStr($str = '')
	{
		return "'" . mysql_real_escape_string($str,$this->_link) . "'";
	}
}
