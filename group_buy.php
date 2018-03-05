<?php

/**
 * ECSHOP 团购商品前台文件
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: group_buy.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

//ecmoban模板堂 --zhuo start
require(ROOT_PATH . 'includes/lib_area.php');  //ecmoban模板堂 --zhuo
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

$keywords   = !empty($_REQUEST['keywords'])   ? htmlspecialchars(trim($_REQUEST['keywords'])):'';

if(isset($_REQUEST['keywords'])){
    clear_all_files();
}

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : 0;

/*------------------------------------------------------ */
//-- act 操作项的初始化
/*------------------------------------------------------ */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
/*------------------------------------------------------ */
//-- 团购商品 --> 团购活动仓库设置
/*------------------------------------------------------ */
if (!empty($_REQUEST['act']) && ($_REQUEST['act'] == 'in_warehouse'))
{
    include ('includes/cls_json.php');
    $json = new JSON();
    $res = array('err_msg' => '', 'result' => '', 'qty' => 1);
    clear_cache_files();
    setcookie('region_id', $pid, gmtime() + (3600 * 24 * 30), $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
    setcookie('regionId', $pid, gmtime() + (3600 * 24 * 30), $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
    $area_region = 0;
    setcookie('area_region', $area_region, gmtime() + (3600 * 24 * 30), $GLOBALS['cookie_path'], $GLOBALS['cookie_domain']);
    $res['goods_id'] = $goods_id;
    exit;
    $json = new JSON();
    die($json->encode($res));
}
/*------------------------------------------------------ */
//-- 团购商品 --> 团购活动商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	//瀑布流 by wu start
	$smarty->assign('category_load_type', $_CFG['category_load_type']);
	$smarty->assign('query_string', preg_replace('/act=\w+&?/', '', $_SERVER['QUERY_STRING']));
	//瀑布流 by wu end
	
    $cat_id       = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
    /* 初始化分页信息 */
    $page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
    $size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10; /* 取得每页记录数 */
    $size = 20;
    
    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
	
    if($_REQUEST['sort'] == 'comments_number'){
            $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
    }else{
            $default_sort_order_type   = 'act_id';
    }

    $sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('act_id', 'goods_id', 'sales_volume', 'comments_number'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
    $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
    
    $children = get_children($cat_id);

    /* 取得团购活动总数 */
    $count = group_buy_count($children, $keywords);
    if ($count > 0)
    {
        
        /* 计算总页数 */
        $page_count = ceil($count / $size);

        /* 取得当前页 */
        $page = isset($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
        $page = $page > $page_count ? $page_count : $page;

        /* 缓存id：语言 - 每页记录数 - 当前页 */
        $cache_id = $_CFG['lang'] . '-' . $cat_id  . '-' . $size . '-' . $page . '-' . $sort  .'-' . $order. '-' . $price_min  .'-' . $price_max. '-' .$keywords;
        $cache_id = sprintf('%X', crc32($cache_id));
    }
    else
    {
        /* 缓存id：语言 */
        $cache_id = $_CFG['lang'];
        $cache_id = sprintf('%X', crc32($cache_id));
    }

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('group_buy_list.dwt', $cache_id))
    {
        if ($count > 0)
        {
            /* 取得当前页的团购活动 */
            $gb_list = group_buy_list($children, $size, $page, $keywords, $sort, $order);
//            print_arr($gb_list);
            $smarty->assign('gb_list',  $gb_list);
                
			//瀑布流 by wu start
			if(!$_CFG['category_load_type'])
			{
				/* 设置分页链接 */
				$pager = get_pager('group_buy.php', array('act' => 'list','keywords' => $keywords,'sort' => $sort,'order' => $order), $count, $page, $size);
				$smarty->assign('pager', $pager);
			}
			//瀑布流 by wu end			
        }

        /* 模板赋值 */
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $position = assign_ur_here();
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        
        $categories_pro = get_category_tree_leve_one();
        $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
        
        $smarty->assign('brand_list',      get_brands());
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        //$smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        $smarty->assign('feed_url',         ($_CFG['rewrite'] == 1) ? "feed-typegroup_buy.xml" : 'feed.php?type=group_buy'); // RSS URL
	
        $smarty->assign('price_max',        $price_max);
        $smarty->assign('price_min',        $price_min);
        
        //顶级分类
        $category_list = cat_list();
        $smarty->assign('category_list',        $category_list);
        
        assign_dynamic('group_buy_list');
    }
    
    /* 显示模板 */
    $smarty->display('group_buy_list.dwt', $cache_id);
}

/*------------------------------------------------------ */
//-- 瀑布流 by wu
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'load_more_goods')
{
    $cat_id       = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
    /* 初始化分页信息 */
    $page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
    $size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10; /* 取得每页记录数 */
    $size = 20;
    
    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
	
    if($_REQUEST['sort'] == 'comments_number'){
            $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
    }else{
            $default_sort_order_type   = 'act_id';
    }

    $sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('act_id', 'goods_id', 'sales_volume', 'comments_number'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
    $order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
    
    $children = get_children($cat_id);

    /* 取得团购活动总数 */
    $count = group_buy_count($children, $keywords);

	if ($count > 0)
	{
		/* 取得当前页的团购活动 */
		$gb_list = group_buy_list($children, $size, $page, $keywords, $sort, $order);
		$smarty->assign('gb_list',  $gb_list); 
		
		$smarty->assign('type',  'group_buy'); 
		$result = array('error' => 0,'message' => '','cat_goods'=>'','best_goods'=>'');
		$result['cat_goods'] = html_entity_decode($smarty->fetch('library/more_goods_page.lbi'));
		die(json_encode($result));		
	} 
}

//属性图片
elseif($_REQUEST['act'] == 'getInfo')
{
    
    require_once(ROOT_PATH .'includes/cls_json.php');
     
    $json = new JSON();
    
    $result = array('error' => 0, 'message'=> '');
    
    $attr_id = $_POST['attr_id'];
    
	$sql = "SELECT attr_gallery_flie FROM " .$GLOBALS['ecs']->table('goods_attr')." WHERE goods_attr_id = '$attr_id' and goods_id = '$goods_id'";
    $row = $db->getRow($sql);
				
	$result['t_img'] = $row['attr_gallery_flie'];
	
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 团购商品 --> 商品详情
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'view')
{
    /* 取得参数：团购活动id */
    $group_buy_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }
	$smarty->assign('comment_percent',     comment_percent($goods_id)); 
    /* 取得团购活动信息 */
    $group_buy = group_buy_info($group_buy_id);
//    print_arr($group_buy);
	//ecmoban模板堂 --zhuo start
	$first_month_day = local_mktime(0,0,0,date('m'),1,date('Y')); //本月第一天
	$last_month_day = local_mktime(0,0,0,date('m'),date('t'),date('Y')) + 24 * 60 * 60 - 1; //本月最后一天
	
	$group_list = get_month_day_start_end_goods($group_buy_id, $first_month_day, $last_month_day);
	$smarty->assign('group_list', $group_list);
	//ecmoban模板堂 --zhuo end
	
    // 该商铺的其他团购 qin
    
    $merchant_group = get_merchant_group_goods($group_buy_id);    
    $smarty->assign('merchant_group_goods', $merchant_group);
    
    $smarty->assign('look_top',get_top_group_goods('click_count'));
    $smarty->assign('buy_top',get_top_group_goods('sales_volume'));

    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }
//    elseif ($group_buy['is_on_sale'] == 0 || $group_buy['is_alone_sale'] == 0)
//    {
//        header("Location: ./\n");
//        exit;
//    }

    /* 缓存id：语言，团购活动id，状态，（如果是进行中）当前数量和是否登录 */
    $cache_id = $_CFG['lang'] . '-' . $group_buy_id . '-' . $group_buy['status'].  gmtime();
    if ($group_buy['status'] == GBS_UNDER_WAY)
    {
        $cache_id = $cache_id . '-' . $group_buy['valid_goods'] . '-' . intval($_SESSION['user_id'] > 0);
    }
    $cache_id = sprintf('%X', crc32($cache_id));

    /* 如果没有缓存，生成缓存 */
    if (!$smarty->is_cached('group_buy_goods.dwt', $cache_id))
    {
        $group_buy['gmt_end_date'] = $group_buy['end_date'];
        $smarty->assign('group_buy', $group_buy);

        /* 取得团购商品信息 */
        $goods_id = $group_buy['goods_id'];
        $goods = goods_info($goods_id);

         /* 读评论信息 */
		$smarty->assign('id',           $goods_id);
		$smarty->assign('type',         0);

        if (empty($goods))
        {
            ecs_header("Location: ./\n");
            exit;
        }
        $goods['url'] = build_uri('goods', array('gid' => $goods_id), $goods['goods_name']);

        $smarty->assign('gb_goods', $goods);
        $properties = get_goods_properties($goods_id, $region_id, $area_id);  // 获得商品的规格和属性
        $smarty->assign('properties',          $properties['pro']);                              // 商品属性
        $smarty->assign('specification',       $properties['spe']);                              // 商品规格
        

        //模板赋值
        $smarty->assign('cfg', $_CFG);
        assign_template();
        $linked_goods = get_linked_goods($goods_id, $region_id, $area_id);
		
        $position = assign_ur_here(0, $goods['goods_name']);
        $smarty->assign('page_title', $position['title']);    // 页面标题
        $smarty->assign('ur_here',    $position['ur_here']);  // 当前位置
        
        $categories_pro = get_category_tree_leve_one();
        $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
        
        $smarty->assign('related_goods',       $linked_goods);                                   // 关联商品
        $smarty->assign('brand_list',      get_brands());
        $smarty->assign('helps',      get_shop_help());       // 网店帮助
        //$smarty->assign('top_goods',  get_top10());           // 销售排行
        $smarty->assign('promotion_info', get_promotion_info());
        
        if($area_info['region_id'] == NULL){
                $area_info['region_id'] = 0;
        }

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
        $smarty->assign('area_htmlType',  'group_buy'); 
        
        assign_dynamic('group_buy_goods');
    }
    
    $smarty->assign('category',        $goods_id);
    
    //更新商品点击次数
    $sql = 'UPDATE ' . $ecs->table('goods') . ' SET click_count = click_count + 1 '.
           "WHERE goods_id = '" . $group_buy['goods_id'] . "'";
    $db->query($sql);
    
    //评分 start
    $comment_all = get_comments_percent($goods_id);
    if($goods['user_id'] > 0){
            $merchants_goods_comment = get_merchants_goods_comment($goods['user_id']); //商家所有商品评分类型汇总
    }
    //评分 end 

    $smarty->assign('comment_all',  $comment_all); 
    $smarty->assign('merch_cmt',  $merchants_goods_comment);
    
    if($GLOBALS['_CFG']['customer_service'] == 0){
            $goods['user_id'] = 0;
    }

    $sql="select province, city, kf_type, kf_ww, kf_qq, shop_name from ".$ecs->table('seller_shopinfo')." where ru_id='" .$goods['user_id']. "'";
    $basic_info = $db->getRow($sql);

    $basic_date = array('region_name');
    $basic_info['province'] = get_table_date('region', "region_id = '" . $basic_info['province'] . "'", $basic_date, 2);
    $basic_info['city'] = get_table_date('region', "region_id= '" . $basic_info['city'] . "'", $basic_date, 2) . "市";
    
     /*处理客服旺旺数组 by kong*/
    if($basic_info['kf_ww']){
        $kf_ww=array_filter(preg_split('/\s+/', $basic_info['kf_ww']));
        $kf_ww=explode("|",$kf_ww[0]);
        if(!empty($kf_ww[1])){
            $basic_info['kf_ww'] = $kf_ww[1];
        }else{
            $basic_info['kf_ww'] ="";
        }
        
    }else{
        $basic_info['kf_ww'] ="";
    }
    /*处理客服QQ数组 by kong*/
    if($basic_info['kf_qq']){
        $kf_qq=array_filter(preg_split('/\s+/', $basic_info['kf_qq']));
        $kf_qq=explode("|",$kf_qq[0]);
        if(!empty($kf_qq[1])){
            $basic_info['kf_qq'] = $kf_qq[1];
        }else{
            $basic_info['kf_qq'] = "";
        }
        
    }else{
        $basic_info['kf_qq'] = "";
    }
        
    //获取商品时候收藏
    $group_buy['is_collect']='';
    if($_SESSION['user_id'] > 0){
        $sql=" SELECT rec_id FROM ".$ecs->table("collect_goods")." WHERE goods_id = '".$group_buy['goods_id']." ' AND  user_id = '".$_SESSION['user_id']."'";
        $group_buy['is_collect']=$db->getOne($sql);
    }

    /*  @author-bylu 判断当前商家是否允许"在线客服" start  */
    $goods_info=goods_info($goods_id);//通过商品ID获取到ru_id;
    $shop_information = get_shop_name($goods_info['user_id']);//通过ru_id获取到店铺信息;
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
    $shop_information['goods_id']=$goods_id;
    $smarty->assign('shop_information',$shop_information);
    /*  @author-bylu  end  */

    $smarty->assign('basic_info',  $basic_info);
//    print_arr($group_buy);
    $smarty->assign('goods', $group_buy);
    $smarty->assign('pictures',            get_goods_gallery($goods_id));                    // 商品相册
    $smarty->assign('now_time',  gmtime());           // 当前系统时间
    
    $linked_goods = get_linked_goods($goods_id, $region_id, $area_info['region_id']);
    $smarty->assign('related_goods',       $linked_goods);
    
    $history_goods = get_history_goods($goods_id, $region_id, $area_info['region_id']);
//    print_arr($history_goods);
    $smarty->assign('history_goods',       $history_goods); 
//    print_arr($linked_goods);
    
    $smarty->assign('region_id',       $region_id);
    $smarty->assign('area_id',       $area_id);
    
    $start_date = $group_buy['xiangou_start_date'];
    $end_date = $group_buy['xiangou_end_date'];
    $order_goods = get_for_purchasing_goods($start_date, $end_date, $goods_id, $_SESSION['user_id'], 'group_buy');
    
    $smarty->assign('xiangou',              $xiangou);
    $smarty->assign('orderG_number',              $order_goods['goods_number']);

    $smarty->assign('user_id', $_SESSION['user_id']);
    $smarty->display('group_buy_goods.dwt', $cache_id);
}

elseif ($_REQUEST['act'] == 'price')
{
    include('includes/cls_json.php');

    $json   = new JSON;
    $res    = array('err_msg' => '', 'err_no' => 0, 'result' => '', 'qty' => 1);

    $goods_id     = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0; //仓库管理的地区ID
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
        
        if(empty($prod)){ //当商品没有属性库存时
        	$attr_number = $goods['goods_number']; 
        }

        $attr_number = !empty($attr_number) ? $attr_number : 0;
        $res['attr_number'] = $attr_number;
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

    die($json->encode($res));
}

/*------------------------------------------------------ */
//-- 团购商品 --> 购买
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

    /* 查询：取得参数：团购活动id */
    $group_buy_id = isset($_POST['group_buy_id']) ? intval($_POST['group_buy_id']) : 0;
    if ($group_buy_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：取得数量 */
    $number = isset($_POST['number']) ? intval($_POST['number']) : 1;
    $number = $number < 1 ? 1 : $number;

    /* 查询：取得团购活动信息 */
    $group_buy = group_buy_info($group_buy_id, $number);
//    print_arr($group_buy);
    if (empty($group_buy))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 查询：检查团购活动是否是进行中 */
    if ($group_buy['status'] != GBS_UNDER_WAY)
    {
        show_message($_LANG['gb_error_status'], '', '', 'error');
    }
    
    /* 查询：取得团购商品信息 */
    $goods = goods_info($group_buy['goods_id'], $warehouse_id, $area_id);
    if (empty($goods))
    {
        ecs_header("Location: ./\n");
        exit;
    }
    
    $start_date = $group_buy['xiangou_start_date'];
    $end_date = $group_buy['xiangou_end_date'];
    $order_goods = get_for_purchasing_goods($start_date, $end_date, $group_buy['goods_id'], $_SESSION['user_id'], 'group_buy');
    $restrict_amount = $number + $order_goods['goods_number'];
    
   /* 查询：判断数量是否足够 */
    if($group_buy['restrict_amount'] > 0 && $restrict_amount > $group_buy['restrict_amount'])
    {
        show_message($_LANG['gb_error_restrict_amount'], '', '', 'error');
    }
    elseif ($group_buy['restrict_amount'] > 0 && ($number > ($group_buy['restrict_amount'] - $group_buy['valid_goods'])))
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

    /* 更新：清空购物车中所有团购商品 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    clear_cart(CART_GROUP_BUY_GOODS);
	
    //ecmoban模板堂 --zhuo start

    $area_info = get_area_info($province_id);
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
    $goods_price = $group_buy['deposit'] > 0 ? $group_buy['deposit'] : $group_buy['cur_price'];
    $cart = array(
        'user_id'        => $_SESSION['user_id'],
        'session_id'     => $sess,
        'goods_id'       => $group_buy['goods_id'],
        'product_id'     => $product_info['product_id'],
        'goods_sn'       => addslashes($goods['goods_sn']),
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_price'    => $goods_price,
        'goods_number'   => $number,
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $specs,
		//ecmoban模板堂 --zhuo start
		'ru_id'			 => $goods['user_id'],
		'warehouse_id'   => $region_id,
		'area_id'  		 => $area_id,
		//ecmoban模板堂 --zhuo end
        'is_real'        => $goods['is_real'],
        'extension_code' => addslashes($goods['extension_code']),
        'parent_id'      => 0,
        'rec_type'       => CART_GROUP_BUY_GOODS,
        'is_gift'        => 0
    );
    $db->autoExecute($ecs->table('cart'), $cart, 'INSERT');

    /* 更新：记录购物流程类型：团购 */
    $_SESSION['flow_type'] = CART_GROUP_BUY_GOODS;
    $_SESSION['extension_code'] = 'group_buy';
    $_SESSION['extension_id'] = $group_buy_id;

    /* 进入收货人页面 */
    $_SESSION['browse_trace'] = "group_buy";
    ecs_header("Location: ./flow.php?step=checkout&direct_shopping=5\n");
    exit;
}

/* 取得团购活动总数 */
function group_buy_count($children, $keywords)
{
    $now = gmtime();
    $where = '';
    
    $where .= " AND g.is_delete = 0 AND ".
             "($children OR " . get_extension_goods($children) . ')';
    
    if ($keywords)
    {
        $where = "AND (ga.act_name LIKE '%$keywords%' OR g.goods_name LIKE '%$keywords%') ";
    }
    $sql = "SELECT COUNT(*) " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') ." AS ga ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON ga.goods_id = g.goods_id " .
            "WHERE ga.act_type = '" . GAT_GROUP_BUY . "' " .
            "AND ga.start_time <= '$now' AND ga.is_finished < 3 " . $where;
    
    return $GLOBALS['db']->getOne($sql);
}

/**
 * 取得某页的所有团购活动
 * @param   int     $size   每页记录数
 * @param   int     $page   当前页
 * @return  array
 */
function group_buy_list($children, $size, $page, $keywords, $sort, $order)
{
    /* 取得团购活动 */
    $gb_list = array();
    $now = gmtime();
    $where = "";
    
    $where .= " AND g.is_delete = 0 AND ".
             "($children OR " . get_extension_goods($children) . ')';
    
    if ($keywords)
    {
        $where = "AND (b.act_name LIKE '%$keywords%' OR g.goods_name LIKE '%$keywords%') ";
    }
    if ($sort == 'comments_number')
    {
        $sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, b.act_id AS group_buy_id, g.market_price,".
                "b.start_time AS start_date, b.end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON b.goods_id = g.goods_id " .
            "WHERE b.act_type = '" . GAT_GROUP_BUY . "' $where " .
            "AND b.start_time <= '$now' AND b.is_finished < 3 ORDER BY g.".$sort.' '.$order;
    }  else
    {
        $sql = "SELECT b.*, IFNULL(g.goods_thumb, '') AS goods_thumb, b.act_id AS group_buy_id, g.market_price,".
                "b.start_time AS start_date, b.end_time AS end_date " .
            "FROM " . $GLOBALS['ecs']->table('goods_activity') . " AS b " .
                "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON b.goods_id = g.goods_id " .
            "WHERE b.act_type = '" . GAT_GROUP_BUY . "' $where " .
            "AND b.start_time <= '$now' AND b.is_finished < 3 ORDER BY b.".$sort.' '.$order;
    }
    
		//瀑布流 by wu start
		if(isset($_REQUEST['act']) && $_REQUEST['act'] == 'load_more_goods')
		{
			$start = intval($_REQUEST['goods_num']);
		}
		else
		{
			$start = ($page - 1) * $size;
		}
		$res = $GLOBALS['db']->selectLimit($sql, $size, $start);
		//瀑布流 by wu end
	
    //$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    while ($group_buy = $GLOBALS['db']->fetchRow($res))
    {
        
        $ext_info = unserialize($group_buy['ext_info']);
        $group_buy = array_merge($group_buy, $ext_info);
//print_arr($ext_info);
        /* 格式化时间 */
        $group_buy['formated_start_date']   = local_date($GLOBALS['_CFG']['time_format'], $group_buy['start_date']);
        $group_buy['formated_end_date']     = local_date($GLOBALS['_CFG']['time_format'], $group_buy['end_date']);
        $group_buy['is_end']     = $now > $group_buy['end_date'] ? 1 : 0 ;

        /* 格式化保证金 */
        $group_buy['formated_deposit'] = price_format($group_buy['deposit'], false);

        /* 处理价格阶梯 */
        $price_ladder = $group_buy['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder))
        {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
        }
        else
        {
            foreach ($price_ladder as $key => $amount_price)
            {
                $price_ladder[$key]['formated_price'] = price_format($amount_price['price']);
            }
        }
        
        $group_buy['price_ladder'] = $price_ladder;
	
        /*团购节省和折扣计算 by ecmoban start*/
        $price    = $group_buy['market_price']; //原价 
        $nowprice = $group_buy['price_ladder'][0]['price']; //现价
        $group_buy['jiesheng'] = $price-$nowprice; //节省金额 
        if($nowprice > 0)
        {
                $group_buy['zhekou'] = round(10 / ($price / $nowprice), 1);
        }
        else 
        { 
                $group_buy['zhekou'] = 0;
        }

        $stat = group_buy_stat($group_buy['act_id'], $ext_info['deposit']);
        $group_buy['cur_amount'] = $stat['valid_goods'];         // 当前数量
		
        $group_buy['goods_thumb'] = get_image_path($group_buy['goods_id'], $group_buy['goods_thumb'], true);
        
        /* 处理链接 */
        $group_buy['url'] = build_uri('group_buy', array('gbid'=>$group_buy['group_buy_id']));
        
        $mc_one = ments_count_rank_num($group_buy['goods_id'],1);		//一颗星
        $mc_two = ments_count_rank_num($group_buy['goods_id'],2);	    //两颗星	
        $mc_three = ments_count_rank_num($group_buy['goods_id'],3);   	//三颗星
        $mc_four = ments_count_rank_num($group_buy['goods_id'],4);		//四颗星
        $mc_five = ments_count_rank_num($group_buy['goods_id'],5);		//五颗星
        $group_buy['zconments'] = get_conments_stars($mc_all,$mc_one,$mc_two,$mc_three,$mc_four,$mc_five);
        
        /* 加入数组 */
        $gb_list[] = $group_buy;
    }
    
    return $gb_list;
}


/**
 * 获得指定商品的关联商品
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  array
 */
function get_linked_goods($goods_id, $warehouse_id = 0, $area_id = 0)
{
	//ecmoban模板堂 --zhuo start
	$leftJoin = '';	
	
	$shop_price = "wg.warehouse_price, wg.warehouse_promote_price, wag.region_price, wag.region_promote_price, g.model_price, g.model_attr, ";
	$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_goods'). " as wg on g.goods_id = wg.goods_id and wg.region_id = '$warehouse_id' ";
	$leftJoin .= " left join " .$GLOBALS['ecs']->table('warehouse_area_goods'). " as wag on g.goods_id = wag.goods_id and wag.region_id = '$area_id' ";
	//ecmoban模板堂 --zhuo end	
	
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.goods_img, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) AS org_price, ' .
                "IFNULL(mp.user_price, IF(g.model_price < 1, g.shop_price, IF(g.model_price < 2, wg.warehouse_price, wag.region_price)) * '$_SESSION[discount]') AS shop_price, ".
                'g.market_price, ' .
				'IF(g.model_price < 1, g.promote_price, IF(g.model_price < 2, wg.warehouse_promote_price, wag.region_promote_price)) as promote_price, ' . 
				' g.promote_start_date, g.promote_end_date ' .
            'FROM ' . $GLOBALS['ecs']->table('link_goods') . ' lg ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = lg.link_goods_id ' .
            "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
			$leftJoin. 		
            "WHERE lg.goods_id = '$goods_id' AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " .
            "LIMIT " . $GLOBALS['_CFG']['related_goods_number'];
    $res = $GLOBALS['db']->query($sql);	

	
    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		
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
		
        $arr[$row['goods_id']]['goods_id']     = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']   = $row['goods_name'];
		$arr[$row['goods_id']]['goods_brief']   = $row['goods_brief'];
        $arr[$row['goods_id']]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
            sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $arr[$row['goods_id']]['goods_thumb']  = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']    = get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
        $arr[$row['goods_id']]['shop_price']   = price_format($row['shop_price']);
        $arr[$row['goods_id']]['url']          = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);

        if ($row['promote_price'] > 0)
        {
            $arr[$row['goods_id']]['promote_price'] = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $arr[$row['goods_id']]['formated_promote_price'] = price_format($arr[$row['goods_id']]['promote_price']);
        }
        else
        {
            $arr[$row['goods_id']]['promote_price'] = 0;
        }
    }

    return $arr;
}

function get_top_group_goods($order)
{
    $sql = "SELECT ga.*, g.sales_volume, g.goods_thumb, g.goods_id FROM " . $GLOBALS['ecs']->table('goods_activity') . " ga"
            ." LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " g ON ga.goods_id = g.goods_id "
            . " WHERE ga.user_id = '$user_id' AND g.goods_id > 0 AND act_type = '" . GAT_GROUP_BUY . "' ORDER BY g.".$order." LIMIT 5 ";
    $look_top_list = $GLOBALS['db']->getAll($sql);

    foreach ($look_top_list as $key => $look_top)
    {
        $ext_info = unserialize($look_top['ext_info']);
        $look_top['ext_info'] = $ext_info;
        // 处理价格阶梯
        $price_ladder = $look_top['ext_info']['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder))
        {
                $price_ladder = array(array('amount' => 0, 'price' => 0));
        }
        else
        {
            foreach ($price_ladder as $k => $amount_price)
            {
                    $price_ladder[$k]['formated_price'] = price_format($amount_price['price'], false);
            }
        }
        $look_top['ext_info']['price_ladder'] = $price_ladder;

        // 计算当前价
        $cur_price  = $price_ladder[0]['price']; // 初始化

        foreach ($price_ladder as $amount_price)
        {
            if ($cur_amount >= $amount_price['amount'])
            {
                    $cur_price = $amount_price['price'];
            }
            else
            {
                    break;
            }
        }
        
        $look_top['goods_thumb']  = get_image_path($look_top['goods_id'], $look_top['goods_thumb'], true); 
        
        $look_top['ext_info']['cur_price'] = price_format($cur_price,false); //现价
        $look_top_list_1[$key] = $look_top;
    }
    
    return $look_top_list_1;
}

function get_merchant_group_goods($group_buy_id){
    $ru_id = $GLOBALS['db']->getOne("SELECT user_id FROM " .$GLOBALS['ecs']->table('goods_activity'). " WHERE act_id = '$group_buy_id'");
    $sql = "SELECT ga.act_id, ga.ext_info, ga.act_name, g.goods_thumb, g.sales_volume FROM " . $GLOBALS['ecs']->table('goods_activity') . " ga"
            ." LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " g ON ga.goods_id = g.goods_id "
            . " WHERE ga.user_id = '$ru_id' AND act_type = '" . GAT_GROUP_BUY . "' LIMIT 4 ";
    $merchant_group = $GLOBALS['db']->getAll($sql);
    
    foreach($merchant_group as $key=>$row){
        $ext_info = unserialize($row['ext_info']);
        $row = array_merge($row, $ext_info);
        $merchant_group[$key]['cur_price'] = $row['ext_info']['cur_price'];
        
        /* 处理价格阶梯 */
        $price_ladder = $row['price_ladder'];
        if (!is_array($price_ladder) || empty($price_ladder))
        {
            $price_ladder = array(array('amount' => 0, 'price' => 0));
        }
        else
        {
            foreach ($price_ladder as $k => $amount_price)
            {
                $price_ladder[$k]['formated_price'] = price_format($amount_price['price'], false);
            }
        }
        
        $merchant_group[$key]['shop_price'] = $price_ladder[0]['formated_price'];   
        $merchant_group[$key]['goods_thumb']  = get_image_path($row['goods_id'], $row['goods_thumb'], true); 
    }
    
    return $merchant_group;
}
?>