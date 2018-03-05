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
include_once('includes/cls_json.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

define('IN_ECS', true);

/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

$step = isset($_REQUEST['step'])  ? htmlspecialchars(trim($_REQUEST['step'])) : ''; //流程步骤
$sid = isset($_REQUEST['sid'])  ? intval($_REQUEST['sid']) : 1; //流程步骤ID
$pid_key = isset($_REQUEST['pid_key'])  ? intval($_REQUEST['pid_key']) : 0; //当前步骤数组key
$ec_shop_bid = isset($_REQUEST['ec_shop_bid']) ? intval($_REQUEST['ec_shop_bid']) : 0; //品牌ID
$brandView = isset($_REQUEST['brandView']) ? htmlspecialchars(trim($_REQUEST['brandView'])) : ''; //为空则显示品牌列表，否则添加或编辑品牌信息
$user_id = $_SESSION['user_id'];
$smarty->assign('helps',           get_shop_help());       // 网店帮助

if(empty($sid)){
    $sid = 1;
}
//ajax数据返回 start
if($step == 'addChildCate'){ //查找二级类目

	$cat_id = isset($_REQUEST['cat_id'])  ? trim($_REQUEST['cat_id']) : 0;
	$type = isset($_REQUEST['type'])  ? intval($_REQUEST['type']) : 0;

	$json  = new JSON;
        $result = array('error' => 0, 'message' => '', 'content' => '', 'cat_id' => '');
	
	if($user_id > 0){
		
		if($type == 1){ //取消二级类目
			$_POST['cateArr']=strip_tags(urldecode($_POST['cateArr']));
			$_POST['cateArr'] = json_str_iconv($_POST['cateArr']);
			$cat = $json->decode($_POST['cateArr']);
			$catarr = $cat->cat_id;
		}
		
		$cate_list = get_first_cate_list($cat_id, $type, $catarr,$_SESSION['user_id']);

		if (!$cat_id)
		{
			$cate_list = array();
		}

		$smarty->assign('cate_list',        $cate_list);	
		$smarty->assign('cat_id',        $cat_id);	
		$result['content'] = $smarty->fetch("library/merchants_cate_list.lbi");
		
		if($type == 1){ //取消二级类目
			$result['type'] = $type;
			$category_info = get_fine_category_info(0, $user_id);
			$smarty->assign('category_info',        $category_info);	
			$result['cate_checked'] = $smarty->fetch("library/merchants_cate_checked_list.lbi");
			
			$permanent_list = get_category_permanent_list($_SESSION['user_id']);
			$smarty->assign('permanent_list',        $permanent_list);	
			$result['catePermanent'] = $smarty->fetch("library/merchants_steps_catePermanent.lbi");
		}
		
	}else{
		$result['error'] = 1;
		$result['message'] = '您需要重新登录！';
	}
	
    die($json->encode($result));
}elseif($step == 'addChildCate_checked'){ //添加二级类目

	$json  = new JSON;
        $result = array('error' => 0, 'message' => '', 'content' => '', 'cat_id' => '');
	
	if($user_id > 0){
		$_POST['cat_id']=strip_tags(urldecode($_POST['cat_id']));
    	$_POST['cat_id'] = json_str_iconv($_POST['cat_id']);
		$cat = $json->decode($_POST['cat_id']);
	
		$child_category = get_child_category($cat->cat_id);
		$category_info = get_fine_category_info($child_category['cat_id'], $user_id);
		$smarty->assign('category_info',        $category_info);
		$result['content'] = $smarty->fetch("library/merchants_cate_checked_list.lbi");	

		$permanent_list = get_category_permanent_list($_SESSION['user_id']);
		$smarty->assign('permanent_list',        $permanent_list);	
		$result['catePermanent'] = $smarty->fetch("library/merchants_steps_catePermanent.lbi");
	}else{
		$result['error'] = 1;
		$result['message'] = '您需要重新登录！';
	}
	
    die($json->encode($result));
}

elseif($step == 'deleteChildCate_checked'){ //删除二级类目
	
	$ct_id = isset($_REQUEST['ct_id'])  ? trim($_REQUEST['ct_id']) : '';

	$json  = new JSON;
	$result = array('error' => 0, 'message' => '', 'content' => '', 'cat_id' => '');
        
	if($user_id > 0){
		
		$catParent = get_temporarydate_ctId_catParent($ct_id);
		if($catParent['num'] == 1){
			$sql = "delete from " .$ecs->table('merchants_dt_file'). " where cat_id = '" .$catParent['parent_id']. "'";
			$db->query($sql);
		}
		
		$sql = "delete from " .$ecs->table('merchants_category_temporarydate'). " where ct_id = '$ct_id'";
		$db->query($sql);
		
		$category_info = get_fine_category_info(0, $user_id);
		$smarty->assign('category_info',        $category_info);	
		$result['content'] = $smarty->fetch("library/merchants_cate_checked_list.lbi");
		
		$permanent_list = get_category_permanent_list($_SESSION['user_id']);
		$smarty->assign('permanent_list',        $permanent_list);	
		$result['catePermanent'] = $smarty->fetch("library/merchants_steps_catePermanent.lbi");
	}else{
		$result['error'] = 1;
		$result['message'] = '您需要重新登录！';
	}
	
	die($json->encode($result));
}elseif($step == 'brandSearch_cn_en'){ //搜索中文品牌名称
        
    $json   = new JSON;
    $result    = array('err_msg' => '', 'err_no' => 0, 'content' => '');
    
    $type = empty($_REQUEST['type']) ? 0 : intval($_REQUEST['type']);
    $value = empty($_REQUEST['value']) ? '' : htmlspecialchars(trim($_REQUEST['value']));
    $brand_list = get_merchants_search_brand($value, $type);
   
    $smarty->assign('type',  $type); 
    $smarty->assign('brand_list',  $brand_list); 
    
    if($brand_list){
        $result['err_no'] = 1;
    }
    $result['type'] = $type;
    $result['content'] = $smarty->fetch("library/brank_type_search.lbi");

    die($json->encode($result));
}elseif($step == 'brandSearch_info'){ //搜索中文品牌名称
        
    $json   = new JSON;
    $result    = array('err_msg' => '', 'err_no' => 0, 'content' => '');
    $brand_id = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
    $brand_type = empty($_REQUEST['brand_type']) ? '' : htmlspecialchars($_REQUEST['brand_type']);
    $submit = !isset($_REQUEST['submit']) ? '' : htmlspecialchars($_REQUEST['submit']);
    
    $brand_name = !isset($_REQUEST['searchBrandZhInput']) ? '' : htmlspecialchars(trim($_REQUEST['searchBrandZhInput']));
    $brand_letter = !isset($_REQUEST['searchBrandEnInput']) ? '' : htmlspecialchars(trim($_REQUEST['searchBrandEnInput']));
   
    $result = get_merchants_search_brand($brand_id, 2, $brand_type, $brand_name, $brand_letter);

    if(!empty($submit)){
        if($result){
            $result['brand_not'] = '已存在';
            $result['err_no'] = 1;
        }else{
            $result['brand_not'] = '不存在';
            $result['err_no'] = 0;
        }
    }        
            
    $result['brand_type'] = $brand_type;
    
    die($json->encode($result));
}    

//ajax数据返回 end

if($user_id <= 0){
	 show_message($_LANG['steps_UserLogin'], $_LANG['UserLogin'], 'user.php');
	 exit;
}

$sql = "select steps_audit from " .$ecs->table('merchants_shop_information'). " where user_id = '" .$_SESSION['user_id']. "'";
$steps_audit = $db->getOne($sql);

if($steps_audit == 1){ //会员已提交申请
	assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
	
	$step = 'stepSubmit'; 
	$smarty->assign('pid_key',         0);  // key值
	$smarty->assign('step',         $step);  // 协议信息
	
	$sql = "select shoprz_brandName, shopNameSuffix, shop_class_keyWords, hopeLoginName, merchants_audit, merchants_message from " .$ecs->table('merchants_shop_information'). " where user_id = '" .$_SESSION['user_id']. "'";
	$shop_info = $db->getRow($sql);
	
	$shop_info['rz_shopName'] = str_replace('|','',$shop_info['rz_shopName']);
	$shop_info['shop_name'] = get_shop_name($_SESSION['user_id'], 1); //店铺名称
        
	$smarty->assign('shop_info',    $shop_info);
	
	$smarty->display('merchants_steps.dwt');	
	exit;
}

//删除品牌 start
if($_REQUEST['del'] == 'deleteBrand'){
	$sql = "delete from " .$ecs->table('merchants_shop_brand'). " where bid = '$ec_shop_bid'";
	$db->query($sql);
}
//删除品牌 end

//删除品牌资质证件信息 start
$b_fid = isset($_REQUEST['del_bFid']) ? intval($_REQUEST['del_bFid']) : 0; 
if($b_fid > 0){
	$sql = "delete from " .$ecs->table('merchants_shop_brandfile'). " where b_fid = '$b_fid'";
	$db->query($sql);
}
//删除品牌资质证件信息 end

$sql = "select fid from " .$ecs->table('merchants_steps_fields'). " where user_id = '$user_id'";
$fid = $db->getOne($sql);

if($fid <= 0 && ($_REQUEST['step'] == 'stepTwo' || $_REQUEST['step'] == 'stepThree' || $_REQUEST['step'] == 'stepSubmit')){
	ecs_header("Location: merchants.php\n");
	exit;
}else{
	if($fid > 0){
		if($step != 'stepThree' && $step != 'stepSubmit'){
			$step = 'stepTwo'; //跳过协议
		}
	}
}

if(!empty($step) && $step == 'stepTwo'){
	$sid = 2;
}elseif(!empty($step) && $step == 'stepThree'){
	$sid = 3;
}elseif(!empty($step) && $step == 'stepSubmit'){
	$sid = 4;
	
	$sql = "select shoprz_brandName, shopNameSuffix, shop_class_keyWords, hopeLoginName, merchants_audit from " .$ecs->table('merchants_shop_information'). " where user_id = '" .$_SESSION['user_id']. "'";
	$shop_info = $db->getRow($sql);
	
	$shop_info['rz_shopName'] = str_replace('|','',$shop_info['rz_shopName']);
	
	$smarty->assign('shop_info',    $shop_info);
}

if (!$smarty->is_cached('merchants_steps.dwt'))
{
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
    
    $smarty->assign('step',         $step);  // 记录流程
    $smarty->assign('sid',         $sid);  // 记录流程ID
	
    if ($sid > 1 && $sid < 4){
		
        //删除临时表数据
        $sql = "delete from " .$ecs->table('merchants_category_temporarydate'). " where user_id = '$user_id' and is_add = 0";
        $db->query($sql);

        /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
        $consignee['country'] = 1;
        $consignee['province'] = 0;
        $consignee['city'] = 0;

        $country_list 	= get_regions_steps();
        $province_list 	= get_regions_steps(1,$consignee['country']);
        $city_list     	= get_regions_steps(2,$consignee['province']);
        $district_list 	= get_regions_steps(3,$consignee['city']);

        $sn = 0;
        $smarty->assign('country_list',    $country_list);
        $smarty->assign('province_list',    $province_list);
    	$smarty->assign('city_list',        $city_list);
    	$smarty->assign('district_list',    $district_list);
        $smarty->assign('consignee',    $consignee);
        $smarty->assign('sn',    $sn);
        
        $process_list = get_root_steps_process_list($sid);
        $process = $process_list[$pid_key];
        
        if(!$process_list){
            $Location = "merchants_steps.php?step=stepThree&pid_key=" . $pid_key;
            ecs_header("Location: " .$Location. "\n");
            exit;
        }
        
        //操作品牌流程 start
        if($process['process_title'] == '添加品牌'){
            
            //品牌操作 start 
            $smarty->assign('b_pidKey',         $pid_key);  // 品牌操作
            $smarty->assign('ec_shop_bid',      $ec_shop_bid);  // 品牌操作类型 大于0则更新，否则为添加
            //品牌操作 end
            
            if($brandView == 'brandView'){
                $smarty->assign('pid_key',         $pid_key + 1);  // key值
            }else{
                $smarty->assign('pid_key',         $pid_key + 2);  // key值
            }
            
            if($step == 'stepThree' && $pid_key == 2){
                $smarty->assign('brandKey',         $pid_key + 1);  // key值 添加新品牌
            }
            
        }elseif($process['process_title'] == '新增品牌'){
            $smarty->assign('pid_key',         $pid_key - 1);  // key值
        }else{
            $smarty->assign('pid_key',         $pid_key + 1);  // key值
        }
        //操作品牌流程 end

        $smarty->assign('process',         $process);  // 步骤信息
        $smarty->assign('brandView',        $brandView);

        $smarty->assign('choose_process',         $GLOBALS['_CFG']['choose_process']);
        if($process['id'] > 0){
            $category_info = get_fine_category_info(0, $user_id); // 详细类目
            $smarty->assign('category_info',        $category_info);

            $permanent_list = get_category_permanent_list($_SESSION['user_id']);// 一级类目证件
            $smarty->assign('permanent_list',        $permanent_list);		

            $steps_title = get_root_merchants_steps_title($process['id'], $user_id);

            $smarty->assign('steps_title',         $steps_title);  // 流程表单信息
        }

    }elseif($sid == 1){
        $merchants_steps = get_root_directory_steps($sid);  //申请流程信息
        $smarty->assign('steps',         $merchants_steps);  // 协议信息	
    }
		
    /* 页面中的动态内容 */
    assign_dynamic('merchants_steps');
}

$smarty->display('merchants_steps.dwt');
?>