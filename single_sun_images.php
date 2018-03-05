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

$result    = array('error' => 0, 'content' => '', 'msg' => '');

$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

if($_REQUEST['act']=='ajax_return_images'){
	
    $img_file = isset($_FILES['SWFUpload']) ? $_FILES['SWFUpload'] : array();

    if (!empty($_SESSION['user_id'])) {
        include_once(ROOT_PATH . '/includes/cls_image.php');
        $image = new cls_image($_CFG['bgcolor']);

        $img_file = $image->upload_image($img_file, 'single_img_temp'); //原图
        $img_thumb = $image->make_thumb($img_file, $GLOBALS['_CFG']['single_thumb_width'], $GLOBALS['_CFG']['single_thumb_height'], DATA_DIR . '/single_img_temp/thumb/'); //缩略图

        $return = array(
            'order_id' => $order_id,
            'goods_id' => $goods_id,
            'user_id' => $_SESSION['user_id'],
            'img_file' => $img_file,
            'img_thumb' => $img_thumb
        );

        $sql = "select count(*) from " . $ecs->table('single_sun_images') . " where user_id = '" . $_SESSION['user_id'] . "' and order_id = '$order_id' and goods_id = '$goods_id'";
        $img_count = $db->getOne($sql);

        if ($img_count < 10 && $img_file) {
            $db->autoExecute($ecs->table('single_sun_images'), $return, 'INSERT');
        } else {
            $result['error'] = 1;
        }
    } else {
        $result['error'] = 2;
    }

    $sql = "select id, img_file, img_thumb from " . $ecs->table('single_sun_images') . " where user_id = '" . $_SESSION['user_id'] . "' and order_id = '$order_id' and goods_id = '$goods_id' order by id desc";
    $img_list = $db->getAll($sql);

    $result['currentImg_path'] = $img_list[0]['img_thumb'];
    $smarty->assign('img_list', $img_list);
    $result['content'] = $smarty->fetch("library/single_sun_img.lbi");

    die($json->encode($result));
}elseif($_REQUEST['act']=='ajax_return_images_list'){
	
	$sql = "select id, img_file, img_thumb from " .$ecs->table('single_sun_images'). " where user_id = '" .$_SESSION['user_id']. "' and order_id = '$order_id' and goods_id = '$goods_id' order by id desc";
	$img_list = $db->getAll($sql);
	
	if($img_list){
		$smarty->assign('img_list',        $img_list);
		$result['content'] = $smarty->fetch("library/single_sun_img.lbi");
	}else{
		$result['error'] = 1;
	}
	
	die($json->encode($result));
}elseif($_REQUEST['act']=='del_pictures'){
    if(empty($_SESSION['user_id'])){
        $result['error'] = 1;
    }
    
    $sql = "select id, img_file, img_thumb from " .$ecs->table('single_sun_images'). " where user_id = '" .$_SESSION['user_id']. "' order by id desc";
    $img_list = $db->getAll($sql);

    foreach($img_list as $key=>$val){
        @unlink(ROOT_PATH . $val['img_file']);
        @unlink(ROOT_PATH . $val['img_thumb']);

        if($id == $val['id']){
            $sql = "delete from " .$ecs->table('single_sun_images'). " where id = '$id'";
            $db->query($sql);
        }else{
            $sql = "delete from " .$ecs->table('single_sun_images'). " where user_id='$_SESSION[user_id]'";
            $db->query($sql);
        }
    }

    $smarty->assign('img_list',        $img_list);
    $result['content'] = $smarty->fetch("library/single_sun_img.lbi");

    die($json->encode($result));
}

?>