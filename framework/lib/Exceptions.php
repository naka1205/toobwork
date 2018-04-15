<?php
namespace framework\lib;

use Exception;

class Exceptions extends Exception
{

    private $httpCode = [
        // 缺少参数或者必传参数为空
        400 => 'Bad Request',
        // 没有访问权限
        403 => 'Forbidden',
        // 访问的资源不存在
        404 => 'Not Found',
        // 代码错误
        500 => 'Internet Server Error',
        // Remote Service error
        503 => 'Service Unavailable'
    ];

    public function __construct($code = 200, $extra = '')
    {

        $this->code = $code;
        if (empty($extra)) {
            $this->message = $this->httpCode[$code];
            return;
        }

        $this->message = $extra . ' ' . $this->httpCode[$code];
        $this->reponse();
    }

    public function reponse()
    {
        $data = [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
            'result'  => ''
        ];
        $infomations  = [
            'file'  => $this->getFile(),
            'line'  => $this->getLine(),
            'trace' => $this->getTrace()
        ];
        Log::write($data,'error');
        Log::write($infomations,'error');

        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public static function reponseErr($e)
    {
        $data = [
            'code'    => 500,
            'message' => $e,
            'result'  => ''
        ];
        $infomations  = [
                    'file'  => $e['file'],
                    'line'  => $e['line']
        ];

        Log::write($data,'error');
        Log::write($infomations,'error');

        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode($data));
    }    

}