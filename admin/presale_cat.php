<?php

/**
 * ECSHOP 商品分类管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: li $
 * $Id: presale_cat.php 17217 2015-11-18 li $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("presale_cat"), $db, 'cid', 'c_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
	
	if($parent_id){
		$cat_list = presale_child_cat($parent_id);
	}else{
		$cat_list = presale_cat_list(0, 0, false, 0, true, 'admin');		
	}
    /* 获取分类列表 */


    //ecmoban模板堂 --zhuo start
    $adminru = get_admin_ru_id();
    $smarty->assign('ru_id',     $adminru['ru_id']);

    if($adminru['ru_id'] == 0){
            $smarty->assign('action_link',  array('href' => 'presale_cat.php?act=add', 'text' => $_LANG['add_presale_cat']));
    }
    //ecmoban模板堂 --zhuo end

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['presale_cat']);
    $smarty->assign('full_page',    1);

    $smarty->assign('cat_info',     $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('presale_cat_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $cat_list = presale_cat_list(0, 0, false);
    $smarty->assign('cat_info',     $cat_list);

    //ecmoban模板堂 --zhuo start
    $adminru = get_admin_ru_id();
    $smarty->assign('ru_id',     $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end

    //make_json_result($smarty->fetch('presale_cat_list.htm'));
}
/*------------------------------------------------------ */
//-- 添加商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限检查 */
    admin_priv('cat_manage');
    
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['add_presale_cat']);
    $smarty->assign('action_link',  array('href' => 'presale_cat.php?act=list', 'text' => $_LANG['presale_cat_list']));
	
	$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
	
	$cat_select = presale_cat_list(0, 0, false, 0, true, '', 1);
	/* 简单处理缩进 */
	foreach($cat_select as $k=>$v){
		if($v['level']){
			$level = str_repeat('&nbsp;', $v['level'] * 4);
			$cat_select[$k]['name'] = $level.$v['name'];
		}
	}
    $smarty->assign('cat_select',   $cat_select);
    $smarty->assign('form_act',     'insert');
    $smarty->assign('cat_info',     array('is_show' => 1, 'parent_cid'=>$parent_id));

    //ecmoban模板堂 --zhuo start
    $adminru = get_admin_ru_id();
    $smarty->assign('ru_id',     $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end

    /* 显示页面 */
    assign_query_info();
    $smarty->display('presale_cat_info.dwt');
}

/*------------------------------------------------------ */
//-- 商品分类添加时的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('cat_manage');

    /* 初始化变量 */
    $cat['cid']       = !empty($_POST['cid'])       ? intval($_POST['cid'])     : 0;
    $cat['parent_cid']    = !empty($_POST['parent_cid'])    ? intval($_POST['parent_cid'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['c_name']     = !empty($_POST['c_name'])     ? trim($_POST['c_name'])     : '';

    if (cname_exists($cat['c_name'], $cat['parent_cid']))
    {
        /* 同级别下不能有重复的分类名称 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['catname_exist'], 0, $link);
    }

    /* 入库的操作 */
    if ($db->autoExecute($ecs->table('presale_cat'), $cat) !== false)
    {
        $cat_id = $db->insert_id();

        admin_log($_POST['c_name'], 'add', 'presale_cat');   // 记录管理员操作
        clear_cache_files();    // 清除缓存

        /*添加链接*/
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'presale_cat.php?act=add';

        $link[1]['text'] = $_LANG['back_list'];
        $link[1]['href'] = 'presale_cat.php?act=list';

        sys_msg($_LANG['catadd_succed'], 0, $link);
    }
 }

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('cat_manage');   // 权限检查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据
    
    $smarty->assign('ur_here',     $_LANG['category_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['presale_cat_list'], 'href' => 'presale_cat.php?act=list'));
	
    //ecmoban模板堂 --zhuo start
    $smarty->assign('cat_id',    $cat_id);

    $adminru = get_admin_ru_id();
    $smarty->assign('ru_id',     $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end
    $smarty->assign('cat_info',    $cat_info);
    $smarty->assign('form_act',    'update');
	
	$cat_select = presale_cat_list(0, $cat_info['parent_cid'], false, 0, true, '', 1);
	/* 简单处理缩进 */
	foreach($cat_select as $k=>$v){
		if($v['level']){
			$level = str_repeat('&nbsp;', $v['level'] * 4);
			$cat_select[$k]['name'] = $level.$v['name'];
		}
	}	
	
    $smarty->assign('cat_select',  $cat_select);

    /* 显示页面 */
    assign_query_info();
    $smarty->display('presale_cat_info.dwt');
}

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 权限检查 */
    admin_priv('cat_manage');

    /* 初始化变量 */
    $cat_id              = !empty($_POST['cid'])       ? intval($_POST['cid'])     : 0;
    $old_cat_name        = $_POST['old_c_name'];
    $cat['parent_cid']    = isset($_POST['parent_cid'])    ? trim($_POST['parent_cid'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['c_name']     = !empty($_POST['c_name'])     ? trim($_POST['c_name'])     : '';
    
    $adminru = get_admin_ru_id();
    /* 判断分类名是否重复 */
            
    if ($cat['c_name'] != $old_cat_name)
    {
        if (presale_cat_exists($cat['c_name'],$cat['parent_cid'], $cat_id))
        {
           $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
           sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }

    $dat = $db->getRow("SELECT c_name FROM ". $ecs->table('presale_cat') . " WHERE cid = '$cat_id'");

    if ($db->autoExecute($ecs->table('presale_cat'), $cat, 'UPDATE', "cid='$cat_id'"))
    {	
        clear_cache_files(); // 清除缓存
        admin_log($_POST['c_name'], 'edit', 'presale_cat'); // 记录管理员操作

        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'presale_cat.php?act=list');
        sys_msg($_LANG['catedit_succed'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('cat_manage');

    /* 初始化分类ID并取得分类名称 */
    $cat_id   = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT c_name FROM ' .$ecs->table('presale_cat'). " WHERE cid='$cat_id'");

    /* 当前分类下是否有子分类 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('presale_cat'). " WHERE parent_cid='$cat_id'");

    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('presale_activity'). " WHERE cid='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0)
    {
        /* 删除分类 */
        $sql = 'DELETE FROM ' .$ecs->table('presale_cat'). " WHERE cid = '$cat_id'";
        if ($db->query($sql))
        {
            clear_cache_files();
            admin_log($cat_name, 'remove', 'presale_cat');
        }
    }
    else
    {
        make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
    }

    $url = 'presale_cat.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */
//
///**
// * 检查分类是否已经存在
// *
// * @param   string      $cat_name       分类名称
// * @param   integer     $parent_cat     上级分类
// * @param   integer     $exclude        排除的分类ID
// *
// * @return  boolean
// */
function presale_cat_exists($cat_name, $parent_cat, $exclude = 0)
{
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('presale_cat').
           " WHERE parent_cid = '$parent_cat' AND c_name = '$cat_name' AND cid<>'$exclude'";
    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
}

/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('presale_cat'), $args, 'update', "cid='$cat_id'");
}

/**
 * 检查分类是否已经存在
 *
 * @param   string      $c_name       分类名称
 * @param   integer     $parent_cat     上级分类
 * @param   integer     $exclude        排除的分类ID
 *
 * @return  boolean
 */
function cname_exists($c_name, $parent_cat, $exclude = 0)
{
    $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('presale_cat').
    " WHERE parent_cid = '$parent_cat' AND c_name = '$c_name' AND cid<>'$exclude'";
    return ($GLOBALS['db']->getOne($sql) > 0) ? true : false;
}

/*预售商品下级分类*/
function presale_child_cat($pid){
	$sql = " SELECT cid, c_name, parent_cid, sort_order FROM ".$GLOBALS['ecs']->table('presale_cat')." WHERE parent_cid = '$pid' ";
	return $GLOBALS['db']->getAll($sql);
}

?>