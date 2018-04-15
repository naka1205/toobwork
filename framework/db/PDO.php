<?php
namespace framework\db;
use framework\interfaces\DAO;
use PDO as MysqlPDO;
use PDOException;
/**
 * 数据库操作对象类
 * @AuthorHTL Naka1205
 * @DateTime  2017-03-19T17:45:03+0800
 */
class PDO implements DAO
{
	private $_host;		//主机地址
	private $_port;		//端口号	
	private $_uname;	//用户名
	private $_pwd;		//密码
	private $_charset;	//字符集
	private $_dbname;	//数据库名

	private $_pdo;		//数据库操作对象
	private static $_instance=NULL;	//数据库操作对象

	const SELECTSQL = "SELECT|SHOW|DESC";
	//const KEYWORDS = '*PRIMARY|AND|OR|LIKE|BINARY|BY|DISTINCT|AS|IN|IS|NULL';
	
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

		$this->_getPDO();
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
		return static::$_instance;
	}
	/**
	 * 获取PDO对象
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:49:42+0800
	 * @return    [type]                   [description]
	 */
	private function _getPDO()
	{	
		$dns = "mysql:host=$this->_host;port=$this->_port;dbname=$this->_dbname;";
		$options = [
			MysqlPDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $this->_charset"
		];
			
		try {
			$this->_pdo = new MysqlPDO( $dns , $this->_uname , $this->_pwd , $options );
		} catch (PDOException $e) {
			echo $e->getMessage();
			// Log::write($e,'error');
			die;
		}
		
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
		if ($bool) {
			$result = $this->_pdo->query($sql);
		}else{
			$result = $this->_pdo->exec($sql);
		}
		if ($result === false) {
			$this->getError($sql);
		}
		return $result;
	}

	/**
	 * 查询所有信息
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:51:37+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function fetchAll($sql){
		$result = $this->execute($sql);
		$rows = $result->fetchAll(MysqlPDO::FETCH_ASSOC);
		$result->closeCursor();
		return $rows;
	}
	/**
	 * 查询一条信息
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:51:50+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function fetchRow($sql){
		$result = $this->execute($sql);
		$row = $result->fetch(MysqlPDO::FETCH_ASSOC);
		$result->closeCursor();
		return $row;
	}
	/**
	 * 查询字段信息
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:51:50+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function fetchRowField($sql){
		$result = $this->execute($sql);
		$rows = $result->fetchColum();
		$result->closeCursor();
		return $rows;
	}
	/**
	 * 获取总记录数
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-21T17:30:24+0800
	 * @return    [type]                   [description]
	 */
	public function getRowCount($sql)
	{
		$result = $this->execute($sql);
		return	$result->fetchColumn();
	}
	public function getInsertId()
	{
		return $this->_pdo->lastInsertId();
	}
	/**
	 * 获取错误信息
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:52:04+0800
	 * @param     [type]                   $sql [description]
	 * @return    [type]                        [description]
	 */
	public function getError($sql)
	{
		$error_info = $this->_pdo->errorInfo();
		//输出错误信息
		echo "SQL语句执行失败：" , "<br>";
		echo "错误的SQL语句：", $sql , "<br>";
		echo "错误的消息为：", $error_info[2] , "<br>";

		// Log::write($error_info,'error');
		die;
	}
	/**
	 * SQL语句转译
	 * @AuthorHTL Naka1205
	 * @DateTime  2017-03-20T00:52:13+0800
	 * @param     string                   $str [description]
	 * @return    [type]                        [description]
	 */
	public function escToStr($str = '')
	{
		return $this->_pdo->quote($str);
	}
 
}
