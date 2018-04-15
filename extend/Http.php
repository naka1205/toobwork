<?php
namespace extend;
/**
* HTTP工具类
*/
class Http
{
	public $request = null;
	public $html = null;
	/**
	 * 构造函数，构造HTTP请求结构
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-08T15:49:32+0800
	 * @param     array                    $info location:请求地址，host：主机地址，method：请求方式
	 */
	public function __construct($info=array())
	{
		//构造HTTP请数据
		$this->request = $info['method'] . " " . $info['location'] . " HTTP/1.1" ."\r\n";
		$this->request .= "Host: " . $info['host'] ."\r\n";
		$this->request .= "Mozilla/5.0 (Windows NT 5.1; rv:46.0) Gecko/20100101 Firefox/46.0"."\r\n";
		$this->request .= "Connection: close"."\r\n";
		if ($info['method'] == "POST") {
			$post_data = http_build_query($info['data']);
			$this->request .= "Content-Type:application/x-www-form-urlencoded"."\r\n";
			$this->request .= "Content-Length:".strlen($post_data)."\r\n";
		}
		$this->request .= "\r\n";
		//请求主体
		//如果是GET，为空
		if ($info['method'] == "POST") {
			$this->request .= $post_data;
		}

		//建立TCP连接
		$link = fsockopen($info['address'],'80');	//获取资源
		if (! $link) {
			return "请求服务器失败";die;
		}
		//发送请求
		fwrite($link, $this->request);
		while(! feof($link))
		{
			$this->html .= fgets($link,1025);
		}
		fclose($link);
	}

	public function get()
	{	
		return $this->html;		
	}
	/**
	 * 获取采集地址
	 * @AuthorHTL naka1205
	 * @DateTime  2016-05-08T16:25:47+0800
	 * @param     array      $info pattern:匹配规则,path:路径,file:文件名,prefix:前缀(信息保存),postfix:后缀(信息保存)
	 */
	public function capture($info=array())
	{
		$subject = $this->html;
		$pattern = $info['pattern'];
		$path = $info['path'] ? $info['path'] : './public/capture/'.date('Y-m-d') . '/';
		$file = $path . $info['file'] . ".txt";
		$prefix = $info['prefix'];
		$postfix = $info['postfix'];
		$type = $info['type'] ? $info['type'] : 'all';
		//文件路径
		if (! is_dir($path)) {
			mkdir($path);
		}
		$handle = fopen($file, 'a'); //追加写
		if ($type == "all") {
			preg_match_all($pattern,$subject,$matches);
			foreach ($matches[1] as $v) {
				$str .= $prefix . $v . $postfix;
				fwrite($handle, $str."\r\n");
			}			
		}
		else{
			preg_match($title_preg,$subject,$matches);
			$str = $prefix . $matches[1]. $postfix;
			fwrite($handle, $str."\r\n");
		}

		fclose($handle);
	}
}