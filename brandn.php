<?php

/**
 * 品牌页面 brand new
 * brand new 
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

/* ------------------------------------------------------ */
//-- INPUT
/* ------------------------------------------------------ */

//ecmoban模板堂 --zhuo start

$area_info = get_area_info($province_id);

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end
//ecmoban模板堂 --zhuo start 仓库//by wang
$smarty->assign('province_row', get_region_name($province_id));
$smarty->assign('city_row', get_region_name($city_id));
$smarty->assign('district_row', get_region_name($district_id));
$province_list = get_warehouse_province();

$smarty->assign('province_list', $province_list); //省、直辖市

$city_list = get_region_city_county($province_id);
$smarty->assign('city_list', $city_list); //省下级市

$district_list = get_region_city_county($city_id);
$smarty->assign('district_list', $district_list); //市下级县

$smarty->assign('open_area_goods', $GLOBALS['_CFG']['open_area_goods']);


/* 获得请求的品牌ID */
$brand_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
/* 获得请求的商家品牌ID */
$mbid = isset($_REQUEST['mbid']) && !empty($_REQUEST['mbid']) ? intval($_REQUEST['mbid']) : 0;

/* 初始化分页信息 */
$page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
$size = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
$cate = !empty($_REQUEST['cat']) && intval($_REQUEST['cat']) > 0 ? intval($_REQUEST['cat']) : 0;
$is_ship = isset($_REQUEST['is_ship']) && !empty($_REQUEST['is_ship']) ? addslashes_deep($_REQUEST['is_ship']) : ''; //by wang是否包邮
$is_self = isset($_REQUEST['is_self']) && !empty($_REQUEST['is_self']) ? intval($_REQUEST['is_self']) : '';

$price_min = !empty($_REQUEST['price_min']) && floatval($_REQUEST['price_min']) > 0 ? floatval($_REQUEST['price_min']) : '';
$price_max = !empty($_REQUEST['price_max']) && floatval($_REQUEST['price_max']) > 0 ? floatval($_REQUEST['price_max']) : '';

/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort = (isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_volume', 'comments_number'))) ? trim($_REQUEST['sort']) : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
$display = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display']) : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7, $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);

$smarty->assign('sort', $sort);
$smarty->assign('order', $order);
$smarty->assign('price_min', $price_min);
$smarty->assign('price_max', $price_max);
$smarty->assign('is_ship', $is_ship);
$smarty->assign('self_support', $is_self);



$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'index';
$smarty->assign('act', $act);

// 品牌收藏 qin
if ($mbid) {
    $mact = 'merchants_brands';
    $brand_info = get_brand_info($mbid, $mact);
    // 本人判断是否收藏， 已经该品牌收藏人数
    $ru_id = $GLOBALS['db']->getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_brand') . " WHERE bid='$mbid' ");
    $brand_info['collect_count'] = get_collect_brand_user_count($mbid, $ru_id);
    $brand_info['is_collect'] = get_collect_user_brand($mbid, $ru_id);
} else {
    $brand_info = get_brand_info($brand_id);
    $brand_info['collect_count'] = get_collect_brand_user_count($brand_id);
    $brand_info['is_collect'] = get_collect_user_brand($brand_id);
}
//print_arr($brand_info);

if (empty($brand_info)) {
    ecs_header("Location: ./\n");
    exit;
}
$smarty->assign('brand', $brand_info);

$smarty->assign('data_dir', DATA_DIR);
$smarty->assign('keywords', htmlspecialchars($brand_info['brand_desc']));
$smarty->assign('description', htmlspecialchars($brand_info['brand_desc']));

/* 赋值固定内容 */
assign_template();

$position = assign_ur_here($cate, $brand_info['brand_name']);

$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here', $position['ur_here']); // 当前位置
$smarty->assign('brand_id', $brand_id);
$smarty->assign('category', $cate);

$categories_pro = get_category_tree_leve_one();
$smarty->assign('categories_pro', $categories_pro); // 分类树加强版

$smarty->assign('helps', get_shop_help());              // 网店帮助
//$smarty->assign('top_goods',      get_top10());                  // 销售排行
$smarty->assign('show_marketprice', $_CFG['show_marketprice']);
$smarty->assign('brand_cat_list', brand_related_cat($brand_id, $act)); // 相关分类 品牌商品所在分类
//    print_arr(brand_related_cat($brand_id, $act));

$smarty->assign('feed_url', ($_CFG['rewrite'] == 1) ? "feed-b$brand_id.xml" : 'feed.php?brand=' . $brand_id);

$smarty->assign('promotion_info', get_promotion_info());

/* * 小图 广告* */
for ($i = 1; $i <= $_CFG['auction_ad']; $i++) {
    $brandn_top_ad .= "'brandn_top_ad" . $i . ","; //品牌商品页面头部左侧广告
    $brandn_left_ad .= "'brandn_left_ad" . $i . ","; //品牌商品页面头部右侧广告
}
$smarty->assign('brandn_top_ad', $brandn_top_ad);
$smarty->assign('brandn_left_ad', $brandn_left_ad);
/* * 小图 广告 end* */

if ($act == 'index')
{
    $smarty->assign('best_goods', brand_recommend_goods('best', $brand_id, $cate, $region_id, $area_info['region_id'], $act)); // 精品
    $smarty->assign('hot_goods', brand_recommend_goods('hot', $brand_id, $cate, $region_id, $area_info['region_id'], $act));
    $smarty->display('brandn_index.dwt');
}
else if ($act == 'new') {
    $goods = brand_recommend_goods('new', $brand_id, $cate, $region_id, $area_info['region_id'], $act);
    $goods = $ecs->page_array($size, $page, $goods);
    $new_goods = $goods['list'];
    assign_pager('brandn', $cate, $goods['record_count'], $size, $sort, $order, $page, '', $brand_id, $price_min, $price_max, $display, '', '', '', 0, '', '', $act, $is_ship, $is_self);
    $smarty->assign('new_goods', $new_goods);
    $smarty->display('brandn_new.dwt');
}
else if ($act == 'hot') {
    $goods = brand_recommend_goods('hot', $brand_id, $cate, $region_id, $area_info['region_id'], $act);
    $goods = $ecs->page_array($size, $page, $goods);
    $hot_goods = $goods['list'];
    assign_pager('brandn', $cate, $goods['record_count'], $size, $sort, $order, $page, '', $brand_id, $price_min, $price_max, $display, '', '', '', 0, '', '', $act, $is_ship, $is_self);
    $smarty->assign('hot_goods', $hot_goods);
    $smarty->display('brandn_hot.dwt');
}
elseif ($act == 'cat')
{
    $goodslist = brand_get_goods($brand_id, $cate, $size, $page, $sort, $order, $region_id, $area_info['region_id'], $act, $is_ship, $price_min, $price_max, $is_self); //by wang
    //新增分类页商品相册 by mike end
    $count = goods_count_by_brand($brand_id, $cate, $act, $is_ship, $price_min, $price_max, $region_id, $area_info['region_id'], $is_self);
    assign_pager('brandn', $cate, $count, $size, $sort, $order, $page, '', $brand_id, $price_min, $price_max, $display, '', '', '', 0, '', '', $act, $is_ship, $is_self);

    $smarty->assign('goods_list', $goodslist);

    $smarty->display('brandn_cat.dwt');
}

// 换一组 qin
elseif ($act == 'change_index')
{
    require(ROOT_PATH . '/includes/cls_json.php');
    $json  = new JSON;
    $result = array('err'=> 0, 'msg'=>'', 'content'=>'');
    
    $best_rand = brand_recommend_goods('best', $brand_id, $cate, $region_id, $area_info['region_id'], $act,'rand');
//    print_arr($best_rand);
    $smarty->assign('best_goods', $best_rand);
    $result['content'] = $GLOBALS['smarty']->fetch('library/brandn_best_goods.lbi');
    die($json->encode($result));
}
// 收藏品牌 qin
elseif ($act == 'collect') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array('error' => 0, 'message' => '', 'url' => '');

    $cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $merchant_id = isset($_GET['merchant_id']) ? intval($_GET['merchant_id']) : 0;
    $script_name = isset($_GET['script_name']) ? htmlspecialchars(trim($_GET['script_name'])) : '';
    $cur_url = isset($_GET['cur_url']) ? htmlspecialchars(trim($_GET['cur_url'])) : '';

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
        if ($script_name != '') {
            if ($script_name == 'category') {
                $result['url'] = get_return_category_url($cat_id);
            } elseif ($script_name == 'search' || $script_name == 'merchants_shop') {
                $result['url'] = $cur_url;
            } elseif ($script_name == 'merchants_store_shop') {
                $result['url'] = get_return_store_shop_url($merchant_id);
            }
        }
        $result['error'] = 2;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    } else {
        // 如果是商家品牌 ru_id商家id，  0为自营
        $ru_id = 0;
        if ($mbid) {
            $ru_id = $GLOBALS['db']->getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_brand') . " WHERE bid='$mbid' ");
        }
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('collect_brand') .
                " WHERE user_id='$_SESSION[user_id]' AND brand_id = '$brand_id' AND ru_id = '$ru_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0) {
            $result['error'] = 1;
            $result['message'] = $GLOBALS['_LANG']['collect_brand_existed'];
            die($json->encode($result));
        } else {
            $time = gmtime();
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('collect_brand') . " (user_id, brand_id, add_time, ru_id)" .
                    "VALUES ('$_SESSION[user_id]', '$brand_id', '$time', '$ru_id')";

            if ($GLOBALS['db']->query($sql) === false) {
                $result['error'] = 1;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            } else {
                $collect_count = get_collect_brand_user_count($brand_id, $ru_id);
                $result['collect_count'] = $collect_count;

                clear_all_files();

                $result['error'] = 0;
                $result['message'] = $GLOBALS['_LANG']['cancel_brand_success'];
                die($json->encode($result));
            }
        }
    }
}
// 取消关注品牌
elseif ($act == 'cancel') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();
    $result = array('error' => 0, 'message' => '', 'url' => '');
    $type = isset($_GET['type']) ? intval($_GET['type']) : 0; //type = 1从用户中心取消关注
    // 如果是商家品牌 ru_id商家id，  0为自营
    $ru_id = 0;
    if (!empty($mbid)) {
        $ru_id = $GLOBALS['db']->getOne("SELECT user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_brand') . " WHERE bid='$mbid' ");
    }

    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('collect_brand') . " WHERE brand_id = '$brand_id' AND ru_id = '$ru_id' ";
    if ($type == 0) {
        if ($GLOBALS['db']->query($sql) === false) {
            $result['error'] = 1;
            $result['message'] = $GLOBALS['db']->errorMsg();
            die($json->encode($result));
        } else {
            $collect_count = get_collect_brand_user_count($brand_id, $ru_id);
            $result['collect_count'] = $collect_count;

            clear_all_files();

            $result['error'] = 0;
            $result['message'] = $GLOBALS['_LANG']['collect_brand_success'];
            die($json->encode($result));
        }
    } elseif ($type == 1) {
        if ($GLOBALS['db']->query($sql) === false) {
            show_message($GLOBALS['db']->errorMsg(), '返回', $ecs->url, 'error');
        } else {
            ecs_header("Location: user.php?act=focus_brand\n");
        }
    }
}

/**
 * 获得指定品牌下的推荐和促销商品
 *
 * @access  private
 * @param   string  $type
 * @param   integer $brand
 * @return  array
 */
function brand_recommend_goods($type, $brand, $cat = 0, $warehouse_id = 0, $area_id = 0, $act = '', $type_rand='')
{
    if (!in_array($type, array('best', 'new', 'hot'))) {
        return array();
    }
    static $result = NULL;

    $time = gmtime();

    if ($result === NULL) {
        if ($cat > 0) {
            $cat_where = "AND " . get_children($cat);
        } else {
            $cat_where = '';
        }

        //ecmoban模板堂 --zhuo start
        $leftJoin = '';
        if ($GLOBALS['_CFG']['open_area_goods'] == 1) {
            $leftJoin .= " left join " . $GLOBALS['ecs']->table('link_area_goods') . " as lag on g.goods_id = lag.goods_id ";
            $cat_where .= " and lag.region_id = '$area_id' ";
        }

        $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
        $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_goods') . " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
        $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_area_goods') . " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

        if ($GLOBALS['_CFG']['review_goods'] == 1) {
            $cat_where .= ' AND g.review_status > 2 ';
        }
        
        if ($type_rand == 'rand')
        {
            $cat_where .= ' AND g.goods_id >= (SELECT floor(RAND() * (SELECT MAX(goods_id) FROM ' . $GLOBALS['ecs']->table('goods') . ' WHERE brand_id='.$brand.''
                    . ' AND is_best = 1)))';
        }

        else if ($type == 'best') {
            $cat_where .= " AND g.is_best = 1";
        }
        else if ($type == 'new') {
            $cat_where .= " AND g.is_new = 1";
        }
        else if ($type == 'hot') {
            $cat_where .= " AND g.is_hot = 1";
        }
        
        $leftJoin .= "LEFT JOIN " . $GLOBALS['ecs']->table('link_brand') . " AS lb " . "ON lb.bid = g.brand_id ";
        $leftJoin .= "LEFT JOIN " . $GLOBALS["ecs"]->table('merchants_shop_brand') . " AS msb ON msb.bid = lb.bid ";
        $cat_where .= " AND ((g.brand_id = '$brand' AND g.user_id = 0) OR (lb.brand_id = '$brand' AND g.brand_id = lb.bid AND msb.audit_status = 1))";
        //ecmoban模板堂 --zhuo end	

        $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.comments_number,g.sales_volume, ' .
                'IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) AS org_price, ' .
                "IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, " .
                "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, " .
                'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, ' .
                'g.is_best, g.is_new, g.is_hot, g.is_promote ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                $leftJoin .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand' AND " .
                "(g.is_best = 1 OR (g.is_promote = 1 AND promote_start_date <= '$time' AND " .
                "promote_end_date >= '$time')) $cat_where" .
                'ORDER BY g.sort_order, g.last_update DESC';

        $result = $GLOBALS['db']->getAll($sql);
    }

    /* 取得每一项的数量限制 */
    $num = 0;
    $type2lib = array('best' => 'recommend_best', 'new' => 'recommend_new', 'hot' => 'recommend_hot', 'promote' => 'recommend_promotion');
    $num = get_library_number($type2lib[$type]);

    $idx = 0;
    $goods = array();
    foreach ($result AS $row) {
//        if ($idx >= $num)
//        {
//            break;
//        }

        if (($type == 'best' && $row['is_best'] == 1) || ($type == 'new' && $row['is_new'] == 1) || ($type == 'hot' && $row['is_hot'] == 1) ||
                ($type == 'promote' && $row['is_promote'] == 1 && $row['promote_start_date'] <= $time && $row['promote_end_date'] >= $time)) {
            if ($row['promote_price'] > 0) {
                $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            } else {
                $goods[$idx]['promote_price'] = 0;
            }
            $price_other = array('market_price' => $row['market_price'], 'org_price' => $row['org_price'], 'shop_price' => $row['shop_price'], 'promote_price' => $promote_price);
            $price_info = get_goods_one_attr_price($row['goods_id'], $warehouse_id, $area_id, $price_other);
            $row = !empty($row) ? array_merge($row, $price_info) : $row;
            $promote_price = $row['promote_price'];

            $goods[$idx]['id'] = $row['goods_id'];
            $goods[$idx]['name'] = $row['goods_name'];
            $goods[$idx]['sales_volume'] = $row['sales_volume'];
            $goods[$idx]['comments_number'] = $row['comments_number'];
            /* 折扣节省计算 by ecmoban start */
            if ($row['market_price'] > 0) {
                $discount_arr = get_discount($row['goods_id']); //函数get_discount参数goods_id
            }
            $goods[$idx]['zhekou'] = $discount_arr['discount'];  //zhekou
            $goods[$idx]['jiesheng'] = $discount_arr['jiesheng']; //jiesheng
            /* 折扣节省计算 by ecmoban end */
            $goods[$idx]['brief'] = $row['goods_brief'];
            $goods[$idx]['brand_name'] = $row['brand_name'];
            $goods[$idx]['short_style_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                    sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['shop_price'] = price_format($row['shop_price']);
            $goods[$idx]['promote_price'] = ($promote_price > 0 ? price_format($promote_price) : '');
            $goods[$idx]['thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
            $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

            $idx++;
        }
    }

    return $goods;
}

/**
 * 获得指定的品牌下的商品总数
 *
 * @access  private
 * @param   integer     $brand_id
 * @param   integer     $cate
 * @return  integer
 */
function goods_count_by_brand($brand_id, $cate = 0, $act = '', $is_ship = '', $price_min = '', $price_max = '', $warehouse_id = 0, $area_id = 0, $is_self) {
    $cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';

    //ecmoban模板堂 --zhuo start
    $leftJoin = '';
    if ($GLOBALS['_CFG']['open_area_goods'] == 1) {
        $leftJoin .= " left join " . $GLOBALS['ecs']->table('link_area_goods') . " as lag on g.goods_id = lag.goods_id ";
        $cate_where .= " and lag.region_id = '$area_id' ";
    }

    $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_goods') . " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_area_goods') . " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

    if ($GLOBALS['_CFG']['review_goods'] == 1) {
        $cate_where .= ' AND g.review_status > 2 ';
    }

    //by wang
    $tag_where = '';
    if ($is_ship == "is_shipping") {
        $tag_where .= " AND g.is_shipping = 1 ";
    }
    if ($is_self == 1) {
        $tag_where .= " AND g.user_id = 0 ";
    }

    if ($price_min) {
        $tag_where.=" AND g.shop_price >= $price_min ";
    }

    if ($price_max) {
        $tag_where.=" AND g.shop_price <= $price_max ";
    }

    if ($sort == 'last_update') {
        $sort = 'g.last_update';
    }

    // 新品 热销过滤
    switch ($act) {
        case 'new':
            $tag_where.=" AND g.is_new = 1 ";
            break;
        case 'hot':
            $tag_where.=" AND g.is_hot = 1 ";
            break;
        case 'best':
            $tag_where.=" AND g.is_best = 1 ";
            break;

        default:
            break;
    }
    //ecmoban模板堂 --zhuo end	

    /* 获得商品列表 */
    $sql = 'SELECT count(g.goods_id) FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            $leftJoin .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            //关联品牌
            'LEFT JOIN ' . $GLOBALS['ecs']->table('link_brand') . ' AS lb ' .
            "ON lb.bid = g.brand_id " .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('merchants_shop_brand') . ' AS msb ' .
            "ON msb.bid = lb.bid " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ((g.brand_id = '$brand_id' AND g.user_id = 0) OR (lb.brand_id = '$brand_id' AND g.brand_id = lb.bid AND msb.audit_status = 1)) $cate_where $tag_where ";
    return $GLOBALS['db']->getOne($sql);
}

/**
 * 获得品牌下的商品
 *
 * @access  private
 * @param   integer  $brand_id
 * @return  array
 */
function brand_get_goods($brand_id, $cate, $size, $page, $sort, $order, $warehouse_id = 0, $area_id = 0, $act = '', $is_ship = '', $price_min, $price_max, $is_self) {
    $cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';

    //ecmoban模板堂 --zhuo start
    $leftJoin = '';
    if ($GLOBALS['_CFG']['open_area_goods'] == 1) {
        $leftJoin .= " left join " . $GLOBALS['ecs']->table('link_area_goods') . " as lag on g.goods_id = lag.goods_id ";
        $cate_where .= " and lag.region_id = '$area_id' ";
    }

    $shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_goods') . " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
    $leftJoin .= " left join " . $GLOBALS['ecs']->table('warehouse_area_goods') . " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";

    if ($GLOBALS['_CFG']['review_goods'] == 1) {
        $cate_where .= ' AND g.review_status > 2 ';
    }

    //by wang
    $tag_where = '';
    if ($is_ship == "is_shipping") {
        $tag_where .= " AND g.is_shipping = 1 ";
    }

    if ($is_self == 1) {
        $tag_where .= " AND g.user_id = 0 ";
    }

    if ($price_min) {
        $tag_where.=" AND g.shop_price >= $price_min ";
    }

    if ($price_max) {
        $tag_where.=" AND g.shop_price <= $price_max ";
    }

    if ($sort == 'last_update') {
        $sort = 'g.last_update';
    }
    //ecmoban模板堂 --zhuo end	

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.user_id, g.goods_name,g.is_hot, g.market_price, g.shop_price AS org_price,g.sales_volume, ' .
            $shop_price .
            "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, " .
            "IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, " .
            'g.promote_start_date, g.promote_end_date, g.is_promote, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
            $leftJoin .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
            "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            //关联品牌
            'LEFT JOIN ' . $GLOBALS['ecs']->table('link_brand') . ' AS lb ' .
            "ON lb.bid = g.brand_id " .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('merchants_shop_brand') . ' AS msb ' .
            "ON msb.bid = lb.bid AND msb.audit_status = 1 " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND ((g.brand_id = '$brand_id' AND g.user_id = 0) OR (lb.brand_id = '$brand_id' AND g.user_id > 0)) $cate_where $tag_where " .
            "ORDER BY $sort $order";

    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res)) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        } else {
            $promote_price = 0;
        }
        $price_other = array('market_price' => $row['market_price'], 'org_price' => $row['org_price'], 'shop_price' => $row['shop_price'], 'promote_price' => $promote_price);
        $price_info = get_goods_one_attr_price($row['goods_id'], $warehouse_id, $area_id, $price_other);
        $row = !empty($row) ? array_merge($row, $price_info) : $row;
        $promote_price = $row['promote_price'];

        $arr[$row['goods_id']]['goods_id'] = $row['goods_id'];
        if ($GLOBALS['display'] == 'grid') {
            $arr[$row['goods_id']]['goods_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        } else {
            $arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
        }
        $arr[$row['goods_id']]['sales_volume'] = $row['sales_volume'];
        $arr[$row['goods_id']]['is_promote'] = $row['is_promote'];
        $arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price'] = price_format($row['shop_price']);
        $arr[$row['goods_id']]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$row['goods_id']]['goods_brief'] = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);

        $arr[$row['goods_id']]['count'] = selled_count($row['goods_id']);
        $arr[$row['goods_id']]['is_hot'] = $row['is_hot'];

        //ecmoban模板堂 --zhuo start
        $sql = "select * from " . $GLOBALS['ecs']->table('seller_shopinfo') . " where ru_id='" . $row['user_id'] . "'";
        $basic_info = $GLOBALS['db']->getRow($sql);
        $arr[$row['goods_id']]['kf_type'] = $basic_info['kf_type'];

        /* 处理客服旺旺数组 by kong */
        if ($basic_info['kf_ww']) {
            $kf_ww = array_filter(preg_split('/\s+/', $basic_info['kf_ww']));
            $kf_ww = explode("|", $kf_ww[0]);
            if (!empty($kf_ww[1])) {
                $arr[$row['goods_id']]['kf_ww'] = $kf_ww[1];
            } else {
                $arr[$row['goods_id']]['kf_ww'] = "";
            }
        } else {
            $arr[$row['goods_id']]['kf_ww'] = "";
        }
        /* 处理客服QQ数组 by kong */
        if ($basic_info['kf_qq']) {
            $kf_qq = array_filter(preg_split('/\s+/', $basic_info['kf_qq']));
            $kf_qq = explode("|", $kf_qq[0]);
            if (!empty($kf_qq[1])) {
                $arr[$row['goods_id']]['kf_qq'] = $kf_qq[1];
            } else {
                $arr[$row['goods_id']]['kf_qq'] = "";
            }
        } else {
            $arr[$row['goods_id']]['kf_qq'] = "";
        }

        $arr[$row['goods_id']]['rz_shopName'] = get_shop_name($row['user_id'], 1); //店铺名称	

        $build_uri = array(
            'urid' => $row['user_id'],
            'append' => $arr[$row['goods_id']]['rz_shopName']
        );

        $domain_url = get_seller_domain_url($row['user_id'], $build_uri);
        $arr[$row['goods_id']]['store_url'] = $domain_url['domain_name'];

        $goods_id = $row['goods_id'];
        $count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('comment') . " where id_value ='$goods_id' AND status = 1 AND parent_id = 0");
        $arr[$row['goods_id']]['review_count'] = $count;
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
    }

    return $arr;
}

/**
 * 获得与指定品牌相关的分类
 *
 * @access  public
 * @param   integer $brand
 * @return  array
 */
function brand_related_cat($brand, $act) {
    $arr[] = array('cat_id' => 0,
        'cat_name' => $GLOBALS['_LANG']['all_category'],
        'url' => build_uri('brandn', array('bid' => $brand, 'act' => $act), $GLOBALS['_LANG']['all_category']));

    $sql = "SELECT c.cat_id, c.cat_name, COUNT(g.goods_id) AS goods_count FROM " .
            $GLOBALS['ecs']->table('category') . " AS c, " .
            $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE g.brand_id = '$brand' AND c.cat_id = g.cat_id " .
            "GROUP BY g.cat_id";
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res)) {
        $row['url'] = build_uri('brandn', array('cid' => $row['cat_id'], 'bid' => $brand, 'act' => $act), $row['cat_name']);
        $arr[] = $row;
    }
    
    return $arr;
}

?>