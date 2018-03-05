<?php

/**
 * ECSHOP 会员管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
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
//-- 用户帐号列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('region_area');
	
	$smarty->assign('menu_select',array('action' => '01_system', 'current' => '09_region_area_management'));

    $smarty->assign('ur_here',      $_LANG['region_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_region'], 'href'=>'region_area.php?act=add'));

    $region_list = region_area_list();

    $smarty->assign('region_list',    $region_list['region_list']);
    $smarty->assign('filter',       $region_list['filter']);
    $smarty->assign('record_count', $region_list['record_count']);
    $smarty->assign('page_count',   $region_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('region_area_list.dwt');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $region_list = region_area_list();

    $smarty->assign('region_list',    $region_list['region_list']);
    $smarty->assign('filter',       $region_list['filter']);
    $smarty->assign('record_count', $region_list['record_count']);
    $smarty->assign('page_count',   $region_list['page_count']);

    $sort_flag  = sort_flag($region_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('region_area_list.dwt'), '', array('filter' => $region_list['filter'], 'page_count' => $region_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('region_area');
	
	$province_list = get_warehouse_province('admin');

    $smarty->assign('ur_here',          $_LANG['add_region']);
    $smarty->assign('action_link',      array('text' => $_LANG['region_list'], 'href'=>'region_area.php?act=list'));
    $smarty->assign('form_action',      'insert');
    $smarty->assign('province_list',             $province_list);

    assign_query_info();
    $smarty->display('region_area_info.dwt');
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 检查权限 */
    admin_priv('region_area');
    $ra_name = empty($_POST['ra_name']) ? '' : trim($_POST['ra_name']);
	$ra_sort = empty($_POST['ra_sort']) ? '' : trim($_POST['ra_sort']);
	$area_list = !isset($_POST['area_list']) ? array() : $_POST['area_list'];
	
	$where = "ra_name = '$ra_name'";
	$date = array('ra_id');
	$ra_id = get_table_date('merchants_region_area', $where, $date);
	
	if($ra_id > 0 || empty($ra_name)){
		$href = "region_area.php?act=add";
		$add_info = $_LANG['add_failed'];
	}else{
		$href = "region_area.php?act=list";
		$add_info = $_LANG['add_success'];
		
		/* 更新会员的其它信息 */
		$other =  array(); 	 	
		$other['ra_name'] = $ra_name;
		$other['ra_sort'] = $ra_sort;
		$other['add_time'] = gmtime();
		$db->autoExecute($ecs->table('merchants_region_area'), $other, 'INSERT');
		$ra_id = $db->insert_id();
		
		get_area_add_bacth($ra_id, $area_list);
	
		/* 记录管理员操作 */
		admin_log($ra_name, 'add', 'merchants_region_area');
	}

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>$href);
    sys_msg(sprintf($add_info, htmlspecialchars(stripslashes($ra_name))), 0, $link);

}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('region_area');
	
	$ra_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	
	$where = "ra_id = '$ra_id'";
	$date = array('ra_id', 'ra_name', 'ra_sort');
	$region_info = get_table_date('merchants_region_area', $where, $date);
	
	$province_list = get_warehouse_province('admin', $ra_id);

    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['edit_region']);
    $smarty->assign('action_link',      array('text' => $_LANG['region_list'], 'href'=>'region_area.php?act=list&' . list_link_postfix()));
    $smarty->assign('region_info',             $region_info);
	$smarty->assign('province_list',             $province_list);
    $smarty->assign('form_action',      'update');

    $smarty->display('region_area_info.dwt');
}

/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('region_area');
    $ra_name = empty($_POST['ra_name']) ? '' : trim($_POST['ra_name']);
	$ra_sort = empty($_POST['ra_sort']) ? '' : trim($_POST['ra_sort']);
	$area_list = !isset($_POST['area_list']) ? array() : $_POST['area_list'];
	
	$where = "ra_name = '$ra_name' and ra_id <> '" .$_POST['id']. "'";
	$date = array('ra_id');
	$ra_id = get_table_date('merchants_region_area', $where, $date);
	
	if($ra_id > 0 || empty($ra_name)){
		$update_info = $_LANG['update_failed'];
	}else{
		$update_info = $_LANG['update_success'];
		
		/* 更新会员的其它信息 */
		$other =  array(); 	 	
		$other['ra_name'] = $ra_name;
		$other['ra_sort'] = $ra_sort;
		$other['up_titme'] = gmtime();
		
		$db->autoExecute($ecs->table('merchants_region_area'), $other, 'UPDATE', "ra_id = '" .$_POST['id']. "'");
		
		get_area_add_bacth($_POST['id'], $area_list);
	
		/* 记录管理员操作 */
		admin_log($ra_name, 'edit', 'merchants_region_area');
	}

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'region_area.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = "region_area.php?act=edit&id=" . $_POST['id'];

    sys_msg($update_info, 0, $links);

}

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('region_area');
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
	
	$db->query("delete from " .$GLOBALS['ecs']->table('merchants_region_area'). " where ra_id = '$id'");
	$db->query("delete from " .$GLOBALS['ecs']->table('merchants_region_info'). " where ra_id = '$id'");

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'region_area.php?act=list&' . list_link_postfix();

    sys_msg($_LANG['remove_success'], 0, $links);

}

/*------------------------------------------------------ */
//-- 批量删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_remove')
{
    /* 检查权限 */
    admin_priv('region_area');
	
	$checkboxes = isset($_POST['checkboxes']) ? $_POST['checkboxes'] : array();
	
	if(count($checkboxes) > 0){
		for($i=0; $i<count($checkboxes); $i++){
			$db->query("delete from " .$GLOBALS['ecs']->table('merchants_region_area'). " where ra_id = '" .$checkboxes[$i]. "'");
			$db->query("delete from " .$GLOBALS['ecs']->table('merchants_region_info'). " where ra_id = '" .$checkboxes[$i]. "'");			
		}
	}

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'region_area.php?act=list&' . list_link_postfix();

    sys_msg($_LANG['remove_success'], 0, $links);
}

/* 编辑区域名称 */
elseif ($_REQUEST['act'] == 'edit_ra_name')
{
    /* 检查权限 */
    check_authz_json('region_area');

    $ra_id       = intval($_POST['id']);
    $ra_name   = $_POST['val'];

	$sql = "update " .$ecs->table('merchants_region_area'). " set ra_name = '$ra_name' where ra_id = '$ra_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($ra_name);
    }
}

/* 编辑区域排序 */
elseif ($_REQUEST['act'] == 'edit_ra_sort')
{
    /* 检查权限 */
    check_authz_json('region_area');

    $ra_id     = intval($_POST['id']);
    $ra_sort   = $_POST['val'];

	$sql = "update " .$ecs->table('merchants_region_area'). " set ra_sort = '$ra_sort' where ra_id = '$ra_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($ra_sort);
    }
}

/**
 *  返回地区区域列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function region_area_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
		
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'ra_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $ex_where = ' WHERE 1 ';

        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('merchants_region_area') . $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT ra_id, ra_name, ra_sort, add_time ".
                " FROM " . $GLOBALS['ecs']->table('merchants_region_area') . $ex_where .
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

    $region_list = $GLOBALS['db']->getAll($sql);

    $count = count($region_list);
    for ($i=0; $i<$count; $i++)
    {
        $region_list[$i]['add_time'] = local_date("Y-m-d H:i:s", $region_list[$i]['add_time']);
		$area = get_area_list($region_list[$i]['ra_id']);
		$region_list[$i]['area_list'] = $area['region_name'];
    }

    $arr = array('region_list' => $region_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
		
    return $arr;
}


//批量添加地区
function get_area_add_bacth($ra_id = 0, $area_list){
	
	$sql = "delete from " .$GLOBALS['ecs']->table('merchants_region_info'). " where ra_id = '$ra_id'";
	$GLOBALS['db']->query($sql);
	
	$other = array();
	if(count($area_list) > 0){
		for($i=0; $i<count($area_list); $i++){
			$other['ra_id'] = $ra_id;
			$other['region_id'] = $area_list[$i];
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_region_info'), $other, 'INSERT');
		}
	}
}

//查询区域地区列表
function get_area_list($ra_id = 0){
	$sql = "select r.region_id, r.region_name from " .$GLOBALS['ecs']->table('merchants_region_info'). " as mri" . 
			" left join " .$GLOBALS['ecs']->table('region'). " as r on mri.region_id = r.region_id" .
			" where mri.ra_id = '$ra_id'";
	$res = $GLOBALS['db']->getAll($sql);		
			
	$arr = array();		
	foreach($res as $key=>$row){
		$arr[$key] = $row;
		$arr['region_name'] .= $row['region_name'] . ',';
	}		
	
	$arr['region_name'] = substr($arr['region_name'], 0, -1);
	
	return $arr;
}



?>