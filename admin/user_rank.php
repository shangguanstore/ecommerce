<?php

/**
 * ECSHOP 会员等级管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user_rank.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc = new exchange($ecs->table("user_rank"), $db, 'rank_id', 'rank_name');
$exc_user = new exchange($ecs->table("users"), $db, 'user_rank', 'user_rank');

/*------------------------------------------------------ */
//-- 会员等级列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    $ranks = array();
    $ranks = $db->getAll("SELECT * FROM " .$ecs->table('user_rank'));

    $smarty->assign('ur_here',      $_LANG['05_user_rank_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_user_rank'], 'href'=>'user_rank.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('rank_count',count($ranks));
    $smarty->assign('user_ranks',   $ranks);

    assign_query_info();
    $smarty->display('user_rank.dwt');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $ranks = array();
    $ranks = $db->getAll("SELECT * FROM " .$ecs->table('user_rank'));
    $smarty->assign('rank_count',count($ranks));
    $smarty->assign('user_ranks',   $ranks);
    make_json_result($smarty->fetch('user_rank.dwt'));
}

/*------------------------------------------------------ */
//-- 添加会员等级
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('user_rank');

    $rank['rank_id']      = 0;
    $rank['rank_special'] = 0;
    $rank['show_price']   = 1;
    $rank['min_points']   = 0;
    $rank['max_points']   = 0;
    $rank['discount']     = 100;

    $form_action          = 'insert';

    $smarty->assign('rank',        $rank);
    $smarty->assign('ur_here',     $_LANG['add_user_rank']);
    $smarty->assign('action_link', array('text' => $_LANG['05_user_rank_list'], 'href'=>'user_rank.php?act=list'));
    $smarty->assign('ur_here',     $_LANG['add_user_rank']);
    $smarty->assign('form_action', $form_action);

    assign_query_info();
    $smarty->display('user_rank_info.dwt');
}

/*------------------------------------------------------ */
//-- 增加会员等级到数据库
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('user_rank');

    $special_rank =  isset($_POST['special_rank']) ? intval($_POST['special_rank']) : 0;
    $_POST['min_points'] = empty($_POST['min_points']) ? 0 : intval($_POST['min_points']);
    $_POST['max_points'] = empty($_POST['max_points']) ? 0 : intval($_POST['max_points']);

    /* 检查是否存在重名的会员等级 */
    if (!$exc->is_only('rank_name', trim($_POST['rank_name'])))
    {
        sys_msg(sprintf($_LANG['rank_name_exists'], trim($_POST['rank_name'])), 1);
    }

    /* 非特殊会员组检查积分的上下限是否合理 */
    if ($_POST['min_points'] >= $_POST['max_points'] && $special_rank == 0)
    {
        sys_msg($_LANG['js_languages']['integral_max_small'], 1);
    }

    /* 特殊等级会员组不判断积分限制 */
    if ($special_rank == 0)
    {
        /* 检查下限制有无重复 */
        if (!$exc->is_only('min_points', intval($_POST['min_points'])))
        {
            sys_msg(sprintf($_LANG['integral_min_exists'], intval($_POST['min_points'])));
        }
    }

    /* 特殊等级会员组不判断积分限制 */
    if ($special_rank == 0)
    {
        /* 检查上限有无重复 */
        if (!$exc->is_only('max_points', intval($_POST['max_points'])))
        {
            sys_msg(sprintf($_LANG['integral_max_exists'], intval($_POST['max_points'])));
        }
    }

    $sql = "INSERT INTO " .$ecs->table('user_rank') ."( ".
                "rank_name, min_points, max_points, discount, special_rank, show_price".
            ") VALUES (".
                "'$_POST[rank_name]', '" .intval($_POST['min_points']). "', '" .intval($_POST['max_points']). "', ".
                "'$_POST[discount]', '$special_rank', '" .intval($_POST['show_price']). "')";
    $db->query($sql);

    /* 管理员日志 */
    admin_log(trim($_POST['rank_name']), 'add', 'user_rank');
    clear_cache_files();

    $lnk[] = array('text' => $_LANG['back_list'],    'href'=>'user_rank.php?act=list');
    $lnk[] = array('text' => $_LANG['add_continue'], 'href'=>'user_rank.php?act=add');
    sys_msg($_LANG['add_rank_success'], 0, $lnk);
}

/*------------------------------------------------------ */
//-- 编辑会员等级
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('user_rank');
    $id = !empty($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
    $sql = "SELECT * FROM".$ecs->table("user_rank")." WHERE rank_id='$id'";
    $rank = $db->getRow("$sql");
    $smarty->assign('rank',        $rank);
    $smarty->assign('ur_here',     $_LANG['add_user_rank']);
    $smarty->assign('action_link', array('text' => $_LANG['05_user_rank_list'], 'href'=>'user_rank.php?act=list'));
    $smarty->assign('ur_here',     $_LANG['add_user_rank']);
    $smarty->assign('form_action', "update");

    assign_query_info();
    $smarty->display('user_rank_info.dwt');
}elseif($_REQUEST['act'] == 'update'){
    admin_priv('user_rank');
    
    $id = !empty($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
    $special_rank =  isset($_POST['special_rank']) ? intval($_POST['special_rank']) : 0;
    $_POST['min_points'] = empty($_POST['min_points']) ? 0 : intval($_POST['min_points']);
    $_POST['max_points'] = empty($_POST['max_points']) ? 0 : intval($_POST['max_points']);
    /* 检查是否存在重名的会员等级 */
    if (!$exc->is_only('rank_name', trim($_POST['rank_name']),'0',"rank_id != '$id'"))
    {
        sys_msg(sprintf($_LANG['rank_name_exists'], trim($_POST['rank_name'])), 1);
    }
    
    /* 非特殊会员组检查积分的上下限是否合理 */
    if ($_POST['min_points'] >= $_POST['max_points'] && $special_rank == 0)
    {
        sys_msg($_LANG['js_languages']['integral_max_small'], 1);
    }

    /* 特殊等级会员组不判断积分限制 */
    if ($special_rank == 0)
    {
        /* 检查下限制有无重复 */
        if (!$exc->is_only('min_points', intval($_POST['min_points']),'0',"rank_id != '$id'"))
        {
            sys_msg(sprintf($_LANG['integral_min_exists'], intval($_POST['min_points'])));
        }
    }

    $sql = "UPDATE".$ecs->table('user_rank')." SET rank_name = '".$_POST['rank_name']."' "
            . ", min_points = '".intval($_POST['min_points'])."' , max_points = '".intval($_POST['max_points'])."' ,"
            . " discount = '".$_POST['discount']."' , special_rank = '$special_rank' , show_price = '".intval($_POST['show_price'])."' WHERE rank_id = '$id'";
    $db->query($sql);
    /* 管理员日志 */
    admin_log(trim($_POST['rank_name']), 'edit', 'user_rank');
    clear_cache_files();

    $lnk[] = array('text' => $_LANG['back_list'],    'href'=>'user_rank.php?act=list');
    sys_msg($_LANG['edit'].$_LANG['success'], 0, $lnk);
}

/*------------------------------------------------------ */
//-- 删除会员等级
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('user_rank');

    $rank_id = intval($_GET['id']);

    if ($exc->drop($rank_id))
    {
        /* 更新会员表的等级字段 */
        $exc_user->edit("user_rank = 0", $rank_id);

        $rank_name = $exc->get_name($rank_id);
        admin_log(addslashes($rank_name), 'remove', 'user_rank');
        clear_cache_files();
    }

    $url = 'user_rank.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;

}

/*------------------------------------------------------ */
//-- 切换是否是特殊会员组
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_special')
{
    check_authz_json('user_rank');

    $rank_id       = intval($_POST['id']);
    $is_special    = intval($_POST['val']);

    if ($exc->edit("special_rank = '$is_special'", $rank_id))
    {
        $rank_name = $exc->get_name($rank_id);
        admin_log(addslashes($rank_name), 'edit', 'user_rank');
        make_json_result($is_special);
    }
    else
    {
        make_json_error($db->error());
    }
}
/*------------------------------------------------------ */
//-- 切换是否显示价格
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_showprice')
{
    check_authz_json('user_rank');

    $rank_id       = intval($_POST['id']);
    $is_show    = intval($_POST['val']);

    if ($exc->edit("show_price = '$is_show'", $rank_id))
    {
        $rank_name = $exc->get_name($rank_id);
        admin_log(addslashes($rank_name), 'edit', 'user_rank');
        clear_cache_files();
        make_json_result($is_show);
    }
    else
    {
        make_json_error($db->error());
    }
}

?>