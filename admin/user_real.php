<?php

/**
 * ECSHOP 实名认证
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 实名认证用户帐号列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('users_real_manage');
    
    $users_real_list = users_real_list();
    
    $smarty->assign('ur_here',$_LANG['users_real_list']);//@模板堂-bylu 语言-会员白条列表;
    $smarty->assign('users_real_list',    $users_real_list['users_real_list']);
    $smarty->assign('filter',       $users_real_list['filter']);
    $smarty->assign('record_count', $users_real_list['record_count']);
    $smarty->assign('page_count',   $users_real_list['page_count']);
    $smarty->assign('full_page',    1);
    
    $user_type = empty($_REQUEST['user_type']) ? 0 : intval($_REQUEST['user_type']);
    $smarty->assign('user_type',    $user_type);
    
    assign_query_info();
    $smarty->display('users_real_list.dwt');
}

elseif($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('users_real_manage');
    
    $real_id = empty($_REQUEST['real_id']) ? 0 : trim($_REQUEST['real_id']);
    $user_type = empty($_REQUEST['user_type']) ? 0 : trim($_REQUEST['user_type']);
    
    $sql = "SELECT ur.*, u.user_name, u.user_id FROM " . $ecs->table('users_real') . " AS ur "
            . " JOIN " . $ecs->table('users') . " AS u ON ur.user_id = u.user_id "
            . " WHERE ur.real_id = '$real_id'";
    $user_real_info = $db->getRow($sql);

    $smarty->assign('ur_here',$_LANG['users_real_edit']); 
    $smarty->assign('action_link',      array('text' => $_LANG['users_real_list'], 'href'=>'user_real.php?act=list&' . list_link_postfix()));
    
    $smarty->assign('user_type',$user_type);
    $smarty->assign('user_real_info',$user_real_info);
    $smarty->display('users_real_info.dwt');
}
elseif($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_real_manage');
	
    $user_id = empty($_POST['user_id']) ? 0 : trim($_POST['user_id']);
    $real_name = empty($_POST['real_name']) ? '' : trim($_POST['real_name']);
    $self_num = empty($_POST['self_num']) ? '' : trim($_POST['self_num']);
    $bank_card = empty($_POST['bank_card']) ? '' : trim($_POST['bank_card']);
    $review_status = empty($_POST['review_status']) ? '' : trim($_POST['review_status']);
    $review_content = empty($_POST['review_content']) ? '' : trim($_POST['review_content']);
    $user_type = empty($_POST['user_type']) ? 0 : intval($_POST['user_type']);
    $post_user_real = array(
        'user_id' => $user_id,
        'real_name' => $real_name,
        'self_num' => $self_num,
        'review_status' => $review_status,
        'review_content' => $review_content,
        'bank_card' => $bank_card
    );
    
    $type = '';
    if($user_type){
        $type = "&user_type=" . $user_type;
    }
    
    if($user_id > 0)
    {
        $sql = "SELECT real_id FROM ".$ecs->table('users_real')." WHERE user_id = '$user_id' AND user_type = '$user_type'";
        $real_id = $db->getOne($sql);
        if($real_id)
        {
            if($db->autoExecute($ecs->table('users_real'), $post_user_real, 'UPDATE', "real_id = '$real_id'"))
            {
                $links[] = array('text' => $_LANG['users_real_list'], 'href'=>'user_real.php?act=list' . $type);
                sys_msg('会员实名更新成功！', 0, $links);
            }
        }
        else
        {
            $post_user_real['add_time'] = gmtime();
            if($db->autoExecute($ecs->table('users_real'), $post_user_real, 'INSERT'))
            {
                $links[] = array('text' => $_LANG['go_back'], 'href'=>'user_real.php?act=list' . $type);
                sys_msg('会员实名设置成功！', 0, $links);
            }
        }	
    }
}


/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    //检查权限
    check_authz_json('users_real_manage');
	
    $users_real_list = users_real_list();

    $smarty->assign('users_real_list', $users_real_list['users_real_list']);
    $smarty->assign('filter',       $users_real_list['filter']);
    $smarty->assign('record_count', $users_real_list['record_count']);
    $smarty->assign('page_count',   $users_real_list['page_count']);

    $sort_flag  = sort_flag($users_real_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('users_real_list.dwt'), '', array('filter' => $users_real_list['filter'], 'page_count' => $users_real_list['page_count']));
}
/*------------------------------------------------------ */
//-- 批量操作实名信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 检查权限 */
    admin_priv('users_real_manage');
    $user_type = empty($_REQUEST['user_type']) ? 0 : intval($_REQUEST['user_type']);
    
    $type = '';
    if($user_type){
        $type = "&user_type=" . $user_type;
    }
    
    if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
    {
        sys_msg("没有选择任何数据", 1);
    }
    $real_id_arr = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
    
    if (isset($_POST['type']))
    {
        // 删除实名
        if ($_POST['type'] == 'batch_remove')
        {
            $sql = "DELETE FROM " . $ecs->table('users_real') .
            " WHERE real_id " . db_create_in($real_id_arr);
    
            if($db->query($sql))
            {
                $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'user_real.php?act=list' . $type);
                sys_msg("删除实名信息成功", 0, $lnk);
            }
            /* 记录日志 */
            admin_log('', 'batch_trash', 'users_real');
        }
        
        // 审核
        elseif ($_POST['type'] == 'review_to')
        {
            // review_status = 0未审核 1审核通过 2审核未通过
            $time = gmtime();
            $review_status = $_POST['review_status'];
            $review_content = !empty($_POST['review_content']) ? trim($_POST['review_content']) : '';
            
            $sql = "UPDATE " . $ecs->table('users_real') ." SET review_status = '$review_status', review_content = '$review_content', review_time = '$time' "
                . " WHERE real_id " . db_create_in($real_id_arr);
            
            if($db->query($sql))
            {
                $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'user_real.php?act=list' . $type);
                sys_msg("实名信息审核状态设置成功", 0, $lnk);
            }
        }
    }
}

/*------------------------------------------------------ */
//-- 删除会员实名认证
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_real_manage');
    $real_id = !empty($_GET[real_id]) ? intval($_GET[real_id]) : 0;
    
    $user_type = empty($_REQUEST['user_type']) ? 0 : intval($_REQUEST['user_type']);
    
    $type = '';
    if($user_type){
        $type = "&user_type=" . $user_type;
    }
    
    if($real_id > 0)
    {
        $sql = "DELETE FROM " .$ecs->table('users_real'). " WHERE real_id = '$real_id'";
        if($db->query($sql))
        {
            /* 记录管理员操作 */
            admin_log(addslashes($real_id), 'remove', 'users_real');

            /* 提示信息 */
            $link[] = array('text' => $_LANG['users_real_list'], 'href'=>'user_real.php?act=list' . $type);
            sys_msg("删除实名用户成功", 0, $link);	
        }
    }
}

/**
 *  实名认证信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function users_real_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        $filter['review_status'] = !isset($_REQUEST['review_status']) ? -1 : intval($_REQUEST['review_status']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
            $filter['review_status'] = json_str_iconv($filter['review_status']);
        }
        
        $filter['user_type']    = isset($_REQUEST['user_type']) ? intval($_REQUEST['user_type']) : 0;
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'real_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $ex_where = ' WHERE 1 ';
        if ($filter['keywords'])
        {
            $ex_where .= " AND u.user_name LIKE '%" . mysqli_like_quote($filter['keywords']) ."%'";
        }
        
        if ($filter['review_status'] != -1)
        { 
            $ex_where .= " AND ur.review_status = '$filter[review_status]'";
        }
        
        $ex_where .= " AND ur.user_type = '$filter[user_type]'";
        
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users_real') ."AS ur "
                . " JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON ur.user_id = u.user_id ". $ex_where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT ur.*,u.user_name ".
                " FROM ". $GLOBALS['ecs']->table('users_real') ."as ur "
                . " JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON ur.user_id = u.user_id "
                . $ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $users_real_list = $GLOBALS['db']->getAll($sql);
   
    for($i = 0; $i <count($users_real_list); $i++){
        if($users_real_list[$i]['user_type']){
            $users_real_list[$i]['user_name'] = get_shop_name($users_real_list[$i]['user_id'], 1);
        }
    }

    $arr = array('users_real_list' => $users_real_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>