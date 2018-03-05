<?php
#需要查询的IP start
if(!isset($_COOKIE['province'])){
	$area_array = get_ip_area_name();
	 
	if($area_array['county_level'] == 2){
		$date = array('region_id', 'parent_id', 'region_name');
		$where = "region_name = '" .$area_array['area_name']. "' AND region_type = 2";
		$city_info = get_table_date('region', $where, $date, 1);
		
		$date = array('region_id', 'region_name');
		$where = "region_id = '" .$city_info[0]['parent_id']. "'";
		$province_info = get_table_date('region', $where, $date);
		
		$where = "parent_id = '" .$city_info[0]['region_id']. "' order by region_id asc limit 0, 1";
		$district_info = get_table_date('region', $where, $date, 1);
		
	}elseif($area_array['county_level'] == 1){
		$area_name = $area_array['area_name'];
		
		$date = array('region_id', 'region_name');
		$where = "region_name = '$area_name'";
		$province_info = get_table_date('region', $where, $date);
		
		$where = "parent_id = '" .$province_info['region_id']. "' order by region_id asc limit 0, 1";
		$city_info = get_table_date('region', $where, $date, 1);
		
		$where = "parent_id = '" .$city_info[0]['region_id']. "' order by region_id asc limit 0, 1";
		$district_info = get_table_date('region', $where, $date, 1);
	}
}
#需要查询的IP end

if($_REQUEST['act'] == 'select_regionChild'){
	
	$result = array('error' => 0, 'message' => '', 'content' => '', 'ra_id' => '', 'region_id' => '');
	$json  = new classes\JSON;
	
	$_POST['region']=strip_tags(urldecode($_POST['region']));
	$_POST['region'] = json_str_iconv($_POST['region']);
	$region = $json->decode($_POST['region']);
	
	$where = "parent_id = '" .$region->region_id. "'";
	$date = array('region_id', 'region_name');
	$city_list = get_table_date('region', $where, $date, 1);
	
	$result['city_list'] = 0;
	if($region->type == 0){
		if(empty($city_list)){
			setcookie('province', $region->region_id, gmtime() + 3600 * 24 * 30);	
		}
	}else if($region->type == 1){
		setcookie('type_province', $region->region_id, gmtime() + 3600 * 24 * 30);	
	}else if($region->type == 2){
		setcookie('type_city', $region->region_id, gmtime() + 3600 * 24 * 30);	
	}
	
	if(empty($city_list)){
		$result['city_list'] = 1;
		
		setcookie('province', $_COOKIE['type_province'], gmtime() + 3600 * 24 * 30);	
		setcookie('city', $_COOKIE['type_city'], gmtime() + 3600 * 24 * 30);	
	}

	setcookie('ra_id', $region->ra_id, gmtime() + 3600 * 24 * 30);
	$GLOBALS['smarty']->assign('city_list',           $city_list);
	$GLOBALS['smarty']->assign('type',           $region->type);
	
	$GLOBALS['smarty']->assign('city_top',           $_COOKIE['city']);
	$GLOBALS['smarty']->assign('district_top',       $_COOKIE['district']);
	
	$result['ra_id'] = $region->ra_id;
	$result['type'] = $region->type;
	$result['region_id'] = $region->region_id;
	//$result['content'] = $GLOBALS['smarty']->fetch("library/merchants_city_list.lbi");
        
        clear_all_files();

    die($json->encode($result));
	
}
elseif($_REQUEST['act'] == 'select_district_list'){
	
	$result = array('error' => 0, 'message' => '', 'content' => '', 'ra_id' => '', 'region_id' => '');
	$json  = new \classes\JSON;
	
	$_POST['region']=strip_tags(urldecode($_POST['region']));
	$_POST['region'] = json_str_iconv($_POST['region']);
	$region = $json->decode($_POST['region']);
	
	$where = "region_id = '" .$region->region_id. "'";
	$date = array('parent_id');
	$province = get_table_date('region', $where, $date, 2);
	
	$where = "parent_id = '" .$region->region_id. "' order by region_id asc limit 0, 1";
	$date = array('region_id', 'region_name');
	$district_list = get_table_date('region', $where, $date, 1);
	
	if($region->type == 0){
		setcookie('province', $province, gmtime() + 3600 * 24 * 30);
		setcookie('city', $region->region_id, gmtime() + 3600 * 24 * 30);
		
		if(count($district_list) > 0){
			setcookie('district', $district_list[0]['region_id'], gmtime() + 3600 * 24 * 30);
		}else{
			setcookie('district', 0, gmtime() + 3600 * 24 * 30);
		}
		
		//清空
		setcookie('type_province', 0, gmtime() + 3600 * 24 * 30);	
		setcookie('type_city', 0, gmtime() + 3600 * 24 * 30);	
		setcookie('type_district', 0, gmtime() + 3600 * 24 * 30);	
	}else{
		setcookie('type_district', $region->region_id, gmtime() + 3600 * 24 * 30);
	}
        
        clear_all_files();
	
    die($json->encode($result));
}

//判断地区关联是否选择完毕 start
$city_district_list = get_isHas_area($_COOKIE['type_city']);
if(!$city_district_list){
	setcookie('type_district', 0, gmtime() + 3600 * 24 * 30);
	$_COOKIE['type_district'] = 0;
}

$provinceT_list = get_isHas_area($_COOKIE['type_province']);
$cityT_list = get_isHas_area($_COOKIE['type_city'], 1);
$districtT_list = get_isHas_area($_COOKIE['type_district'], 1);

if($_COOKIE['type_province'] > 0 && $provinceT_list){
	if($city_district_list){
		if($cityT_list['parent_id'] == $_COOKIE['type_province'] && $_COOKIE['type_city'] == $districtT_list['parent_id']){
			$_COOKIE['province'] = $_COOKIE['type_province'];
			if($_COOKIE['type_city'] > 0){
				$_COOKIE['city'] = $_COOKIE['type_city'];
			}
			
			if($_COOKIE['type_district'] > 0){
				$_COOKIE['district'] = $_COOKIE['type_district'];
			}
		}
	}else{
		if($cityT_list['parent_id'] == $_COOKIE['type_province']){
			$_COOKIE['province'] = $_COOKIE['type_province'];
			if($_COOKIE['type_city'] > 0){
				$_COOKIE['city'] = $_COOKIE['type_city'];
			}
			
			if($_COOKIE['type_district'] > 0){
				$_COOKIE['district'] = $_COOKIE['type_district'];
			}
		}
	}
}
//判断地区关联是否选择完毕 end

$province_id = isset($_COOKIE['province']) ? $_COOKIE['province'] : $province_info['region_id'];
$city_id = isset($_COOKIE['city']) ? $_COOKIE['city'] : $city_info[0]['region_id'];
$district_id = isset($_COOKIE['district']) ? $_COOKIE['district'] : $district_info[0]['region_id'];

//判断仓库是否存在该地区
$warehouse_date = array('region_id', 'region_name');
$warehouse_where = "regionId = '$province_id'";
$warehouse_province = get_table_date('region_warehouse', $warehouse_where, $warehouse_date);

$sellerInfo = get_seller_info_area();
if(!$warehouse_province){
	$province_id = $sellerInfo['province'];
	$city_id = $sellerInfo['city'];
	$district_id = $sellerInfo['district'];
}

setcookie('province', $province_id, gmtime() + 3600 * 24 * 30);
setcookie('city', $city_id, gmtime() + 3600 * 24 * 30);
setcookie('district', $district_id, gmtime() + 3600 * 24 * 30);

$where = "region_id = '" .$city_id. "'";
$date = array('region_name');
$region_name = get_table_date('region', $where, $date, 2);
$GLOBALS['smarty']->assign('region_name',           $region_name);

$where = "parent_id = '" .$_COOKIE['province']. "' order by region_id asc";
$date = array('region_id', 'region_name');
$city_list = get_table_date('region', $where, $date, 1);
$GLOBALS['smarty']->assign('city_list', $city_list);

//ecmoban模板堂 -by wang start
$city_cache_data = read_static_cache('pin_regions', '/data/sc_file/');
if ($city_cache_data === false){
    $city_region_list = get_city_region();
    $pin_region_list = $city_region_list;
}else{
    $pin_region_list = $city_cache_data;
}

ksort($pin_region_list);
$GLOBALS['smarty']->assign('pin_region_list', $pin_region_list);
$GLOBALS['smarty']->assign('area_phpName', 'index.php');
$GLOBALS['smarty']->assign('province', $province_id);
$GLOBALS['smarty']->assign('ra_id', $_COOKIE['ra_id']);
$GLOBALS['smarty']->assign('city_top', $city_id);
$GLOBALS['smarty']->assign('district_top', $district_id);

$selProvince_list = get_all_area_list();
$GLOBALS['smarty']->assign('selProvince_list', $selProvince_list);

$selCity_list = get_all_area_list($province_id, 2);
$GLOBALS['smarty']->assign('selCity_list', $selCity_list);

$selDistrict_list = get_all_area_list($city_id, 3);
$GLOBALS['smarty']->assign('selDistrict_list', $selDistrict_list);

$selectLocate = 0;
if(isset($_COOKIE['province'])){
	$selectLocate = 1;
}
$GLOBALS['smarty']->assign('selectLocate', $selectLocate);

$sql="select kf_qq, kf_ww, kf_type, kf_tel from ".$GLOBALS['ecs']->table('seller_shopinfo')." where ru_id = 0 LIMIT 1";
$basic_info = $GLOBALS['db']->getRow($sql);
$GLOBALS['smarty']->assign('basic_info',  $basic_info);

$GLOBALS['smarty']->assign('user_id',  $_SESSION['user_id']);
//ecmoban模板堂 --zhuo end
?>