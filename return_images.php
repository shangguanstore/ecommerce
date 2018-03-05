<?php

/**
 * ECSHOP 浏览列表插件
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

include('includes/cls_json.php');
$json   = new JSON;

$result    = array('error' => 0, 'content' => '');
$rec_id = isset($_REQUEST['rec_id']) ? intval($_REQUEST['rec_id']) : 0;

if($_REQUEST['act']=='ajax_return_images'){

    $img_file = isset($_FILES['filename']) ? $_FILES['filename'] : array();
    $user_id = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
    $sessid = isset($_GET['sessid']) ? trim($_GET['sessid']) : '';
    
    $sql = "SELECT count(*) FROM ". $ecs->table('sessions') ." WHERE userid = '$user_id' AND sesskey='$sessid'";
    if(!empty($user_id) && $db->getOne($sql) > 0){
        include_once(ROOT_PATH . '/includes/cls_image.php'); 
        $image = new cls_image($_CFG['bgcolor']);
        $img_file = $image->upload_image($img_file, 'return_images');
        
        get_oss_add_file(array($img_file));
        
        $return = array(
            'rec_id' => $rec_id,
            'user_id' => $user_id,
            'img_file' => $img_file,
            'add_time' => gmtime()
        );
		
	$sql = "select count(*) from " .$ecs->table('return_images'). " where user_id = '$user_id' and rec_id = '$rec_id'";
        $img_count = $db->getOne($sql);
        
        if($img_count < $GLOBALS['_CFG']['return_pictures']){
            $db->autoExecute($ecs->table('return_images'), $return, 'INSERT');
        }else{
            $result['error'] = 1;
        }
        
    }else{
        $result['error'] = 2;
    }
	
	$sql = "select img_file from " .$ecs->table('return_images'). " where user_id = '$user_id' and rec_id = '$rec_id' order by id desc";
	$img_list = $db->getAll($sql);

	$smarty->assign('img_list',        $img_list);
	$result['content'] = $smarty->fetch("library/return_goods_img.lbi");

    die($json->encode($result));
}elseif($_REQUEST['act']=='ajax_return_images_list'){
	
	$sql = "select img_file from " .$ecs->table('return_images'). " where user_id = '$user_id' and rec_id = '$rec_id' order by id desc";
	$img_list = $db->getAll($sql);
	
	if($img_list){
		$smarty->assign('img_list',        $img_list);
		$result['content'] = $smarty->fetch("library/return_goods_img.lbi");
	}else{
		$result['error'] = 1;
	}
	
	die($json->encode($result));
}elseif($_REQUEST['act']=='clear_pictures'){
	
	$sql = "select img_file from " .$ecs->table('return_images'). " where user_id = '" .$_SESSION['user_id']. "' and rec_id = '$rec_id'";
	$img_list = $db->getAll($sql);
	
	foreach($img_list as $key=>$row){
            get_oss_del_file(array($row['img_file']));
            @unlink(ROOT_PATH . $row['img_file']);
	}
	
	$sql = "delete from " .$ecs->table('return_images'). " where user_id = '" .$_SESSION['user_id']. "' and rec_id = '$rec_id'";
	$db->query($sql);
	
	die($json->encode($result));
}

?>