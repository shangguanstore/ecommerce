<?php

/**
 * ECSHOP 商品比较程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: compare.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

//ecmoban模板堂 --zhuo start
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

if (!empty($_REQUEST['goods']) && is_array($_REQUEST['goods']) && count($_REQUEST['goods']) > 1)
{
	//ecmoban模板堂 --zhuo start
	$leftJoin = '';		
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";
    //ecmoban模板堂 --zhuo end
	
	$compare = !empty($_REQUEST['compare']) ? $_REQUEST['compare'] : '';
	$highlight = !empty($_REQUEST['highlight']) ? $_REQUEST['highlight'] : '';
    $where = db_create_in($_REQUEST['goods'], 'id_value');
    $sql = "SELECT id_value , AVG(comment_rank) AS cmt_rank, COUNT(*) AS cmt_count" .
           " FROM " .$ecs->table('comment') .
           " WHERE $where AND comment_type = 0".
           ' GROUP BY id_value ';
    $query = $db->query($sql);
    $cmt = array();
    while ($row = $db->fetch_array($query))
    {
        $cmt[$row['id_value']] = $row;
    }

    $where = db_create_in($_REQUEST['goods'], 'g.goods_id');
    $sql = "SELECT g.goods_id, g.goods_type, g.goods_name, g.user_id, g.brand_id, " . 
			"IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, " .
			"IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, " .
			"g.promote_start_date, g.promote_end_date, g.is_promote, " . 
			" g.goods_weight, g.goods_thumb, g.goods_brief, ".
                "a.attr_name, v.attr_value, a.attr_id, a.attr_input_category, b.brand_name, ".
                "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS rank_price " .
            "FROM " .$ecs->table('goods'). " AS g ".
            "LEFT JOIN " . $ecs->table('goods_attr'). " AS v ON v.goods_id = g.goods_id ".
            "LEFT JOIN " . $ecs->table('attribute') . " AS a ON a.attr_id = v.attr_id " .
            "LEFT JOIN " . $ecs->table('brand') . " AS b ON g.brand_id = b.brand_id " .
			
			$leftJoin . 
			
            "LEFT JOIN " . $ecs->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            "WHERE g.is_delete = 0 AND $where ".
            "ORDER BY a.attr_id";
    $res = $db->query($sql);
    $basic_arr = array();
    $ids = $_REQUEST['goods'];
    $attr_name = array();
    $type_id = 0;
    
    $param_goods_id = '';
    $g_count = count($_REQUEST['goods']);
    $new_goods_arr = array_unique($_REQUEST['goods']);
    $n_count = count($new_goods_arr);
    foreach($_REQUEST['goods'] as $goods_id_val)
    {
    	if(empty($goods_id_val) || $g_count != $n_count)
    	{
    		show_message('已经是最后一条了-_-');
    	}
    	$param_goods_id .= 'goods[]=' . $goods_id_val . '&amp;';
    }
    $param_goods_id = substr($param_goods_id, 0, -5);
    
    while ($row = $db->fetchRow($res))
    {
		$goods_id = $row['goods_id'];
		
		if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }
		
		if($promote_price > 0){
			$row['shop_price'] = $promote_price;
		}

    	$type_id = $row['goods_type'];
    	$goods_list[$goods_id]['goods_id']     = $goods_id;
    	$goods_list[$goods_id]['url']          = build_uri('goods', array('gid' => $goods_id), $row['goods_name']);
    	$goods_list[$goods_id]['goods_name']   = $row['goods_name'];
    	$goods_list[$goods_id]['shop_price']   = price_format($row['shop_price']);
    	$goods_list[$goods_id]['rank_price']   = price_format($row['rank_price']);
    	$goods_list[$goods_id]['goods_weight'] = (intval($row['goods_weight']) > 0) ?
    	ceil($row['goods_weight']) . $_LANG['kilogram'] : ceil($row['goods_weight'] * 1000) . $_LANG['gram'];
    	$goods_list[$goods_id]['goods_thumb']  = get_image_path($row['goods_id'], $row['goods_thumb'], true);
    	$goods_list[$goods_id]['goods_brief']  = $row['goods_brief'];
    	$goods_list[$goods_id]['brand_name']   = $row['brand_name'];
    	
		//如果是商家商品则获取商家关联品牌 by wu start
		if($row['user_id']>0)
		{
			/* 商家品牌筛选 */
			$sql = " SELECT b.brand_name ".
					" FROM " . $GLOBALS['ecs']->table('brand') . " AS b ".
					" LEFT JOIN " . $GLOBALS['ecs']->table('link_brand') . " AS lb ON lb.brand_id = b.brand_id ".
					" LEFT JOIN " . $GLOBALS['ecs']->table('merchants_shop_brand') . " AS msb ON msb.bid = lb.bid AND msb.audit_status = 1 AND msb.is_show = 1 ".					
					" WHERE msb.bid = '$row[brand_id]' ";

			$msb_brand_name = $GLOBALS['db']->getOne($sql);
			$goods_list[$goods_id]['brand_name'] = $msb_brand_name;			
		}
		//如果是商家商品则获取商家关联品牌 by wu end

    	$tmp = $ids;
    	$key = array_search($goods_id, $tmp);
    		
    	if ($key !== null && $key !== false)
    	{
    		unset($tmp[$key]);
    	}
    	
    		
    	$goods_list[$goods_id]['ids'] = !empty($tmp) ? "goods[]=" . implode('&amp;goods[]=', $tmp) : '';
			
			$basic_arr[$goods_id]['properties'][$row['attr_id']]['name']  = $row['attr_name'];
			if (!empty($basic_arr[$goods_id]['properties'][$row['attr_id']]['value']))
			{
				$basic_arr[$goods_id]['properties'][$row['attr_id']]['value'] .= ',' . $row['attr_value'];
			}
			else
			{
				$basic_arr[$goods_id]['properties'][$row['attr_id']]['value'] = $row['attr_value'];
			}
			
			if (!isset($basic_arr[$goods_id]['comment_rank']))
			{
				$basic_arr[$goods_id]['comment_rank']   = isset($cmt[$goods_id]) ? ceil($cmt[$goods_id]['cmt_rank']) : 0;
				$basic_arr[$goods_id]['comment_number'] = isset($cmt[$goods_id]) ? $cmt[$goods_id]['cmt_count']      : 0;
				$basic_arr[$goods_id]['comment_number'] = sprintf($_LANG['comment_num'], $basic_arr[$goods_id]['comment_number']);
			}
    }

    $sql = "SELECT attr_id,attr_name, attr_input_category FROM " . $ecs->table('attribute') . " WHERE cat_id='$type_id' ORDER BY attr_id";

    $attribute = array();

    $query = $db->query($sql);
    while ($rt = $db->fetch_array($query))
    {
    	$attribute[$rt['attr_id']]['attr_id'] = $rt['attr_id'];
    	$attribute[$rt['attr_id']]['attr_name'] = $rt['attr_name'];
    	$attribute_basic[$rt['attr_id']] = $rt['attr_name'];
    }
    

    //高亮显示不同
    if($highlight == 1)
    {
    	foreach($attribute as $key => $val)
    	{
    		$basic_gid_arr = array();
    		$function_gid_arr = array();
    		$hardware_gid_arr = array();
    		$basic_gid = array();
    		$function_gid = array();
    		$hardware_gid = array();
    		foreach($basic_arr as $gid => $v)
    		{
    			$basic_gid_arr[] = str_replace(' ', '', $basic_arr[$gid]['properties'][$key]['value']);
    			$basic_gid[] = $gid;
    		}
    		 
    		$basic_unique = array_unique($basic_gid_arr);
    		 
    		if(!(count($basic_unique) == 1))
    		{
    			$attribute[$key]['attr_highlight'] = 1;
    		}
    	}
    }
    
	//隐藏相同项
    //拿出第一个数组的key
    //查找其他数组中是否存在这个key
    //如果所有的数组都存在这个key则隐藏，反之则跳过
    if($compare == 1)
    {
	    foreach($attribute as $key => $val)
	    {
		    $basic_gid_arr = array();
		    $function_gid_arr = array();
		    $hardware_gid_arr = array();
		    $basic_gid = array();
		    $function_gid = array();
		    $hardware_gid = array();
	    	foreach($basic_arr as $gid => $v)
	    	{
	    		$basic_gid_arr[] = str_replace(' ', '', $basic_arr[$gid]['properties'][$key]['value']);
	    		$basic_gid[] = $gid;
	    	}
	    	
	    	$basic_unique = array_unique($basic_gid_arr);
	    	
	    	if(count($basic_unique) == 1)
	    	{
	    		foreach($basic_gid as $b_val)
	    		{
	    			unset($basic_arr[$b_val]['properties'][$key]);
	    		}
				unset($attribute[$key]);
	    	}
	    }
    }

	//@author guan 暂无对比项 start
	$len = 4-count($goods_list);
	$goods_count = array();
	for($c=1; $c<=$len; $c++)
	{
		$goods_count[] = $c;
	}
    $smarty->assign('goods_count', $goods_count);
	//@author guan 暂无对比项 end
    
    $smarty->assign('attribute', $attribute);
    $smarty->assign('goods_list', $goods_list);
    $smarty->assign('basic_arr', $basic_arr);
    $smarty->assign('is_compare', $compare);
    $smarty->assign('is_highlight', $highlight);
    $smarty->assign('ids', $param_goods_id);
}
else
{
    show_message($_LANG['compare_no_goods']);
    exit;
}

assign_template();
$position = assign_ur_here(0, $_LANG['goods_compare']);
$smarty->assign('page_title', $position['title']);    // 页面标题
$smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
$smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品

$categories_pro = get_category_tree_leve_one();
$smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
$smarty->assign('helps',      get_shop_help());       // 网店帮助

assign_dynamic('compare');

$smarty->display('category_compare.dwt');

?>