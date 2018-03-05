<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');//by wang

//ecmoban模板堂 --zhuo start 仓库
$pid = isset($_REQUEST['pid'])  ? intval($_REQUEST['pid']) : 0;
$user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : 0;
//ecmoban模板堂 --zhuo end 仓库

//分类导航页
$smarty->assign('pre_nav_list', get_pre_nav());
/*------------------------------------------------------ */
//-- act 操作项的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'index';
}

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

$order_area = get_user_order_area($user_id);
$user_area = get_user_area_reg($user_id); //2014-02-25

if($order_area['province'] && $user_id > 0){
	$province_id = $order_area['province'];
}else{
	if($user_area['province'] > 0){
		$province_id = $user_area['province'];
		setcookie('province', $user_area['province'], gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
		$region_id = get_province_id_warehouse($province_id);
	}else{
		
		$sql = "select region_name from " .$ecs->table('region_warehouse'). " where regionId = '" .$province_info['region_id']. "'";
		$warehouse_name = $db->getOne($sql);
		
		$province_id = $province_info['region_id'];
		$cangku_name = $warehouse_name;
		$region_id = get_warehouse_name_id(0,$cangku_name);
	}
	
}

if($order_area['province'] && $user_id > 0){
	$city_id = $order_area['city'];
}else{
	if($user_area['city'] > 0){
		$city_id = $user_area['city'];
		setcookie('city', $user_area['city'], gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	}else{
		$city_id = $city_info[0]['region_id'];
	}
}

if($order_area['province'] && $user_id > 0){
	$district_id = $order_area['district'];
}else{
	if($user_area['district'] > 0){
		$district_id = $user_area['district'];
		setcookie('district', $user_area['district'], gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	}else{
		$district_id = $district_info[0]['region_id'];
	}
}

$province_id = isset($_COOKIE['province']) ? $_COOKIE['province'] : $province_id;

$child_num = get_region_child_num($province_id);
if($child_num > 0){
	$city_id = isset($_COOKIE['city']) ? $_COOKIE['city'] : $city_id;
}else{
	$city_id = '';
}

$child_num = get_region_child_num($city_id);
if($child_num > 0){
	$district_id = isset($_COOKIE['district']) ? $_COOKIE['district'] : $district_id;
}else{
	$district_id = '';
}

$region_id = !isset($_COOKIE['region_id']) ? $region_id : $_COOKIE['region_id'];

$goods_warehouse = get_warehouse_goods_region($province_id); //查询用户选择的配送地址所属仓库
if($goods_warehouse){
	$regionId = $goods_warehouse['region_id'];
	if($_COOKIE['region_id'] && $_COOKIE['regionId']){
		$gw = 0;
	}else{
		$gw = 1;
		
	}
}
if($gw){
	$region_id = $regionId;
	setcookie('area_region', $region_id, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
}

setcookie('goodsId', $goods_id, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);

if(empty($province_id)){
	$province_id = $sellerInfo['province'];
	$city_id = $sellerInfo['city'];
	$district_id = 0;
	
	setcookie('province', $province_id, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	setcookie('city', $city_id, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	setcookie('district', $district_id, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	
	$region_id = get_warehouse_goods_region($province_id);
        
        
}

$area_info = get_area_info($province_id);
//ecmoban模板堂 --zhuo end 仓库

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'price')
{
    $goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
    include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'err_no' => 0, 'result' => '', 'qty' => 1);

    $attr_id    = isset($_REQUEST['attr']) ? explode(',', $_REQUEST['attr']) : array();
    $number     = (isset($_REQUEST['number'])) ? intval($_REQUEST['number']) : 1;
    $warehouse_id     = (isset($_REQUEST['warehouse_id'])) ? intval($_REQUEST['warehouse_id']) : 0;
    $area_id     = (isset($_REQUEST['area_id'])) ? intval($_REQUEST['area_id']) : 0; //仓库管理的地区ID
	
    $onload     = (isset($_REQUEST['onload'])) ? trim($_REQUEST['onload']) : ''; //仓库管理的地区ID
    
    $goods = get_goods_info($goods_id, $warehouse_id, $area_id);

    if ($goods_id == 0)
    {
        $res['err_msg'] = $_LANG['err_change_attr'];
        $res['err_no']  = 1;
    }
    else
    {
        if ($number == 0)
        {
            $res['qty'] = $number = 1;
        }
        else
        {
            $res['qty'] = $number;
        }
		
        //ecmoban模板堂 --zhuo start
        $products = get_warehouse_id_attr_number($goods_id, $_REQUEST['attr'], $goods['user_id'], $warehouse_id, $area_id);
        $attr_number = $products['product_number'];
        

        if($goods['model_attr'] == 1){
                $table_products = "products_warehouse";
                $type_files = " and warehouse_id = '$warehouse_id'";
        }elseif($goods['model_attr'] == 2){
                $table_products = "products_area";
                $type_files = " and area_id = '$area_id'";
        }else{
                $table_products = "products";
                $type_files = "";
        }

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table($table_products). " WHERE goods_id = '$goods_id'" .$type_files. " LIMIT 0, 1";
        $prod = $GLOBALS['db']->getRow($sql);
        if ($goods['goods_type'] == 0)
        {
            //当商品没有属性库存时
            $attr_number = $goods['goods_number']; 
        }
        elseif(empty($prod))
        { 
            //当商品没有属性库存时
        	$attr_number = $goods['goods_number']; 
        }

        if (empty($prod))
        {
            $res['bar_code'] = $goods['bar_code'];
        }
        else
        {
            $res['bar_code'] = $products['bar_code'];
        }

        $attr_number = !empty($attr_number) ? $attr_number : 0;
        
        $res['attr_number'] = $attr_number;
//        $shop_price  = get_final_price($goods_id, $number, true, $attr_id, $warehouse_id, $area_id, 0, 1);
        //ecmoban模板堂 --zhuo end
		
        $res['shop_price'] = price_format($goods['shop_price']);
        $res['market_price'] = $goods['market_price'];
        
        //属性价格
        $spec_price  = get_final_price($goods_id, $number, true, $attr_id, $warehouse_id, $area_id, 1, 1);
        $res['marketPrice_amount'] = price_format($spec_price + $goods['marketPrice']);
        
        $res['result'] = price_format($shop_price * $number);
    }

    $goods_fittings = get_goods_fittings_info($goods_id, $warehouse_id, $area_id, '', 1);
    $fittings_list = get_goods_fittings(array($goods_id), $warehouse_id, $area_id);

    if($fittings_list){
        if(is_array($fittings_list)){
            foreach($fittings_list as $vo){
                $fittings_index[$vo['group_id']] = $vo['group_id'];//关联数组
            }
        }
        ksort($fittings_index);//重新排序

        $merge_fittings = get_merge_fittings_array($fittings_index, $fittings_list); //配件商品重新分组
        $fitts = get_fittings_array_list($merge_fittings, $goods_fittings);

        for($i=0; $i<count($fitts); $i++){
            $fittings_interval = $fitts[$i]['fittings_interval'];

            $res['fittings_interval'][$i]['fittings_minMax'] = price_format($fittings_interval['fittings_min']) ."-". number_format($fittings_interval['fittings_max'], 2, '.', '');
            $res['fittings_interval'][$i]['market_minMax'] = price_format($fittings_interval['market_min']) ."-". number_format($fittings_interval['market_max'], 2, '.', '');	

            if($fittings_interval['save_minPrice'] == $fittings_interval['save_maxPrice']){
                    $res['fittings_interval'][$i]['save_minMaxPrice'] = price_format($fittings_interval['save_minPrice']);
            }else{
                    $res['fittings_interval'][$i]['save_minMaxPrice'] = price_format($fittings_interval['save_minPrice']) ."-". number_format($fittings_interval['save_maxPrice'], 2, '.', '');
            }

            $res['fittings_interval'][$i]['groupId'] = $fittings_interval['groupId'];
        }
    }
    
    
    if($GLOBALS['_CFG']['open_area_goods'] == 1){
        
        $area_list = get_goods_link_area_list($goods_id, $goods['user_id']);
        if($area_list['goods_area']){
            if(!in_array($area_id, $area_list['goods_area'])){
                $res['err_no']  = 2;  
            }
        } else {
            $res['err_no']  = 2;  
        }
    }
    
    $presale = get_presale_time($goods_id);
    $res['act_id'] = isset($presale['act_id']) ? $presale['act_id'] : 0;
    $res['presale'] = $presale;

    die($json->encode($res));
}elseif($_REQUEST['act'] == 'in_stock'){
	
	include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '', 'qty' => 1);
	
	clear_cache_files();
    
    $act_id = empty($_GET['act_id']) ? 0 : intval($_GET['act_id']);
    $goods_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
    $province = empty($_GET['province']) ? 1 : intval($_GET['province']);
    $city = empty($_GET['city']) ? 52 : intval($_GET['city']);
    $district = empty($_GET['district']) ? 500 : intval($_GET['district']);
	$d_null = empty($_GET['d_null']) ? 0 : intval($_GET['d_null']);
	$user_id = empty($_GET['user_id']) ? 0 : ($_GET['user_id']);
	
	$user_address = get_user_address_region($user_id);
	$user_address = explode(",",$user_address['region_address']);

	setcookie('province', $province, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	setcookie('city', $city, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	
	setcookie('district', $district, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	
	$regionId = 0;
	setcookie('regionId', $regionId, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);

	//清空
	setcookie('type_province', 0, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);	
	setcookie('type_city', 0, gmtime() + 3600 * 24 * 30);	
	setcookie('type_district', 0, gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);	
	
	$res['d_null'] = $d_null;
	
	if($d_null == 0){
		if(in_array($district,$user_address)){
			$res['isRegion'] = 1;
		}else{
			$res['message'] = "您尚未拥有此配送地区，请您填写配送地址";	
			$res['isRegion'] = 88; //原为0
		}
	}else{
		setcookie('district', '', gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	}
	
	$res['goods_id'] = $goods_id;
        $res['act_id'] = $act_id;

    die($json->encode($res));
	
}

/*------------------------------------------------------ */
//-- 预售 --> 首页
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'index')
{
    // 调用数据
    $pre_goods = get_pre_cat();
    $smarty->assign('pre_cat_goods', $pre_goods);
    
    assign_template();
    $smarty->assign('helps',      get_shop_help());       // 网店帮助
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
    
    /**小图 start**/
    for($i=1;$i<=$_CFG['auction_ad'];$i++)
    {
        $presale_banner   .= "'presale_banner".$i.","; //预售轮播banner
        $presale_banner_small   .= "'presale_banner_small".$i.","; //预售小轮播
        $presale_banner_small_left   .= "'presale_banner_small_left".$i.","; //预售小轮播 左侧
        $presale_banner_small_right   .= "'presale_banner_small_right".$i.","; //预售小轮播 右侧
    }
    
    $smarty->assign('pager', array('act'=>'index'));
    $smarty->assign('presale_banner',       $presale_banner);
    $smarty->assign('presale_banner_small',       $presale_banner_small);
    $smarty->assign('presale_banner_small_left',       $presale_banner_small_left);
    $smarty->assign('presale_banner_small_right',       $presale_banner_small_right);
    
    /**小图 end**/
    
    /* 显示模板 */
    $smarty->display('presale_index.dwt');
}

/*------------------------------------------------------ */
//-- 预售 --> 特惠专区
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'area')
{
    // 调用数据
    
    /* 显示模板 */
    $smarty->display('presale_area.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 预售 --> 新品发布
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'new')
{
    $where = '';
    // 筛选条件
    $cid = isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0 ? intval($_REQUEST['cid']) : 0;
    $status = isset($_REQUEST['status']) && intval($_REQUEST['status']) > 0 ? intval($_REQUEST['status']) : 0;// 状态1即将开始，2预约中，3已结束
    
    if ($cid > 0 )
    {
        $where .= " AND a.cid = '$cid' ";
    }
    //1未开始，2进行中，3结束
    $now = gmtime();
    if ($status == 1)
    {
        $where .= " AND a.start_time > $now ";
    }
    elseif ($status == 2)
    {
        $where .= " AND a.start_time < $now AND $now < a.end_time ";
    }
    elseif ($status == 3)
    {
        $where .= " AND $now > a.end_time ";
    }
    
    $pager = array('cid'=>$cid, 'act' => 'new', 'status' => $status);
    $smarty->assign('pager',$pager);
    // 调用数据
    $pre_category = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." ORDER BY sort_order ASC ");
    $smarty->assign('pre_category', $pre_category);

    $sql = "SELECT a.*, g.goods_thumb, g.goods_img, g.goods_name, g.shop_price, g.market_price, g.sales_volume FROM ".$GLOBALS['ecs']->table('presale_activity')." AS a"
            . " LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON a.goods_id = g.goods_id "
            . " WHERE g.goods_id > 0 $where AND g.is_on_sale = 0 ORDER BY a.end_time DESC,a.start_time DESC ";
    $res = $GLOBALS['db']->getAll($sql);
    
    foreach ($res as $key => $row)
    {
        $res[$key]['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $res[$key]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $res[$key]['url'] = build_uri('presale', array('act' => 'view', 'presaleid' => $row['act_id']));
        
        $res[$key]['end_time_date'] = local_date("Y-m-d H:i:s", $row['end_time']);
        $res[$key]['end_time_day'] = local_date("Y-m-d", $row['end_time']);
        
        $res[$key]['start_time_date'] = local_date("Y-m-d H:i:s", $row['start_time']);
        $res[$key]['start_time_day'] = local_date("Y-m-d", $row['start_time']);
        
        if ($row['start_time'] >= $now )
        {
            $res[$key]['no_start'] = 1;
        }
        if ($row['end_time'] <= $now) {
            $res[$key]['already_over'] = 1;
        }
    }
//    print_arr($res);
    // 按日期重新排序数据分组
    $date_array = array();
    foreach ($res as $key => $row)
    {
        $date_array[$row['end_time_day']][] = $row;
        
    }

    // 把日期键值替换成数字0、1、2...,日期楼层下商品归类
    $date_result = array();
    foreach ($date_array as $key => $value)
    {
        $date_result[]['goods'] = $value;
    }

    foreach ($date_result as $key => $value)
    {
        $date_result[$key]['end_time_day'] = $value['goods'][0]['end_time_day'];
        $date_result[$key]['end_time_y'] = local_date('Y', gmstr2time($value['goods'][0]['end_time_day']));
        $date_result[$key]['end_time_m'] = local_date('m', gmstr2time($value['goods'][0]['end_time_day']));
        $date_result[$key]['end_time_d'] = local_date('d', gmstr2time($value['goods'][0]['end_time_day']));
        $date_result[$key]['count_goods'] = count($value['goods']);
    }

    
    $smarty->assign('date_result', $date_result);
    
    assign_template();
    $smarty->assign('helps',      get_shop_help());       // 网店帮助
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
    
    /**小图 start**/
    for($i=1;$i<=$_CFG['auction_ad'];$i++)
    {
        $presale_banner_new   .= "'presale_banner_new".$i.","; //预售轮播banner
    }
    
    $smarty->assign('presale_banner_new',       $presale_banner_new);
    
    /* 显示模板 */
    $smarty->display('presale_new.dwt');
}

/*------------------------------------------------------ */
//-- 预售 --> 抢先订
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'advance')
{
    //筛选条件
    $price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
    $price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;

    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
    $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'act_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'start_time');
    
    $sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('shop_price', 'start_time', 'act_id'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
    $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
    
    $cid = isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0 ? intval($_REQUEST['cid']) : 0;
    $status = isset($_REQUEST['status']) && intval($_REQUEST['status']) > 0 ? intval($_REQUEST['status']) : 0;// 状态1即将开始，2预约中，3已结束
    // 调用数据
    $goods = get_pre_goods($cid, $min=0, $max=0, $start_time, $end_time, $sort, $status, $order);
//    print_arr($goods);
    $pre_category = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." ORDER BY sort_order ASC ");
    $smarty->assign('pre_category', $pre_category);

    //    print_arr($pre_category);
    $pager = array('cid'=>$cid, 'act' => 'advance','price_min' => $price_min,'price_max' => $price_max,'sort' => $sort,'order' => $order,'status' => $status);
    $smarty->assign('pager',$pager);
    $smarty->assign("goods", $goods);
    
    assign_template();
    $smarty->assign('helps',      get_shop_help());       // 网店帮助
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
    
    /**小图 start**/
    for($i=1;$i<=$_CFG['auction_ad'];$i++)
    {
        $presale_banner_advance   .= "'presale_banner_advance".$i.","; //预售轮播banner
    }
    
    $smarty->assign('presale_banner_advance',       $presale_banner_advance);
    
    /* 显示模板 */
    $smarty->display('presale_advance.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 预售 --> 抢先订
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'category')
{
    //筛选条件
    $price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
    $price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;

    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
    $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'act_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'start_time');
    
    $sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('shop_price', 'start_time', 'act_id'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
    $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
    
    $cid = isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0 ? intval($_REQUEST['cid']) : 0;
    $status = isset($_REQUEST['status']) && intval($_REQUEST['status']) > 0 ? intval($_REQUEST['status']) : 0;// 状态1即将开始，2预约中，3已结束
    // 调用数据
    $goods = get_pre_goods($cid, $min=0, $max=0, $start_time, $end_time, $sort, $status, $order);
    $pre_category = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." ORDER BY sort_order ASC ");
    $smarty->assign('pre_category', $pre_category);

    //    print_arr($pre_category);
    $pager = array('cid'=>$cid, 'act' => 'category','price_min' => $price_min,'price_max' => $price_max,'sort' => $sort,'order' => $order,'status' => $status);
    $smarty->assign('pager',$pager);
    $smarty->assign("goods", $goods);
    
    assign_template();
    $smarty->assign('helps',      get_shop_help());       // 网店帮助
    $position = assign_ur_here();
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
    
    /**小图 start**/
    for($i=1;$i<=$_CFG['auction_ad'];$i++)
    {
        $presale_banner_category   .= "'presale_banner_category".$i.","; //预售轮播banner
    }
    
    $smarty->assign('presale_banner_category',       $presale_banner_category);
    
    /* 显示模板 */
    $smarty->display('presale_category.dwt', $cache_id);
}
/*------------------------------------------------------ */
//-- 猜你喜欢--换一组ajax处理
/*------------------------------------------------------ */
elseif (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'guess_goods')
{
    include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '');
    
    $page    = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 1;
    if($page > 3){
        $page = 1;
    }
    $need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;
    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    $guess_goods = get_guess_goods($user_id, 1, $page, 7);
    
    $smarty->assign('guess_goods', $guess_goods);
    $smarty->assign('pager', $pager);
    
    $res['page'] = $page;
    $res['result'] = $GLOBALS['smarty']->fetch('library/guess_goods_love.lbi');

    $GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;
    
    die($json->encode($res));
}
/*------------------------------------------------------ */
//-- 预售 --> 商品详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    
    /* 取得参数：预售活动id */
    $presale_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($presale_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得预售活动信息 */
    $presale = presale_info($presale_id);

    if (empty($presale))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    assign_template();
    $categories_pro = get_category_tree_leve_one();
    $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版

    /* 缓存id：语言，预售活动id，状态，（如果是进行中）当前数量和是否登录 */
    $cache_id = $_CFG['lang'] . '-presale-' . $presale_id . '-' . $presale['status'].  time();
    if ($presale['status'] == GBS_UNDER_WAY)
    {
        $cache_id = $cache_id . '-' . $presale['valid_goods'] . '-' . intval($_SESSION['user_id'] > 0);
    }
    $cache_id = sprintf('%X', crc32($cache_id));
    
    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('presale_goods.dwt', $cache_id))
    {
        $now = gmtime();
        $presale['gmt_end_date'] = local_strtotime($presale['end_time']);
        $presale['gmt_start_date'] = local_strtotime($presale['start_time']);
        if ($presale['gmt_start_date'] >= $now )
        {
            $presale['no_start'] = 1;
        }
        if ($presale['gmt_end_date'] <= $now)
        {
            $presale['already_over'] = 1;
        }
        $smarty->assign('presale', $presale);

        /* 取得预售商品信息 */
        $goods_id = $presale['goods_id'];
        $goods = get_goods_info($goods_id, $region_id, $area_id);
        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        
        $smarty->assign('goods', $goods);
        
        $smarty->assign('id',           $goods_id);
        $smarty->assign('type',         0);
        
        //评分 start
        $comment_all = get_comments_percent($goods_id);
        $smarty->assign('comment_all',  $comment_all); 
        if($goods['user_id'] > 0){
                $merchants_goods_comment = get_merchants_goods_comment($goods['user_id']); //商家所有商品评分类型汇总
                $smarty->assign('merch_cmt',  $merchants_goods_comment); 
        }
        //评分 end 

        //ecmoban模板堂 --zhuo start
        $shop_info = get_merchants_shop_info('merchants_steps_fields', $goods['user_id']);
        $adress = get_license_comp_adress($shop_info['license_comp_adress']);

        $smarty->assign('shop_info',       $shop_info);
        $smarty->assign('adress',       $adress);

        $province_list = get_warehouse_province();

        $smarty->assign('province_list',                $province_list); //省、直辖市

        $city_list = get_region_city_county($province_id);
        $smarty->assign('city_list',                $city_list); //省下级市

        $district_list = get_region_city_county($city_id); 
        $smarty->assign('district_list',                $district_list);//市下级县

        $smarty->assign('goods_id',			$goods_id); //商品ID

        $warehouse_list = get_warehouse_list_goods();  
        $smarty->assign('warehouse_list',			$warehouse_list); //仓库列

        $warehouse_name = get_warehouse_name_id($region_id);

        $smarty->assign('warehouse_name',			$warehouse_name); //仓库名称
        $smarty->assign('region_id',			$region_id); //商品仓库region_id 

        $smarty->assign('user_id',			$_SESSION['user_id']);  

        $smarty->assign('shop_price_type',			$goods['model_price']); //商品价格运营模式 0代表统一价格（默认） 1、代表仓库价格 2、代表地区价格
        $smarty->assign('area_id',  $area_info['region_id']); //地区ID 
        //ecmoban模板堂 --zhuo start 仓库

        /* 取得商品的规格 */
        $properties = get_goods_properties($goods_id);
        $smarty->assign('properties', $properties['pro']);    //商品属性
        $smarty->assign('specification', $properties['spe']); // 商品规格
        
        $smarty->assign('area_htmlType',  'presale'); 
        
        $smarty->assign('province_row',  get_region_name($province_id));  
        $smarty->assign('city_row',  get_region_name($city_id));  
        $smarty->assign('district_row',  get_region_name($district_id));

        //模板赋值
        $smarty->assign('cfg', $_CFG);

        $position = assign_ur_here(0, $presale['goods_name']);

        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置

        $smarty->assign('categories', get_categories_tree()); // 分类树
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        $smarty->assign('top_goods',  get_top10('', 'presale'));           // 销售排行
        $smarty->assign('guess_goods',     get_guess_goods($user_id, 1, $page=1, 7,$region_id, $area_info['region_id']));         //猜你喜欢
        $smarty->assign('best_goods',      get_recommend_goods('best', '', $region_id, $area_info['region_id'], $goods['user_id'], 1, 'presale'));    // 推荐商品
        $smarty->assign('new_goods',       get_recommend_goods('new', '', $region_id, $area_info['region_id'], $goods['user_id'], 1, 'presale'));     // 最新商品
        $smarty->assign('hot_goods',       get_recommend_goods('hot', '', $region_id, $area_info['region_id'], $goods['user_id'], 1, 'presale'));     // 最新商品
        $smarty->assign('pictures',   get_goods_gallery($goods_id)); // 商品相册
        $smarty->assign('promotion_info', get_promotion_info());
    }
    
    $linked_goods = get_linked_goods($goods_id, $region_id, $area_info['region_id']);
    $smarty->assign('related_goods', $linked_goods);
    
    //　详情部分 评分 start
    $comment_all = get_comments_percent($goods_id);

    if($goods['user_id'] > 0){
            $merchants_goods_comment = get_merchants_goods_comment($goods['user_id']); //商家所有商品评分类型汇总
    }
    $smarty->assign('comment_all',  $comment_all); 
    
    //$cat_info = cat_list(0, 0, false, 0, true, '', 0, $goods['user_id']);
    //$goods_store_cat = goods_admin_store_cat_list($cat_info);

    if ($goods['user_id'])
    {
        $goods_store_cat = get_child_tree_pro(0, 0, 'merchants_category');

        if ($goods_store_cat)
        {
            $goods_store_cat = array_values($goods_store_cat);
        }
        
        $smarty->assign('goods_store_cat', $goods_store_cat); 
    }
    
    $discuss_list = get_discuss_all_list($goods_id, 0, 1, 10);
    $smarty->assign('discuss_list',       $discuss_list);
    
    
    //更新商品点击次数
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $group_buy['goods_id'] . "'";
    $db->query($sql);

    $smarty->assign('act_id',  $presale_id);   
    $smarty->assign('now_time',  gmtime());           // 当前系统时间
    
    $smarty->assign('area_htmlType',       'presale');
    
    $basic_info = get_shop_info_content($goods['user_id']);

    $basic_date = array('region_name');
    $basic_info['province'] = get_table_date('region', "region_id = '" . $basic_info['province'] . "'", $basic_date, 2);
    $basic_info['city'] = get_table_date('region', "region_id= '" . $basic_info['city'] . "'", $basic_date, 2) . "市";    
    
    /*  @author-bylu 判断当前商家是否允许"在线客服" start  */
    $shop_information = get_shop_name($goods['user_id']);//通过ru_id获取到店铺信息;
//    $goods_info['user_id'] == 0 ? $shop_information['is_dsc'] = true : $shop_information['is_dsc'] = false;//判断当前商家是平台,还是入驻商家 bylu
    //判断当前商家是平台,还是入驻商家 bylu
    if($goods_info['user_id'] == 0){
        //判断平台是否开启了IM在线客服
        if($db->getOne("SELECT kf_im_switch FROM ".$ecs->table('seller_shopinfo')."WHERE ru_id = 0")){
            $shop_information['is_dsc'] = true;
        }else{
            $shop_information['is_dsc'] = false;
        }
    }else{
        $shop_information['is_dsc'] = false;
    }
    $smarty->assign('shop_information',$shop_information);
    /*  @author-bylu  end  */

    $smarty->assign('basic_info',  $basic_info);
    
    $area = array(
        'region_id' => $region_id,  //仓库ID
        'province_id' => $province_id,
        'city_id' => $city_id,
        'district_id' => $district_id,
        'goods_id' => $goods_id,
        'user_id' => $user_id,
        'area_id' => $area_info['region_id'],
        'merchant_id' => $goods['user_id'],
    );

    $smarty->assign('area',  $area);
     
    $smarty->display('presale_goods.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 预售商品 --> 购买
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'buy')
{
    /* 查询：判断是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        show_message($_LANG['gb_error_login'], '', '', 'error');
    }
    
    $warehouse_id     = (isset($_REQUEST['warehouse_id'])) ? intval($_REQUEST['warehouse_id']) : 0;
    $area_id     = (isset($_REQUEST['area_id'])) ? intval($_REQUEST['area_id']) : 0; //仓库管理的地区ID

    /* 查询：取得参数：预售活动id */
    $presale_id = isset($_POST['presale_id']) ? intval($_POST['presale_id']) : 0;
    if ($presale_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：取得数量 */
    $number = isset($_POST['number']) ? intval($_POST['number']) : 1;
    $number = $number < 1 ? 1 : $number;

    /* 查询：取得预售活动信息 */
    $presale = presale_info($presale_id, $number);
    if (empty($presale))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：检查预售活动是否是进行中 */
    if ($presale['status'] != GBS_UNDER_WAY)
    {
        show_message($_LANG['presale_error_status'], '', '', 'error');
    }
    
    /* 查询：取得预售商品信息 */
    $goods = goods_info($presale['goods_id'], $warehouse_id, $area_id);
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：判断数量是否足够 */
    if (($goods['goods_number'] > 0 && $number > ($goods['goods_number'] - $presale['valid_goods'])))
    {
        show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
    }

    /* 查询：取得规格 */
    $specs = isset($_POST['goods_spec']) ? htmlspecialchars(trim($_POST['goods_spec'])) : '';

    /* 查询：如果商品有规格则取规格商品信息 配件除外 */
    if ($specs)
    {
        $_specs = explode(',', $specs);
        $product_info = get_products_info($goods['goods_id'], $_specs, $warehouse_id, $area_id);
    }

    empty($product_info) ? $product_info = array('product_number' => 0, 'product_id' => 0) : '';
    
    if($goods['model_attr'] == 1){
            $table_products = "products_warehouse";
            $type_files = " and warehouse_id = '$warehouse_id'";
    }elseif($goods['model_attr'] == 2){
            $table_products = "products_area";
            $type_files = " and area_id = '$area_id'";
    }else{
            $table_products = "products";
            $type_files = "";
    }

    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table($table_products). " WHERE goods_id = '" .$goods['goods_id']. "'" .$type_files. " LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);

    /* 检查：库存 */
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        /* 查询：判断指定规格的货品数量是否足够 */
        if ($prod && $number > $product_info['product_number'])
        {
            show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
        }else{
            /* 查询：判断数量是否足够 */
            if ($number > $goods['goods_number'])
            {
                show_message($_LANG['gb_error_goods_lacking'], '', '', 'error');
            }
        }
    }    

    /* 查询：查询规格名称和值，不考虑价格 */
    $attr_list = array();
    $sql = "SELECT a.attr_name, g.attr_value " .
            "FROM " . $ecs->table('goods_attr') . " AS g, " .
                $ecs->table('attribute') . " AS a " .
            "WHERE g.attr_id = a.attr_id " .
            "AND g.goods_attr_id " . db_create_in($specs);
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
    }
    $goods_attr = join(chr(13) . chr(10), $attr_list);

    /* 更新：清空购物车中所有预售商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_PRESALE_GOODS);
	
    //ecmoban模板堂 --zhuo start

   
    $area_id = $area_info['region_id'];

    $where = "regionId = '$province_id'";
    $date = array('parent_id');
    $region_id = get_table_date('region_warehouse', $where, $date, 2);

    if(!empty($_SESSION['user_id'])){
            $sess = "";
    }else{
            $sess = real_cart_mac_ip();
    }
    //ecmoban模板堂 --zhuo end

    /* 更新：加入购物车 */
    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => $sess,
        'goods_id'       => $presale['goods_id'],
        'product_id'     => $product_info['product_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $goods['shop_price'],
        'goods_number'   => $number,
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $specs,
        //ecmoban模板堂 --zhuo start
        'ru_id'			 => $goods['user_id'],
        'warehouse_id'   => $region_id,
        'area_id'  		 => $area_id,
        //ecmoban模板堂 --zhuo end
        'is_real'        => $goods['is_real'],
        'extension_code' => 'presale',
        'parent_id'      => 0,
        'rec_type'       => CART_PRESALE_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 更新：记录购物流程类型：预售 */
    $_SESSION['flow_type'] = CART_PRESALE_GOODS;
    $_SESSION['extension_code'] = 'presale';
    $_SESSION['extension_id'] = $presale['act_id'];

    /* 进入收货人页面 */
    $_SESSION['browse_trace'] = "presale";
    ecs_header("Location: ./flow.php?step=checkout\n");
    exit;
}

/**
 * 取得某页的所有预售商品
 * 
 */
function get_pre_goods($cid, $min=0, $max=0, $start_time=0, $end_time=0, $sort, $status=0, $order)
{
    $now = gmtime();
    $where = '';
    if ($cid > 0)
    {
        $where = "AND a.cid = '$cid' ";
    }
    
    //1未开始，2进行中，3结束
    if ($status == 1)
    {
        $where .= " AND a.start_time > $now ";
    }
    elseif ($status == 2)
    {
        $where .= " AND a.start_time < $now AND $now < a.end_time ";
    }
    elseif ($status == 3)
    {
        $where .= " AND $now > a.end_time ";
    }
    
    if ($sort == 'shop_price')
    {
        $sort = "g.$sort";
    }  else
    {
        $sort = "a.$sort";
    }
    
    $sql = "SELECT a.*, g.goods_thumb, g.goods_img, g.goods_name, g.shop_price, g.market_price, g.sales_volume FROM ".$GLOBALS['ecs']->table('presale_activity')." AS a "
                . " LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON a.goods_id = g.goods_id "
                . " WHERE g.goods_id > 0 $where AND g.is_on_sale = 0 ORDER BY $sort $order";
    
     $res =  $GLOBALS['db']->getAll($sql);
     foreach ($res as $key => $row)
    {
        $res[$key]['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $res[$key]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $res[$key]['url'] = build_uri('presale', array('act' => 'view', 'presaleid' => $row['act_id']));
        
        $res[$key]['end_time_date'] = local_date('Y-m-d H:i:s', $row['end_time']);
        $res[$key]['start_time_date'] = local_date('Y-m-d H:i:s', $row['start_time']);
        
        if ($row['start_time'] >= $now )
        {
            $res[$key]['no_start'] = 1;
        }
        if ($row['end_time'] <= $now) {
            $res[$key]['already_over'] = 1;
        }
    }
//    print_arr($res);
    return $res;
}

/**
 * 获得预售分类商品
 *
 */
function get_pre_cat()
{
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." ORDER BY sort_order ASC ";
    $cat_res = $GLOBALS['db']->getAll($sql);

    foreach ($cat_res as $key => $row)
    {
        $cat_res[$key]['goods'] = get_cat_goods($row['cid'], $row['act_id']);
        $cat_res[$key]['count_goods'] = count(get_cat_goods($row['cid']));
        $cat_res[$key]['cat_url'] = build_uri('presale', array('act' => 'category', 'presaleid' => $row['cid']));
    }
    
    return $cat_res;
}

// 获取分类下商品并进行分组
function get_cat_goods($cat_id)
{
    $now = gmtime();
    $sql = "SELECT a.*, g.goods_thumb, g.goods_img, g.goods_name, g.shop_price, g.market_price, g.sales_volume FROM ".$GLOBALS['ecs']->table('presale_activity')." AS a "
                . " LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON a.goods_id = g.goods_id "
                . "WHERE a.cid = '$cat_id' AND g.is_on_sale = 0 ";
    
    $res = $GLOBALS['db']->getAll($sql);
    foreach ($res as $key => $row)
    {
        $res[$key]['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $res[$key]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $res[$key]['url'] = build_uri('presale', array('act' => 'view', 'presaleid' => $row['act_id']), $row['goods_name']);
        
        $res[$key]['end_time_date'] = local_date('Y-m-d H:i:s', $row['end_time']);
        $res[$key]['start_time_date'] = local_date('Y-m-d H:i:s', $row['start_time']);
        
        if ($row['start_time'] >= $now )
        {
            $res[$key]['no_start'] = 1;
        }
        if ($row['end_time'] <= $now) {
            $res[$key]['already_over'] = 1;
        }
    }
    return $res;
}

// 获取预售导航信息
function get_pre_nav()
{
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." WHERE parent_cid = 0 ORDER BY sort_order ASC LIMIT 7 ";
    $res = $GLOBALS['db']->getAll($sql);
    return $res;
}

/*
 * 查询商品是否预售
 * 是，则返回预售结束时间
 */
function get_presale_time($goods_id){
    $sql = "SELECT act_id, end_time FROM " .$GLOBALS['ecs']->table('presale_activity'). " WHERE goods_id = '$goods_id' LIMIT 1";
    $res = $GLOBALS['db']->getRow($sql);

    if($res['end_time']){
        $res['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $res['end_time']);
        $res['str_time'] = substr($res['end_time'], 0, 13);
    }
    
    return $res;
}
function get_linked_goods($goods_id, $warehouse_id = 0, $area_id = 0)
{
    $where = "";
    $leftJoin = "";
    $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_goods') . " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_area_goods') . " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

    if ($GLOBALS['_CFG']['open_area_goods'] == 1) {
        $leftJoin .= " left join " . $GLOBALS['ecs']->table('link_area_goods') . " as lag on g.goods_id = lag.goods_id ";
        $where .= " and lag.region_id = '$area_id' ";
    }

    $sql = "SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, ".
            "IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) AS org_price, ".
            "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, g.market_price, g.sales_volume, ".
            "IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price,  g.promote_start_date, g.promote_end_date ".
            "FROM " . $GLOBALS['ecs']->table('link_goods') . " lg LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.goods_id = lg.link_goods_id ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]'" . $leftJoin . 
            "WHERE lg.goods_id = '$goods_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " . $where . 
            "LIMIT " . $GLOBALS['_CFG']['related_goods_number'];
    $res = $GLOBALS['db']->query($sql);
    $arr = array();

    while ($row = $GLOBALS['db']->fetchRow($res)) {
        if ($row['promote_price' > 0]) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else {
            $promote_price = 0;
        }

        $price_other = array('market_price' => $row['market_price'], 'org_price' => $row['org_price'], 'shop_price' => $row['shop_price'], 'promote_price' => $promote_price);
        $price_info = get_goods_one_attr_price($row['goods_id'], $warehouse_id, $area_id, $price_other);
        $row = !empty($row) ? array_merge($row, $price_info) : $row;
        $promote_price = $row['promote_price'];
        $arr[$row['goods_id']]['goods_id'] = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
        $arr[$row['goods_id']]['short_name'] = ($GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name']);
        $arr[$row['goods_id']]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price'] = price_format($row['shop_price']);
        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0 ? price_format($promote_price) : '');
        $arr[$row['goods_id']]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
        $arr[$row['goods_id']]['sales_volume'] = $row['sales_volume'];
    }

    return $arr;
}
?>