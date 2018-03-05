<?php

/**
 * ECSHOP 购物流程
 * ============================================================================
 * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: flow.php 17218 2011-01-24 04:10:41Z douqinghua $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$user_id = $_SESSION['user_id'];

if($user_id <= 0){
	 show_message($_LANG['steps_UserLogin'], $_LANG['UserLogin'], 'user.php');
	 exit;
}

$sql = "select steps_site from " .$ecs->table('merchants_steps_fields'). " where user_id = '$user_id'";
$steps_site = $db->getOne($sql);

if(empty($steps_site)){
	$steps_site = 'merchants_steps.php';
}

ecs_header("Location: " .$steps_site. "\n");
exit;
?>