<?php
namespace framework\lib;

use framework\db\PDO;
use framework\db\Mysql;

/**
* 基础模型类
* @AuthorHTL  naka1205
* @DateTime  2016-04-13T20:00:01+0800
*/
class Model
{
	protected $_DB;	//数据库操作对象
	protected $tabelName;		//数据库表
	protected $modleName;		//当前模型
	public $autotime = false;
	public $tabel = '';	//操作数据
	public $name = '';	//操作数据
	public $data = array();	//操作数据
	public $option=array(			//查询条件
			'in' 	=> "",			//集合
			'join' 	=> "",			//连表
			'field' => "",			//字段名
			'where' => "",			//条件
			'order' => "",			//排序
			'limit' => ""			//限制行
	);
	protected $_config;
	/**
	 * 构造方法
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T22:18:17+0800
	 */
	public function __construct()
	{
		$this->_config = config('mysql',true);
		
		//初始化数据库连接
		$this->_initDB();
		//设置操作表名
		$class_name = get_called_class();
		$class_name = strstr( $class_name , 'model\\');
		$modle_name = substr(strstr( $class_name , '\\'),1);
		$this->modleName = strtolower($modle_name);
		$this->tabelName = !empty($this->name) ? $this->_config['prefix'] . $this->name : $this->_config['prefix'] . $this->modleName;
	}
	/**
	 * 初始化数据库操作连接
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-13T22:15:20+0800
	 */
	protected function _initDB()
	{
		if ( extension_loaded('pdo') ) {
			$this->_DB = PDO::init($this->_config);
		}else{
			$this->_DB = Mysql::init($this->_config);
		}
		
	}
	/**
	 * 转译数组内所有元素值
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-27T20:19:44+0800
	 * @param     array                    $data 需要转译的数据
	 */
	protected function _escArray($data = array())
	{
		$res = array();
		foreach ($data as $key => $value) {
			$res[$key] = $this->_DB->escToStr($value);
		}
		return $res;
	}	
	/**
	 * 获取多行数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:21:42+0800
	 */
	public function select(){
		$field = !empty($this->option['field']) ? $this->option['field'] : "*";
		$in  = !empty($this->option['in']) ?  $this->option['in'] : "";
		$join  = !empty($this->option['join']) ?  " as a ".$this->option['join'] : "";
		$where = !empty($this->option['where']) ?  " where ".$this->option['where'] : "";
		$order = !empty($this->option['order']) ?  " order by ".$this->option['order'] : "";
		$limit = !empty($this->option['limit']) ?  " limit ".$this->option['limit'] : "";

		if ( !empty($where) && !empty($in) ) {
			$in = $this->option['in'];
			$where = $where . ' AND ' . $in;
		}elseif ( !empty($in) ) {
			$where = " where ". $in;
		}

		//将条件初始化
		foreach ($this->option as &$v) {
			$v="";
		}

		$sql = "SELECT $field FROM {$this->tabelName} $join $where $order $limit";

		return	$this->_DB->fetchAll($sql);
	}
	/**
	 * 获取一行数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:21:28+0800
	 */
	public function find( $id = 0 )
	{
		$field = !empty($this->option['field']) ? $this->option['field'] : "*";
		$join  = !empty($this->option['join']) ?  " as a ".$this->option['join'] : "";
		$where = !empty($this->option['where']) ?  " where ".$this->option['where'] : "";

		if ( $id > 0 && empty($join)) {
			$where = empty($where) ? 'where id = ' . $id :  $where . 'AND id = ' . $id;
		}else if( $id > 0 && !empty($join) ){
			$where = empty($where) ? 'where a.id = ' . $id :  $where . 'AND a.id = ' . $id;
		}
		//将条件初始化
		foreach ($this->option as &$v) {
			$v="";
		}
		$sql = "SELECT $field FROM {$this->tabelName} $join $where LIMIT 1";
		return	$this->_DB->fetchRow($sql);
	}
	/**
	 * 获取字段值
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:22:07+0800
	 */
	public function value()
	{		
		$field = !empty($this->option['field']) ? $this->option['field'] : "*";
		$row = $this->find();
		return	$row[$field];
	}

	/**
	 * 获取总数
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-22T13:09:16+0800
	 */
	public function count()
	{
		$where = !empty($this->option['where']) ?  " where ".$this->option['where'] : "";
		$sql = "SELECT COUNT(*) FROM {$this->tabelName} $where";
		return	$this->_DB->getRowCount($sql);	
	}

	public function max($field = 'id')
	{
		$sql = "SELECT MAX($field) FROM {$this->tabelName}";
		return	$this->_DB->getRowCount($sql);			
	}

	public function query($sql)
	{
		return $this->_DB->execute($sql);
	}
	/**
	 * 更新数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T12:29:53+0800
	 */
	public function update($data=[])
	{
		if (empty($data)) {
			$data = $this->data;	
		}

		if ($this->autotime) {
			$data['update_time'] = time();
		}

		$where = !empty($this->option['where']) ?  " WHERE ".$this->option['where'] : "";

		//判断是否设置条件
		if (empty($where)) {
			//获取操作数据ID
			$pk = $data['id'] ;
			$where = " WHERE id=$pk";
			unset($data['id']);
		}
		$data_str = '';
		$data = $this->_escArray($data);	
		foreach ($data as $key => $value) {
			$data_str .=" `$key` = $value ,";
		}
		$data_str = rtrim($data_str,',');
		$sql = "UPDATE {$this->tabelName} SET $data_str $where";
		return $this->_DB->execute($sql);
	}
	/**
	 * 保存数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T12:29:25+0800
	 */
	public function add($data = [])
	{
		if (empty($data)) {
			$data = $this->data;	
		}

		if ($this->autotime) {
			$data['create_time'] = time();
		}
		
		$data_str = '';
		$field_str = '';
		//转译操作数据
		$data = $this->_escArray($data);	
		//拼接操作数据
		foreach ($data as $key => $value) {
			$field_str .= " `$key` ,";
			$data_str .= " $value ,";
		}
		$field_str = rtrim($field_str,',');
		$data_str = rtrim($data_str,',');
		$sql = "INSERT INTO {$this->tabelName} ($field_str) VALUES ($data_str)";
		$result = $this->_DB->execute($sql);
		if ($result) {
			$result = $this->_DB->getInsertId();
		}
		return $result;
	}
	/**
	 * 删除数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T12:29:37+0800
	 */
	public function delete()
	{
		$data = $this->data;	
		$where = !empty($this->option['where']) ?  " WHERE ".$this->option['where'] : "";
		//判断是设置条件
		if (empty($where)) {
			//获取操作数据ID
			$pk = $data['id'];
			$where = " WHERE id=$pk";
		}
		$sql = "DELETE FROM {$this->tabelName} $where";
		return $this->_DB->execute($sql);
	}
	/**
	 * 设置操作数据
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T21:15:16+0800
	 * @param     [type]                   $data [description]
	 */
	public function data($data)
	{
		$this->data = $data;
		return $this;
	}
	/**
	 * 设置查询字段
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:22:46+0800
	 * @param     [type]                   $field [description]
	 */
	public function field($field){
		$this->option['field'] = $field;
		return $this;
	}
	/**
	 * 设置查询条件
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:23:05+0800
	 * @param     [type]                   $where [description]
	 */
	public function where($where){
		if ( is_array($where) && !empty($where) ) {
			$where_str = '';
			$i = 0;
			// if ($this->modleName == 'posts') {
			// 	dump();die;
			// }
			foreach ($where as $key => $value) {
				if ( $i > 0) {
					$where_str .=  " AND ";
				}

				if ( stristr($value,'like') === false) {
					$where_str .= !empty($this->option['join']) ? 'a.' . $key . "='" . $value ."'" :  $key . "='" . $value ."'";
				}else{
					$where_str .= !empty($this->option['join']) ? 'a.' . $key . " " . $value :  $key . " " . $value;
				}
				$i++;
			}
			$where = $where_str;
		}

		$this->option['where'] = $where;
		return $this;
	}
	/**
	 * 设置排序
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:23:20+0800
	 * @param     [type]                   $order [description]
	 */
	public function order($order){
		$this->option['order'] = $order;
		return $this;
	}
	/**
	 * 限制行数
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-03T10:23:37+0800
	 * @param     [type]                   $limit [description]
	 */
	public function limit( $limit, $offset = 0){
		$this->option['limit'] = $offset . ',' .  $limit;
		return $this;
	}

	/**
	 * 连表操作
	 * @AuthorHTL
	 * @DateTime  2017-08-29T20:38:21+0800
	 * @param     [type]                   $name [description]
	 * @param     boolean                  $left [description]
	 */
	public function join($name,$left = true)
	{

		$join = '';
		$mode = $left ? ' left ' : ' right ';
		if ( is_array($name)) {
			foreach ($name as $key => $value) {
				$tabel = strtolower($value);
				$join .= " $mode join " . $this->_config['prefix'] . $tabel . ' as b'.$key.' on a.id=b'.$key.'.'. $this->modleName .'_id';
			}
		}else{
			$tabel = strtolower($name);
			$join = " $mode join " . $this->_config['prefix'] . $tabel . ' as b on a.id=b.'. $this->modleName .'_id';
		}

		$this->option['join'] = $join;
		return $this;
	}

	public function in($name,$id = [])
	{
		if (!empty($id) && is_array($id)) {
			$id_str = implode ( "," ,  $id );
		}
		$this->option['in'] = $name . ' IN (' . $id_str .') ';
		return $this;
	}

	public function getErrorInfo()
	{
		return $this->_DB->getError();
	}
}