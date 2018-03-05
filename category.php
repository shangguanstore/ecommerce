<?php

/**
 * ECSHOP 商品分类
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

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo
require(ROOT_PATH . '/includes/lib_order.php');  //ecmoban模板堂 --zhuo
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
$uachar = '/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i';

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
    $cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
    $cat_id = intval($_REQUEST['category']);
}
else
{
    if ((($ua == '') || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']), 'wap')) {
        $Loaction = 'mobile/index.php?r=category';

        if (!empty($Loaction))
        {
            /* 如果分类ID为0，则返回首页 */
            ecs_header("Location: $Location\n");

            exit;
        }
    }
    else
    {
    
        /* 如果分类ID为0，则返回首页 */
        ecs_header("Location: ./\n");

        exit;
    }
}

if ((($ua == '') || preg_match($uachar, $ua)) && !strpos(strtolower($_SERVER['REQUEST_URI']), 'wap')) {
    $Loaction = 'mobile/index.php?r=category/index/products&id=' . $cat_id;

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");
        exit;
    }
}

/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;

$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
$brand = $ecs->get_explode_filter($_REQUEST['brand']); //过滤品牌参数

$ship = isset($_REQUEST['ship'])  && !empty($_REQUEST['ship']) ? intval($_REQUEST['ship']) : 0; //by wang
$self = isset($_REQUEST['self']) && !empty($_REQUEST['self']) ? intval($_REQUEST['self']): 0;

$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
$filter_attr_str = isset($_REQUEST['filter_attr']) ? addslashes(trim($_REQUEST['filter_attr'])) : '0';

$filter_attr_str = trim(urldecode($filter_attr_str));
//$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr_str = preg_match('/^[\d,\.]+$/',$filter_attr_str) ? $filter_attr_str : ''; //by zhang

$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_volume', 'comments_number'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */
$cat_keys = get_array_keys_cat($cat_id);
$smarty->assign('category',         $cat_id);
$smarty->assign('rewrite',         $_CFG['rewrite']);
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

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr_str.'-'.$ship.'-'.$self));

//判断是否顶级分类调用不同模板   by zhouhuan
$sql = 'SELECT parent_id, is_top_style, top_style_tpl, cat_name,cat_icon, cat_id FROM '.$ecs->table("category")." WHERE cat_id = '$cat_id' LIMIT 1"; //顶级分类页模板 by wu
$cat_row = $db->getRow($sql);

if($cat_row['parent_id'] == 0 && $cat_row['is_top_style'] == 1)
{
    for($i=1;$i<=$_CFG['auction_ad'];$i++){
        $category_top_left   .= "'category_top_left".$i.","; //轮播图
        $category_top_banner   .= "'category_top_banner".$i.","; //轮播图
		$top_style_elec_brand .= "'top_style_elec_brand".$i.","; //顶级分类品牌广告(家电模板) by wu
		$top_style_elec_banner .= "'top_style_elec_banner".$i.","; //顶级分类轮播广告(家电模板) by wu
		$top_style_food_banner .= "'top_style_food_banner".$i.","; //顶级分类轮播广告(食品模板) by wu
		$top_style_food_hot .= "'top_style_food_hot".$i.","; //顶级分类热门广告(食品模板) by wu
    }
    $smarty->assign('category_top_left',$category_top_left);
    $smarty->assign('category_top_banner',$category_top_banner);
	$smarty->assign('top_style_elec_brand',$top_style_elec_brand);
	$smarty->assign('top_style_elec_banner',$top_style_elec_banner);
	$smarty->assign('top_style_food_banner',$top_style_food_banner);
	$smarty->assign('top_style_food_hot',$top_style_food_hot);
	//顶级分类页顶部横幅广告(家电模板) by wu
	$top_style_elec_foot = "'top_style_elec_foot,";
	$smarty->assign('top_style_elec_foot',$top_style_elec_foot);
    
    $dwt_name = 'category_top';
}
else
{
    for ($i = 1; $i <= $_CFG['auction_ad']; $i++)
    {
        $category_top_ad .= "'category_top_ad" . $i . ",";
    }

    $smarty->assign('category_top_ad', $category_top_ad);
    $dwt_name = 'category';
}

//ecmoban模板堂 --zhuo start
$parent_id =$cat_row['parent_id'];

$smarty->assign('cate_info',$cat_row);//by wang
$smarty->assign('parent_id',         $parent_id);

$sql = "select parent_id from " .$ecs->table('category'). " where cat_id = '$parent_id' LIMIT 1";
$parentCat = $db->getOne($sql);
$smarty->assign('parentCat',$parentCat);

$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);

if(isset($_COOKIE['region_id']) && !empty($_COOKIE['region_id'])){
    $region_id = $_COOKIE['region_id'];
}
//ecmoban模板堂 --zhuo end

//瀑布流加载分类商品 by wu start
$smarty->assign('category_load_type', $_CFG['category_load_type']);
$smarty->assign('query_string', $_SERVER['QUERY_STRING']);	
if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
{
	$goods_num = empty($_REQUEST['goods_num'])? 0:intval($_REQUEST['goods_num']);
	$best_num = empty($_REQUEST['best_num'])? 0:intval($_REQUEST['best_num']);
        
        $goods_floor = floor($goods_num/4*8/5 - $best_num);
        
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
//瀑布流加载分类商品 by wu end

if ((isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')  || !$smarty->is_cached($dwt_name.'.dwt', $cache_id)) //瀑布流加载分类商品 by wu
{
    /* 如果页面没有被缓存则重新获取页面的内容 */
    $children = get_children($cat_id);
    $cat_select = array('cat_name', 'keywords', 'cat_desc', 'style', 'grade', 'filter_attr', 'parent_id');

    $cat = get_cat_info($cat_id, $cat_select);   // 获得分类的相关信息

    if (!empty($cat))
    {
        $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
        $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
        $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
    }
    else
    {
        /* 如果分类不存在则返回首页 */
        ecs_header("Location: ./\n");
        exit;
    }

	if (!empty($brand)) // by zhang
    {
        $sql = "SELECT brand_name FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_id = '$brand' LIMIT 1";

        $brand_name = $db->getOne($sql);
    }
    else
    {
        $brand_name = '';
    }

    /* 获取价格分级 */
    if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
    {
        $cat['grade'] = get_parent_grade($cat_id); //如果当前分类级别为空，取最近的上级分类
    }
	
    //ecmoban模板堂 --zhuo start 
    if(($cat_row['parent_id'] == 0 && $cat_row['is_top_style'] == 1) == false){ //非顶级分类页和设置为顶级分类样式

        $leftJoin = '';	

        $tag_where = '';
        if($GLOBALS['_CFG']['open_area_goods'] == 1){ //关联地区显示商品
                $leftJoin .= " left join " .$GLOBALS['ecs']->table('link_area_goods'). " as lag on g.goods_id = lag.goods_id ";
                $tag_where = " and lag.region_id = '$area_id' ";
        }
        //ecmoban模板堂 --zhuo end	

        if ($cat['grade'] > 1)
        {
            /* 需要价格分级 */

            /*
                算法思路：
                    1、当分级大于1时，进行价格分级
                    2、取出该类下商品价格的最大值、最小值
                    3、根据商品价格的最大值来计算商品价格的分级数量级：
                            价格范围(不含最大值)    分级数量级
                            0-0.1                   0.001
                            0.1-1                   0.01
                            1-10                    0.1
                            10-100                  1
                            100-1000                10
                            1000-10000              100
                    4、计算价格跨度：
                            取整((最大值-最小值) / (价格分级数) / 数量级) * 数量级
                    5、根据价格跨度计算价格范围区间
                    6、查询数据库

                可能存在问题：
                    1、
                    由于价格跨度是由最大值、最小值计算出来的
                    然后再通过价格跨度来确定显示时的价格范围区间
                    所以可能会存在价格分级数量不正确的问题
                    该问题没有证明
                    2、
                    当价格=最大值时，分级会多出来，已被证明存在
            */

            //ecmoban模板堂 --zhuo start		
            $mm_shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr ";
            $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$region_id' ";
            $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";
            //ecmoban模板堂 --zhuo end	

            $sql = "SELECT min(IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price))) AS min, " .
                                            " max(IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price))) as max ".
                   " FROM " . $ecs->table('goods'). " AS g ". 
                               $leftJoin .
                   " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1' . $tag_where;
                   //获得当前分类下商品价格的最大值、最小值

            $row = $db->getRow($sql);

            // 取得价格分级最小单位级数，比如，千元商品最小以100为级数
            $price_grade = 0.0001;
            for($i=-2; $i<= log10($row['max']); $i++)
            {
                $price_grade *= 10;
            }

            //跨度
            $dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
            if($dx == 0)
            {
                $dx = $price_grade;
            }

            for($i = 1; $row['min'] > $dx * $i; $i ++);

            for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
            $row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

            for(; $row['max'] >= $dx * $i; $i ++);
            $row['max'] = $dx * ($i) + $price_grade * ($j - 1);

            $sql = "SELECT (FLOOR((IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
                   " FROM " . $ecs->table('goods') . " AS g ". $leftJoin .
                   " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
                   " GROUP BY sn ";

            $price_grade = $db->getAll($sql);
            
            
            foreach ($price_grade as $key=>$val)
            {
                            if($val['sn'] != ''){
                                    $temp_key = $key;
                                    $price_grade[$temp_key]['goods_num'] = $val['goods_num'];
                                    $price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
                                    $price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
                                    $price_grade[$temp_key]['price_range'] = $price_grade[$temp_key]['start'] . '&nbsp;-&nbsp;' . $price_grade[$temp_key]['end'];
                                    $price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
                                    $price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
                                    $price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

                                    /* 判断价格区间是否被选中 */
                                    if (isset($_REQUEST['price_min']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
                                    {
                                            $price_grade[$temp_key]['selected'] = 1;
                                    }
                                    else
                                    {
                                            $price_grade[$temp_key]['selected'] = 0;
                                    }
                            }
            }

            if($price_min == 0 && $price_max == 0){
                $smarty->assign('price_grade',     $price_grade);
            } //by zhang
        }
 
        $where_having = '';
        $brand_select = '';
        $brand_tag_where = '';
        if($GLOBALS['_CFG']['open_area_goods'] == 1){ //关联地区显示商品
                $brand_select = " , ( SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('link_area_goods'). " as lag WHERE lag.goods_id = g.goods_id AND lag.region_id = '$area_id' LIMIT 1) AS area_goods_num ";
                $where_having = " AND area_goods_num > 0 ";
        }

        if($GLOBALS['_CFG']['review_goods'] == 1){
                $brand_tag_where .= ' AND g.review_status > 2 ';
        }

        /* 平台品牌筛选 */
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(*) AS goods_num " . $brand_select . 
                "FROM " . $GLOBALS['ecs']->table('brand') . "AS b ".
                    " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.brand_id = b.brand_id AND g.user_id = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $brand_tag_where ". 
                    " LEFT JOIN " . $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .	
                " WHERE $children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), $cat_keys))) . " AND b.is_show = 1 " .
                "GROUP BY b.brand_id HAVING goods_num > 0 $where_having ORDER BY b.sort_order, b.brand_id ASC";

        $brands_list = $GLOBALS['db']->getAll($sql);
        
        /* 商家品牌筛选 */
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(*) AS goods_num, g.user_id " . $brand_select . 
                "FROM " . $GLOBALS['ecs']->table('brand') . "AS b ".
                " LEFT JOIN " . $GLOBALS['ecs']->table('link_brand') . "AS lb ON lb.brand_id = b.brand_id ".
                    " LEFT JOIN " . $GLOBALS['ecs']->table('merchants_shop_brand') . "AS msb ON msb.bid = lb.bid AND msb.audit_status = 1 AND msb.is_show = 1 ".
                    " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.brand_id = msb.bid AND g.user_id > 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 $brand_tag_where ". 
                    " LEFT JOIN " . $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .	
                " WHERE $children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), $cat_keys))) . " AND b.is_show = 1 " .
                "GROUP BY b.brand_id HAVING goods_num > 0 $where_having ORDER BY b.sort_order, b.brand_id ASC";

        $msb_brands_list = $GLOBALS['db']->getAll($sql);
        
        if($msb_brands_list){
            $new_array = array_merge($brands_list, $msb_brands_list);
            $new = unique_arr($new_array);
            $brands_list = $new;
        }
                
        //by zhang
        $pin = new pin();    /*  增加获取字母类 这里实例化对象 */ 

        $brands = array();	
        foreach ($brands_list AS $key => $val)
        {
            $temp_key = $key; //by zhang

            $brands[$temp_key]['brand_id'] = $val['brand_id'];
            $brands[$temp_key]['brand_name'] = $val['brand_name'];

            //by zhang start
            $bdimg_path="data/brandlogo/";   				   // 图片路径
            $bd_logo=$val['brand_logo']?$val['brand_logo']:""; // 图片名称
            if(empty($bd_logo)){
                $brands[$temp_key]['brand_logo'] =""; 		   // 获取品牌图片 
            }else{
                $brands[$temp_key]['brand_logo'] =$bdimg_path.$bd_logo;
            }
            
            $brands[$temp_key]['brand_letters']=strtoupper(substr($pin->Pinyin($val['brand_name'],'UTF8'),0,1));  //获取品牌字母
            //by zhang end
            
            //OSS文件存储ecmoban模板堂 --zhuo start
            if($GLOBALS['_CFG']['open_oss'] == 1 && $brands[$temp_key]['brand_logo']){
                $bucket_info = get_bucket_info();
                $brands[$temp_key]['brand_logo'] = $bucket_info['endpoint'] . $brands[$temp_key]['brand_logo'];
            }
            //OSS文件存储ecmoban模板堂 --zhuo end

            $brands[$temp_key]['url'] = build_uri('category', array('cid' => $cat_id, 'bid' => $val['brand_id'], 'price_min'=>$price_min, 'price_max'=> $price_max, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

            /* 判断品牌是否被选中 */ // by zhang
            if (!strpos($brand,",") && $brand == $brands_list[$key]['brand_id'])
            {
                $brands[$temp_key]['selected'] = 1;
            }
            if (stripos($brand,","))
            {
                $brand2=explode(",",$brand);
                for ($i=0; $i <$brand2[$i] ; $i++) { 
                    if($brand2[$i]==$brands_list[$key]['brand_id']){
                        $brands[$temp_key]['selected'] = 1;
                    }
                }
            }
        }

        $ubrand = isset($_REQUEST['ubrand']) ? intval($_REQUEST['ubrand']) : 0;

        $smarty->assign('ubrand', $ubrand);
        //ecmoban模板堂 --zhuo end

            // 分配字母 by zhang start
        $letter=range('A','Z');
        $smarty->assign('letter', $letter);

        // 为0或没设置的时候 加载模板
        if($brands){
          $smarty->assign('brands', $brands);
        }
        
        $get_bd = array();
        $bd = "";
        foreach ($brands as $key => $value) {
            if ($value['selected'] == 1) {
                $bd.=$value['brand_name'] . ",";
                $get_bd[$key]['brand_id'] = $value['brand_id'];

                if ($_CFG['rewrite']) {
                    $brand_id = "b" . $get_bd[$key]['brand_id'];
                    if (stripos($value['url'], $brand_id)) {
                        $get_bd[$key]['url'] = str_replace($brand_id, "b0", $value['url']);
                    }
                } else {
                    $brand_id = "brand=" . $get_bd[$key]['brand_id'];
                    if (stripos($value['url'], $brand_id)) {
                        $get_bd[$key]['url'] = str_replace($brand_id, "brand=0", $value['url']);
                    }
                }
                $br_url = $get_bd[$key]['url'];
            }
        }

        $get_brand['br_url']=$br_url;
        $get_brand['bd']=substr($bd,0,-1);
        
        $smarty->assign('get_bd',            $get_brand);               // 品牌已选模块
        //by zhang end

        //$smarty->assign('brands', $brands); by zhang
        
        $g_price=array();       // 选中的价格
        for ($i=0; $i<count($price_grade); $i++) { 
             if($price_grade[$i]['selected']==1){
                $g_price[$i]['price_range']=$price_grade[$i]['price_range'];
                $g_price[$i]['url']=$price_grade[$i]['url'];
                $p_url=$g_price[$i]['url'];
                $p_a = $_GET['price_min'];
                $p_b = $_GET['price_max'];
                if(stripos($p_url,$p_a) && stripos($p_url,$p_b)){
                    
                    if($p_a > $p_b){
                        $price=array($p_a,$p_b);
                        $p_a=$price[1];
                        $p_b=$price[0];
                    } 
            
                    if($p_a > 0 && $p_b > 0)
                    {
                        $g_price[$i]['url']=str_replace($p_b,0,str_replace($p_a,0,$p_url));
                    }
                    elseif($p_a == 0 && $p_b > 0)
                    {
                        $g_price[$i]['url']=str_replace($p_b,0,$p_url);
                    }
                }
                
                break;
             }
        }  
        // 处理交换价格

        if(empty($g_price) && ($price_min>0 || $price_max>0)){
            if($price_min > $price_max){
                    $price=array($price_min,$price_max);
                    $price_min=$price[1];
                    $price_max=$price[0];
            } 
            
            $parray=array();
            
            $parray['purl'] = build_uri('category', array('cid' => $cat_id, 'bid' => $brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
            $parray['min_max']=$price_min." - ".$price_max;
            $smarty->assign('parray',           $parray);     // 自定义价格恢复

        }

        $smarty->assign('g_price',           $g_price);              // 价格已选模块 

        /* 属性筛选 */
        $ext = ''; //商品查询条件扩展

        if($self == 1){ 
            $ext .= " AND g.user_id = 0 ";
        }
        
        if($ship == 1){ //ecmoban模板堂 --zhuo
            $ext .= " AND g.is_shipping = 1 ";
        }

        if ($cat['filter_attr'] > 0)
        {
            $cat_filter_attr = explode(',', $cat['filter_attr']);       //提取出此分类的筛选属性
            $all_attr_list = array();
            $attributeInfo = array();

            foreach ($cat_filter_attr AS $key => $value)
            {	
                $sql = "SELECT a.attr_name, attr_cat_type FROM " . $ecs->table('attribute') . " AS a, " . $ecs->table('goods_attr') . " AS ga left join  " . $ecs->table('goods') . " AS g on g.goods_id = ga.goods_id " .$leftJoin. " WHERE ($children OR " . get_extension_goods($children) . ") AND a.attr_id = ga.attr_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND a.attr_id='$value'" . $tag_where;
                            $attributeInfo = $db->getRow($sql);

                if($attributeInfo)
                {
                    $all_attr_list[$key]['filter_attr_name'] = $attributeInfo['attr_name'];
                                    $all_attr_list[$key]['attr_cat_type'] = $attributeInfo['attr_cat_type'];

                                    $all_attr_list[$key]['filter_attr_id'] = $value; //by zhang

                    $sql = "SELECT a.attr_id, MIN(a.goods_attr_id ) AS goods_id, a.attr_value AS attr_value, a.color_value FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods') .
                           " AS g" .
                           " WHERE ($children OR " . get_extension_goods($children) . ') AND g.goods_id = a.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
                           " AND a.attr_id='$value' ".
                           " GROUP BY a.attr_value";

                    $attr_list = $db->getAll($sql);

                    $temp_arrt_url_arr = array();

                    for ($i = 0; $i < count($cat_filter_attr); $i++)        //获取当前url中已选择属性的值，并保留在数组中
                    {
                        $temp_arrt_url_arr[$i] = !empty($filter_attr[$i]) ? $filter_attr[$i] : 0;
                                    }

                    //by zhang start
                        foreach ($attr_list as $k => $v){
                        $temp_key = $k;
                        $temp_arrt_url_arr[$key] = $v['goods_id'];        	  //为url中代表当前筛选属性的位置变量赋值,并生成以‘.’分隔的筛选属性字符串
                        $temp_arrt_url = implode('.', $temp_arrt_url_arr);

                        if(!empty($v['color_value'])){
                                    $arr_color2['c_value'] = $v['attr_value'];
                                    $arr_color2['c_url' ]= "#" . $v['color_value'];
                                    $v['attr_value'] = $arr_color2;
                        }
                        $all_attr_list[$key]['attr_list'][$temp_key]['attr_value'] = $v['attr_value'];
                        $all_attr_list[$key]['attr_list'][$temp_key]['goods_id'] = $v['goods_id']; // 取分类ID
                        $all_attr_list[$key]['attr_list'][$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);

                        if (!empty($filter_attr[$key]))
                        {
                            if(!stripos($filter_attr[$key],",") && $filter_attr[$key] == $v['goods_id']){
                                          $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 1;
                            } 

                            if (stripos($filter_attr[$key],",")) {
                                    $color_arr=explode(",",$filter_attr[$key]);
                                    for ($i=0; $i <count($color_arr) ; $i++) { 
                                        if($color_arr[$i]== $v['goods_id']){
                                           $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 1; 
                                        } 
                                    }
                            } 
                        }

                    }
                                    //by zhang end
                }

            }

            /**-------------------多条件筛选数组处理 by zhang start-----------------------**/
            // 颜色区块单独拿出  
            $color_list=array();
            for ($i=0; $i <count($all_attr_list)+1; $i++) { 
                if($all_attr_list[$i]['attr_cat_type']== 1){
                    for ($k=0; $k < count($all_attr_list[$i]['attr_list']); $k++) { 
                        $array_color=$all_attr_list[$i]['attr_list'];
                            if(count($array_color[$k]['attr_value'])==1){
                                $array['c_value']=$array_color[$k]['attr_value'];
                                $array['c_url']="#FFFFFF";
                                $all_attr_list[$i]['attr_list'][$k]['attr_value']=$array;
                            }
                    }
                    $color_list=$all_attr_list[$i];
                    unset($all_attr_list[$i]);
                }
            }

            $c_array=array();       
            $k="";
            for ($i=0; $i <count($color_list['attr_list']); $i++) { 
                if($color_list['attr_list'][$i]['selected']==1){
                        $c_array[$i]['filter_attr_name']=$color_list['filter_attr_name'];
                        $c_array[$i]['attr_list']['attr_value']=$color_list['attr_list'][$i]['attr_value']['c_value'];
                        $c_array[$i]['attr_list']['goods_id']=$color_list['attr_list'][$i]['goods_id'];
                        $color_id=$c_array[$i]['attr_list']['goods_id'];
                        $k.=$c_array[$i]['attr_list']['attr_value'].","; // 取选中的名称
                        $color_url=$color_list['attr_list'][$i]['url'];
                        if(strpos($color_url,$color_id)){
                            $c_array[$i]['attr_list']['url']=str_replace($color_id,0,$color_url);
                        }
                        $c_url=$c_array[$i]['attr_list']['url'];         // 还原

                }
            }

            // 颜色合并
            $c_array=array();
            $c_array['filter_attr_name']=$color_list['filter_attr_name'];
            $c_array['attr_value']=substr($k,0,-1);
            $c_array['url']=$c_url;


            $g_array=array();        // 选中的分类( 不含颜色分类 )   
            for ($i = 0; $i < count($all_attr_list) + 3; $i++) {
                $k = "";
                for ($j = 0; $j < count($all_attr_list[$i]['attr_list']); $j++) {
                    if ($all_attr_list[$i]['attr_list'][$j]['selected'] == 1) {
                        $g_array[$i]['filter_attr_name'] = $all_attr_list[$i]['filter_attr_name'];
                        $g_array[$i]['attr_list']['value'] = $all_attr_list[$i]['attr_list'][$j]['attr_value'];
                        $g_array[$i]['attr_list']['goods_id'] = $all_attr_list[$i]['attr_list'][$j]['goods_id'];
                        $g_url = $all_attr_list[$i]['attr_list'][$j]['url']; // 被选中的URL        
                        $sid = $g_array[$i]['attr_list']['goods_id'];     // 被选中的ID
                        if (strpos($g_url, $sid)) {
                            $g_array[$i]['attr_list']['url'] = str_replace($sid, 0, $g_url);
                        }
                        $k.=$all_attr_list[$i]['attr_list'][$j]['attr_value'] . ",";
                        $g_array[$i]['g_name'] = substr($k, 0, -1);
                        $g_array[$i]['g_url'] = $g_array[$i]['attr_list']['url'];
                    }
                }
            }


            $smarty->assign('c_array',           $c_array);              // 颜色已选模块    
            $smarty->assign('g_array',           $g_array);              // 其他已选模块
            $smarty->assign('color_search',      $color_list);           // 颜色筛选模块
            /**-------------------多条件筛选数组处理 by zhang end-----------------------**/

            $smarty->assign('filter_attr_list',  $all_attr_list);
            /* 扩展商品查询条件 */
            if (!empty($filter_attr))
            {
                $ext_sql = "SELECT DISTINCT(b.goods_id) FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods_attr') . " AS b " .  "WHERE ";
                $ext_group_goods = array();

                foreach ($filter_attr AS $k => $v)                      // 查出符合所有筛选属性条件的商品id */
                {
                    //by zhang start
                    if (is_numeric($v) && $v !=0 && isset($cat_filter_attr[$k]))
                    {
                        $sql = $ext_sql . "b.attr_value = a.attr_value AND b.attr_id = " . $cat_filter_attr[$k] ." AND a.goods_attr_id  in ($v)";                  
                        $ext_group_goods = $db->getColCached($sql);
                        $ext .= ' AND ' . db_create_in($ext_group_goods, 'g.goods_id');
                    }
                    //by zhang end
                }
            }
        }
    }
	
    assign_template('c', array($cat_id));
	
    $position = assign_ur_here($cat_id, $brand_name);
    $smarty->assign('page_title',       $position['title']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
    $smarty->assign('helps',            get_shop_help());              // 网店帮助
    //$smarty->assign('top_goods',        get_top10());                  // 销售排行
    
    $smarty->assign('show_marketprice', $_CFG['show_marketprice']);

    /* 周改 */
    $smarty->assign('brand_id',         $brand);
    $smarty->assign('price_max',        $price_max);
    $smarty->assign('price_min',        $price_min);
    $smarty->assign('filter_attr',      $filter_attr_str);
    $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-c$cat_id.xml" : 'feed.php?cat=' . $cat_id); // RSS URL
	$smarty->assign('cat_brand',      get_category_brands_ad($cat_id)); //分类品牌 by wu
	/********************************************************************************************/  
    //在分类页面调用当前分类以及其二级分类的信息，再在每个分类下调用5个商品  
    $cats = get_children_tree($cat_id);//获取当前分类信息以及子分类信息  
    $cat_detail = array();  
    foreach ($cats as $key => $val)  
    {  
        foreach($cats[$key]['children'] as $_k =>$_v){  
            $cats[$key]['children'][$_k]['goods_detail'] = get_cat_id_goods_list($_v['id'],10);  
            foreach($cats[$key]['children'][$_k]['goods_detail'] as $_ke =>$_va){  
                $cats[$key]['children'][$_k]['goods_detail'][$_ke]['thumb_url'] = get_thumb($_va['id']);  
            }  
        }  
        $cat_detail = $cats[$key]['children'];//当前分类下的二级分类及其商品信息  
    }  
    $smarty->assign('cat_detail',      $cat_detail);  
	
    /********************************************************************************************/
    //顶级分类页据输出部分by wang start
    if($cat_row['parent_id'] == 0 && $cat_row['is_top_style'] == 1)
    {
            $categories_child = get_parent_cat_tree($cat_id);
            //顶级分类页的幻灯片

            for($i=1;$i<=$_CFG['auction_ad'];$i++){
                    $cat_top_ad   .= "'cat_top_ad".$i.","; //首页楼层轮播图
                    $cat_top_new_ad .= "'cat_top_new_ad".$i.","; //首页新品首发左侧上广告图
                    $cat_top_newt_ad .= "'cat_top_newt_ad".$i.","; //首页新品首发左侧下广告图
                    $cat_top_prom_ad .= "'cat_top_prom_ad".$i.","; //首页幻灯片下优惠商品左侧广告					
            }

            //根据顶级分类页模板调用不同数量的商品 by wu start
            $topStyle=array('new'=>10,'hot'=>10,'best'=>10,'promote'=>10);
            if($cat_row['top_style_tpl']==1)
            {
                $topStyle=array('new'=>10,'hot'=>5,'best'=>5,'promote'=>4);
            }
            elseif($cat_row['top_style_tpl']==2)
            {
                $topStyle=array('new'=>10,'hot'=>10,'best'=>10,'promote'=>10);
            }
            elseif($cat_row['top_style_tpl']==3)
            {
                $topStyle=array('new'=>3,'hot'=>18,'best'=>3,'promote'=>7);
            }
            else
            {
                $topStyle=array('new'=>10,'hot'=>8,'best'=>10,'promote'=>5);
            }			
            //根据顶级分类页模板调用不同数量的商品 by wu end
			
            //获取分类下的团购商品 by wu start
            $sql=" select ga.*,g.cat_id,g.goods_name,g.goods_thumb,g.sales_volume from ".$GLOBALS['ecs']->table('goods_activity')." as ga ".
                    " left join ".$GLOBALS['ecs']->table('goods')." as g on g.goods_id=ga.goods_id ".
                    " where ga.act_type = '" . GAT_GROUP_BUY . "' AND ga.start_time <= '".time()."' AND ga.is_finished < 3 and $children order by ga.act_id limit 10";
            $cate_top_group_goods=$GLOBALS['db']->getAll($sql);
            foreach($cate_top_group_goods as $key=>$val)
            {
                    $ext_info = unserialize($val['ext_info']);
                    $cate_top_group_goods[$key] = array_merge($val, $ext_info);
            }
            $smarty->assign('cate_top_group_goods',$cate_top_group_goods);
            //获取分类下的团购商品 by wu end
			
            //新品首发
            $cate_top_new_goods = get_category_recommend_goods('new', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $topStyle['new']);   
            $smarty->assign('cate_top_new_goods', $cate_top_new_goods);
            
            //热销排行
            $cate_top_hot_goods = get_category_recommend_goods('hot', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $topStyle['hot']);
            $smarty->assign('cate_top_hot_goods', $cate_top_hot_goods);
            
            //促销商品
            $smarty->assign('cate_top_promote_goods',get_category_recommend_goods('promote', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $topStyle['promote']));
			
			//精品推荐 by wu
			$cate_top_best_goods = get_category_recommend_goods('best', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'],$topStyle['best']);
			$smarty->assign('cate_top_best_goods', $cate_top_best_goods);

            //浏览记录
            $history_goods = get_history_goods(0, $region_id, $area_info['region_id']);

            $history_count=array();
            if ($history_goods) {
                for ($i = 0; $i < count($history_goods) / 6; $i++) {
                    //$history_count[$i]=$i; 修改浏览记录 by wu
                    for ($j = 0; $j < 6; $j++) {
                        if (pos($history_goods)) {
                            $history_count[$i][] = pos($history_goods);
                            next($history_goods);
                        } else {
                            break;
                        }
                    }
                }
            }
            //$havealook=get_goods_havealook(10);
            $havealook=get_category_recommend_goods('rand', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'],6);
            $smarty->assign('havealook',$havealook);
            $smarty->assign('history_count',$history_count);
            $smarty->assign('history_goods',$history_goods);
            $smarty->assign('cat_top_ad',$cat_top_ad);
            $smarty->assign('cat_top_new_ad',$cat_top_new_ad);
            $smarty->assign('cat_top_newt_ad',$cat_top_newt_ad);
            $smarty->assign('cat_top_prom_ad',$cat_top_prom_ad);
            $smarty->assign('categories_child',$categories_child);//获取当前顶级分类下的所有子分类
            
            //$categories_child_goods = get_cat_child_goods($cat_id,1,10);
            //$smarty->assign('categories_child_goods', $categories_child_goods); //获取当前顶级分类下的所有子分类商品
            
            //$suspension_two = get_template_js(array('suspension-two'));
            //$smarty->assign('suspension_two',          $suspension_two); 
    }else{
        $categories_pro = get_category_tree_leve_one();
        $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
        $smarty->assign('guess_goods',     get_guess_goods($user_id, 1,1, 6,$region_id, $area_info['region_id']));         //猜你喜欢
        
        /*
        if ($brand > 0)
        {
            $arr['all'] = array('brand_id'  => 0,
                            'brand_name'    => $GLOBALS['_LANG']['all_goods'],
                            'brand_logo'    => '',
                            'goods_num'     => '',
                            'url'           => build_uri('category', array('cid'=>$cat_id), $cat['cat_name'])
                        );
        }
        else
        {
            $arr = array();
        }

        $brand_list = array_merge($arr, get_brands($cat_id, 'category'));
        */
        $smarty->assign('data_dir',    DATA_DIR);
        //$smarty->assign('brand_list',      $brand_list);
        //$smarty->assign('promotion_info', get_promotion_info());


        /* 调查 */
        $vote = get_vote();
        if (!empty($vote))
        {
            $smarty->assign('vote_id',     $vote['id']);
            $smarty->assign('vote',        $vote['content']);
        }

        $smarty->assign('best_goods',      get_category_recommend_goods('best', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $best_size, $best_num)); //瀑布流加载分类商品 by wu end
        $smarty->assign('promotion_goods', get_category_recommend_goods('promote', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $best_size, $best_num));
        $smarty->assign('hot_goods',       get_category_recommend_goods('hot', $children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $best_size, $best_num));

        $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext, $region_id, $area_info['region_id'], $ubrand);
        $max_page = ($count> 0) ? ceil($count / $size) : 1;
        if ($page > $max_page)
        {
            $page = $max_page;
        }
		
        $region = array(1, $province_id, $city_id, $district_id);
        $goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order, $region_id, $area_info['region_id'], $ubrand, $region);

        //新增分类页商品相册 by mike end

        if($display == 'grid')
        {
            if(count($goodslist) % 2 != 0)
            {
                $goodslist[] = array();
            }
        }
        $smarty->assign('goods_list',       $goodslist);		
        $smarty->assign('category',         $cat_id);
        $smarty->assign('script_name', $dwt_name);

        //瀑布流加载分类商品 by wu
        if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
        {
                $smarty->assign('model', intval($_REQUEST['model']));
                $result = array('error' => 0,'message' => '','cat_goods'=>'','best_goods'=>'');
                $result['cat_goods'] = html_entity_decode($smarty->fetch('library/more_goods.lbi')); //分类商品
                $result['best_goods'] = html_entity_decode($smarty->fetch('library/more_goods_best.lbi')); //推广商品
                die(json_encode($result));
        }		

        //瀑布流加载分类商品 by wu
        assign_pager('category',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str, '', '', '','', $ubrand, '', $ship, $self); // 分页
    }

    //顶级分类页据输出部分by wang end
	
    $smarty->assign('region_id', $region_id);
    $smarty->assign('area_id', $area_info['region_id']);
    
    assign_dynamic($dwt_name); // 动态内容
}

if ($rank = get_rank_info())
{
	$smarty->assign('rank_name', $rank['rank_name']);
}

$smarty->assign('info',        get_user_default($_SESSION['user_id']));

$cart_info = insert_cart_info(1);
$smarty->assign('cart_info',        $cart_info);

$smarty->display($dwt_name.'.dwt', $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order, $warehouse_id = 0, $area_id = 0, $ubrand, $region)
{
    $display = $GLOBALS['display'];
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND ".
            "g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    $leftJoin = '';	  
    $where_brand = '';
    if ($brand > 0)
    {   
        $where_brand = "AND b.brand_id IN(" .$brand. ")";
    }

    $sel_msb = "(g.brand_id IN(SELECT msb.bid FROM " .$GLOBALS['ecs']->table('brand') ." AS b, ". $GLOBALS['ecs']->table('link_brand') ." AS lb, " .$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb". 
            " WHERE b.is_show = 1 AND b.brand_id = lb.brand_id AND lb.bid = msb.bid AND msb.is_show = 1 AND msb.audit_status = 1 $where_brand) AND g.user_id > 0)";
    $sel_brand = "(g.brand_id IN(SELECT b.brand_id FROM " .$GLOBALS['ecs']->table('brand') ." AS b" . " WHERE b.is_show = 1 $where_brand) AND g.user_id = 0)";
    $where .= "AND ( " .$sel_brand. " OR " .$sel_msb. ")";
	
    //ecmoban模板堂 --zhuo start
    $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

    if($GLOBALS['_CFG']['open_area_goods'] == 1){
            $leftJoin .= " left join " .$GLOBALS['ecs']->table('link_area_goods'). " as lag on g.goods_id = lag.goods_id ";
            $where .= " and lag.region_id = '$area_id' ";
    }
    //ecmoban模板堂 --zhuo end

    if ($min > 0)
    {
        $where .= " AND IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) <= $max ";
    }
	
    if($sort == 'last_update'){
            $sort ='g.last_update';
    }

    //ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['review_goods'] == 1){
            $where .= ' AND g.review_status > 2 ';
    }
    //ecmoban模板堂 --zhuo end	

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.user_id, g.goods_name, ' .$shop_price. ' g.goods_name_style, g.comments_number,g.sales_volume,g.market_price, g.is_new, g.is_best, g.is_hot, g.store_new, g.store_best, g.store_hot, ' .
            ' IF(g.model_price < 1, g.goods_number, IF(g.model_price < 2, wg.region_number, wag.region_number)) AS goods_number, ' .
            ' IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) AS org_price, g.model_price, ' .
            "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, " .
            "IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, g.goods_type, " .		
            'g.promote_start_date, g.promote_end_date, g.is_promote, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            $leftJoin.
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE $where $ext group by g.goods_id  ORDER BY $sort $order";			

    //瀑布流加载分类商品 by wu
    if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
    {
        $start = intval($_REQUEST['goods_num']);
    }
    else
    {
        $start = ($page - 1) * $size;
    }

    $res = $GLOBALS['db']->selectLimit($sql, $size, $start);	
    //$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['goods_id']]['org_price']             = $row['org_price'];
        $arr[$row['goods_id']]['model_price']             = $row['model_price'];

        $arr[$row['goods_id']]['warehouse_price']         = $row['warehouse_price'];
        $arr[$row['goods_id']]['warehouse_promote_price'] = $row['warehouse_promote_price'];
        $arr[$row['goods_id']]['region_price']            = $row['region_price'];
        $arr[$row['goods_id']]['region_promote_price']    = $row['region_promote_price'];

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

        $arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']             = $row['goods_name'];
        $arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
        $arr[$row['goods_id']]['sales_volume']      = $row['sales_volume'];
        $arr[$row['goods_id']]['is_promote']             = $row['is_promote'];
        /* 折扣节省计算 by ecmoban start */
        if($row['market_price'] > 0)
        {
                $discount_arr = get_discount($row['goods_id']); //函数get_discount参数goods_id
        }
        $arr[$row['goods_id']]['zhekou']  = $discount_arr['discount'];  //zhekou
        $arr[$row['goods_id']]['jiesheng']  = $discount_arr['jiesheng']; //jiesheng
        /* 折扣节省计算 by ecmoban end */
        $arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);

        $arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']       = price_format($row['shop_price']);
        $arr[$row['goods_id']]['type']             = $row['goods_type'];
        $arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $arr[$row['goods_id']]['is_hot']             = $row['user_id'] > 0 ? $row['store_hot']  : $row['is_hot'];
        $arr[$row['goods_id']]['is_best']             = $row['user_id'] > 0 ? $row['store_best']  : $row['is_best'];
        $arr[$row['goods_id']]['is_new']             = $row['user_id'] > 0 ? $row['store_new']  : $row['is_new'];

        //ecmoban模板堂 --zhuo start
        if($row['model_attr'] == 1){
                $table_products = "products_warehouse";
                $type_files = " and warehouse_id = '$warehouse_id'";
        }elseif($row['model_attr'] == 2){
                $table_products = "products_area";
                $type_files = " and area_id = '$area_id'";
        }else{
                $table_products = "products";
                $type_files = "";
        }

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table($table_products). " WHERE goods_id = '" .$row['goods_id']. "'" .$type_files. " LIMIT 0, 1";
        $arr[$row['goods_id']]['prod'] = $GLOBALS['db']->getRow($sql);

        if(empty($prod)){ //当商品没有属性库存时
            $arr[$row['goods_id']]['prod'] = 1; 
        }else{
            $arr[$row['goods_id']]['prod'] = 0;
        }

        $arr[$row['goods_id']]['goods_number'] = $row['goods_number'];

        $sql="select * from ".$GLOBALS['ecs']->table('seller_shopinfo')." where ru_id='" .$row['user_id']. "'";
        $basic_info = $GLOBALS['db']->getRow($sql);	
        $arr[$row['goods_id']]['kf_type'] = $basic_info['kf_type'];
        
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
        
        $arr[$row['goods_id']]['rz_shopName'] = get_shop_name($row['user_id'], 1); //店铺名称
        $arr[$row['goods_id']]['user_id'] = $row['user_id'];
        
        $build_uri = array(
            'urid' => $row['user_id'],
            'append' => $arr[$row['goods_id']]['rz_shopName']
        );

        $domain_url = get_seller_domain_url($row['user_id'], $build_uri);
        $arr[$row['goods_id']]['store_url'] = $domain_url['domain_name'];

        $arr[$row['goods_id']]['count'] = selled_count($row['goods_id']);
        $mc_all = ments_count_all($row['goods_id']);       //总条数
        $mc_one = ments_count_rank_num($row['goods_id'],1);		//一颗星
        $mc_two = ments_count_rank_num($row['goods_id'],2);	    //两颗星	
        $mc_three = ments_count_rank_num($row['goods_id'],3);   	//三颗星
        $mc_four = ments_count_rank_num($row['goods_id'],4);		//四颗星
        $mc_five = ments_count_rank_num($row['goods_id'],5);		//五颗星
        $arr[$row['goods_id']]['zconments'] = get_conments_stars($mc_all,$mc_one,$mc_two,$mc_three,$mc_four,$mc_five);
        $arr[$row['goods_id']]['review_count'] = $arr[$row['goods_id']]['zconments']['allmen'];
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
        //ecmoban模板堂 --zhuo end          
        
        //商品运费by wu 
        $shippingFee = goodsShippingFee($row['goods_id'], $warehouse_id, $region);
        $arr[$row['goods_id']]['shipping_fee_formated'] = $shippingFee['shipping_fee_formated'];
        
        $arr[$row['goods_id']]['is_collect'] = get_collect_user_goods($row['goods_id']);
    }
    
    return $arr;
}

/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($children, $brand = 0, $min = 0, $max = 0, $ext='', $warehouse_id = 0, $area_id = 0, $ubrand)
{
	
    $where  = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';
    
    $leftJoin = '';		
    $where_brand = '';
    if ($brand > 0)
    {   
        $where_brand = "AND b.brand_id IN(" .$brand. ")";
    }
 
    $sel_msb = "(g.brand_id IN(SELECT msb.bid FROM " .$GLOBALS['ecs']->table('brand') ." AS b, ". $GLOBALS['ecs']->table('link_brand') ." AS lb, " .$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb". 
            " WHERE b.is_show = 1 AND b.brand_id = lb.brand_id AND lb.bid = msb.bid AND msb.is_show = 1 AND msb.audit_status = 1 $where_brand) AND g.user_id > 0)";
    $sel_brand = "(g.brand_id IN(SELECT b.brand_id FROM " .$GLOBALS['ecs']->table('brand') ." AS b" . " WHERE b.is_show = 1 $where_brand) AND g.user_id = 0)";
    $where .= "AND ( " .$sel_brand. " OR " .$sel_msb. ")";
	
    //ecmoban模板堂 --zhuo start
    $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, ";
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

    if($GLOBALS['_CFG']['open_area_goods'] == 1){
            $leftJoin .= " left join " .$GLOBALS['ecs']->table('link_area_goods'). " as lag on g.goods_id = lag.goods_id ";
            $where .= " and lag.region_id = '$area_id' ";
    }
    //ecmoban模板堂 --zhuo end

    if ($min > 0)
    {
        $where .= " AND IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) <= $max ";
    }
	
	//ecmoban模板堂 --zhuo start
	if($GLOBALS['_CFG']['review_goods'] == 1){
		$where .= ' AND g.review_status > 2 ';
	}
	//ecmoban模板堂 --zhuo end	

    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . " AS g " .$leftJoin. " WHERE $where $ext");
}

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
/*function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}*/

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}

?>
