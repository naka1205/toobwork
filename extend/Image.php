<?php
namespace extend;
/**
* 图片处理类
*/
class Image
{
	public $file;		//原图资源
	public $image;	//图片资源
	public $path;		//保存路径
	public $water = './public/admin/image/logo.png';	//水印地址
	public $method;	//处理方式
	public function __construct($file_path='')
	{
		//header('Content-type:image/jpg');
		$file_info = getimagesize($file_path);
		$mime = $file_info['mime'];
		$method_list = array(
			'image/jpeg' => 'imagecreatefromjpeg',
			'image/png' => 'imagecreatefrompng',
			'image/gif' => 'imagecreatefromgif',
			);
		$method = $method_list[$mime];
		$this->flie = $method($file_path);
		$this->path = './public/upload/gallery/';
		

	}
	/**
	 * 设置文件上传的路径
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-24T18:57:11+0800
	 * @param     string                   $upload_path 默认为'./'目录根
	 */
	public function setPath($upload_path = './public/upload/gallery/')
	{	
		if (is_dir($upload_path)) {
			$this->path = $upload_path;
		}		
	}	
	/**
	 * 创建布画
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-28T20:47:48+0800
	 * @param     [type]                   $width    布画宽
	 * @param     [type]                   $height   布画高
	 * @param     array                    $bg_color 背景色
	 */
	public function create($width,$height,$bg_color=array())
	{
		$thumb_img = imagecreatetruecolor($width,$height);
		if (! empty($bg_color)) {
			//填充布画背景色
			$bg_color = imagecolorallocate($thumb_img, $bg_color[0] ,$bg_color[1] , $bg_color[2] );//设置颜色  白色
			imagefill($thumb_img, 0, 0, $bg_color);
		}
		$this->image = $thumb_img;
	}
	/**
	 * 获取缩略图
	 * @AuthorHTL naka1205
	 * @DateTime  2016-04-28T20:27:44+0800
	 * @param     int                  $thumb_w 缩略图宽
	 * @param     int                   $thumb_h 缩略图高
	 * @param     bool                  $ratio 是否等比例
	 */
	public function getThumb($thumb_w,$thumb_h,$flie_name,$ratio=false)
	{
		//获取画布宽与高
		$src_w = imagesx($this->flie); 
		$src_h = imagesy($this->flie); 
		//创建缩略图画布
		$this->create($thumb_w,$thumb_h,array(255,255,255));

		//图片采样、复制原图、调整大小
		//原图参数
		$src_dst_x = 0;	//原图X坐标
		$src_dst_y = 0;	//原图X坐标
		$src_dst_w = $src_w;	//原图宽
		$src_dst_h = $src_h;	//原图高

		if ($ratio) {
			//计算有效区域的宽与高
			//等比例缩放   比较原图与缩略图的高、宽的缩放比例
			if ($src_w/$thumb_w > $src_h/$thumb_h) 
			{
				//缩放比例宽大于高
				$thumb_dst_w = $thumb_w;
				$thumb_dst_h = $src_h/$src_w * $thumb_dst_w;
			}else
			{
				//缩放比例高大于宽
				$thumb_dst_h = $thumb_h;
				$thumb_dst_w = $src_h/$src_w * $thumb_dst_h;
			}
			//复制区域 计算位置
			$thumb_dst_x = ($thumb_w - $thumb_dst_w) / 2;//缩略图X坐标 缩略图的宽 - 缩略图有效区宽 / 2
			$thumb_dst_y = ($thumb_h - $thumb_dst_h) / 2;	//缩略图Y坐标 缩略图的高 - 缩略图有效区高 / 2
		}
		else{
			//缩略图参数
			$thumb_dst_x = 0;	//缩略图X坐标
			$thumb_dst_y = 0;	//缩略图X坐标
			$thumb_dst_w = $thumb_w;	//缩略图宽
			$thumb_dst_h = $thumb_h;	//缩略图高
		}

		imagecopyresampled($this->image,$this->flie, $thumb_dst_x, $thumb_dst_y, $src_dst_x, $src_dst_y, $thumb_dst_w, $thumb_dst_h, $src_dst_w, $src_dst_h);
		//输出布画
		//imagejpeg($this->image);
		//保存缩略图
		$sub_dir = date('Ymd') . '/';	//子目录，日期为文件夹名
		$save_path = $this->path . $sub_dir;
		if (! is_dir($save_path)) {
			mkdir($save_path);
		}
		$file_ext = substr(strrchr($flie_name, '.'), 1);
		$save_name = 'small_' . basename($flie_name,".".$file_ext) . '.' . $file_ext;
		imagejpeg($this->image,$save_path.$save_name);
		return $save_path.$save_name;
	}
	public function waterMark()
	{

	}
	public function __destruct()
	{
		//关闭布画
		imagedestroy($this->flie);
		imagedestroy($this->image);
	}

}