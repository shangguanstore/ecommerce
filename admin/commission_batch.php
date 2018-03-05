<?php
/**
 * ECSHOP 商品批量上传、修改
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_batch.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* 取得可选语言 */
$lang_list = array(
    'UTF8'      => $_LANG['charset']['utf8'],
    'GB2312'    => $_LANG['charset']['zh_cn'],
    'BIG5'      => $_LANG['charset']['zh_tw'],
);
$download_list = array();
$smarty->assign('lang_list',     $lang_list);

/* 参数赋值 */
$ur_here = $_LANG['13_batch_add'];
$smarty->assign('ur_here', $ur_here);

/*------------------------------------------------------ */
//-- 批量上传
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('commission_batch');
    
    $smarty->assign('full_page', 1);
    
    /* 显示模板 */
    assign_query_info();
    $smarty->display('commission_batch_add.htm');
}

/*------------------------------------------------------ */
//-- 批量修改：提交
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'commission_add')
{
    if($_FILES['file']['name']){
        $line_number = 0;
        $arr = array();
        $commission_list = array();
        $field_list = array_keys($_LANG['upload_commission']); // 字段列表
        $_POST['charset'] = 'GB2312';
        $data = file($_FILES['file']['tmp_name']);
                                
        if(count($data) > 0){
            foreach ($data AS $line)
            {
                    // 跳过第一行
                    if ($line_number == 0)
                    {
                            $line_number++;
                            continue;
                    }

                    // 转换编码
                    if (($_POST['charset'] != 'UTF8') && (strpos(strtolower(EC_CHARSET), 'utf') === 0))
                    {
                            $line = ecs_iconv($_POST['charset'], 'UTF8', $line);
                    }

                    // 初始化
                    $arr    = array();
                    $buff   = '';
                    $quote  = 0;
                    $len    = strlen($line);
                    for ($i = 0; $i < $len; $i++)
                    {
                            $char = $line[$i];

                            if ('\\' == $char)
                            {
                                    $i++;
                                    $char = $line[$i];

                                    switch ($char)
                                    {
                                            case '"':
                                                    $buff .= '"';
                                                    break;
                                            case '\'':
                                                    $buff .= '\'';
                                                    break;
                                            case ',';
                                                    $buff .= ',';
                                                    break;
                                            default:
                                                    $buff .= '\\' . $char;
                                                    break;
                                    }
                            }
                            elseif ('"' == $char)
                            {
                                    if (0 == $quote)
                                    {
                                            $quote++;
                                    }
                                    else
                                    {
                                            $quote = 0;
                                    }
                            }
                            elseif (',' == $char)
                            {
                                    if (0 == $quote)
                                    {
                                            if (!isset($field_list[count($arr)]))
                                            {
                                                    continue;
                                            }
                                            $field_name = $field_list[count($arr)];
                                            $arr[$field_name] = trim($buff);
                                            $buff = '';
                                            $quote = 0;
                                    }
                                    else
                                    {
                                            $buff .= $char;
                                    }
                            }
                            else
                            {
                                    $buff .= $char;
                            }

                            if ($i == $len - 1)
                            {
                                    if (!isset($field_list[count($arr)]))
                                    {
                                            continue;
                                    }
                                    $field_name = $field_list[count($arr)];
                                    $arr[$field_name] = trim($buff);
                            }
                    }
                    $commission_list[] = $arr;
            }
        }
    }    
    
    $commission_list = get_commission_list($commission_list);
    $_SESSION['commission_list'] = $commission_list;
    
    $smarty->assign('full_page', 2);
    $smarty->assign('page', 1);
    
    /* 显示模板 */
    assign_query_info();
    $smarty->display('commission_batch_add.htm');
}

/*------------------------------------------------------ */
//-- 处理系统设置订单自动确认收货订单
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_insert')
{
    /* 检查权限 */
    admin_priv('commission_batch');
    
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    
    $page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 1;
    
   /* 设置最长执行时间为5分钟 */
    @set_time_limit(300);
    
    if(isset($_SESSION['commission_list']) && $_SESSION['commission_list'])
    {
        $commission_list = $_SESSION['commission_list'];
        $commission_list = $ecs->page_array($page_size, $page, $commission_list);
        
        $result['list'] = $commission_list['list'][0];
        
        $result['page'] = $commission_list['filter']['page'] + 1;
        $result['page_size'] = $commission_list['filter']['page_size'];
        $result['record_count'] = $commission_list['filter']['record_count'];
        $result['page_count'] = $commission_list['filter']['page_count'];
        
        if(empty($result['list']['user_name'])){
            $result['list']['user_name'] = 0;
        }
        
        $result['is_stop'] = 1;
        if($page > $commission_list['filter']['page_count']){
            $result['is_stop'] = 0;
        }
        
        $sql = "SELECT server_id FROM " .$GLOBALS['ecs']->table('merchants_server'). " WHERE user_id = '" .$result['list']['user_id']. "'";
        
        if($GLOBALS['db']->getOne($sql)){
            $result['status_lang'] = $GLOBALS['_LANG']['already_show'];
        }else{
            if($result['is_stop']){
                $other = array(
                    'user_id'               => $result['list']['user_id'],
                    'suppliers_percent'     => $result['list']['suppliers_percent']
                );

                $db->autoExecute($ecs->table('merchants_server'), $other, 'INSERT');

                if($db->insert_id()){
                    $result['status_lang'] = $GLOBALS['_LANG']['status_succeed'];
                }else{
                    $result['status_lang'] = $GLOBALS['_LANG']['status_failure'];
                }
            }
        }
        
    }    

    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 下载文件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'download')
{
    /* 检查权限 */
    admin_priv('commission_batch');

    // 文件标签
    // Header("Content-type: application/octet-stream");
    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    Header("Content-Disposition: attachment; filename=commission_list.csv");

    // 下载
    if ($_GET['charset'] != $_CFG['lang'])
    {
        $lang_file = '../languages/' . $_GET['charset'] . '/admin/commission_batch.php';
        if (file_exists($lang_file))
        {
            unset($_LANG['upload_commission']);
            require($lang_file);
        }
    }
    if (isset($_LANG['upload_commission']))
    {
        /* 创建字符集转换对象 */
        if ($_GET['charset'] == 'zh_cn' || $_GET['charset'] == 'zh_tw')
        {
            $to_charset = $_GET['charset'] == 'zh_cn' ? 'GB2312' : 'BIG5';
            echo ecs_iconv(EC_CHARSET, $to_charset, join(',', $_LANG['upload_commission']));
        }
        else
        {
            echo join(',', $_LANG['upload_commission']);
        }
    }
    else
    {
        echo 'error: $_LANG[upload_commission] not exists';
    }
}


function get_commission_list($commission_list){
    
    if($commission_list){
        foreach($commission_list as $key=>$rows){
            $commission_list[$key]['suppliers_value'] = $rows['suppliers_percent'];
            
            $sql = "SELECT user_id FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_name = '" .$rows['user_name']. "' LIMIT 1";            
            $users = $GLOBALS['db']->getRow($sql);
            
            $sql = "SELECT percent_id FROM " .$GLOBALS['ecs']->table('merchants_percent'). " WHERE percent_value = '" .$rows['suppliers_percent']. "' LIMIT 1";
            $percent = $GLOBALS['db']->getRow($sql);
            
            $commission_list[$key]['user_id'] = $users['user_id'];
            $commission_list[$key]['suppliers_percent'] = $percent['percent_id'];
            $commission_list[$key]['shop_name'] = get_shop_name($users['user_id'], 1); 
            
            if(!$users['user_id']){
                unset($commission_list[$key]);
            }
        }
    }
    
    return $commission_list;
}
?>