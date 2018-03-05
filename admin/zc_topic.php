<?php

/**
 * ECSHOP 众筹话题管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: zc_topic.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('zc_topic'), $db, 'topic_id', 'topic_status');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
$smarty->assign('act', $_REQUEST['act']);
/*------------------------------------------------------ */
//-- 列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限检查 */
    admin_priv('zc_topic_manage');
	
	/* 列表类型：父子话题 */
	if($_REQUEST['parent_id'])
	{
		$smarty->assign('child_list', 1);
		$smarty->assign('action_link',   array('href' => 'zc_topic.php?act=list', 'text' => $_LANG['zc_parent_list']));
	}
	
    $smarty->assign('ur_here', $_LANG['04_topic_list']);
	$list = zc_topic_list();
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);
	$smarty->assign('list', $list['topic_list']);
    $smarty->display('zc_topic_list.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query')
{
	$list = zc_topic_list();
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('list',    $list['topic_list']);   //  把结果赋值给页面
    make_json_result($smarty->fetch('zc_topic_list.dwt'), '',
    array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 修改显示状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_display')
{
    check_authz_json('zc_topic_manage');

    $topic_id       = intval($_POST['id']);
    $topic_status        = intval($_POST['val']);

    if ($exc->edit(" topic_status = '$topic_status' ", $topic_id))
    {
        clear_cache_files();
        make_json_result($topic_status);
    }
}

/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del')
{
    /* 权限检查 */
    admin_priv('zc_topic_manage');
	
    $topic_id = intval($_REQUEST['id']);
	
	$sql = "SELECT COUNT(*) " . "FROM " . $ecs->table('zc_topic') .
            " WHERE parent_topic_id = '$topic_id'";
    $child_topic_num = $db->getOne($sql);
	
	if($child_topic_num > 0){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['zc_child_exist'],0,$links);	
		exit;
	}
	
	$sql = " DELETE FROM ".$ecs->table('zc_topic')." WHERE topic_id = '$topic_id' ";
	$db->query($sql);
	Header('Location:zc_topic.php?act=list');
}

/*------------------------------------------------------ */
//-- 批量删除用户评论
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'batch')
{
    /* 检查权限 */
    admin_priv('zc_topic_manage');
    
    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'deny';

    if (isset($_POST['checkboxes']))
    {
        switch ($action)
        {
            case 'remove':
				$zt = 0;
				$note = "";
				foreach($_POST['checkboxes'] as $key=>$val)
				{
					$sql = "SELECT COUNT(*) " . "FROM " . $ecs->table('zc_topic') .
							" WHERE parent_topic_id = '$val'";
					$child_topic_num = $db->getOne($sql);
					if($child_topic_num > 0)
					{
						$zt++;
						unset($_POST['checkboxes'][$key]);
					}
				}
				if($zt > 0)
				{
					$note = sprintf($_LANG['batch_drop_note'], $zt);
				}
				
				$db->query("DELETE FROM " . $ecs->table('zc_topic') . " WHERE " . db_create_in($_POST['checkboxes'], 'topic_id'));
				break;

           case 'allow' :
               $db->query("UPDATE " . $ecs->table('zc_topic') . " SET topic_status = 1  WHERE " . db_create_in($_POST['checkboxes'], 'topic_id'));
               break;

           case 'deny' :
               $db->query("UPDATE " . $ecs->table('zc_topic') . " SET topic_status = 0  WHERE " . db_create_in($_POST['checkboxes'], 'topic_id'));
               break;

           default :
               break;
        }

        clear_cache_files();

        $link[] = array('text' => $_LANG['go_list'], 'href' => 'zc_topic.php?act=list');
        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])) . $note, 0, $link);
    }
    else
    {
        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_list'], 'href' => 'zc_topic.php?act=list');
        sys_msg($_LANG['no_select_topic'], 0, $link);
    }
}

/**
 * 获得发起人列表
 */
function zc_topic_list($conditions = '')
{

    $result = get_filter();
	
    if ($result === false)
    {

        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		$filter['parent_id']        = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
		
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'topic_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		$where = " WHERE 1=1 ";
		
        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND zt.topic_content LIKE '%" . mysqli_like_quote($filter['keyword']) . "%' ";
        }
		
		/* 父子话题 */
		if(!empty($filter['parent_id']))
		{
			$where .= " AND zt.parent_topic_id = '$filter[parent_id]' ";
		}
		else
		{
			$where .= " AND zt.parent_topic_id = 0 ";
		}

        $where .= $conditions;
		
		$leftjoin = " LEFT JOIN ".$GLOBALS['ecs']->table('users')." AS u ON u.user_id = zt.user_id ".
			" LEFT JOIN ".$GLOBALS['ecs']->table('zc_project')." AS zp ON zp.id = zt.pid ";

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('zc_topic'). " AS zt " . $leftjoin . $where ;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT zt.*, u.user_name, zp.title " .
                    " FROM " . $GLOBALS['ecs']->table('zc_topic') . " AS zt " . $leftjoin . $where .
                    " ORDER BY $filter[sort_by] $filter[sort_order] ".
                    " LIMIT " . $filter['start'] . ",$filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);
	
	//处理数据
	foreach($row as $key => $val)
	{
		$row[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
	}
	
    return array('topic_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
