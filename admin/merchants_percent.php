<?php

/**
 * ECSHOP 管理中心奖励额度管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wanglei $
 * $Id: suppliers.php 15013 2009-05-13 09:31:42Z wanglei $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

define('SUPPLIERS_ACTION_LIST', 'delivery_view,back_view');

/*------------------------------------------------------ */
//-- 奖励额度列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     admin_priv('merchants_percent');
	 
   	 $result = percent_list();

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['suppliers_percent_list']); // 当前导航
    $smarty->assign('action_link', array('href' => 'merchants_percent.php?act=add', 'text' => $_LANG['add_suppliers_percent']));
	$smarty->assign('action_link2', array('href' => 'merchants_commission.php?act=list', 'text' => $_LANG['suppliers_list_server']));
	
    $smarty->assign('full_page',        1); // 翻页参数

    $smarty->assign('percent_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_suppliers_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('merchants_percent_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('merchants_percent');

    $result = percent_list();

    $smarty->assign('percent_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('merchants_percent_list.dwt'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/*------------------------------------------------------ */
//-- 列表页编辑名称 ajax
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_percent_value')
{
    check_authz_json('merchants_percent');

    $id     = intval($_POST['id']);
    $value   = json_str_iconv(trim($_POST['val']));

    /* 判断名称是否重复 */
    $sql = "SELECT percent_id
            FROM " . $ecs->table('merchants_percent') . "
            WHERE percent_value = '$value'
            AND percent_id <> '$id' ";
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['percent_name_exist'], $value));
    }
    else
    {
        /* 保存奖励额度信息 */
        $sql = "UPDATE " . $ecs->table('merchants_percent') . "
                SET percent_value = '$value'
                WHERE percent_id = '$id'";
        if ($result = $db->query($sql))
        {
            /* 记日志 */
            admin_log($value, 'edit', 'merchants_percent');

            clear_cache_files();

            make_json_result(stripslashes($value));
        }
        else
        {
            make_json_result(sprintf($_LANG['agency_edit_fail'], $value));
        }
    }
}

/*------------------------------------------------------ */
//-- 列表页编辑排序 ajax
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('merchants_percent');

    $id     = intval($_POST['id']);
    $value   = json_str_iconv(trim($_POST['val']));

    /* 判断名称是否重复 */
    $sql = "SELECT percent_id
            FROM " . $ecs->table('merchants_percent') . "
            WHERE sort_order = '$value'
            AND percent_id <> '$id' ";
    if ($db->getOne($sql))
    {
        make_json_error(sprintf($_LANG['percent_sort_exist'], $value));
    }
    else
    {
        /* 保存奖励额度信息 */
        $sql = "UPDATE " . $ecs->table('merchants_percent') . "
                SET sort_order = '$value'
                WHERE percent_id = '$id'";
        if ($result = $db->query($sql))
        {
            /* 记日志 */
            admin_log($value, 'edit', 'merchants_percent');

            clear_cache_files();

            make_json_result(stripslashes($value));
        }
        else
        {
            make_json_result(sprintf($_LANG['agency_edit_fail'], $value));
        }
    }
}

/*------------------------------------------------------ */
//-- 删除奖励额度
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('merchants_percent');

    $id = intval($_REQUEST['id']);

	$sql = "DELETE FROM " . $ecs->table('merchants_percent') . "
		WHERE percent_id = '$id'";
	$db->query($sql);

	/* 记日志 */
	admin_log($suppliers['percent_id'], 'remove', 'merchants_percent');

	/* 清除缓存 */
	clear_cache_files();

    $url = 'merchants_percent.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");

    exit;
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_record_selected']);
    }
    else
    {
        /* 检查权限 */
        admin_priv('merchants_percent');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['remove']))
        {
            $sql = "SELECT *
                    FROM " . $ecs->table('merchants_percent') . "
                    WHERE percent_id " . db_create_in($ids);
            $percent = $db->getAll($sql);
            if (empty($percent))
            {
                sys_msg($_LANG['batch_drop_no']);
            }


            $sql = "DELETE FROM " . $ecs->table('merchants_percent') . "
                WHERE percent_id " . db_create_in($ids);
            $db->query($sql);

            /* 记日志 */
            $percent_value = '';
            foreach ($percent as $value)
            {
                $percent_value .= $value['percent_value'] . '|';
            }
            admin_log($percent_value, 'remove', 'suppliers');

            /* 清除缓存 */
            clear_cache_files();

            sys_msg($_LANG['batch_drop_ok']);
        }
    }
}

/*------------------------------------------------------ */
//-- 添加、编辑奖励额度
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('add', 'edit')))
{
    /* 检查权限 */
    admin_priv('merchants_percent');
	
	$smarty->assign('action_link', array('href' => 'merchants_percent.php?act=list', 'text' => $_LANG['suppliers_percent_list']));
    
	if ($_REQUEST['act'] == 'add')
    {
        $smarty->assign('ur_here', $_LANG['add_suppliers_percent']);
        

        $smarty->assign('form_action', 'insert');

        assign_query_info();

        $smarty->display('merchants_percent_info.dwt');

    }
    elseif ($_REQUEST['act'] == 'edit')
    {
        $suppliers = array();

        /* 取得奖励额度信息 */
        $id = $_REQUEST['id'];
        $sql = "SELECT * FROM " . $ecs->table('merchants_percent') . " WHERE percent_id = '$id'";
        $percent = $db->getRow($sql);
        if (count($percent) <= 0)
        {
            sys_msg('suppliers_percent does not exist');
        }

        $sql = "SELECT * FROM " . $ecs->table('merchants_percent') . " WHERE percent_id = '$id'";
		
        $percent = $db->getRow($sql);

        $smarty->assign('ur_here', $_LANG['edit_suppliers_percent']);

        $smarty->assign('form_action', 'update');
        $smarty->assign('percent', $percent);

        assign_query_info();

        $smarty->display('merchants_percent_info.dwt');
    }

}

/*------------------------------------------------------ */
//-- 提交添加、编辑奖励额度
/*------------------------------------------------------ */
elseif (in_array($_REQUEST['act'], array('insert', 'update')))
{
    /* 检查权限 */
    admin_priv('merchants_percent');

    if ($_REQUEST['act'] == 'insert')
    {
        /* 提交值 */
        $suppliers = array( 'percent_value'   => trim($_POST['percent_value']),
							'sort_order'   => trim($_POST['sort_order']),
							'add_time'   => gmtime()
                           );

        /* 判断名称是否重复 */
        $sql = "SELECT percent_id
                FROM " . $ecs->table('merchants_percent') . "
                WHERE percent_value = '" . $suppliers['percent_value'] . "' ";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['percent_name_exist']);
        }

        $db->autoExecute($ecs->table('merchants_percent'), $suppliers, 'INSERT');
        $suppliers['percent_id'] = $db->insert_id();


        /* 记日志 */
        admin_log($suppliers['percent_value'], 'add', 'merchants_percent');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(array('href' => 'merchants_percent.php?act=add',  'text' => $_LANG['continue_add_percent']),
                       array('href' => 'merchants_percent.php?act=list', 'text' => $_LANG['back_percent_list'])
                       );
        sys_msg($_LANG['add_percent_ok'], 0, $links);

    }

    if ($_REQUEST['act'] == 'update')
    {
        /* 提交值 */
        $percent = array('id'   => trim($_POST['id']));

        $percent['new'] = array( 'percent_value'   => trim($_POST['percent_value']),
								 'sort_order'   => trim($_POST['sort_order'])
                           );

        /* 取得奖励额度信息 */
        $sql = "SELECT * FROM " . $ecs->table('merchants_percent') . " WHERE percent_id = '" . $percent['id'] . "'";
        $percent['old'] = $db->getRow($sql);
        if (empty($percent['old']['percent_id']))
        {
            sys_msg('suppliers_percent does not exist');
        }

        /* 判断名称是否重复 */
        $sql = "SELECT percent_id
                FROM " . $ecs->table('merchants_percent') . "
                WHERE percent_value = '" . $suppliers['new']['percent_value'] . "'
                AND percent_id <> '" . $percent['id'] . "'";
        if ($db->getOne($sql))
        {
            sys_msg($_LANG['percent_name_exist']);
        }
		
		/* 保存奖励额度信息 */
        $db->autoExecute($ecs->table('merchants_percent'), $percent['new'], 'UPDATE', "percent_id = '" . $percent['id'] . "'");

        /* 记日志 */
        admin_log($suppliers['old']['percent_value'], 'edit', 'merchants_percent');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links[] = array('href' => 'merchants_percent.php?act=list', 'text' => $_LANG['back_percent_list']);
        sys_msg($_LANG['edit_percent_ok'], 0, $links);
    }

}

/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function percent_list()  //ecmoban模板堂 --zhuo
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'percent_id' : trim($_REQUEST['sort_by']);  //ecmoban模板堂 --zhuo
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);  

        $where = 'WHERE 1 ';

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('merchants_percent') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        $sql = "SELECT percent_id, percent_value , sort_order, add_time  
                FROM " . $GLOBALS['ecs']->table("merchants_percent") . $au_sql . " 
                $where ". $admin_sup ."
                ORDER BY " . $filter['sort_by'] . " " .$filter['sort_order']. "
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $percent_list = $GLOBALS['db']->getAll($sql);
	
	$count = count($percent_list);
    for ($i=0; $i<$count; $i++)
    {
        $percent_list[$i]['add_time'] = local_date($GLOBALS['_CFG']['date_format'], $percent_list[$i]['add_time']);
    }
	
    $arr = array('result' => $percent_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
?>