 TOOBWORK
===============
## 介绍
这是一个个人独立开发的MVC框架。希望对一些正在学习开发自己的框架的程序员能有所帮助。
很多人会说，市面上有那么多优秀的框架不用，要自己写干什么，更何况自己写的根本用不到项目的实际开发中。
其实我个人认为，虽然这是重复造轮子，但这并不重要，重要的是，在这个框架开发过程中所学到的编码技术，对面向对象的理解，让我对自己本身的技术提高了不少，也体会到面向对象编程的乐趣。
一开始我并不打算把代码开源，原因是我认为写得并不怎么样，甚至说和如今流行的WEB技术框架相比，这还是个入门级的。
在一次工作面试中，一个面试官的鼓励给了我勇气，所以我决定开源它。

## 设计架构
~~~
application                     -框架应用目录
	/home		            -PC端模块
		/controller             -控制器
		/model                  -数据模型
		/view                   -视图模板
		common.php	        -模块公共函数
		config.php		-模块配置项					
command				-命令行目录
	Demo.php                    -命令行测试
config			        -配置文件目录
	mysql.php                   -数据库配置
	default.php                 -框架默认配置
	route.php                   -路由配置
	log.php           	    -日志配置
extend				-类库扩展目录	
	/smarty			    -Smarty模板引擎	
	/upload			    -文件上传类
	/verify			    -验证码类
	Http.php		    -HTTP类
	Image.php		    -图片处理类
framework			-框架目录
	/interfaces		    -接口类库
		DAO.php 	        -数据库操作接口
	/lib			    -框架核心类库
		Application.php		-应用处理类
		Controller.php		-基础控制器类
		Model.php		-基础模型类
		View.php		-视图渲染类
		Exceptions.php		-异常错误类
		Factory.php		-工厂类
		MYSQLDB.php             -MYSQL操作类
		PDODB.php               -PDO操作类
		Log.php                 -日志类
		Route.php		-HTTP路由类
		Request.php		-请求操作类
		Page.php		-分页处理类
		Loader.php		-自动加载类
	helper.php 		    -框架助手函数
	initialize.php 		    -初始化配置
	start.php 		    -应用启动
public			        -静态文件目录
	/home			    -前台公共文件目录	
	/upload                     -上传文件目录
	index.php                   -入口文件
run				-运行文件目录
	/cache			    -缓存文件目录	
	/log			    -日志文件目录	
	/temp			    -临时文件目录
toob				-命令行入口文件		
~~~
## 更新日志
* 2016-05-23
*   更新日志：
*   1.将核心类文件列表归档整理：class.config；
*   2.优化MYSQL操作类；
*   3.添加PC端模块；
*   4.添加模块配置文件；
*   5.将自动加载移入框架公共函数：common.php；
*   6.添加模块公共函数：common.php；
*   7.强化视图渲染类，引入Smarty；
*   8.添加运行文件目录。
* 2016-06-27
*   更新日志：
*   1.加入应用处理类，将入口文件代码整理归类；
*   2.加入路由处理类，框架支持伪静态,采用全路径路由，支持正反向解析；
*   3.加入URI处理类，获取URL参数专用；
*   4.加入数据验证类，变量输入过滤；
*   5.加入日志类；
*   6.加入分页处理类；
*   7.加入数据采集类；
*   8.去除控制器内方法名后缀-Action；
*   9.优化模块URL地址，统一采用静态地址；
*   10.更新前端页面内容输出。
* 2017-03-20
*   更新日志：
*   1.优化自动加载、基础模型类等；
*   2.加入DAO层数据库操作驱动，MYSQL操作类、PDO操作类自动调配；
*   3.加入run.php框架运行初始化文件，入口文件可以灵活设置。
* 2017-08-24
*   更新日志：
*   1.添加命名空间；
*   2.整理框架核心类库；
*   3.添加扩展类库；
*   4.添加框架助手函数：helper.php；
*   5.移除框架公共函数：common.php；
*   6.修改run.php为start.php；
*   7.优化基础模型类；
*   8.删除控制器后缀名;
*   9.删除模型后缀名;
*   10.删除配置文件后缀。
* 2017-09-13
*   更新日志：
*   1.调整框架目录；
*   2.增加命令行的引用。
* 2017-11-18
*   更新日志：
*   1.优化分页处理；
*   2.增加缓存操作，默认文件缓存；
*   3.增加模块配置示例；
*   4.增加控制器、模型定义的示例以及框架其它操作的示例。
