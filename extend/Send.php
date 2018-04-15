<?php
namespace extend;

use extend\PHPMailer\PHPMailer;

/**
* 验证发送类
*/
class Send
{
	public $config;	
	public function __construct($configs = [])
	{
		$this->config = $configs;
	}

	public function regCheck($address,$code)
	{
        $mail = new PHPMailer;
        $mail->CharSet ="utf-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;// 启用SMTP调试功能1 = errors and messages 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = "SMTP";                 // 安全协议
        $mail->Host       = $this->config['host'];      // SMTP 服务器
        $mail->Port       = $this->config['port'];                   // SMTP服务器的端口号
        $mail->Username   = $this->config['username'];  // SMTP服务器用户名
        $mail->Password   = $this->config['password'];          // SMTP服务器密码
        $mail->SetFrom($this->config['from'],$this->config['name']);
        $mail->AddReplyTo($this->config['from'],$this->config['name']);//增加回复标签，参数1地址，参数2名称

        $url = 'http://www.toobwork.com/check.html?code=' . $code;
        $content = $this->config['check'];
        $content = str_ireplace('{#URL#}', $url, $content);

        $subject = $this->config['name'] . '--注册验证';

        $mail->Subject = $subject;  //主题
        $mail->MsgHTML($content); //正文  支持html格式
        $mail->AddAddress($address, " ");//增加收件人 参数1为收件人邮箱，参数2为收件人称呼
        if(isset($attachment_dir)){
            $mail->AddAttachment($attachment_dir);//附件的路径和附件名
        }   
        return $mail->Send(); 	
	}
}