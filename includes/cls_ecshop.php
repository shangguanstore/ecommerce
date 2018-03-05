<?php

/**
 * ECSHOP 基础类
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: cls_ecshop.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('APPNAME', 'ECMOBAN_DSC');
define('VERSION', 'v1.8.8'); 
define('RELEASE', '20161117');

class ECS
{
    //var $db_name = '';
    //var $prefix  = 'ecs_';

    var $db_name = 'pjshch';
    var $prefix  = 'dsc_';

    /**
     * 构造函数 php7不支持类和构造函数重名，已把函数名ECS改成Construct_ECS 2018/01/05
     *
     * @access  public
     * @param   string      $ver        版本号
     *
     * @return  void
     */
    function Construct_ECS($db_name, $prefix)
    {
        $this->db_name = $db_name;
        $this->prefix  = $prefix;
    }

    /**
     * 将指定的表名加上前缀后返回
     *
     * @access  public
     * @param   string      $str        表名
     *
     * @return  string
     */
    function table($str)
    {
        return '`' . $this->db_name . '`.`' . $this->prefix . $str . '`';
    }

    /**
     * ECSHOP 密码编译方法;
     *
     * @access  public
     * @param   string      $pass       需要编译的原始密码
     *
     * @return  string
     */
    function compile_password($pass)
    {
        return md5($pass);
    }

    /**
     * 取得当前的域名
     *
     * @access  public
     *
     * @return  string      当前的域名
     */
    function get_domain()
    {
        /* 协议 */
        $protocol = $this->http();

        /* 域名或IP地址 */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
        {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }
        elseif (isset($_SERVER['HTTP_HOST']))
        {
            $host = $_SERVER['HTTP_HOST'];
        }
        else
        {
            /* 端口 */
            if (isset($_SERVER['SERVER_PORT']))
            {
                $port = ':' . $_SERVER['SERVER_PORT'];

                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
                {
                    $port = '';
                }
            }
            else
            {
                $port = '';
            }

            if (isset($_SERVER['SERVER_NAME']))
            {
                $host = $_SERVER['SERVER_NAME'] . $port;
            }
            elseif (isset($_SERVER['SERVER_ADDR']))
            {
                $host = $_SERVER['SERVER_ADDR'] . $port; //IP
            }
        }

        return $protocol . $host;
    }

    /**
     * 获得 ECSHOP 当前环境的 URL 地址
     *
     * @access  public
     *
     * @return  void
     */
    function url()
    {
        $curr = strpos(PHP_SELF, ADMIN_PATH . '/') !== false ?
                preg_replace('/(.*)(' . ADMIN_PATH . ')(\/?)(.)*/i', '\1', dirname(PHP_SELF)) :
                dirname(PHP_SELF);

        $root = str_replace('\\', '/', $curr);

        if (substr($root, -1) != '/')
        {
            $root .= '/';
        }

        return $this->get_domain() . $root;
    }

    /**
     * 获得 ECSHOP 当前环境的 商家后台 URL 地址  by kong
     *
     * @access  public
     *
     * @return  void
     */
    function seller_url()
    {
        $curr = strpos(PHP_SELF, SELLER_PATH . '/') !== false ?
                preg_replace('/(.*)(' . SELLER_PATH . ')(\/?)(.)*/i', '\1', dirname(PHP_SELF)) :
                dirname(PHP_SELF);

        $root = str_replace('\\', '/', $curr);

        if (substr($root, -1) != '/')
        {
            $root .= '/';
        }

        return $this->get_domain() . $root;
    }	
	
    
        /**
     * 获得 ECSHOP 当前环境的 门店后台 URL 地址  by kong
     *
     * @access  public
     *
     * @return  void
     */
    function stores_url()
    {
        $curr = strpos(PHP_SELF, STORES_PATH . '/') !== false ?
                preg_replace('/(.*)(' . STORES_PATH . ')(\/?)(.)*/i', '\1', dirname(PHP_SELF)) :
                dirname(PHP_SELF);

        $root = str_replace('\\', '/', $curr);

        if (substr($root, -1) != '/')
        {
            $root .= '/';
        }

        return $this->get_domain() . $root;
    }
    
    /**
     * 获得 ECSHOP 当前环境的 HTTP 协议方式
     *
     * @access  public
     *
     * @return  void
     */
    function http()
    {
        return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }

    /**
     * 获得数据目录的路径
     *
     * @param int $sid
     *
     * @return string 路径
     */
    function data_dir($sid = 0)
    {
        if (empty($sid))
        {
            $s = 'data';
        }
        else
        {
            $s = 'user_files/';
            $s .= ceil($sid / 3000) . '/';
            $s .= $sid % 3000;
        }
        return $s;
    }

    /**
     * 获得图片的目录路径
     *
     * @param int $sid
     *
     * @return string 路径
     */
    function image_dir($sid = 0)
    {
        if (empty($sid))
        {
            $s = 'images';
        }
        else
        {
            $s = 'user_files/';
            $s .= ceil($sid / 3000) . '/';
            $s .= ($sid % 3000) . '/';
            $s .= 'images';
        }
        return $s;
    }
    
    /*
     * 阿里大鱼短信接口
     */
    function ali_yu($msg){
        
        $phones = $msg['mobile_phone'];
        include(ROOT_PATH . 'plugins/aliyunyu/TopSdk.php');
        $c = new TopClient;
        $c->appkey = $GLOBALS['_CFG']['ali_appkey'];
        $c->secretKey = $GLOBALS['_CFG']['ali_secretkey'];
        $c->format = 'json';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType($msg['SmsType']);
        $req->setSmsFreeSignName($msg['SignName']);
        $req->setSmsParam($msg['smsParams']);
        $req->setRecNum("{$phones}");
        $req->setSmsTemplateCode($msg['SmsCdoe']);
        $resp = $c->execute($req);
        return $resp;
    }
    
    /**
    * 数组分页函数 核心函数 array_slice
    * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
    * $page_size  每页多少条数据
    * $page  当前第几页
    * $array  查询出来的所有数组
    * order 0 - 不变   1- 反序
    */
   function page_array($page_size = 1, $page = 1, $array = array(), $order = 0){
       
       $arr = array();
       $pagedata = array();
       if($array)
       {
            global $countpage; #定全局变量

            $start=($page - 1) * $page_size; #计算每次分页的开始位置
            
            if($order==1){
                $array=array_reverse($array);
            }  
            
            $totals=count($array); 
            $countpage=ceil($totals/$page_size); #计算总页面数
            $pagedata=array_slice($array,$start,$page_size);

            $filter = array(
                'page'           => $page,
                'page_size'     => $page_size,
                'record_count'  => $totals,
                'page_count'    => $countpage
            );

            $arr = array('list' => $pagedata, 'filter' => $filter, 'page_count'=> $countpage, 'record_count' => $totals);   
       }
       
       return $arr; #返回查询数据
   }
   
    /* 
     * 防止SQL注入
     * 过滤数组参数
     */
    function get_explode_filter($str_arr, $type = 0){
        switch ($type){
            case 1 :
                $str = 1;
                break;
            default :
                $str = $this->return_intval($str_arr);
                break;
        }

        return $str;
    }
   
    /*
     * 整数类型
     * 返回intval
     */
    function return_intval($str){

        $new_str = '';
        if($str){
            $str = explode(",", $str);
            
            foreach($str as $key=>$row){
                $row = intval($row);
                
                if($row){
                    $new_str .= $row . ",";
                }
            }
        }
        
        $new_str = substr($new_str, 0, -1);
        return $new_str;
    }
    
    /**
     * 判断是否纯字母
     */
    function preg_is_letter($str) {
        $preg = '[^A-Za-z]+';
        if (preg_match("/$preg/", $str)) {
            return false;   //不是由纯字母组成
        } else {
            return true;    //全部由字母组成
        }
    }
    
    /**
     * 查询MYSQL拼接字符串数据
     * $select_array 查询的字段
     * $select_id 查询的ID值
     * $where 查询的条件 比如（AND goods_id = '$goods_id'）
     * $table 表名称
     * $id 被查询的字段 
     * $is_db 查询返回数组方式
     */
    function get_select_find_in_set($is_db = 0, $select_id, $select_array = array(), $where = '', $table = '', $id = '', $replace = '') {
        
        if($replace){
            $replace = "REPLACE ($id,'$replace',',')";
        }else{
            $replace = "$id";
        }
        
        if($select_array){
            $select = explode(',', $select_array);
        }else{
            $select = '*';
        }
        
        $sql = "SELECT $select FROM " . $GLOBALS['ecs']->table($table) . " WHERE find_in_set('$select_id', $replace) OR find_in_set('$select_id', $replace) $where";
        
        if($is_db == 1){
            //多条数组数据
            return $GLOBALS['db']->getAll($sql);
        }elseif($is_db == 2){
            //一条数组数据
            return $GLOBALS['db']->getRow($sql);
        }else{
            //返回某个字段的值
            return $GLOBALS['db']->getOne($sql, true);
        }
    }
    
    /**
     * 删除MYSQL拼接字符串数据
     * $select_id 查询的ID值
     * $where 查询的条件 比如（AND goods_id = '$goods_id'）
     * $table 表名称
     * $id 被查询的字段 
     */
    function get_del_find_in_set( $select_id, $where = '', $table = '', $id = '', $replace = '') {
        
        if($replace){
            $replace = "REPLACE ($id,'$replace',',')";
        }else{
            $replace = "$id";
        }
        
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table($table) . " WHERE find_in_set('$select_id', $replace) OR find_in_set('$select_id', $replace) $where";
        $GLOBALS['db']->query($sql);
    }
}

?>