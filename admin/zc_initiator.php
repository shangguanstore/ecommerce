<?php

/**
 * ECSHOP 众筹发起人管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: zc_initiator.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
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
//-- 项目发起人列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $smarty->assign('ur_here', $_LANG['03_project_initiator']);
    $action_link = array('href' => 'zc_initiator.php?act=rank_logo', 'text' => $_LANG['rank_logo_manage']);
    $action_link2 = array('href' => 'zc_initiator.php?act=add', 'text' => $_LANG['add_zc_initiator']);	
	$smarty->assign('action_link2',$action_link2);
	$smarty->assign('action_link',$action_link);
	$list = zc_initiator_list();
	foreach($list['zc_initiator'] as $k=>$v ){//处理等级标识
		$logo = explode(',',$v['rank']);
		if($logo){
			foreach($logo as $val){
				$list['zc_initiator'][$k]['logo'][] = get_rank_logo($val);
				}
		}
	}
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);
	$smarty->assign('initiator',$list['zc_initiator']);
    $smarty->display('zc_initiator_list.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query')
{
	$list = zc_initiator_list();
	foreach($list['zc_initiator'] as $k=>$v ){//处理等级标识
		$logo = explode(',',$v['rank']);
		if($logo){
			foreach($logo as $val){
				$list['zc_initiator'][$k]['logo'][] = get_rank_logo($val);
				}
		}
	}
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('initiator',    $list['zc_initiator']);   //  把结果赋值给页面
    make_json_result($smarty->fetch('zc_initiator_list.dwt'), '',
    array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑发起人
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'add'||$_REQUEST['act'] == 'edit')
{   
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	if($_REQUEST['act'] == 'add')
	{
		$smarty->assign('ur_here', $_LANG['add_zc_initiator']);
	}
	if($_REQUEST['act'] == 'edit')
	{
		$smarty->assign('ur_here', $_LANG['edit_zc_initiator']);
	}
	
    $action_link = array('href' => 'zc_initiator.php?act=list', 'text' => $_LANG['03_project_initiator']);	
	$smarty->assign('action_link',$action_link);
	
	if($_GET['id']){
		$id = $_GET['id'];
		$sql = " SELECT * FROM ".$ecs->table('zc_initiator')." WHERE id = '$id' ";
		$result = $db->getRow($sql);
		$logo_sql = " SELECT id, logo_name FROM ".$ecs->table('zc_rank_logo');
		$res = $db->getAll($logo_sql);
		$smarty->assign('logo',$res);
		$smarty->assign('state','update');
		$smarty->assign('result',$result);
		$smarty->display('zc_initiator_info.dwt');
	}else{
		$sql = " SELECT id, logo_name FROM ".$ecs->table('zc_rank_logo');
		$res = $db->getAll($sql);
		$smarty->assign('logo',$res);
		$smarty->assign('state','insert');
		$smarty->display('zc_initiator_info.dwt');
	}
}

/*------------------------------------------------------ */
//-- 添加发起人时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');	
	
	//处理接收数据
	$name      =  !empty($_POST['name'])      ?trim($_POST['name'])      : '';
	$company   =  !empty($_POST['company'])   ?trim($_POST['company'])   : '';
	$intro     =  !empty($_POST['intro'])     ?trim($_POST['intro'])     : '';
	$describe  =  !empty($_POST['describe'])  ?trim($_POST['describe'])  : '';
	$logo      =  !empty($_POST['logo'])      ?intval($_POST['logo']): 0;
	//用户等级，关联等级标识
	/*$logo 	   =  '';
	foreach($_POST['logo'] as $k=>$v){
		$logo .= $k.',';
	}
	$logo = trim($logo,',');*/

	//判断名称不能重复
	$sql = " SELECT id FROM ".$ecs->table('zc_initiator')." WHERE name = '$name' ";
	$is_exist = $db->getOne($sql);
	if($is_exist){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['name_repeat'],1,$links);	
		exit;
	}
    /* 处理商品图片 */
	$img		 = '';  // 初始化说明图片
	$dir         = 'initiator_image';
	$img = $image->upload_image($_FILES['img'],$dir);
	$sql = " INSERT INTO".$ecs->table('zc_initiator')."(`id`,`name`,`company`,`img`,`intro`,`describe`,`rank`) ".
		   " VALUES ('','$name','$company','$img','$intro','$describe','$logo') ";
	$insert = $db->query($sql);
	if($insert){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_initiator.php?act=list';
		sys_msg($_LANG['add_succeed'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['add_failure'],1,$links);
	}
}

/*------------------------------------------------------ */
//-- 编辑发起人时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'update')
{  
    /* 权限检查 */
    admin_priv('zc_initiator_manage');

	//处理接收数据
	$id        =  !empty($_POST['init_id'])   ?trim($_POST['init_id'])   : 0;
	$name      =  !empty($_POST['name'])      ?trim($_POST['name'])      : '';
	$company   =  !empty($_POST['company'])   ?trim($_POST['company'])   : '';
	$intro     =  !empty($_POST['intro'])     ?trim($_POST['intro'])     : '';
	$describe  =  !empty($_POST['describe'])  ?trim($_POST['describe'])  : '';
	$logo      =  !empty($_POST['logo'])      ?intval($_POST['logo']): 0;
	//用户等级，关联等级标识
	/*$logo 	   =  '';
	foreach($_POST['logo'] as $k=>$v){
		$logo .= $k.',';
	}
	$logo = trim($logo,',');*/
	
	//判断名称不能重复
	$sql = " SELECT id FROM ".$ecs->table('zc_initiator')." WHERE name = '$name' and id <> $id ";
	$is_exist = $db->getOne($sql);
	if($is_exist){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['name_repeat'],1,$links);	
		exit;
	}
	
    /* 处理商品图片 */
	$img		 = '';  // 初始化说明图片
	$dir         = 'initiator_image';
	if(!empty($_FILES['img']['name'])){
		$img = $image->upload_image($_FILES['img'],$dir);
	}
	//有上传图片，删除原图
        $sql = "SELECT img "." FROM " . $ecs->table('zc_initiator')." WHERE id = '$id'";
        $row = $db->getRow($sql);
        if ( $img != ''&& $row['img'])
        {
            @unlink(ROOT_PATH . $row['img']);
        }
	$sql = " UPDATE ".$ecs->table('zc_initiator')." SET ".
		   " `name`='$name', ".
		   " `company`='$company', ".
		   " `intro`='$intro', ".
		   " `describe`='$describe', ";
	if($img){
		$sql .= " `img`='$img', ";
	}
		   
		   $sql .= " `rank`='$logo' WHERE id='$id' ";
    $update = $db->query($sql);
	if($update){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_initiator.php?act=list';
		sys_msg($_LANG['edit_success'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['edit_fail'],1,$links);
	}
}

/*------------------------------------------------------ */
//-- 删除发起人
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	$id = $_GET['id'];
	$sql = " SELECT count(*) FROM ".$ecs->table('zc_initiator')." WHERE id = '$id' ";
	$res = $db->getOne($sql);
	$sql = "SELECT img "." FROM " . $ecs->table('zc_initiator')." WHERE id = '$id'";
	$row = $db->getRow($sql);
		@unlink(ROOT_PATH . $row['img']);
	$sql = " DELETE FROM ".$ecs->table('zc_initiator')." WHERE id = '$id' ";
	$db->query($sql);
	Header('Location:zc_initiator.php?act=list');
}

/*------------------------------------------------------ */
//-- 等级标识列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'rank_logo')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
    
    $smarty->assign('ur_here', $_LANG['rank_logo_manage']);
    $action_link = array('href' => 'zc_initiator.php?act=list', 'text' => $_LANG['03_project_initiator']);
    $action_link2 = array('href' => 'zc_initiator.php?act=add_rank_logo', 'text' => $_LANG['add_rank_logo']);	
	$smarty->assign('action_link',$action_link);
	$smarty->assign('action_link2',$action_link2);

	$list = zc_rank_logo_list();
	$smarty->assign('arr_zc',$list);
	$smarty->assign('full_page',    1);
    $smarty->display('zc_rank_logo_list.dwt');
}

/*------------------------------------------------------ */
//-- 添加/编辑等级身份标识
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'add_rank_logo'||$_REQUEST['act'] == 'edit_rank_logo')
{   
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	if($_REQUEST['act'] == 'add_rank_logo')
	{
		$smarty->assign('ur_here', $_LANG['add_rank_logo']);
	}
	if($_REQUEST['act'] == 'edit_rank_logo')
	{
		$smarty->assign('ur_here', $_LANG['edit_rank_logo']);
	}		
	
    $action_link = array('href' => 'zc_initiator.php?act=rank_logo', 'text' => $_LANG['rank_logo_manage']);	
	$smarty->assign('action_link',$action_link);		
	
	if($_GET['id']){
		$id = $_GET['id'];
		$sql = " SELECT * FROM ".$ecs->table('zc_rank_logo')." WHERE id = '$id' ";
		$result = $db->getRow($sql);
		$smarty->assign('logo_id',$id);
		$smarty->assign('state','update_rank');
		$smarty->assign('result',$result);
		$smarty->display('zc_rank_logo_info.dwt');
	}else{
		$smarty->assign('state','insert_rank');
		$smarty->display('zc_rank_logo_info.dwt');
	}
}

/*------------------------------------------------------ */
//-- 添加等级身份标识时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'insert_rank')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	//处理接收数据
	$logo_name =  !empty($_POST['logo_name']) ?trim($_POST['logo_name']) : '';
	$intro     =  !empty($_POST['intro'])     ?trim($_POST['intro'])     : '';
	//判断名称不能重复
	$sql = " SELECT id FROM ".$ecs->table('zc_rank_logo')." WHERE logo_name = '$logo_name' ";
	$is_exist = $db->getOne($sql);
	if($is_exist){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['name_repeat'],1,$links);	
		exit;
	}
    /* 处理商品图片 */
	$img		 = '';  // 初始化说明图片
	$dir         = 'rank_image';
	$img = $image->upload_image($_FILES['img'],$dir);
	$sql = " INSERT INTO".$ecs->table('zc_rank_logo')."(`id`,`logo_name`,`img`,`logo_intro`) ".
		   " VALUES ('','$logo_name','$img','$intro') ";
	$insert = $db->query($sql);
	if($insert){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_initiator.php?act=rank_logo';
		sys_msg($_LANG['add_succeed'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['add_failure'],1,$links);
	}
}

/*------------------------------------------------------ */
//-- 编辑等级身份标识时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'update_rank')
{  
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	//处理接收数据
	$id        =  !empty($_POST['logo_id'])   ?trim($_POST['logo_id'])   : 0;
	$logo_name =  !empty($_POST['logo_name']) ?trim($_POST['logo_name']) : '';
	$intro     =  !empty($_POST['intro'])     ?trim($_POST['intro'])     : '';
	//判断名称不能重复
	/*$sql = " SELECT id FROM ".$ecs->table('zc_rank_logo')." WHERE logo_name = '$logo_name' ";
	$is_exist = $db->getOne($sql);
	if($is_exist){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['name_repeat'],1,$links);	
		exit;
	}*/
    /* 处理商品图片 */
	$img		 = '';  // 初始化说明图片
	$dir         = 'rank_image';
	if(!empty($_FILES['img']['name'])){
		$img = $image->upload_image($_FILES['img'],$dir);
	}
	//有上传图片，删除原图
        $sql = "SELECT img "." FROM " . $ecs->table('zc_rank_logo')." WHERE id = '$id'";
        $row = $db->getRow($sql);
        if ( $img != ''&& $row['img'])
        {
            @unlink(ROOT_PATH . $row['img']);
        }
	$sql = " UPDATE ".$ecs->table('zc_rank_logo')." SET ".
		   " `logo_name`='$logo_name', ";
	if($img){
		$sql .= " `img`='$img', ";
	}
		   
	$sql .= " `logo_intro`='$intro' WHERE id='$id' ";
    $update = $db->query($sql);
	if($update){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_initiator.php?act=rank_logo';
		sys_msg($_LANG['edit_success'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['edit_fail'],1,$links);
	}
}

/*------------------------------------------------------ */
//-- 删除等级身份标识
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del_rank_logo')
{
    /* 权限检查 */
    admin_priv('zc_initiator_manage');
	
	$id = $_GET['id'];
	$sql = "SELECT img "." FROM " . $ecs->table('zc_rank_logo')." WHERE id = '$id'";
	$row = $db->getRow($sql);
		@unlink(ROOT_PATH . $row['img']);
	$sql = " DELETE FROM ".$ecs->table('zc_rank_logo')." WHERE id = '$id' ";
	$db->query($sql);
	Header('Location:zc_initiator.php?act=rank_logo');
}


/**
 * 获得发起人列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @params  integer $conditions
 * @return  array
 */
function zc_initiator_list($conditions = '')
{

    $result = get_filter();
	
    if ($result === false)
    {

        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		$where = " WHERE 1=1 ";
	
		/*if(isset($_REQUEST['seller_id'])&&intval($_REQUEST['seller_id'])<3)
		{
			$filter['seller_id']           = empty($_REQUEST['seller_id']) ? 0 : intval($_REQUEST['seller_id']);//by wang 商家入住
			if(intval($_REQUEST['seller_id'])>0)
			{
				$where .=" and seller_id>0 ";		
			}
			else
			{
				$where .=" and seller_id=0";	
			}
		}*/
		
        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND name LIKE '%" . mysqli_like_quote($filter['keyword']) . "%' ";
        }

        $where .= $conditions;

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('zc_initiator'). $where ;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT `id`, `name`, `img`, `company`, `intro`, `describe`, `rank` " .
                    " FROM " . $GLOBALS['ecs']->table('zc_initiator') . $where .
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
    return array('zc_initiator' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
/**
 * 获得等级标识列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @params  integer $conditions
 * @return  array
 */
function zc_rank_logo_list()
{
    $sql = "SELECT `id`, `logo_name`, `img`, `logo_intro` FROM " . $GLOBALS['ecs']->table('zc_rank_logo') ;
    $row = $GLOBALS['db']->getAll($sql);

    return $row;
}
//取得等级身份标识
function get_rank_logo($id){
	$sql = " SELECT img FROM ".$GLOBALS['ecs']->table('zc_rank_logo')." WHERE id = '$id' ";
	$row = $GLOBALS['db']->getRow($sql);
	return $row;
}

?>
