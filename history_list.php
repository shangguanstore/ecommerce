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

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_volume'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;

$act = isset($_REQUEST['act'])  ? $_REQUEST['act'] : '';
$goods_id = isset($_REQUEST['goods_id'])  ? intval($_REQUEST['goods_id']) : 0;

assign_template('c', 0);

$position = assign_ur_here(0, '浏览历史');
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$categories_pro = get_category_tree_leve_one();
$smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
    
$smarty->assign('helps',            get_shop_help());              // 网店帮助
//$smarty->assign('top_goods',        get_top10());                  // 销售排行
$smarty->assign('show_marketprice', $_CFG['show_marketprice']);

//ecmoban模板堂 --zhuo start
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$warehouse_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

$count = cate_history_count();

$max_page = ($count> 0) ? ceil($count / $size) : 1;
if ($page > $max_page)
{
	$page = $max_page;
}

if($act == 'delHistory'){
	include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'result' => '', 'qty' => 1);
	
	$goods_history = explode(',',$_COOKIE['ECS']['history']);
	$list_history = explode(',',$_COOKIE['ECS']['list_history']);
	
	$one_history = get_setcookie_goods($goods_history,$goods_id);
	$two_history = get_setcookie_goods($list_history,$goods_id);
	
	setcookie('ECS[history]', implode(',', $one_history), gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
	setcookie('ECS[list_history]', implode(',', $two_history), gmtime() + 3600 * 24 * 30, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);	
	
	die($json->encode($res));
}

$goodslist = cate_history($size,$page, $sort, $order, $warehouse_id, $area_id);
$smarty->assign('script_name', 'history_list');
$smarty->assign('category', 0);
$smarty->assign('best_goods',      get_category_recommend_goods('best', '', 0, 0, 0, '', $warehouse_id, $area_id));

$smarty->assign('goods_list',       $goodslist); // 分类游览历史记录 ecmoban模板堂 --zhuo
$smarty->assign('dwt_filename',  'history_list'); 

assign_pager('history_list',           0, $count, $size, $sort, $order, $page, '', '', '', '', '', '',''); // 分页

$smarty->display('history_list.dwt', $cache_id);

function get_setcookie_goods($list_history,$goods_id){
	
	for($i = 0; $i<=count($list_history); $i++){
		if($list_history[$i] == $goods_id){
			unset($list_history[$i]);
		}
	}
	
	return $list_history;
}

?>