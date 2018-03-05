<?php

/**
 * ECSHOP 专题前台
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * @author:     webboy <laupeng@163.com>
 * @version:    v2.1
 * ---------------------------------------------
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo
$topic_id  = empty($_REQUEST['topic_id']) ? 0 : intval($_REQUEST['topic_id']);

$sql = "SELECT template FROM " . $ecs->table('topic') .
        "WHERE topic_id = '$topic_id' and  " . gmtime() . " >= start_time and " . gmtime() . "<= end_time";

$topic = $db->getRow($sql);

if(empty($topic))
{
    /* 如果没有找到任何记录则跳回到首页 */
    ecs_header("Location: ./\n");
    exit;
}

$templates = empty($topic['template']) ? 'topic.dwt' : $topic['template'];

$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang'] . '-' . $topic_id));

if (!$smarty->is_cached($templates, $cache_id))
{
    $sql = "SELECT * FROM " . $ecs->table('topic') . " WHERE topic_id = '$topic_id'";

    $topic = $db->getRow($sql);
    $topic['data'] = addcslashes($topic['data'], "'");
    $tmp = @unserialize($topic["data"]);
    $arr = (array)$tmp;

    $goods_id = array();

    foreach ($arr AS $key=>$value)
    {
        foreach($value AS $k => $val)
        {
            $opt = explode('|', $val);
            $arr[$key][$k] = $opt[1];
            $goods_id[] = $opt[1];
        }
    }

	//ecmoban模板堂 --zhuo start
	require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo
	$area_info = get_area_info($province_id);
	$area_id = $area_info['region_id'];

	$where = "regionId = '$province_id'";
	$date = array('parent_id');
	$region_id = get_table_date('region_warehouse', $where, $date, 2);
	//ecmoban模板堂 --zhuo end
	
	//关联仓库地区价格 by wu	
	$leftJoin = '';	
	$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$region_id' ";
	$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";
	
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot, g.shop_price AS org_price, ' .
				"IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, ".
				"IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, " . 
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
				$leftJoin .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE " . db_create_in($goods_id, 'g.goods_id');

    $res = $GLOBALS['db']->query($sql);

    $sort_goods_arr = array();

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $row['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        }
        else
        {
            $row['promote_price'] = '';
        }

        if ($row['shop_price'] > 0)
        {
            $row['shop_price'] =  price_format($row['shop_price']);
        }
        else
        {
            $row['shop_price'] = price_format(0);
        }

        $row['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $row['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $row['short_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $row['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $row['short_style_name'] = add_style($row['short_name'], $row['goods_name_style']);

        foreach ($arr AS $key => $value)
        {
            foreach ($value AS $val)
            {
                if ($val == $row['goods_id'])
                {
                    $key = $key == 'default' ? $_LANG['all_goods'] : $key;
                    $sort_goods_arr[$key][] = $row;
                }
            }
        }
    }

    /* 模板赋值 */
    assign_template();
	$position = assign_ur_here(0, $topic['title']);
    $smarty->assign('page_title',       $position['title']);       // 页面标题
    //$smarty->assign('ur_here',          $position['ur_here'] . '> ' . $topic['title']);     // 当前位置 remove by wu
	$smarty->assign('ur_here',          $position['ur_here']);     // 当前位置
    $smarty->assign('helps',            get_shop_help());              // 网店帮助
    
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);
    $smarty->assign('sort_goods_arr',   $sort_goods_arr);          // 商品列表
    $smarty->assign('topic',            $topic);                   // 专题信息
    $smarty->assign('keywords',         $topic['keywords']);       // 专题信息
    $smarty->assign('description',      $topic['description']);    // 专题信息
    $smarty->assign('title_pic',        $topic['title_pic']);      // 分类标题图片地址
    $smarty->assign('base_style',       '#' . $topic['base_style']);     // 基本风格样式颜色
    
    $categories_pro = get_category_tree_leve_one();
    $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版

    $template_file = empty($topic['template']) ? 'topic.dwt' : $topic['template'];
}
/* 显示模板 */
$smarty->display($templates, $cache_id);

?>