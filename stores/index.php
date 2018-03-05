<?php

/**
 * ECSHOP 控制台首页
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if($_REQUEST['act'] == 'merchants_first' || $_REQUEST['act'] == 'shop_top'|| $_REQUEST['act'] == 'merchants_second'){
    $smarty->assign('action_type',"index");
}else{
    $smarty->assign('action_type',"");
}

$store_id = $_SESSION['stores_id'];
$ru_id = $GLOBALS['db']->getOne(" SELECT ru_id FROM ".$GLOBALS['ecs']->table('offline_store')." WHERE id = '$store_id' ");
/* ------------------------------------------------------ */
//-- 框架
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == '') {
    header("location:goods.php?act=list");
    $smarty->display('index.dwt');
}

//上传门店头像
elseif($_REQUEST['act'] == 'upload_store_img')
{
	$result = array("error"=>0, "message"=>"", "content"=>"");
	include_once(ROOT_PATH . '/includes/cls_image.php');
	$image = new cls_image($_CFG['bgcolor']);
	if($_FILES['img']['name'])
	{
		//$name_arr = explode(".", $_FILES['img']['name']);
		//$file_type = end($name_arr);
		//$img_name = $store_id . "." . $file_type;
		$dir         = 'store_user';
		$img_name = $image->upload_image($_FILES['img'],$dir);
		if($img_name)
		{
			$result['error'] = 1;
			$result['content'] = "../" . $img_name;			
			//删除原图片
			$store_user_img = $GLOBALS['db']->getOne(" SELECT store_user_img FROM ".$GLOBALS['ecs']->table('store_user')." WHERE id = '$store_user_id' ");
			@unlink("../" . $store_user_img);
			//插入新图片
			$sql = " UPDATE ".$GLOBALS['ecs']->table('store_user')." SET store_user_img = '$img_name' WHERE id = '$store_user_id' ";
			$GLOBALS['db']->query($sql);
		}
	}
	die(json_encode($result));
}

//上传门店图片
elseif($_REQUEST['act'] == 'upload_stores_img')
{
	$result = array("error"=>0, "message"=>"", "content"=>"");
	include_once(ROOT_PATH . '/includes/cls_image.php');
	$image = new cls_image($_CFG['bgcolor']);
	if($_FILES['stores_img']['name'])
	{
		//$name_arr = explode(".", $_FILES['img']['name']);
		//$file_type = end($name_arr);
		//$img_name = $store_id . "." . $file_type;
		$dir         = 'offline_store';
		$img_name = $image->upload_image($_FILES['stores_img'],$dir);
		if($img_name)
		{
			$result['error'] = 1;
			$result['content'] = "../" . $img_name;			
			//删除原图片
			$stores_img = $GLOBALS['db']->getOne(" SELECT stores_img FROM ".$GLOBALS['ecs']->table('offline_store')." WHERE id = '$store_id' ");
			@unlink("../" . $stores_img);
			//插入新图片
			$sql = " UPDATE ".$GLOBALS['ecs']->table('offline_store')." SET stores_img = '$img_name' WHERE id = '$store_id' ";
			$GLOBALS['db']->query($sql);
		}
	}
	die(json_encode($result));
}

elseif($_REQUEST['act'] == 'clear_cache'){
    if(file_exists(ROOT_PATH . 'mobile/api/script/clear_cache.php')){
        require_once(ROOT_PATH . 'mobile/api/script/clear_cache.php');
    }
    
    clear_all_files('', STORES_PATH);
    sys_msg($_LANG['caches_cleared']);
}

?>