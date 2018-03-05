<?php

/**
 * ECSHOP 管理中心服务站管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wanglei $
 * $Id: suppliers_server.php 15013 2009-05-13 09:31:42Z wanglei $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

/* 检查权限 */
admin_priv('seller_account');

if(!isset($_REQUEST['act_type'])){
    $_REQUEST['act_type'] = 'detail';
}

/*------------------------------------------------------ */
//-- 账户管理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $store_list = get_common_store_list();
    $smarty->assign('store_list', $store_list);
    
    if(isset($_REQUEST['ru_id'])){
        $action_link = "&ru_id=" . $_REQUEST['ru_id'];
        $smarty->assign('ru_id', $_REQUEST['ru_id']);
    }
        
    $smarty->assign('action_link2',  array('text' => $_LANG['04_seller_detail'], 'href'=>'merchants_account.php?act=list&act_type=detail'.$action_link));
    $smarty->assign('action_link',  array('text' => $_LANG['05_seller_account_log'], 'href'=>'merchants_account.php?act=list&act_type=account_log'.$action_link));
    $smarty->assign('full_page',    1);
    
    if ($_REQUEST['act_type'] == 'detail') {

        $smarty->assign('ur_here', $_LANG['04_seller_detail']);
        $list = get_account_log_list($adminru['ru_id'], array(2, 3, 4));

        $smarty->assign('log_list', $list['log_list']);
        $smarty->assign('filter', $list['filter']);
        $smarty->assign('record_count', $list['record_count']);
        $smarty->assign('page_count', $list['page_count']);
        $smarty->assign('act_type', 'detail');

        assign_query_info();
        $smarty->display('merchants_detail.dwt');
    } 
    
    else if ($_REQUEST['act_type'] == 'account_log') 
    {
        
        $smarty->assign('ur_here', $_LANG['05_seller_account_log']);
        $list = get_account_log_list($adminru['ru_id'], array(1, 4));

        $smarty->assign('log_list', $list['log_list']);
        $smarty->assign('filter', $list['filter']);
        $smarty->assign('record_count', $list['record_count']);
        $smarty->assign('page_count', $list['page_count']);
        $smarty->assign('act_type', 'account_log');

        assign_query_info();
        $smarty->display('merchants_account_log.dwt');
    }
}

/*------------------------------------------------------ */
//-- ajax返回列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    if($_REQUEST['act_type'] == 'detail'){
        $list = get_account_log_list($adminru['ru_id'],array(2,3,4));
        $fetch = "merchants_detail";
    }
    
    else if($_REQUEST['act_type'] == 'account_log')
    {
        $list = get_account_log_list($adminru['ru_id'],array(1, 4));
        $fetch = "merchants_account_log";
    }
    
    if($_REQUEST['act_type'] == 'detail' || $_REQUEST['act_type'] == 'account_log')
    {
        $smarty->assign('log_list', $list['log_list']);
        $smarty->assign('filter',       $list['filter']);
        $smarty->assign('record_count', $list['record_count']);
        $smarty->assign('page_count',   $list['page_count']);

        $sort_flag  = sort_flag($list['filter']);
        $smarty->assign($sort_flag['tag'], $sort_flag['img']);

        make_json_result($smarty->fetch($fetch . '.dwt'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
    }
}

/*------------------------------------------------------ */
//-- 查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'check')
{
    $smarty->assign('action_link2',  array('text' => $_LANG['04_seller_detail'], 'href'=>'merchants_account.php?act=list&act_type=detail'));
    $smarty->assign('action_link',  array('text' => $_LANG['05_seller_account_log'], 'href'=>'merchants_account.php?act=list&act_type=account_log'));
    $smarty->assign('ur_here', $_LANG['check']);
    $log_id = isset($_REQUEST['log_id']) ? intval($_REQUEST['log_id']) : 0;
    $act_type = isset($_REQUEST['act_type']) ? addslashes($_REQUEST['act_type']) : 0;
    
    $smarty->assign('log_id',   $log_id);
    $smarty->assign('form_action',   "update_check");
    
    $log_info = get_account_log_info($log_id);
    $smarty->assign('log_info',   $log_info);
    $smarty->assign('act_type',   $act_type);
    
    $seller_shopinfo = get_seller_shopinfo($log_info['ru_id'], array('seller_money'));
    $smarty->assign('seller_shopinfo', $seller_shopinfo);
    
    $users_real = get_users_real($log_info['ru_id'], 1);
    $smarty->assign('real', $users_real);
    
    assign_query_info();
    $smarty->display('merchants_log_check.dwt');
}

/*------------------------------------------------------ */
//-- 查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_check')
{
    include_once(ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    
    $log_id = isset($_REQUEST['log_id']) ? intval($_REQUEST['log_id']) : 0;
    $log_reply = isset($_REQUEST['log_reply']) ? addslashes(trim($_REQUEST['log_reply'])) : 0;
    $log_status = isset($_REQUEST['log_status']) ? intval($_REQUEST['log_status']) : 0;
    $certificate_img = isset($_FILES['certificate_img']) ? $_FILES['certificate_img'] : array();
    $msg_type = 0;
    if($log_status == 1){
        if($certificate_img['name']){
          $certificate = $image->upload_image('', 'seller_account', '', 1, $certificate_img['name'], $certificate_img['type'], $certificate_img['tmp_name'], $certificate_img['error'], $certificate_img['size']);  //图片存放地址 -- data/seller_account
        }

        $log_info = get_account_log_info($log_id);
       
        if($log_info['payment_info']['pay_name'] == '银行汇款/转帐'){

            /* 改变商家金额 */
            $sql = " UPDATE " . $ecs->table('seller_shopinfo') . " SET seller_money = seller_money + " . $log_info['amount'] . " WHERE ru_id = '" . $log_info['ru_id'] . "'";
            $db->query($sql);

            $change_desc = "商家充值，操作员：" .$_SESSION['admin_name'];

            $log_type = 3;

            $handler = $_LANG['topup_account_ok'];
            $href = "merchants_account.php?act=list";
            $text = $_LANG['04_seller_detail'];
        }else{

            if($log_info['deposit_mode'] == 1){
                /* 改变会员金额 */
                $sql = " UPDATE " . $ecs->table('users') . " SET user_money = user_money + " . $log_info['amount'] . " WHERE user_id = '" . $log_info['ru_id'] . "'";
                $db->query($sql);
            }

            /* 改变商家金额 */
            $sql = " UPDATE " . $ecs->table('seller_shopinfo') . " SET seller_money = seller_money - " . $log_info['amount'] . " WHERE ru_id = '" . $log_info['ru_id'] . "'";
            $db->query($sql);

            $change_desc = "商家提现，操作员：" .$_SESSION['admin_name'];

            $log_type = 4;
            $handler = $_LANG['deposit_account_ok'];
            $href = "merchants_account.php?act=list&act_type=account_log";
            $text = $_LANG['05_seller_account_log'];
            
            if($log_info['deposit_mode'] == 1){
                $user_account_log = array(
                    'user_id' => $log_info['ru_id'],
                    'user_money' => "+" . $log_info['amount'],
                    'change_desc' => $change_desc,
                    'process_type' => 0,
                    'payment' => '',
                    'change_time' => gmtime(),
                    'change_type' => 2,
                );

                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $user_account_log, 'INSERT');
            }
        }

        /* 改变会员金额 */
        $sql = " UPDATE " . $ecs->table('seller_account_log') . " SET is_paid = $log_status, admin_note = '$log_reply', certificate_img = '$certificate', log_type = '$log_type' WHERE log_id = '$log_id'";
        $db->query($sql);
    }else{
        $handler = $_LANG['handler_failure'];
        $msg_type = 1 ;
        $text = $_LANG['go_back'];
        if($log_info['payment_info']['pay_name'] == '银行汇款/转帐'){
            $href = "merchants_account.php?act=list";
        }else{
            $href = "merchants_account.php?act=list&act_type=account_log";
        }
    }
    
    $link[0] = array('href' => $href, 'text' => $text);
    sys_msg($handler, $msg_type, $link);
}
?>