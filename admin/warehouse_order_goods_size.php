<?php

/**
 * ECSHOP 会员管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单仓库列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('warehouse_manage');
	
	$rec_id = isset($_REQUEST['rec_id']) ? intval($_REQUEST['rec_id']) : 0;
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	
    $smarty->assign('ur_here',      "商品属性类型");
	
	$size_attr = get_size_attr_goods($rec_id);
	$size_attr = get_explode_arr($size_attr,',');
	$sizeAttr = get_arr_two($size_attr);
	$size_list = get_size_list_order_goods($sizeAttr,$goods_id,2);	
	
	$_SESSION['rec_id'] = $rec_id;

    $smarty->assign('size_list',    $size_list);
    $smarty->assign('full_page',    1);
	$smarty->assign('goods_id',    $goods_id);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('order_goods_size_warehouse_list.htm');
}

/*------------------------------------------------------ */
//-- ajax返回订单仓库列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	$warehouse_id = $_SESSION['warehouse_order_goods_warehouseId'];
	$order_id = $_SESSION['warehouse_order_goods_orderId'];
    $order_goods_list = order_warehouse_list($order_id,$warehouse_id);

    $smarty->assign('order_goods_list',    $order_goods_list['order_goods_list']);
    $smarty->assign('filter',       $order_goods_list['filter']);
    $smarty->assign('record_count', $order_goods_list['record_count']);
    $smarty->assign('page_count',   $order_goods_list['page_count']);

    $sort_flag  = sort_flag($warehouse_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('order_goods_size_warehouse_list.htm'), '', array('filter' => $order_goods_list['filter'], 'page_count' => $order_goods_list['page_count']));
}

//查询商品属性与尺码
function get_size_attr_goods($rec_id){
	$sql = "select size_attr from " .$GLOBALS['ecs']->table('order_goods'). " where rec_id = '$rec_id'";
	
	return $GLOBALS['db']->getOne($sql);
}
?>