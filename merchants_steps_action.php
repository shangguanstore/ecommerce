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

/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

$user_id = $_SESSION['user_id'];
$step = isset($_REQUEST['step'])  ? htmlspecialchars(trim($_REQUEST['step'])) : '';
$sid = isset($_REQUEST['sid'])  ? intval($_REQUEST['sid']) : 1;
$agreement = isset($_REQUEST['agreement'])  ? intval($_REQUEST['agreement']) : 0; //协议
$pid_key = isset($_REQUEST['pid_key']) ? intval($_REQUEST['pid_key']) : 0; //KEY传值
$brandView = isset($_REQUEST['brandView']) ? htmlspecialchars(trim($_REQUEST['brandView'])) : ''; //为空则显示品牌列表，否则添加或编辑品牌信息

$brandId = isset($_REQUEST['brandId']) ? intval($_REQUEST['brandId']) : 0;
$search_brandType = isset($_REQUEST['search_brandType']) ? htmlspecialchars($_REQUEST['search_brandType']) : '';
$searchBrandZhInput = isset($_REQUEST['searchBrandZhInput']) ? htmlspecialchars(trim($_REQUEST['searchBrandZhInput'])) : '';
$searchBrandZhInput = !empty($searchBrandZhInput) ? addslashes($searchBrandZhInput) : '';
$searchBrandEnInput = isset($_REQUEST['searchBrandEnInput']) ? htmlspecialchars(trim($_REQUEST['searchBrandEnInput'])) : '';
$searchBrandEnInput = !empty($searchBrandEnInput) ? addslashes($searchBrandEnInput) : '';

if($user_id <= 0){
	 show_message($_LANG['steps_UserLogin'], $_LANG['UserLogin'], 'user.php');
	 exit;
}

$sql = "select agreement from " .$ecs->table('merchants_steps_fields'). " where user_id = '$user_id'";
$sf_agreement = $db->getOne($sql);

if($sf_agreement != 1){
	if($agreement == 1){
		$parent = array(
					'user_id' => $user_id,
					'agreement' => $agreement
				);
				
		$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_steps_fields'), $parent, 'INSERT');		
	}
}else{
	$process_list = get_root_steps_process_list($sid);
	$process = $process_list[$pid_key];
	
	$noWkey = $pid_key - 1;
	$noWprocess = $process_list[$noWkey];
	$form = get_steps_title_insert_form($noWprocess['id']);
	
	$parent = get_setps_form_insert_date($form['formName']);
	$parent['site_process'] = !empty($parent['site_process']) ? addslashes($parent['site_process']) : $parent['site_process'];
	$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_steps_fields'), $parent, 'UPDATE', "user_id = '$user_id'");	
	
	if($step == 'stepTwo'){	
		
		if(!is_array($process)){
			$step = 'stepThree';
			$pid_key = 0;
			$sid = $sid + 1;
		}else{
			$step = 'stepTwo';
			$pid_key = $pid_key;
		}
		
	}elseif($step == 'stepThree'){
		
		if(!is_array($process)){
		
                        $ec_rz_shopName = isset($_REQUEST['ec_rz_shopName']) ? trim($_REQUEST['ec_rz_shopName']) : '';
			$ec_hopeLoginName = isset($_REQUEST['ec_hopeLoginName']) ? trim($_REQUEST['ec_hopeLoginName']) : '';
                        
                        $sql = "select user_id from " .$ecs->table('merchants_shop_information'). " where rz_shopName = '$ec_rz_shopName' AND user_id <> '" .$_SESSION['user_id']. "'";
                        if($db->getOne($sql)){
				show_message('期望店铺名称已存在，请您重新填写', '返回上一步', "merchants_steps.php?step=" .$step. "&pid_key=" .$noWkey);
				exit;
			}else{
				$sql = "update " .$ecs->table('merchants_shop_information'). " set steps_audit = 1" . " where user_id = '" .$_SESSION['user_id']. "'";
				$db->query($sql);
			
				$step = 'stepSubmit';
				$pid_key = 0;
			}
                        
			$sql = "select user_id from " .$ecs->table('admin_user'). " where user_name = '$ec_hopeLoginName' AND ru_id <> '" .$_SESSION['user_id']. "'";
			if($db->getOne($sql)){
				show_message('店铺登陆用户名已存在，请您重新填写', '返回上一步', "merchants_steps.php?step=" .$step. "&pid_key=" .$noWkey);
				exit;
			}else{
				$sql = "update " .$ecs->table('merchants_shop_information'). " set steps_audit = 1" . " where user_id = '" .$_SESSION['user_id']. "'";
				$db->query($sql);
			
				$step = 'stepSubmit';
				$pid_key = 0;
			}
		}
	}
}

if(empty($step)){
	$step = 'stepOne';
}

//操作品牌 start
$act = '';
if($brandView == "brandView"){
    $pid_key -= 1;
}elseif($brandView == "add_brand"){ //添加新品牌
    
    if($brandId > 0){
       $act .= "&brandId=" . $brandId . '&search_brandType=' . $search_brandType;  
    }
    
    if($searchBrandZhInput != ''){
        $act .= "&searchBrandZhInput=" . $searchBrandZhInput;
    }
    
    if($searchBrandEnInput != ''){
        $act .= "&searchBrandEnInput=" . $searchBrandEnInput;
    }
    
    
    $act .= "&brandView=brandView";
}
//操作品牌 end
    
$steps_site = "merchants_steps.php?step=" .$step. "&pid_key=" .$pid_key . $act;

$sql = " select site_process from " .$ecs->table('merchants_steps_fields'). " where user_id = '$user_id'";
$site_process = $db->getOne($sql);

$strpos = strpos($site_process, $steps_site);
if($strpos === false){ //不存在
    if(!empty($site_process)){
            $site_process .= ','  . $steps_site;
    }else{
            $site_process = $steps_site;
    }

    $sql = "update " .$ecs->table('merchants_steps_fields'). " set steps_site = '$steps_site', site_process = '$site_process' where user_id = '$user_id'";
    $db->query($sql);
}

ecs_header("Location: " .$steps_site. "\n");
exit;
?>