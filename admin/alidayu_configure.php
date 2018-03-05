<?php

/**
 * ECSHOP 阿里大鱼短信模板管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . '/data/sms_templates.php');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
/* 检查权限 */
//admin_priv('oss_configure');

$smarty->assign('menu_select',array('action' => '01_system', 'current' => 'alidayu_configure'));
/*------------------------------------------------------ */
//-- 阿里大鱼短信模板列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    
    $smarty->assign('action_link',  array('text' => $_LANG['alidayu_add'], 'href'=>'alidayu_configure.php?act=add'));
    $smarty->assign('action_link2',  array('text2' => $_LANG['alidayu_set'], 'href'=>'alidayu_configure.php?act=set_up'));
    
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['alidayu_configure']);
    $smarty->assign('form_act',    'insert');
    
    $note_list = note_list();
	
	foreach($note_list['note_list'] as $k=>$v){
		$note_list['note_list'][$k]['send_time'] = array_search($v['send_time'],$send_time);
		$note_list['note_list'][$k]['add_time'] = local_date('Y-m-d H:i:s',$v['add_time']);
	}
	
    $smarty->assign('note_list',    $note_list['note_list']);
    $smarty->assign('filter',       $note_list['filter']);
    $smarty->assign('record_count', $note_list['record_count']);
    $smarty->assign('page_count',   $note_list['page_count']);
    $smarty->assign('full_page',    1);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('alidayu_configure_list.dwt');
}

/*------------------------------------------------------ */
//-- ajax返回短信模板列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $note_list = note_list();

	foreach($note_list['note_list'] as $k=>$v){
		$note_list['note_list'][$k]['send_time'] = array_search($v['send_time'],$send_time);
		$note_list['note_list'][$k]['add_time'] = local_date('Y-m-d H:i:s',$v['add_time']);
	}	
	
    $smarty->assign('note_list',    $note_list['note_list']);
    $smarty->assign('filter',       $note_list['filter']);
    $smarty->assign('record_count', $note_list['record_count']);
    $smarty->assign('page_count',   $note_list['page_count']);
    
    $sort_flag  = sort_flag($note_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('alidayu_configure_list.dwt'), '', array('filter' => $note_list['filter'], 'page_count' => $note_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加短信模板
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    $smarty->assign('action_link',  array('text' => $_LANG['alidayu_list'], 'href'=>'alidayu_configure.php?act=list'));
    
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['alidayu_configure']);
    $smarty->assign('form_act',    'insert');
	$smarty->assign('send_time',    $send_time);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('alidayu_configure_info.dwt');
}

/*------------------------------------------------------ */
//-- 基本信息设置
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'set_up')
{
    $smarty->assign('action_link',  array('text' => $_LANG['alidayu_list'], 'href'=>'alidayu_configure.php?act=list'));
    
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['alidayu_configure']);
    $smarty->assign('form_act',    'set_update');
    $smarty->assign('alidayu_type',    $_CFG['alidayu_type']);
    
    /* 列表页面 */
    assign_query_info();
    $smarty->display('alidayu_set_up.dwt');
}

if ($_REQUEST['act'] == 'set_update')
{
    $other['value'] = !empty($_REQUEST['alayu_type']) ? $_REQUEST['alayu_type'] : 0;
    $db->autoExecute($ecs->table('shop_config'), $other, "UPDATE", "code = 'alidayu_type'");
    
    $href = 'alidayu_configure.php?act=list';
    
    /* 清除缓存 */
    clear_all_files();
    
    load_config();
    
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>$href);
    sys_msg('', 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑短信模板
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    
    $smarty->assign('action_link',  array('text' => $_LANG['alidayu_list'], 'href'=>'alidayu_configure.php?act=list'));
    
    $date = array('*');
    $where = "id = '$id'";
    $note_info = get_table_date('alidayu_configure', $where, $date);
    $smarty->assign('note',    $note_info);
	$smarty->assign('send_time',    $send_time);  
	
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['alidayu_configure']);
    $smarty->assign('form_act',    'update');

    /* 列表页面 */
    assign_query_info();
    $smarty->display('alidayu_configure_info.dwt');
}

/*------------------------------------------------------ */
//-- 添加/编辑短信模板
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    $signature = empty($_POST['signature']) ? 0 : intval($_POST['signature']);    
	
    $other['temp_id'] = empty($_POST['temp_id']) ? '' : trim($_POST['temp_id']);
    $other['temp_content'] = empty($_POST['temp_content']) ? '' : trim($_POST['temp_content']);
	$other['set_sign'] = empty($_POST['set_sign']) ? '' : trim($_POST['set_sign']);	
    $other['send_time'] = empty($_POST['send_time']) ? '' : trim($_POST['send_time']);
    $other['add_time'] = gmtime();
    $date = array('temp_id');
    $where = "temp_id = '" .$other['temp_id']. "'";
    $where .= !empty($id) ? " AND id <> '$id'" : '';
    $note_info = get_table_date('alidayu_configure', $where, $date);
    
    if($id){
        $db->autoExecute($ecs->table('alidayu_configure'), $other, "UPDATE", "id = '$id'");
        $href = 'alidayu_configure.php?act=edit&id=' . $id;
        
        $lang_name = $_LANG['edit_success'];
    }else{
        $db->autoExecute($ecs->table('alidayu_configure'), $other);
        $href = 'alidayu_configure.php?act=list';
        $lang_name = $_LANG['add_success'];
    }
    
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>$href);
    sys_msg(sprintf($lang_name, htmlspecialchars(stripslashes($other['temp_id']))), 0, $link);
}

/*------------------------------------------------------ */
//-- 删除短信模板
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
    
    $sql = "SELECT temp_id FROM " . $GLOBALS['ecs']->table('alidayu_configure') . " WHERE id = '$id'";
    $temp_id = $GLOBALS['db']->getOne($sql);
    
    $sql = "DELETE FROM " .$GLOBALS['ecs']->table('alidayu_configure'). " WHERE id = '$id'";
    $GLOBALS['db']->query($sql);
    
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'alidayu_configure.php?act=list');
    sys_msg(sprintf($_LANG['remove_success'], $temp_id), 0, $link);
}

/* ------------------------------------------------------ */
//-- 读取默认模板信息
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'loat_template') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $tpl = !empty($_REQUEST['tpl']) ? trim($_REQUEST['tpl']) : '';
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $result['content'] = $template[$tpl];
    $result['tpl'] = $tpl;

    $json = new JSON;
    die($json->encode($result));
}

/**
 *  返回sms列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function note_list()
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
        
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);
        
        $where = " WHERE 1 ";
        
        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('alidayu_configure') . $where);
        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('alidayu_configure') . $where .
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
    
    $note_list = $GLOBALS['db']->getAll($sql);
    
    $arr = array('note_list' => $note_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
} 
   
?>