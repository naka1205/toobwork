<?php
namespace extend\verify;
/**
* 验证码工具类
 * @AuthorHTL naka1205
 * @DateTime  2016-04-24T11:20:57+0800
*/
class Verify
{
	public $file_path;
	public function __construct()
	{
		$this->file_path = dirname(__FILE__) . "/resource/";
	}
	/**
	 * 生成验证码，并输出验证码图片
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T11:20:57+0800
	 * @param     int                  $code_length 验证码长度
	 */
	public function getCode($code_length = 4)
	{
		$char_list = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
		$list_length = strlen($char_list);	//字符长度
		//$code_length = 4;	//验证码长度
		$code = '';		//初始化验证码

		for ($i = 1; $i <= $code_length; $i++) { 
			$rang_index = mt_rand(0,$list_length-1);	//随机获取字符中字符
			$code .= $char_list[$rang_index]; //拼接字符，生成验证码
		}
		@session_start();
		$_SESSION['captca_code'] = $code;
		//随机获取背景图片地址
		$bg_file = $this->file_path . mt_rand(1,8) . '.jpg';	
		//根据背景图片创建布画
		$image = imagecreatefromjpeg($bg_file);

		//分配颜色
		if (mt_rand(1,3) >= 2) {	
			//三分之二概率为黑色
			$code_color = imagecolorallocate($image, 0, 0, 0);
		}else{
			//三分之一概率为白色色
			$code_color = imagecolorallocate($image, 255, 255, 255);
		}
		//字体大小
		$font = 5;
		//计算位置
		//布画高、宽
		$image_w = imagesx($image);
		$image_h = imagesy($image);
		//字体高、宽
		$font_w = imagefontwidth($font);
		$font_h = imagefontheight($font);
		//字符串高、宽
		$code_w = $font_w * $code_length;
		$code_h = $font_h;
		//X坐标
		$str_x = ($image_w - $code_w) / 2;
		//Y坐标
		$str_y = ($image_h - $code_h) / 2;
		//写入字符串
		imagestring($image, $font, $str_x, $str_y, $code, $code_color);
		//输出布画
		header('Content-type:image/jpeg');
		imagejpeg($image);
		//销毁布画
		imagedestroy($image);
	}
	/**
	 * 自定义验证码
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T14:36:49+0800
	 * @param     int                  $style      字符样式
	 * @param     int                  $code_length 字符长度
	 * @param     int                  $image_w     图片宽度
	 * @param     int                  $image_h      图片高度
	 */
	public function makeImage($style = 1,$code_length = 4,$image_w = 100,$image_h = 30 )
	{	
		//判断验证码字符类型
		switch ($style) {
			case  1:
				$str="qwertyuipasdfhjkzxcvbnmQWERTYUPASDFGHJKLZXCVBNM";
				break;
			case  2:
				$str="1234567890";
				break;
			case  3:
				$str="qwertyuipasdfhjkzxcvbnmQWERTYUPASDFGHJKLZXCVBNM23456789";
				break;
			default:
				$str="1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLMNCBVXZ";
				break;
		}
		//随机拼接字符串
		$code="";
		//初始化字符串数组
		$code_arr=array();
		for ($i = 0; $i < $code_length; $i++) { 
			$code_tmp=$str[rand(0,strlen($str)-1)];
			$code.=$code_tmp;
			$code_arr[]=$code_tmp;	
		}
		@session_start();
		$_SESSION['captcha_code'] = $code;
		//创建布画
		$image=imagecreate($image_w, $image_h);
		// 设置画布颜色
		imagecolorallocate($image, 255, 245, 245);
		// 画点
		for($i = 0;$i < 30;$i++){
			$c=imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
			imagesetpixel($image, rand(0,$image_w), rand(0,$image_h), $c);
		}
		// 画线
		for($i = 0;$i < 5;$i++){
			$c=imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
			imageline($image, rand(0,$image_w), rand(0,$image_h), rand(0,$image_w), rand(0,$image_h), $c);
		}
		//写入验证码字符串
		//获取字体文件地址
		$font_file = $this->file_path . mt_rand(1,6) . '.ttf';
		//循环字符串数组
		foreach ($code_arr as $k=>$v) {
			$c=imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
			imagefttext($image, rand(18,20), rand(-20,20), 5+$k*18, rand(24,26), $c,$font_file, $v);
		}
		// 输出画布到浏览器
		header("content-type:image/jpeg");
		imagegif($image);
		//销毁布画
		imagedestroy($image);
	}
	/**
	 * 验证码校验
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T12:24:31+0800
	 * @param     string                   $check_code 用户验证码
	 * @return    bool                       如果用户验证码与系统验证码一致则返回真，否则返回假
	 */
	public function checkCode($check_code='')
	{
		$result = false;
		@session_start();
		//判断是否系统设置验证码，并且提交用户验证码
		if (isset($_SESSION['captcha_code']) && isset($check_code) ) {
			$captcha_code=$_SESSION['captcha_code'];
			//转换验证码为小写，并进行对比
			$result = strtoupper($captcha_code) == strtoupper($check_code);
			//销毁系统验证码
			unset($_SESSION['captcha_code']);
		}
		return $result;
	}

	public function number()
	{
		$num1 = rand(1,10);
 		$num2 = rand(1,10);

 		$result = $num1 + $num2;

 		$_SESSION['check_num'] = $result;

 		return $num1 . '+' . $num2 . '=?';

	}

	public function checkNum($check_code='')
	{
		$result = false;

		//判断是否系统设置验证码，并且提交用户验证码
		if (isset($_SESSION['check_num']) && isset($check_code) ) {
			$check_num = $_SESSION['check_num'];
			//转换验证码为小写，并进行对比
			$result = intval($check_num) == intval($check_code);
			//销毁系统验证码
			unset($_SESSION['check_num']);
		}
		return $result;
	}
}