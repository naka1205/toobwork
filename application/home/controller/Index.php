<?php
namespace home\controller;
use framework\lib\Controller;
use framework\lib\Request;
use framework\lib\Page;

use home\model\Demo;

class Index extends Common
{	
	public function index()
	{
		echo 'HELLO WORLD!';
	}

	public function demo()
	{

		//获取GET或者POST提交的数据,并调式打印输出
		$request = Request::instance();
		$param = $request->param();
		dump($param);

		//数据分页的示例
		//所有的用户自定义数据表模型必须继承基础模型Model
		//模型的定义请参考Demo模型类
		$request = Request::instance();
		$param = $request->param();
		//获取请求的分页变量，如果不存在则为第一页
		$param['p']  = isset($param['p']) && $param['p'] > 0 ? $param['p'] : 1;

		//限制每页10条数据
		$limit = 10;
		$offset = ( $param['p']  - 1 ) * $limit;
		//根据字段create_time，id进行排序，查询status字段值为1的第一页数据
		$demo = new Demo;
		$where['status'] = 1;
		$reslut = $demo->where($where)->limit($limit,$offset)->order('create_time desc,id desc')->select();
		//取得满足条件的总数据
		$count = $demo->where($where)->count();
		//获取分页实例，传入参数
		$page = new Page($count,$limit,$param);
		$pages = $page->show();

		//将分页按钮展示在模板，这里的$this为当前的控制器的实例
		$this->assign('pages',$pages);
		//模板展示，参数为模板名称，默认为方法名
		$this->display();

		//数据库操作的示例
		//数据模型写入一条数据,并返回新增id
		$demo = new Demo;
		$data = [
			'name'  => 'demo',
			'status'    => 1
		];
		$model_id = $demo->add($data);

		//数据模型字段name为demo的数据修改字段status为1
		$demo = new Demo;
		$where['name'] = 'demo';
		$data = [
			'status'    => 1
		];
		$demo->where($where)->update($data);

		//数据模型获取字段status为1的多条数据
		$demo = new Demo;
		$where['status'] = 1;
		$reslut = $demo->where($where)->select();

		//数据模型获取字段id为1的一条数据，并过滤字段
		$demo = new Demo;
		$reslut = $demo->field('name,status')->find(1);

		//数据模型获取字段id为1的一条数据中字段name的值
		$demo = new Demo;
		$where['id'] = 1;
		$reslut = $demo->where($where)->value('name');

		//数据模型获取字段id的最大值
		$demo = new Demo;
		$reslut = $demo->max();
		
		//数据缓存的示例

		//设置key为config的缓存，有效时间为60秒
		$config = [
			'name' => 'demo',
			'status' => 1
		];
		Cache::set('config',$config,60);

		//判断缓存是否存在或有效，返回布尔值
		$bool = Cache::has('config');

		//获取key为config的缓存
		$config = Cache::get('config');

		//清除所有缓存
		Cache::clear();
	}
}