<?php
namespace framework\lib;

class Page{


    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 11;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;

    // 分页显示
    public $config  = array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        'theme'  => '%HEADER% %FIRST% %NOW_PAGE% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    );
     //按钮模板
    public $template  = array(
        'num'       => '<li class="%STYLE%"><a  href="%HREF%">%TEXT%</a></li>',
        'prev'       => '<li class="%STYLE%"><a  href="%HREF%">%TEXT%</a></li>',
        'next'        => '<li class="%STYLE%"><a  href="%HREF%">%TEXT%</a></li>',
        'first'         => '<li class="%STYLE%"><a  href="%HREF%">%TEXT%</a></li>',
        'last'         => '<li class="%STYLE%"><a  href="%HREF%">%TEXT%</a></li>',
        'current'   => '<li class="%STYLE%">%TEXT%</li>',
    );  
     
    //按钮样式
    public $style  = array(
        'num'         => 'num',
        'prev'      => 'prev',
        'next'      => 'next',
        'first'     => 'first',
        'last'      => 'last',
        'current'     => 'current',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()) {
        //设置分页参数
        $config = config('page');
        if (isset($config['p'])) {
            $this->p = $config['p']; 
        }
        if (isset($config['roll'])) {
            $this->rollPage = $config['roll']; 
        }
        if (isset($config['config'])) {
            $this->config = array_merge($this->config, $config['config']); 
        }
        if (!empty($config['template'])) {
            $this->template = array_merge($this->template, $config['template']); 
        }
        if (!empty($config['style'])) {
            $this->style = array_merge($this->style, $config['style']); 
        }


        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        if ( !isset($_GET[$this->p]) || !isset($parameter[$this->p])) {
            $_GET[$this->p] = 1;
        }
        $this->nowPage    = !isset($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page,$bool = false){

 
            if ( $bool ) {
                    $url =   $_SERVER['REQUEST_URI'];

                    $url = str_replace('.html', '',$url );
                    if ( $_GET[$this->p] == 1&&  (strpos($url , $_GET[$this->p]) ===  false)  ) {
                        $parameter = $this->parameter;

                        unset($parameter[$this->p]);
                        
                        if ( strpos($url, '?') !== false ) {
                            $url =  strstr($url, '?', true);
                        }

                        foreach ($parameter as $key => $value) {
                                if ( strpos($url, $value) !== false ) {
                                    continue;
                                }
                                $url .= '/' . $value;
                        }
                        $url .= '/' . $this->parameter[$this->p];
                    }
                    $url = str_replace($_GET[$this->p], $page,$url );

            }else{
                    $url = str_replace('.html', '',$this->url );
                    $url = str_replace('/' .$_GET[$this->p], '/' . $page, $url);
            }
            return $url . '.html';
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->url = $this->url($this->parameter[$this->p],true);
        
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
                $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page =  '';
        if ( $up_row > 0 ) {
            $template = $this->template['prev'];
            $template = str_replace ( "%STYLE%" , $this->style['prev']  , $template);
            $template = str_replace ( "%HREF%" , $this->url($up_row) , $template);
            $template = str_replace ( "%TEXT%" , $this->config['prev'] , $template);
            $up_page = $template;
        }

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = '';
        if ( $down_row <= $this->totalPages ) {
            $template = $this->template['next'];
            $template = str_replace ( "%STYLE%" , $this->style['next']  , $template);
            $template = str_replace ( "%HREF%" , $this->url($down_row) , $template);
            $template = str_replace ( "%TEXT%" , $this->config['next'] , $template);
            $down_page = $template;
        }

        //第一页
        $the_first = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
            $template = $this->template['first'];
            $template = str_replace ( "%STYLE%" , $this->style['first']  , $template);
            $template = str_replace ( "%HREF%" , $this->url(1) , $template);
            $template = str_replace ( "%TEXT%" , $this->config['first'] , $template);
            $the_first = $template;
        }

        //最后一页
        $the_end = '';
        if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
            $template = $this->template['last'];
            $template = str_replace ( "%STYLE%" , $this->style['last']  ,$template);
            $template = str_replace ( "%HREF%" , $this->url($this->totalPages) , $template);
            $template = str_replace ( "%TEXT%" , $this->config['last'] , $template);
            $the_end = $template;
        }

        //数字连接
        $link_page = "";
        for($i = 1; $i <= $this->rollPage; $i++){
                    if(($this->nowPage - $now_cool_page) <= 0 ){
                            $page = $i;
                    }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                             $page = $this->totalPages - $this->rollPage + $i;
                    }else{
                             $page = $this->nowPage - $now_cool_page_ceil + $i;
                    }
                    if($page > 0 && $page != $this->nowPage){

                            if($page <= $this->totalPages){
                                $template = $this->template['num'];
                                $template = str_replace ( "%STYLE%" , $this->style['num']  , $template);
                                $template = str_replace ( "%HREF%" , $this->url($page) , $template);
                                $template = str_replace ( "%TEXT%" , $page  , $template);
                                $link_page .= $template;
                            }else{
                                break;
                            }
                    }else{
                        if($page > 0 && $this->totalPages != 1){
                            $template = $this->template['current'];
                            $template = str_replace ( "%STYLE%" , $this->style['current']  , $template);
                            $template = str_replace ( "%HREF%" , $this->url($page) , $template);
                            $template = str_replace ( "%TEXT%" , $page  , $template);
                            $link_page .= $template;
                        }
                    }
        }
        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages),
            $this->config['theme']);
        return "{$page_str}";
    }

}
