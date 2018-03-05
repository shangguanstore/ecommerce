<?php

/**
 * ECSHOP 众筹项目管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: zc_project.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('zc_project'), $db, 'id', 'title');
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
//-- 众筹商品列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    /* 权限检查 */
    admin_priv('zc_project_manage');

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $smarty->assign('ur_here', $_LANG['01_crowdfunding_list']);
    $action_link = array('href' => 'zc_project.php?act=add', 'text' => $_LANG['add_zc_project']);
    $smarty->assign('action_link', $action_link);

    $list = zc_project_list();

    $smarty->assign('zc_cat_list', zc_cat_list(0, $cat_id));
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);
    $smarty->assign('full_page', 1);
    $smarty->assign('arr_zc', $list['zc_projects']);
    $smarty->display('zc_project_list.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query') {
    $list = zc_project_list();
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);
    $smarty->assign('arr_zc', $list['zc_projects']);   //  把结果赋值给页面

    make_json_result($smarty->fetch('zc_project_list.dwt'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑众筹商品
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'add'||$_REQUEST['act'] == 'edit')
{   
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	if($_REQUEST['act'] == 'add')
	{
		$smarty->assign('ur_here', $_LANG['add_zc_project']);
	}
	if($_REQUEST['act'] == 'edit')
	{
		$smarty->assign('ur_here', $_LANG['edit_zc_project']);
	}	
	
    $action_link = array('href' => 'zc_project.php?act=list', 'text' => $_LANG['01_crowdfunding_list']);	
	$smarty->assign('action_link',$action_link);	
	
	if($_GET['id'])
	{
		$id = $_GET['id'];
		$sql = " SELECT * FROM ".$ecs->table('zc_project')." WHERE id = '$id' ";
		$result = $db->getRow($sql);
		//创建 html editor
		create_html_editor2('details', 'details', $result['details']);
		create_html_editor2('describe', 'describe', $result['describe']);
		create_html_editor2('risk_instruction', 'risk_instruction', $result['risk_instruction']);

		$result['start_time'] = date('Y-m-d',$result['start_time']);
		$result['end_time'] = date('Y-m-d',$result['end_time']);
		$cat_id = $result['cat_id'];
		$sql1 = " SELECT cat_name FROM ".$ecs->table('zc_category')." WHERE cat_id = '$cat_id' ";
		$result['cat_name'] = $db->getOne($sql1);
		$sql = " SELECT id, name FROM ".$ecs->table('zc_initiator');
		$initiator = $db->getAll($sql);
		$smarty->assign('initiator',$initiator);
		$smarty->assign('item_id',$id);
		$smarty->assign('cat_select',  zc_cat_list(0, 0, false));
		$smarty->assign('info',$result);
		$smarty->assign('state','update');
		$smarty->display('zc_project_info.dwt');
	}
	else
	{
		//创建 html editor
		create_html_editor2('details', 'details');
		create_html_editor2('describe', 'describe');
		create_html_editor2('risk_instruction', 'risk_instruction');		
		$sql = " SELECT id, name FROM ".$ecs->table('zc_initiator');
		$initiator = $db->getAll($sql);
		$smarty->assign('initiator',$initiator);
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d',strtotime('+1 month'));
		$smarty->assign('state','insert');
		$smarty->assign('start_date',$start_date);
		$smarty->assign('end_date',$end_date);
		$smarty->assign('cat_select',  zc_cat_list(0, 0, false));
		$smarty->assign('zc_id',$zc_id);
		$smarty->display('zc_project_info.dwt');
	}
}
/*------------------------------------------------------ */
//-- 添加众筹商品时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'insert')
{   
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$title       =   !empty($_POST['title'])              ?trim($_POST['title'])              :  '';
	$cat_id      =   !empty($_POST['cat_id'])             ?trim($_POST['cat_id'])             :  0;
	$amount      =   !empty($_POST['money'])              ?trim($_POST['money'])    		  :  0;
	$start_time  =   !empty($_POST['promote_start_date']) ?strtotime(trim($_POST['promote_start_date'])) :  date('Y-m-d');
	$end_time    =   !empty($_POST['promote_end_date'])   ?strtotime(trim($_POST['promote_end_date']))   :  date('Y-m-d',strtotime('+1 month'));
	$details 	 =   !empty($_POST['details']) 			  ? trim($_POST['details']) 		  : '无描述';
	$describe    =   !empty($_POST['describe'])     	  ?trim($_POST['describe'])   		  :  '无描述';
	$risk_instruction = !empty($_POST['risk_instruction'])?trim($_POST['risk_instruction'])   :  '无描述';
	$initiator   =   !empty($_POST['initiator'])		  ?trim($_POST['initiator'])          :  '';
	$is_best     =   !empty($_POST['is_best'])		      ?trim($_POST['is_best'])            :  0;
    /* 处理商品图片 */
    $title_img   = '';  // 初始化基本图片
	$dir_title   = 'zc_title_images';
	$title_img = $image->upload_image($_FILES['tit_img'],$dir_title);
	$sql = "INSERT INTO".$ecs->table('zc_project').
	"(`id`,`cat_id`,`title`,`init_id`,`is_best`,`start_time`,`end_time`,`amount`,`title_img`, `details`,`describe`,`risk_instruction`) ".
	" VALUES (NULL,'$cat_id','$title','$initiator','$is_best','$start_time','$end_time','$amount','$title_img', '$details','$describe','$risk_instruction')";
    $insert = $db->query($sql);
	if($insert){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=list';
		sys_msg($_LANG['add_succeed'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['add_failure'],1,$links);
	}
}
/*------------------------------------------------------ */
//-- 编辑众筹项目时的处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'update')
{   
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$id			 =   $_POST['item_id'];
	$title       =   !empty($_POST['title'])              ?trim($_POST['title'])              :  '';
	$cat_id      =   !empty($_POST['cat_id'])             ?trim($_POST['cat_id'])             :  0;
	$amount      =   !empty($_POST['money'])              ?trim($_POST['money'])    		  :  0;
	$start_time  =   !empty($_POST['promote_start_date']) ?strtotime(trim($_POST['promote_start_date'])) :  date('Y-m-d');
	$end_time    =   !empty($_POST['promote_end_date'])   ?strtotime(trim($_POST['promote_end_date']))   :  date('Y-m-d',strtotime('+1 month'));
	$details 	 = 	 !empty($_POST['details']) 			  ? trim($_POST['details']) 		  : '无描述';
	$describe    =   !empty($_POST['describe'])     	  ?trim($_POST['describe'])           :  '无描述';
	$risk_instruction = !empty($_POST['risk_instruction'])?trim($_POST['risk_instruction'])   :  '无描述';
	$initiator   =   !empty($_POST['initiator'])		  ?trim($_POST['initiator'])          :  '';
	$is_best     =   !empty($_POST['is_best'])		      ?trim($_POST['is_best'])            :  0;

    /* 处理商品图片 */
    $title_img   = '';  // 初始化基本图片
	$img		 = '';  // 初始化说明图片
	$dir_title   = 'zc_title_images';
	$sql = " UPDATE ".$ecs->table('zc_project')." SET ".
		   " `cat_id`='$cat_id', ".
		   " `title`='$title', ".
		   " `init_id`='$initiator', ".
		   " `is_best`='$is_best', ".
		   " `start_time`='$start_time', ".
		   " `end_time`='$end_time', ".
		   " `amount`='$amount', ";
	if($title_img){
		$sql .=  " `title_img`='$title_img', ";
	}
	$sql .= " `details`='$details', ";
	$sql .= " `risk_instruction`='$risk_instruction', ";
	$sql .= " `describe`='$describe' WHERE id='$id' ";

    $update = $db->query($sql);
	if($update){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=list';
		sys_msg($_LANG['edit_success'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['edit_fail'],1,$links);
	}
}
/*------------------------------------------------------ */
//-- 删除众筹项目
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del')
{
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$id = $_GET['id'];
	$sql = " SELECT count(*) FROM ".$ecs->table('zc_goods')." WHERE pid = '$id' ";
	$res = $db->getOne($sql);
	if($res){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['zc_project_del_fail'],1,$links);
	}else{
	$sql = "SELECT img, title_img "." FROM " . $ecs->table('zc_project')." WHERE id = '$id'";
	$row = $db->getRow($sql);
		@unlink(ROOT_PATH . $row['title_img']);
	$row['img'] = unserialize($row['img']);
		foreach ($row['img'] as $v){
			@unlink(ROOT_PATH . $v);
		}

	$sql = " DELETE FROM ".$ecs->table('zc_project')." WHERE id = '$id' ";
	$db->query($sql);
	Header('Location:zc_project.php?act=list');
	}
}
/*------------------------------------------------------ */
//-- 修改精品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_best')
{
    $id       = intval($_POST['id']);
    $is_best        = intval($_POST['val']);

    if ($exc->edit("is_best = '$is_best'", $id))
    {
        clear_cache_files();
        make_json_result($is_best);
    }
}
/*------------------------------------------------------ */
//-- 项目方案列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'product_list')
{
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$id = $_GET['id'];
    $smarty->assign('ur_here', $_LANG['zc_goods_manage']);
    $action_link = array('href' => 'zc_project.php?act=add_product&id='.$id, 'text' => $_LANG['add_zc_goods']);
	//取得当前项目下的方案列表
	$sql = " SELECT * FROM ".$ecs->table('zc_goods')." WHERE pid = '$id' ";
	$result = $db->getAll($sql);
	$smarty->assign('id',$id);
	$smarty->assign('product_list',$result);
	$smarty->assign('action_link',$action_link);
	$smarty->assign('full_page', 1);
    $smarty->display('zc_goods_list.dwt');	
}
/*------------------------------------------------------ */
//-- 添加项目方案
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'add_product'||$_REQUEST['act'] == 'edit_product')
{
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	if($_REQUEST['act'] == 'add_product')
	{
		$smarty->assign('ur_here', $_LANG['add_zc_goods']);
	}
	if($_REQUEST['act'] == 'edit_product')
	{
		$smarty->assign('ur_here', $_LANG['edit_zc_goods']);
	}	
	
    $action_link = array('href' => 'zc_project.php?act=product_list&id='.intval($_GET['id']), 'text' => $_LANG['zc_goods_manage']);	
	$smarty->assign('action_link',$action_link);
	
	if($_GET['product_id']){
		$id = $_GET['id'];
		$product_id = $_GET['product_id'];
		$sql = " SELECT * FROM ".$ecs->table('zc_goods')." WHERE id = '$product_id' ";
		$row = $db->getRow($sql);
		$smarty->assign('id',$id);
		$smarty->assign('item_id',$product_id);
		$smarty->assign('product',$row);
		$smarty->assign('state','update_product');
		$smarty->display('zc_goods_info.dwt');		
	}else{
		$id = $_GET['id'];
		$smarty->assign('item_id',$id);
		$smarty->assign('state','insert_product');
		$smarty->display('zc_goods_info.dwt');
	}
}
/*------------------------------------------------------ */
//-- 添加项目方案处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'insert_product')
{
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$id = $_POST['item_id'];
	$limit       =   !empty($_POST['limit'])     	? trim(intval($_POST['limit']))      :  0;
	$price       =   !empty($_POST['price'])     	? trim($_POST['price'])              :  0;
	$carriage    =   !empty($_POST['yunfei'])     	? trim($_POST['yunfei'])             :  0;
	$return_time =   !empty($_POST['return_time'])  ? trim($_POST['return_time'])        :  '';
	$return_cont =   !empty($_POST['content'])      ? trim($_POST['content'])            :  '';
	
    /* 处理商品图片 */
    $product_img   = '';  // 初始化方案图片
	$dir_product = 'zc_product_images';
	$product_img = $image->upload_image($_FILES['product_img'],$dir_product);
	$sql = "INSERT INTO".$ecs->table('zc_goods')."(`id`,`pid`,`limit`,`price`,`shipping_fee`,`content`,`img`,`return_time`) VALUES ('','$id','$limit','$price','$carriage','$return_cont','$product_img','$return_time')";
    $insert = $db->query($sql);
	if($insert){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=product_list&id='.$id;
		sys_msg($_LANG['add_succeed'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['add_failure'],1,$links);
	}	
}
/*------------------------------------------------------ */
//-- 编辑项目方案处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'update_product')
{   
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$id			 =	 $_POST['id'];
	$product_id	 =   $_POST['item_id'];
	$limit       =   !empty($_POST['limit'])     		   ?trim($_POST['limit'])              :  0;
	$price       =   !empty($_POST['price'])     		   ?trim($_POST['price'])              :  0;
	$carriage    =   !empty($_POST['yunfei'])     		   ?trim($_POST['yunfei'])             :  0;
	$return_time =   !empty($_POST['return_time'])         ?trim($_POST['return_time'])        :  '';
	$return_cont =   !empty($_POST['content'])        	   ?trim($_POST['content'])            :  '';

	if (!empty($_POST['infinite']))
	{
		$limit = -1;
	}

    /* 处理商品图片 */
    $product_img   = '';  // 初始化基本图片
	$dir_product = 'zc_product_images';
	if(!empty($_FILES['product_img']['name'])){
		$product_img = $image->upload_image($_FILES['product_img'],$dir_product);
	}
	//有上传图片，删除原图
        $sql = "SELECT img FROM " . $ecs->table('zc_goods')." WHERE id = '$product_id'";
        $row = $db->getRow($sql);
        if ( $product_img != ''&& $row['img'])
        {
            @unlink(ROOT_PATH . $row['product_img']);
        }
	$sql = " UPDATE ".$ecs->table('zc_goods')." SET ".
		   " `limit`='$limit', ".
		   " `price`='$price', ".
		   " `shipping_fee`='$carriage', ".
		   " `return_time`='$return_time', ";
	if($product_img){
		$sql .= " `img`='$product_img', ";
	}
		   
		   $sql .= " `content`='$return_cont' WHERE id='$product_id' ";
    $update = $db->query($sql);
	if($update){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=product_list&id='.$id;
		sys_msg($_LANG['edit_success'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['edit_fail'],1,$links);
	}
}
/*------------------------------------------------------ */
//-- 删除项目方案
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del_product')
{	
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$id = $_GET['id'];
	$product_id = $_GET['product_id'];
	//检查是否有订单
	$sql = " SELECT count(*) FROM ".$ecs->table('order_info')." WHERE zc_goods_id = '$product_id' ";
	$res = $db->getOne($sql);
	if($res){
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['zc_goods_del_fail'],1,$links);
	}else{	
	$sql = "SELECT img FROM " . $ecs->table('zc_goods')." WHERE id = '$product_id'";
	$row = $db->getRow($sql);
	@unlink(ROOT_PATH . $row['img']);
	$sql = "DELETE FROM ".$ecs->table('zc_goods')." WHERE id = '$product_id' ";
	$db->query($sql);
	Header('Location:zc_project.php?act=product_list&id='.$id);
	}
}
/*------------------------------------------------------ */
//-- 项目进展列表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'progress')
{	
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$item_id = $_GET['id'];
    $smarty->assign('ur_here', $_LANG['zc_progress_manage']);
    $action_link = array('href' => 'zc_project.php?act=add_evolve&id='.$item_id, 'text' => $_LANG['add_zc_progress']);
	//取得当前项目下的方案列表
	$sql = " SELECT * FROM ".$ecs->table('zc_progress')." WHERE pid = '$item_id' ";
	$result = $db->getAll($sql);
	foreach($result as $k=>$v)
	{
		$result[$k]['add_time'] = date("Y-m-d",$v['add_time']);
		$result[$k]['img'] = unserialize($v['img']);
	}
	$smarty->assign('item_id',$item_id);
	$smarty->assign('evolve_list',$result);
	$smarty->assign('action_link',$action_link);
	$smarty->assign('full_page', 1);
    $smarty->display('zc_progress_list.dwt');	
}
/*------------------------------------------------------ */
//-- 添加/编辑项目进展
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'add_evolve'||$_REQUEST['act'] == 'edit_evolve')
{	
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	if($_REQUEST['act'] == 'add_evolve')
	{
		$smarty->assign('ur_here', $_LANG['add_zc_progress']);
	}
	if($_REQUEST['act'] == 'edit_evolve')
	{
		$smarty->assign('ur_here', $_LANG['edit_zc_progress']);
	}

    $action_link = array('href' => 'zc_project.php?act=progress&id='.intval($_GET['id']), 'text' => $_LANG['zc_progress_manage']);	
	$smarty->assign('action_link',$action_link);	
	
	if($_GET['evolve_id'])
	{
		$id = $_GET['id'];
		$evolve_id = $_GET['evolve_id'];
		$sql = " SELECT * FROM ".$ecs->table('zc_progress')." WHERE id = '$evolve_id' ";
		$row = $db->getRow($sql);
		$row['img'] = unserialize($row['img']);
		$smarty->assign('id',$evolve_id);
		$smarty->assign('item_id',$id);
		$smarty->assign('evolve',$row);
		$smarty->assign('state','update_evolve');
		$smarty->display('zc_progress_info.dwt');	
	}
	else
	{
		$id = $_GET['id'];
		$smarty->assign('item_id',$id);
		$smarty->assign('state','insert_evolve');
		$smarty->display('zc_progress_info.dwt');		
	}
}
/*------------------------------------------------------ */
//-- 添加项目进展处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'insert_evolve')
{
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$item_id     =  $_POST['item_id'];
	$progress =  !empty($_POST['progress']) ?trim($_POST['progress']) : '';
	$add_time    =  gmtime();
    /* 处理商品图片 */
    $evolve_img   = '';  // 初始化方案图片
	$dir_evolve = 'funding_evolve_images';
	
	if(have_file_upload())
	{	
		for($i=0;$i<count($_FILES);$i++){
		if($_FILES['img_'.$i]){
		$evolve_img[] = $image->upload_image($_FILES['img_'.$i],$dir_evolve);}
		}
	}
	$evolve_img = serialize($evolve_img);
	$sql = "INSERT INTO".$ecs->table('zc_progress')."(`id`,`pid`,`progress`,`add_time`,`img`) ".
	" VALUES ('','$item_id','$progress','$add_time','$evolve_img') ";
    $insert = $db->query($sql);
	if($insert){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=progress&id='.$item_id;
		sys_msg($_LANG['add_succeed'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['add_failure'],1,$links);
	}	
}
/*------------------------------------------------------ */
//-- 编辑项目进展处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'update_evolve')
{ 
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	//处理接收数据
	$id     	 =  $_POST['id'];
	$item_id	 =  $_POST['item_id'];
	$progress =  !empty($_POST['progress']) ?trim($_POST['progress']) : '';

    /* 处理商品图片 */
    $evolve_img   = '';  // 初始化方案图片
	$dir_evolve = 'funding_evolve_images';
	for($i=0;$i<count($_FILES);$i++){
		if(!empty($_FILES['img_'.$i]['name'])){
		$evolve_img[] = $image->upload_image($_FILES['img_'.$i],$dir_evolve);
		}
	} 
	
	if($evolve_img){
		$sql = "SELECT img FROM " . $ecs->table('zc_progress')." WHERE id = '$id'";
		$row = $db->getRow($sql);
		if($row['img'])
		{
			$row['img'] = unserialize($row['img']);		
			$evolve_img = array_merge($evolve_img,$row['img']);
		}		
		$evolve_img = serialize($evolve_img);
	}
	$sql = " UPDATE ".$ecs->table('zc_progress')." SET ".
		   " `progress`='$progress' ";
	if($evolve_img){
		$sql .= " , `img`='$evolve_img' ";
	}
		   
		   $sql .= " WHERE id='$id' ";
    $update = $db->query($sql);
	if($update){
        $links[0]['text'] = $_LANG['go_list'];
        $links[0]['href'] = 'zc_project.php?act=progress&id='.$item_id;
		sys_msg($_LANG['edit_success'],0,$links);
	}else{
		$links[0]['text'] = $GLOBALS['_LANG']['go_back'];
        $links[0]['href'] = 'javascript:history.go(-1)';
		sys_msg($_LANG['edit_fail'],1,$links);
	}
}
/*------------------------------------------------------ */
//-- 删除项目进展
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'del_evolve')
{	
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$id = $_GET['id'];
	$evolve_id = $_GET['evolve_id'];
	$sql = "SELECT img FROM " . $ecs->table('zc_progress')." WHERE id = '$evolve_id'";
	$row = $db->getRow($sql);
	$row['img'] = unserialize($row['img']);
		foreach ($row['img'] as $v){
			@unlink(ROOT_PATH . $v);
		}
	$sql = " DELETE FROM ".$ecs->table('zc_progress')." WHERE id = '$evolve_id' ";
	$db->query($sql);
	Header('Location:zc_project.php?act=progress&id='.$id);
}
/*------------------------------------------------------ */
//-- 删除图片 by wu
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'delete_image')
{	
    /* 权限检查 */
    admin_priv('zc_project_manage');
	
	$result = array('error'=>0, 'message'=>'', 'content'=>'');
	$type = empty($_REQUEST['type'])? '':trim($_REQUEST['type']);
	$id = empty($_REQUEST['id'])? 0:intval($_REQUEST['id']);
	$key = empty($_REQUEST['key'])? 0:intval($_REQUEST['key']);
	
	//处理众筹项目图片
	if($type == 'project')
	{
		$sql = " SELECT img FROM ".$GLOBALS['ecs']->table('zc_project')." WHERE id = '$id' ";
		$img = $GLOBALS['db']->getOne($sql);
		if($img)
		{
			//处理数据
			$img_arr = unserialize($img);
			@unlink(ROOT_PATH . $img_arr[$key]);
			unset($img_arr[$key]);
			//更新数据
			$img = serialize($img_arr);
			$sql = " UPDATE ".$GLOBALS['ecs']->table('zc_project')." SET img = '$img' WHERE id = '$id' ";
			$GLOBALS['db']->query($sql);
		}
	}	
	//处理众筹进展图片
	if($type == 'progress')
	{
		$sql = " SELECT img FROM ".$GLOBALS['ecs']->table('zc_progress')." WHERE id = '$id' ";
		$img = $GLOBALS['db']->getOne($sql);
		if($img)
		{
			//处理数据
			$img_arr = unserialize($img);
			@unlink(ROOT_PATH . $img_arr[$key]);
			unset($img_arr[$key]);
			//更新数据
			$img = serialize($img_arr);
			$sql = " UPDATE ".$GLOBALS['ecs']->table('zc_progress')." SET img = '$img' WHERE id = '$id' ";
			$GLOBALS['db']->query($sql);
		}
	}
	
	die(json_encode($result));
}
/**
 * 获取众筹项目列表
 * @access  public
 * @return void
 */
function get_item_list($cat_id = '')
{
    $result = get_filter();
    if ($result === false)
    {
        /* 查询条件 */
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('zc_project');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
		if($cat_id){
			$where = " WHERE cat_id = '$cat_id' ";
		}else{
			$where = " ";
		}
        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('zc_project'). $where ." ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[] = $row;
    }

    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 过滤和排序所有分类，返回一个带有缩进级别的数组
 *
 * @access  private
 * @param   int     $cat_id     上级分类ID
 * @param   array   $arr        含有所有分类的数组
 * @param   int     $level      级别
 * @return  void
 */
function zc_cat_options($spec_cat_id, $arr)
{
    static $cat_options = array();

    if (isset($cat_options[$spec_cat_id]))
    {
        return $cat_options[$spec_cat_id];
    }

    if (!isset($cat_options[0]))
    {
        $level = $last_cat_id = 0;
        $options = $cat_id_array = $level_array = array();
        $data = read_static_cache('zc_cat_option_static');
        if ($data === false)
        {
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $cat_id = $value['cat_id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }

                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);

                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_cat_id  = $cat_id;
                        $cat_id_array = array($cat_id);
                        $level_array[$last_cat_id] = ++$level;
                        continue;
                    }

                    if ($value['parent_id'] == $last_cat_id)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);

                        if ($value['has_children'] > 0)
                        {
                            if (end($cat_id_array) != $last_cat_id)
                            {
                                $cat_id_array[] = $last_cat_id;
                            }
                            $last_cat_id    = $cat_id;
                            $cat_id_array[] = $cat_id;
                            $level_array[$last_cat_id] = ++$level;
                        }
                    }
                    elseif ($value['parent_id'] > $last_cat_id)
                    {
                        break;
                    }
                }

                $count = count($cat_id_array);
                if ($count > 1)
                {
                    $last_cat_id = array_pop($cat_id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_cat_id != end($cat_id_array))
                    {
                        $last_cat_id = end($cat_id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_cat_id = 0;
                        $cat_id_array = array();
                        continue;
                    }
                }

                if ($last_cat_id && isset($level_array[$last_cat_id]))
                {
                    $level = $level_array[$last_cat_id];
                }
                else
                {
                    $level = 0;
                }
            }
            //如果数组过大，不采用静态缓存方式
            if (count($options) <= 2000)
            {
                write_static_cache('zc_cat_option_static', $options);
            }
        }
        else
        {
            $options = $data;
        }
        $cat_options[0] = $options;
    }
    else
    {
        $options = $cat_options[0];
    }

    if (!$spec_cat_id)
    {
        return $options;
    }
    else
    {
        if (empty($options[$spec_cat_id]))
        {
            return array();
        }

        $spec_cat_id_level = $options[$spec_cat_id]['level'];

        foreach ($options AS $key => $value)
        {
            if ($key != $spec_cat_id)
            {
                unset($options[$key]);
            }
            else
            {
                break;
            }
        }

        $spec_cat_id_array = array();
        foreach ($options AS $key => $value)
        {
            if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                ($spec_cat_id_level > $value['level']))
            {
                break;
            }
            else
            {
                $spec_cat_id_array[$key] = $value;
            }
        }
        $cat_options[$spec_cat_id] = $spec_cat_id_array;

        return $spec_cat_id_array;
    }
}
/**
 * 获得商品列表
 *
 * @access  public
 * @params  integer $isdelete
 * @params  integer $real_goods
 * @params  integer $conditions
 * @return  array
 */
function zc_project_list($conditions = '')
{

    $result = get_filter();
	
    if ($result === false)
    {

        $filter['cat_id']           = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['keyword']          = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		$where = " WHERE 1=1 ";
        $where .= $filter['cat_id'] > 0 ? " AND " . zc_get_children($filter['cat_id']) : '';
		
        /* 关键字 */
        if (!empty($filter['keyword']))
        {
            $where .= " AND title LIKE '%" . mysqli_like_quote($filter['keyword']) . "%' ";
        }

        $where .= $conditions;

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('zc_project'). " AS g  $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT `id`, `cat_id`, `title`, `start_time`, `end_time`, `amount`, `is_best`, `join_money`, `join_num`, `title_img`, `describe` " .
                    " FROM " . $GLOBALS['ecs']->table('zc_project') . " AS g  $where" .
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
    
    foreach($row as $k=>$val){
        $row[$k]['start_time'] = date('Y-m-d',$val['start_time']);
        $row[$k]['end_time'] = date('Y-m-d',$val['end_time']);
    }

    return array('zc_projects' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
/**
 * 获得指定分类下所有底层分类的ID
 *
 * @access  public
 * @param   integer     $cat        指定的分类ID
 * @return  string
 */
function zc_get_children($cat = 0)
{
    return 'g.cat_id ' . db_create_in(array_unique(array_merge(array($cat), array_keys(zc_cat_list($cat, 0, false)))));
}

?>