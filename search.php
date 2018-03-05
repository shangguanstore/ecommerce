<?php

/**
 * ECSHOP 搜索程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: search.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

if (!function_exists("htmlspecialchars_decode"))
{
    function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT)
    {
        return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}

if (empty($_GET['encode']))
{
    $string = array_merge($_GET, $_POST);
    if (get_magic_quotes_gpc())
    {
        require(dirname(__FILE__) . '/includes/lib_base.php');
        //require(dirname(__FILE__) . '/includes/lib_common.php');

        $string = stripslashes_deep($string);
    }
    $string['search_encode_time'] = time();
    $string = str_replace('+', '%2b', base64_encode(serialize($string)));

    header("Location:search.php?encode=$string\n");

    exit;
}
else
{
    $string = base64_decode(trim($_GET['encode']));
	
    if($string !== false)
    {
        $string = unserialize($string);

        if($string !== false)
        {
            /* 用户在重定向的情况下当作一次访问 */
            if (!empty($string['search_encode_time']))
            {
                if (time() > $string['search_encode_time'] + 2)
                {
                    define('INGORE_VISIT_STATS', true);
                }
            }
            else
            {
                define('INGORE_VISIT_STATS', true);
            }

			/*  @author-bylu 优惠券列表入口 start  */
			if(@$string['keywords']=='优惠券')
				header("location:http://".str_replace(strrchr($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'],'/'),'',$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'/coupons.php?act=coupons_index'));
			/*  @author-bylu  end  */

        }
        else
        {
            $string = array();
        }
    }
    else
    {
        $string = array();
    }
}

require(dirname(__FILE__) . '/includes/init.php');

//ecmoban模板堂 --zhuo start
require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

$_REQUEST = array_merge($_REQUEST, addslashes_deep($string));

$_REQUEST['act'] = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
$search_type = !empty($_REQUEST['store_search_cmt']) ? intval($_REQUEST['store_search_cmt']) : 0; //搜索类型
get_request_filter();

//调位置
$_REQUEST['keywords'] = strip_tags($_REQUEST['keywords']); //去除html、php代码，主要防止js注入 by wu
$_REQUEST['keywords']   = !empty($_REQUEST['keywords'])   ? addslashes_deep(trim($_REQUEST['keywords'])) : '';
$_REQUEST['brand']      = !empty($_REQUEST['brand'])      ? intval($_REQUEST['brand'])      : 0;
$_REQUEST['category']   = !empty($_REQUEST['category'])   ? intval($_REQUEST['category'])   : 0;
$_REQUEST['price_min']  = !empty($_REQUEST['price_min'])  ? intval($_REQUEST['price_min'])  : 0;
$_REQUEST['price_max']  = !empty($_REQUEST['price_max'])  ? intval($_REQUEST['price_max'])  : 0;
$_REQUEST['goods_type'] = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
$_REQUEST['sc_ds']      = !empty($_REQUEST['sc_ds']) ? intval($_REQUEST['sc_ds']) : 0;
$_REQUEST['outstock']   = !empty($_REQUEST['outstock']) ? 1 : 0;

$smarty->assign('search_type', $search_type);
$smarty->assign('search_keywords',   stripslashes(htmlspecialchars_decode($_REQUEST['keywords'])));

/* 排序、显示方式以及类型 */
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';

$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_SESSION['display_search']) ? $_SESSION['display_search'] : 'list');

$_SESSION['display_search'] = $display;

if($search_type == 1){
	if($display == 'list'){ //店铺列表
		
		$default_sort_order_type = "shop_id";
		$sort = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('shop_id', 'goods_number', 'sales_volume'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
	}elseif($display == 'grid' || $display == 'text'){ //大图商品列表
		$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
		$sort = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_volume'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
	}
}else{
	
	//ecmoban模板堂 --zhuo start 仓库//by wang
	$smarty->assign('province_row',  get_region_name($province_id));  
	$smarty->assign('city_row',  get_region_name($city_id));  
	$smarty->assign('district_row',  get_region_name($district_id));
	$province_list = get_warehouse_province();
	
	$smarty->assign('province_list',                $province_list); //省、直辖市
	
	$city_list = get_region_city_county($province_id);
	$smarty->assign('city_list',                $city_list); //省下级市
	
	$district_list = get_region_city_county($city_id); 
	$smarty->assign('district_list',                $district_list);//市下级县
	
	$smarty->assign('open_area_goods',$GLOBALS['_CFG']['open_area_goods']);
	
	$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
	$sort = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_volume','comments_number'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
	$is_ship=isset($_REQUEST['is_ship'])&&!empty($_REQUEST['is_ship'])?addslashes_deep(trim($_REQUEST['is_ship'])):'';
        
	$is_self=isset($_REQUEST['is_self'])&&!empty($_REQUEST['is_self'])?  intval($_REQUEST['is_self']):'';
}

$page       = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size       = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;


//瀑布流 by wu start
$smarty->assign('category_load_type', $_CFG['category_load_type']);
$smarty->assign('query_string', $_SERVER['QUERY_STRING']);	
if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
{
	$goods_num = empty($_REQUEST['goods_num'])? 0:intval($_REQUEST['goods_num']);
	$best_num = empty($_REQUEST['best_num'])? 0:intval($_REQUEST['best_num']);
        
        $goods_floor = floor($goods_num/4*5/4 - $best_num);
        
        if($goods_floor < 0){
            $best_size = $_REQUEST['best_num'];
        }else{
            $best_size = $goods_floor + 2;
        }
}
else
{
	$best_num = 0;
	$best_size = 6;
}
//瀑布流 by wu end

/*------------------------------------------------------ */
//-- 高级搜索
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'advanced_search')
{
    $goods_type = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
    $attributes = get_seachable_attributes($goods_type);
    $smarty->assign('goods_type_selected', $goods_type);
    $smarty->assign('goods_type_list',     $attributes['cate']);
    $smarty->assign('goods_attributes',    $attributes['attr']);

    assign_template();
    assign_dynamic('search');
    $position = assign_ur_here(0, $_LANG['advanced_search']);
    $smarty->assign('page_title', $position['title']);    // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置

    $categories_pro = get_category_tree_leve_one();
    $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
        
    $smarty->assign('helps',      get_shop_help());       // 网店帮助
    //$smarty->assign('top_goods',  get_top10());           // 销售排行
    $smarty->assign('promotion_info', get_promotion_info());
    //$smarty->assign('cat_list',   cat_list(0, 0, true, 2, false));
    //$smarty->assign('brand_list', get_brand_list());
    $smarty->assign('action',     'form');
    $smarty->assign('use_storage', $_CFG['use_storage']);

    if($search_type == 0){
            $smarty->assign('best_goods',get_recommend_goods('best', '', $region_id, $area_info['region_id'], $goods['user_id'], 1));        
            $smarty->display('search.dwt');
    }elseif($search_type == 1){
            $smarty->display('merchants_shop_list.dwt');
    }

    exit;
}

/*------------------------------------------------------ */
//-- 搜索结果
/*------------------------------------------------------ */
else
{

        if($search_type == 0){ //搜索商品
			
            $ur_here = "搜索商品";
        }elseif($search_type == 1){ //店铺搜索
            $ur_here = "搜索店铺";
        }
        
	assign_template();
	assign_dynamic('search');
	$position = assign_ur_here(0, $ur_here . ($_REQUEST['keywords'] ? '_' . $_REQUEST['keywords'] : ''));
	$smarty->assign('page_title', $position['title']);    // 页面标题
	$smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        
        $categories_pro = get_category_tree_leve_one();
        $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
        
	$smarty->assign('intromode',      $intromode);
	$smarty->assign('helps',       get_shop_help());      // 网店帮助
	//$smarty->assign('top_goods',  get_top10());           // 销售排行
	$smarty->assign('promotion_info', get_promotion_info());
	
	$smarty->assign('region_id', $region_id);
	$smarty->assign('area_id', $area_id);
		
	if($search_type == 0){ //搜索商品
		$action = '';
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'form')
		{
			/* 要显示高级搜索栏 */
			$adv_value['keywords']  = htmlspecialchars(stripcslashes($_REQUEST['keywords']));
			$adv_value['brand']     = $_REQUEST['brand'];
			$adv_value['price_min'] = $_REQUEST['price_min'];
			$adv_value['price_max'] = $_REQUEST['price_max'];
			$adv_value['category']  = $_REQUEST['category'];
	
			$attributes = get_seachable_attributes($_REQUEST['goods_type']);
	
			/* 将提交数据重新赋值 */
			foreach ($attributes['attr'] AS $key => $val)
			{
				if (!empty($_REQUEST['attr'][$val['id']]))
				{
					if ($val['type'] == 2)
					{
						$attributes['attr'][$key]['value']['from'] = !empty($_REQUEST['attr'][$val['id']]['from']) ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]['from']))) : '';
						$attributes['attr'][$key]['value']['to']   = !empty($_REQUEST['attr'][$val['id']]['to'])   ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]['to'])))   : '';
					}
					else
					{
						$attributes['attr'][$key]['value'] = !empty($_REQUEST['attr'][$val['id']]) ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]))) : '';
					}
				}
			}
			if ($_REQUEST['sc_ds'])
			{
				$smarty->assign('scck',            'checked');
			}
			$smarty->assign('adv_val',             $adv_value);
			$smarty->assign('goods_type_list',     $attributes['cate']);
			$smarty->assign('goods_attributes',    $attributes['attr']);
			$smarty->assign('goods_type_selected', $_REQUEST['goods_type']);
			//$smarty->assign('cat_list',            cat_list(0, $adv_value['category'], true, 2, false));
			//$smarty->assign('brand_list',          get_brand_list());
			$smarty->assign('action',              'form');
			$smarty->assign('use_storage',          $_CFG['use_storage']);
	
			$action = 'form';
		}
	
		/* 初始化搜索条件 */
		$keywords  = '';
		$tag_where = '';
		if (!empty($_REQUEST['keywords']))
		{
			$arr = array();
			//@author guan start
			$insert_keyword = trim($_REQUEST['keywords']);
			
			//用法：
			$pin = new pin();
			$pinyin = $pin->Pinyin($insert_keyword,'UTF8');
			$addtime = local_date('Y-m-d', gmtime());
			$sql = "INSERT INTO " . $ecs->table('search_keyword') . "(keyword, pinyin, is_on, count, addtime, pinyin_keyword)VALUES('$insert_keyword', '', '0', '1', '$addtime', '$pinyin')";
			$db->query($sql);
	
			$scws_res = scws($_REQUEST['keywords']);//这里可以把关键词分词：诺基亚，耳机
			$arr        = explode(',', $scws_res);
			$arr_keyword = $arr;
			$operator   = " AND ";
	
			if(empty($arr[0]))
			{
				$arr[0] = $insert_keyword;
			}
			//@author guan end
	
			$keywords = 'AND (';
			$goods_ids = array();
			foreach ($arr AS $key => $val)
			{
				if ($key > 0 && $key < count($arr) && count($arr) > 1)
				{
					$keywords .= $operator;
				}
				$val        = mysqli_like_quote(trim($val));
				$sc_dsad    = $_REQUEST['sc_ds'] ? " OR goods_desc LIKE '%$val%'" : '';
				$keywords  .= "(goods_name LIKE '%$val%' OR goods_sn LIKE '%$val%' OR keywords LIKE '%$val%' $sc_dsad)";
	
				$sql = 'SELECT DISTINCT goods_id FROM ' . $ecs->table('tag') . " WHERE tag_words LIKE '%$val%' ";
				$res = $db->query($sql);
				while ($row = $db->FetchRow($res))
				{
					$goods_ids[] = $row['goods_id'];
				}
	
				$db->autoReplace($ecs->table('keywords'), array('date' => local_date('Y-m-d'),
					'searchengine' => 'DSC_B2B2C', 'keyword' => addslashes(str_replace('%', '', $val)), 'count' => 1), array('count' => 1));
			}
			$keywords .= ')';
	
			$goods_ids = array_unique($goods_ids);
			$tag_where = implode(',', $goods_ids);
			if (!empty($tag_where))
			{
				$tag_where = 'OR g.goods_id ' . db_create_in($tag_where);
			}
		}
                
                //ecmoban模板堂 --zhuo start
                $category = !empty($_REQUEST['category']) ? intval($_REQUEST['category']) : 0;
				$categories = '';

				if (!empty($category))
				{
					$children = get_children($category);
					$categories = " AND " . $children;
				}
                //ecmoban模板堂 --zhuo end
                
		$brand      = $_REQUEST['brand']            ? " AND brand_id = '$_REQUEST[brand]'" : '';
		$outstock   = !empty($_REQUEST['outstock']) ? " AND g.goods_number > 0 "           : '';
	
		$price_min  = $_REQUEST['price_min'] != 0                               ? " AND g.shop_price >= '$_REQUEST[price_min]'" : '';
		$price_max  = $_REQUEST['price_max'] != 0 || $_REQUEST['price_min'] < 0 ? " AND g.shop_price <= '$_REQUEST[price_max]'" : '';

		$intromode = '';    //方式，用于决定搜索结果页标题图片
	
		if (!empty($_REQUEST['intro']))
		{
			switch ($_REQUEST['intro'])
			{
				case 'best':
					$intro   = ' AND g.is_best = 1';
					$intromode = 'best';
					$ur_here = $_LANG['best_goods'];
					break;
				case 'new':
					$intro   = ' AND g.is_new = 1';
					$intromode ='new';
					$ur_here = $_LANG['new_goods'];
					break;
				case 'hot':
					$intro   = ' AND g.is_hot = 1';
					$intromode = 'hot';
					$ur_here = $_LANG['hot_goods'];
					break;
				case 'promotion':
					$time    = gmtime();
					$intro   = " AND g.promote_price > 0 AND g.promote_start_date <= '$time' AND g.promote_end_date >= '$time'";
					$intromode = 'promotion';
					$ur_here = $_LANG['promotion_goods'];
					break;
				default:
					$intro   = '';
			}
		}
		else
		{
			$intro = '';
		}
	
		if (empty($ur_here))
		{
			$ur_here = $_LANG['search_goods'];
		}
	
		/*------------------------------------------------------ */
		//-- 属性检索
		/*------------------------------------------------------ */
		$attr_in  = '';
		$attr_num = 0;
		$attr_url = '';
		$attr_arg = array();
	
		if (!empty($_REQUEST['attr']))
		{
			$sql = "SELECT goods_id, COUNT(*) AS num FROM " . $ecs->table("goods_attr") . " WHERE 0 ";
			foreach ($_REQUEST['attr'] AS $key => $val)
			{
				if (is_not_null($val) && is_numeric($key))
				{
					$attr_num++;
					$sql .= " OR (1 ";
	
					if (is_array($val))
					{
						$sql .= " AND attr_id = '$key'";
	
						if (!empty($val['from']))
						{
							$sql .= is_numeric($val['from']) ? " AND attr_value >= " . floatval($val['from'])  : " AND attr_value >= '$val[from]'";
							$attr_arg["attr[$key][from]"] = $val['from'];
							$attr_url .= "&amp;attr[$key][from]=$val[from]";
						}
	
						if (!empty($val['to']))
						{
							$sql .= is_numeric($val['to']) ? " AND attr_value <= " . floatval($val['to']) : " AND attr_value <= '$val[to]'";
							$attr_arg["attr[$key][to]"] = $val['to'];
							$attr_url .= "&amp;attr[$key][to]=$val[to]";
						}
					}
					else
					{
						/* 处理选购中心过来的链接 */
						$sql .= isset($_REQUEST['pickout']) ? " AND attr_id = '$key' AND attr_value = '" . $val . "' " : " AND attr_id = '$key' AND attr_value LIKE '%" . mysqli_like_quote($val) . "%' ";
						$attr_url .= "&amp;attr[$key]=$val";
						$attr_arg["attr[$key]"] = $val;
					}
	
					$sql .= ')';
				}
			}
	
			/* 如果检索条件都是无效的，就不用检索 */
			if ($attr_num > 0)
			{
				$sql .= " GROUP BY goods_id HAVING num = '$attr_num'";
	
				$row = $db->getCol($sql);
				if (count($row))
				{
					$attr_in = " AND " . db_create_in($row, 'g.goods_id');
				}
				else
				{
					$attr_in = " AND 0 ";
				}
			}
		}
		elseif (isset($_REQUEST['pickout']))
		{
			/* 从选购中心进入的链接 */
			$sql = "SELECT DISTINCT(goods_id) FROM " . $ecs->table('goods_attr');
			$col = $db->getCol($sql);
			//如果商店没有设置商品属性,那么此检索条件是无效的
			if (!empty($col))
			{
				$attr_in = " AND " . db_create_in($col, 'g.goods_id');
			}
		}
                
                //ecmoban模板堂 --zhuo start
		$leftJoin = '';	
                
                $leftJoin .= "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b " . "ON b.brand_id = g.brand_id ";
                $leftJoin .= "LEFT JOIN " . $GLOBALS['ecs']->table('link_brand') . " AS lb " . "ON lb.bid = g.brand_id ";
                $leftJoin .= 'LEFT JOIN ' . $GLOBALS['ecs']->table('merchants_shop_brand') . ' AS msb ' . "ON msb.bid = lb.bid ";
                $tag_where .=  "AND (b.audit_status = 1 OR msb.audit_status = 1) ";
		
		$shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
		$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$region_id' ";
		$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";
		
		$area_where = '';
		if($GLOBALS['_CFG']['open_area_goods'] == 1){
			$leftJoin .= " left join " .$GLOBALS['ecs']->table('link_area_goods'). " as lag on g.goods_id = lag.goods_id ";
			$area_where = " and lag.region_id = '$area_id' ";
		}
		
		if($GLOBALS['_CFG']['review_goods'] == 1){
			$tag_where .= ' AND g.review_status > 2 ';
		}
		//ecmoban模板堂 --zhuo end	
		if($is_ship == "is_shipping"){ //ecmoban模板堂 --zhuo
			$tag_where .= " AND g.is_shipping = 1 ";
		}
                
		if($is_self == 1){ //ecmoban模板堂 --zhuo
			$tag_where .= " AND g.user_id = 0 ";
		}

		/*  @author-bylu 优惠券商品条件 start  */
                if ($cou_id = $_REQUEST['cou_id']) {

                    $cou_data = $db->getRow("SELECT * FROM " . $ecs->table('coupons') . " WHERE cou_id = '" . $cou_id . "'");

                    //如果是购物送(任务集市)
                    if ($cou_data['cou_type'] == 2) {
                        if ($cou_data['cou_ok_goods'] != 0) {
                            $cou_goods_where = " AND g.goods_id IN ({$cou_data['cou_ok_goods']}) AND g.user_id ='" . $cou_data['ru_id'] . "' ";
                        } else {
                            $cou_goods_where = " AND g.user_id ='" . $cou_data['ru_id'] . "' ";
                        }
                    } else {
                        //如果指定了使用的商品
                        if ($cou_data['cou_goods'] != 0) {
                            $cou_goods_where = " AND g.goods_id IN ({$cou_data['cou_goods']}) AND g.user_id ='" . $cou_data['ru_id'] . "' ";
                        } else {
                            $cou_goods_where = " AND g.user_id ='" . $cou_data['ru_id'] . "' ";
                        }
                    }
                    $smarty->assign('cou_id', $cou_id); // 优惠券商品搜索标记(用于列表顶部类型检索) bylu
                    $cou_page_data = "&cou_id=" . $cou_id; //优惠券商品搜索标记(用于分页) bylu
                }
                /*  @author-bylu  end  */

                /* 获得符合条件的商品总数 */
		$sql   = "SELECT COUNT(*) FROM " .$ecs->table('goods'). " AS g ".
			$leftJoin . 
			"WHERE g.is_delete = 0 AND g.is_on_sale = 1 " .$area_where. " AND g.is_alone_sale = 1 $attr_in ".
			"AND (( 1 " . $categories . $keywords . $brand . $price_min . $price_max . $intro . $outstock ." ) ".$tag_where." ) $cou_goods_where ";//优惠券商品条件 bylu
			
		$count = $db->getOne($sql);
                
		$max_page = ($count> 0) ? ceil($count / $size) : 1;
		if ($page > $max_page)
		{
			$page = $max_page;
		}
		
		$sel_msb = "(g.brand_id IN(SELECT msb.bid FROM " .$GLOBALS['ecs']->table('brand') ." AS b, ". $GLOBALS['ecs']->table('link_brand') ." AS lb, " .$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb". 
                        " WHERE b.is_show = 1 AND b.brand_id = lb.brand_id AND lb.bid = msb.bid AND msb.is_show = 1 AND msb.audit_status = 1) AND g.user_id > 0)";
        $sel_brand = "(g.brand_id IN(SELECT b.brand_id FROM " .$GLOBALS['ecs']->table('brand') ." AS b" . " WHERE b.is_show = 1) AND g.user_id = 0)";
        $tag_where .= "AND ( " .$sel_brand. " OR " .$sel_msb. ")";
	
		/* 查询商品 */
		$sql = "SELECT g.goods_id, g.user_id, g.goods_name, g.market_price, g.is_new, g.comments_number, g.sales_volume, g.is_best, g.is_hot,g.store_new, g.store_best, g.store_hot, " .
				$shop_price .
				"IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) AS org_price, ".
				"IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, ".
				"IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, " . 
				"IF(g.model_price < 1, g.goods_number, IF(g.model_price < 2, wg.region_number, wag.region_number)) AS goods_number, ".
				"g.promote_start_date, g.promote_end_date, g.is_promote, g.goods_thumb, g.goods_img, g.goods_brief, g.goods_type ".
				"FROM " .$ecs->table('goods'). " AS g ".
				$leftJoin.
				"LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
						"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
				"WHERE g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 $attr_in $cou_goods_where ". //优惠券商品条件 bylu
					"AND (( 1 " . $categories . $keywords . $brand . $price_min . $price_max . $intro . $outstock . " ) ".$tag_where." ) " .
					
				"ORDER BY g.$sort $order"; //增加表别名 g. by wanganlin

		//瀑布流 by wu start
		if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
		{
			$start = intval($_REQUEST['goods_num']);
		}
		else
		{
			$start = ($page - 1) * $size;
		}
		$res = $db->SelectLimit($sql, $size, $start);
		//瀑布流 by wu end
				
		//$res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
		$region = array(1, $province_id, $city_id, $district_id);
	
		$arr = array();
		while ($row = $db->FetchRow($res))
		{
			if ($row['promote_price'] > 0)
			{
				$promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
			}
			else
			{
				$promote_price = 0;
			}
			$price_other = array('market_price' => $row['market_price'], 'org_price' => $row['org_price'], 'shop_price' => $row['shop_price'], 'promote_price' => $promote_price);
			$price_info = get_goods_one_attr_price($row['goods_id'], $warehouse_id, $area_id, $price_other);
			$row = !empty($row) ? array_merge($row, $price_info) : $row;
			$promote_price = $row['promote_price'];
	
			/* 处理商品水印图片 */
			$watermark_img = '';
	
			if ($promote_price != 0)
			{
				$watermark_img = "watermark_promote_small";
			}
			elseif ($row['is_new'] != 0)
			{
				$watermark_img = "watermark_new_small";
			}
			elseif ($row['is_best'] != 0)
			{
				$watermark_img = "watermark_best_small";
			}
			elseif ($row['is_hot'] != 0)
			{
				$watermark_img = 'watermark_hot_small';
			}
	
			if ($watermark_img != '')
			{
				$arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
			}

			$arr[$row['goods_id']]['goods_id']      = $row['goods_id'];

			if ($row['model_attr'] == 1)
			{
				$table_products = 'products_warehouse';
				$type_files = " and warehouse_id = '$warehouse_id'";
			}
			else if ($row['model_attr'] == 2)
			{
				$table_products = 'products_area';
				$type_files = " and area_id = '$area_id'";
			}
			else
			{
				$table_products = 'products';
				$type_files = "";
			}

			$sql = "SELECT * FROM " . $GLOBALS['ecs']->table($table_products) . " WHERE goods_id = '" . $row['goods_id'] . "'" . $type_files . " LIMIT 0, 1";
			$arr[$row['goods_id']]['prod'] = $GLOBALS['db']->getRow($sql);

			if (empty($prod))
			{
				$arr[$row['goods_id']]['prod'] = 1;
			}
			else
			{
				$arr[$row['goods_id']]['prod'] = 0;
			}

			if($display == 'grid')
			{
				//$arr[$row['goods_id']]['goods_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
				
				//@Author guan 关键字高亮显示 start
				$goods_name_keyword = sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']);
				$goods_name_keyword = '<text>' . $goods_name . '</text>';
				foreach($arr_keyword as $key => $val_keyword)
				{
					$goods_name_keyword = preg_replace("/(>.*)($val_keyword)(.*<)/Ui","$1<font style='color:#ec5151;'>$val_keyword</font>\$3", $goods_name);
				}
				//exit;
				$arr[$row['goods_id']]['goods_name_keyword']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?  $goods_name_keyword  : $goods_name_keyword;
				//模版页面样式错误，为模版页面的的goods_name改为goods_name2。以防止样式错误。
				$arr[$row['goods_id']]['goods_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ?  $row['goods_name']  : $row['goods_name'];
				//@Author guan 关键字高亮显示 end
			}
			else
			{
				//$arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
				
				//@Author guan 关键字高亮显示 start
				$goods_name_keyword = '<text>' . $row['goods_name'] . '</text>';
				foreach($arr_keyword as $key => $val_keyword)
				{
					//$goods_name = preg_replace("/($val_keyword)/i","<font style='color:red'>$1</font>", $goods_name);
					$goods_name_keyword = preg_replace("/(>.*)($val_keyword)(.*<)/Ui","$1<font style='color:#ec5151;'>$val_keyword</font>\$3", $goods_name_keyword);
				}
				$arr[$row['goods_id']]['goods_name_keyword'] = $goods_name_keyword;
				$arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
				//@Author guan 关键字高亮显示 end
			}
			
			$arr[$row['goods_id']]['goods_number'] = $row['goods_number'];
			/* 折扣节省计算 by ecmoban start */
			if($row['market_price'] > 0)
			{
					$discount_arr = get_discount($row['goods_id']); //函数get_discount参数goods_id
			}
			$arr[$row['goods_id']]['zhekou']  = $discount_arr['discount'];  //zhekou
			$arr[$row['goods_id']]['jiesheng']  = $discount_arr['jiesheng']; //jiesheng
			/* 折扣节省计算 by ecmoban end */
			$arr[$row['goods_id']]['type']          = $row['goods_type'];
			$arr[$row['goods_id']]['is_promote']          = $row['is_promote'];
			$arr[$row['goods_id']]['sales_volume']          = $row['sales_volume'];
			$arr[$row['goods_id']]['market_price']  = price_format($row['market_price']);
			$arr[$row['goods_id']]['shop_price']    = price_format($row['shop_price']);
			$arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
			$arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
			$arr[$row['goods_id']]['goods_thumb']   = get_image_path($row['goods_id'], $row['goods_thumb'], true);
			$arr[$row['goods_id']]['goods_img']     = get_image_path($row['goods_id'], $row['goods_img']);
			$arr[$row['goods_id']]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
			$arr[$row['goods_id']]['count'] = selled_count($row['goods_id']);
			
			$mc_all = ments_count_all($row['goods_id']);       //总条数
			$mc_one = ments_count_rank_num($row['goods_id'],1);		//一颗星
			$mc_two = ments_count_rank_num($row['goods_id'],2);	    //两颗星	
			$mc_three = ments_count_rank_num($row['goods_id'],3);   	//三颗星
			$mc_four = ments_count_rank_num($row['goods_id'],4);		//四颗星
			$mc_five = ments_count_rank_num($row['goods_id'],5);		//五颗星
			$arr[$row['goods_id']]['zconments'] = get_conments_stars($mc_all,$mc_one,$mc_two,$mc_three,$mc_four,$mc_five);
			
			$arr[$row['goods_id']]['review_count']      = $arr[$row['goods_id']]['zconments']['allmen'];
			$arr[$row['goods_id']]['rz_shopName'] = get_shop_name($row['user_id'], 1); //店铺名称	

			$arr[$row['goods_id']]['pictures'] = get_goods_gallery($row['goods_id'], 6);
			$shop_information = get_shop_name($row['user_id']);
			$arr[$row['goods_id']]['is_IM'] = $shop_information['is_IM'];

			if ($row['user_id'] == 0)
			{
				if ($GLOBALS['db']->getOne("SELECT kf_im_switch FROM " . $GLOBALS['ecs']->table('seller_shopinfo') . "WHERE ru_id = 0", true))
				{
					$arr[$row['goods_id']]['is_dsc'] = true;
				}
				else
				{
					$arr[$row['goods_id']]['is_dsc'] = false;
				}
			}
			else
			{
				$arr[$row['goods_id']]['is_dsc'] = false;
			}
                        
            $build_uri = array(
                        'urid' => $row['user_id'],
                        'append' => $arr[$row['goods_id']]['rz_shopName']
                        );

            $domain_url = get_seller_domain_url($row['user_id'], $build_uri);
            $arr[$row['goods_id']]['store_url'] = $domain_url['domain_name'];
        
            $arr[$row['goods_id']]['is_new'] =  $row['is_new'];
            $arr[$row['goods_id']]['is_best'] =  $row['is_best'];
            $arr[$row['goods_id']]['is_hot'] =  $row['is_hot'];
			//ecmoban模板堂 --zhuo start
			$sql="select * from ".$GLOBALS['ecs']->table('seller_shopinfo')." where ru_id='" .$row['user_id']. "'";
			$basic_info = $GLOBALS['db']->getRow($sql);	
			$arr[$row['goods_id']]['kf_type'] = $basic_info['kf_type'];
                        
                        /*处理客服QQ数组 by kong*/
                        if($basic_info['kf_qq']){
                            $kf_qq=array_filter(preg_split('/\s+/', $basic_info['kf_qq']));
                            $kf_qq=explode("|",$kf_qq[0]);
                            if(!empty($kf_qq[1])){
                                $arr[$row['goods_id']]['kf_qq'] = $kf_qq[1];
                            }else{
                                $arr[$row['goods_id']]['kf_qq'] = "";
                            }
                            
                        }else{
                            $arr[$row['goods_id']]['kf_qq'] = "";
                        }
                        /*处理客服旺旺数组 by kong*/
                        if($basic_info['kf_ww']){
                            $kf_ww=array_filter(preg_split('/\s+/', $basic_info['kf_ww']));
                            $kf_ww=explode("|",$kf_ww[0]);
                            if(!empty($kf_ww[1])){
                                $arr[$row['goods_id']]['kf_ww'] = $kf_ww[1];
                            }else{
                                $arr[$row['goods_id']]['kf_ww'] ="";
                            }
                            
                        }else{
                            $arr[$row['goods_id']]['kf_ww'] ="";
                        }
                        
                        
			//ecmoban模板堂 --zhuo end
                        
                        $arr[$row['goods_id']]['is_collect'] = get_collect_user_goods($row['goods_id']);
		}
	
		$shippingFee = goodsshippingfee($row['goods_id'], $region_id, $region);
		$arr[$row['goods_id']]['shipping_fee_formated'] = $shippingFee['shipping_fee_formated'];

		if($display == 'grid')
		{
			if(count($arr) % 2 != 0)
			{
				$arr[] = array();
			}
		}		
		
		$smarty->assign('goods_list', $arr);
		$smarty->assign('category',   $category);
		$smarty->assign('keywords',   htmlspecialchars(stripslashes($_REQUEST['keywords'])));
		$smarty->assign('brand',      $_REQUEST['brand']);
		$smarty->assign('price_min',  $price_min);
		$smarty->assign('price_max',  $price_max);
		$smarty->assign('outstock',  $_REQUEST['outstock']);
		
        //瀑布流 by wu start
        if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
        {
			$smarty->assign('model', intval($_REQUEST['model']));
            $result = array('error' => 0,'message' => '','cat_goods'=>'','best_goods'=>'');
            $result['cat_goods'] = html_entity_decode($smarty->fetch('library/more_goods.lbi')); //分类商品
            $result['best_goods'] = html_entity_decode($smarty->fetch('library/more_goods_best.lbi')); //推广商品
            die(json_encode($result));
        }
        //瀑布流 by wu end
	
		/* 分页 */
		$url_format = "search.php?category=$category&amp;keywords=" . urlencode(stripslashes($_REQUEST['keywords'])) . "&amp;brand=" . $_REQUEST['brand']."&amp;action=".$action."&amp;goods_type=" . $_REQUEST['goods_type'] . "&amp;sc_ds=" . $_REQUEST['sc_ds'] . $cou_page_data; //搜索优惠券商品的标记 bylu;
		if (!empty($intromode))
		{
			$url_format .= "&amp;intro=" . $intromode;
		}
		if (isset($_REQUEST['pickout']))
		{
			$url_format .= '&amp;pickout=1';
		}
		$url_format .= "&amp;price_min=" . $_REQUEST['price_min'] ."&amp;price_max=" . $_REQUEST['price_max'] . "&amp;sort=$sort";

		$url_format .= "$attr_url&amp;order=$order&amp;page=";

		$pager['search'] = array(
			'keywords'   => stripslashes(trim($_REQUEST['keywords'])),
			'category'   => $category,
			'store_search_cmt'   => intval($_REQUEST['store_search_cmt']),
			'brand'      => $_REQUEST['brand'],
			'sort'       => $sort,
			'order'      => $order,
			'price_min'  => $_REQUEST['price_min'],
			'price_max'  => $_REQUEST['price_max'],
			'action'     => $action,
			'intro'      => empty($intromode) ? '' : trim($intromode),
			'goods_type' => $_REQUEST['goods_type'],
			'sc_ds'      => $_REQUEST['sc_ds'],
			'outstock'   => $_REQUEST['outstock'],
			'is_ship'=>$is_ship,
                    
			'self_support'=>$is_self,
			'is_in_stock'=>$is_in_stock,

            'cou_id'=>$cou_id //优惠券商品分页标记 bylu

		);

		$pager['search'] = array_merge($pager['search'], $attr_arg);

		$pager = get_pager('search.php', $pager['search'], $count, $page, $size);
		$pager['display'] = $display;
	
		$smarty->assign('url_format', $url_format);
        
		//瀑布流 by wu start
		$smarty->assign('pager', $pager);
		//瀑布流 by wu end


//		$smarty->assign('pager', $pager);


		 /**小图 start by wang头部广告**/
		for($i=1;$i<=$_CFG['auction_ad'];$i++){
			$search_left_ad   .= "'search_left_ad".$i.","; //搜索商品页面头部左侧广告
			$search_right_ad   .= "'search_right_ad".$i.","; //搜索商品页面头部右侧广告
			
		}
		
		$smarty->assign('search_left_ad', $search_left_ad);
		$smarty->assign('search_right_ad', $search_right_ad);
		$smarty->assign('best_goods',get_recommend_goods('best', '', $region_id, $area_info['region_id'], $goods['user_id'], 1));
		
                $cur_url = get_return_self_url();
                $smarty->assign('cur_url', $cur_url);
                $smarty->assign('script_name', 'search');
		$smarty->display('search.dwt');
		
	}elseif($search_type == 1){ //搜索店铺

		$keywords = htmlspecialchars(stripcslashes($_REQUEST['keywords']));

		if($display == 'list'){ //店铺列表
		
                        $size = 10;
                        $count = get_store_shop_count($keywords, $sort);
			$store_shop_list = get_store_shop_list(1, $keywords, $count, $size, $page, $sort, $order, $region_id, $area_id);   
                        
			$smarty->assign('store_shop_list', $store_shop_list['shop_list']);
                        $smarty->assign('pager', $store_shop_list['pager']);
			
		}elseif($display == 'grid' || $display == 'text'){ //大图商品列表
		
			if($display == 'text'){
                            $size = 21;
                        }else{
                            $size = 20;
                        }
                        
			$shop_goods_list = get_store_shop_goods_list($keywords, $size, $page, $sort, $order, $region_id, $area_id);

			$smarty->assign('shop_goods_list', $shop_goods_list);
			
			$count = get_store_shop_goods_count($keywords, $sort);
		}
		
                if($display == 'grid' || $display == 'text'){
                    /* 分页 */
                    $url_format = "search.php?category=0&amp;keywords=" . urlencode(stripslashes($_REQUEST['keywords']));

                    $url_format .= "&amp;sort=$sort";

                    $url_format .= "&amp;order=$order&amp;page=";

                    $pager['search'] = array(
                            'keywords'   => stripslashes(trim($_REQUEST['keywords'])),
                            'category'   => 0,
                            'store_search_cmt'   => intval($_REQUEST['store_search_cmt']),
                            'sort'       => $sort,
                            'order'      => $order,
                    );

                    $pager = get_pager('search.php', $pager['search'], $count, $page, $size);
                    $pager['display'] = $display;

                    $smarty->assign('url_format', $url_format);
                    $smarty->assign('count', $count);
                    $smarty->assign('page', $page);
                    $smarty->assign('pager', $pager);
                }
                
                $smarty->assign('size', $size);
                $smarty->assign('count', $count);
                $smarty->assign('display', $display);
                $smarty->assign('sort', $sort);
                
		
		$store_best_list = get_shop_goods_count_list(0, $region_id, $area_id, 1, 'store_best', 1);
		$smarty->assign('store_best_list', $store_best_list);
                
                $cur_url = get_return_self_url();
                $smarty->assign('cur_url', $cur_url);
                $smarty->assign('script_name', 'merchants_shop');
		$smarty->display('merchants_shop_list.dwt');
	}
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */
/**
 *
 *
 * @access public
 * @param
 *
 * @return void
 */
function is_not_null($value)
{
    if (is_array($value))
    {
        return (!empty($value['from'])) || (!empty($value['to']));
    }
    else
    {
        return !empty($value);
    }
}

/**
 * 获得可以检索的属性
 *
 * @access  public
 * @params  integer $cat_id
 * @return  void
 */
function get_seachable_attributes($cat_id = 0)
{
    $attributes = array(
        'cate' => array(),
        'attr' => array()
    );

    /* 获得可用的商品类型 */
    $sql = "SELECT t.cat_id, cat_name FROM " .$GLOBALS['ecs']->table('goods_type'). " AS t, ".
           $GLOBALS['ecs']->table('attribute') ." AS a".
           " WHERE t.cat_id = a.cat_id AND t.enabled = 1 AND a.attr_index > 0 ";
    $cat = $GLOBALS['db']->getAll($sql);

    /* 获取可以检索的属性 */
    if (!empty($cat))
    {
        foreach ($cat AS $val)
        {
            $attributes['cate'][$val['cat_id']] = $val['cat_name'];
        }
        $where = $cat_id > 0 ? ' AND a.cat_id = ' . $cat_id : " AND a.cat_id = " . $cat[0]['cat_id'];

        $sql = 'SELECT attr_id, attr_name, attr_input_type, attr_type, attr_values, attr_index, sort_order ' .
               ' FROM ' . $GLOBALS['ecs']->table('attribute') . ' AS a ' .
               ' WHERE a.attr_index > 0 ' .$where.
               ' ORDER BY cat_id, sort_order ASC';
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->FetchRow($res))
        {
            if ($row['attr_index'] == 1 && $row['attr_input_type'] == 1)
            {
                $row['attr_values'] = str_replace("\r", '', $row['attr_values']);
                $options = explode("\n", $row['attr_values']);

                $attr_value = array();
                foreach ($options AS $opt)
                {
                    $attr_value[$opt] = $opt;
                }
                $attributes['attr'][] = array(
                    'id'      => $row['attr_id'],
                    'attr'    => $row['attr_name'],
                    'options' => $attr_value,
                    'type'    => 3
                );
            }
            else
            {
                $attributes['attr'][] = array(
                    'id'   => $row['attr_id'],
                    'attr' => $row['attr_name'],
                    'type' => $row['attr_index']
                );
            }
        }
    }

    return $attributes;
}
?>