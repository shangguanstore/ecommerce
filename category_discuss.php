<?php

/**
 * ECSHOP 购物流程
 * ============================================================================
 * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: zblikai $
 * $Id: flow.php 15632 2009-02-20 03:58:31Z zblikai $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
//正则去掉js代码
$preg = "/<script[\s\S]*?<\/script>/i";
$id  = isset($_REQUEST['id']) ? strtolower($_REQUEST['id']) : 0;
$id  =!empty($id) ? preg_replace($preg,"",stripslashes($id)): 0;

$goods_id = intval($id);
$cache_id = sprintf('%X', crc32($goods_id . '-' . $_SESSION['user_rank'] . '-' . $_CFG['lang']));

//ecmoban模板堂 --zhuo start
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

$history_goods = get_history_goods($goods_id, $region_id, $area_id);
$smarty->assign('history_goods',       $history_goods);                                   // 商品浏览历史

$goodsInfo = get_goods_info($goods_id, $region_id, $area_id);
$goodsInfo['goods_price'] = price_format($goodsInfo['goods_price']);
$smarty->assign('goodsInfo',  $goodsInfo);

//评分 start
$mc_all = ments_count_all($goods_id);       //总条数
$mc_one = ments_count_rank_num($goods_id,1);		//一颗星
$mc_two = ments_count_rank_num($goods_id,2);	    //两颗星	
$mc_three = ments_count_rank_num($goods_id,3);   	//三颗星
$mc_four = ments_count_rank_num($goods_id,4);		//四颗星
$mc_five = ments_count_rank_num($goods_id,5);		//五颗星
$comment_all = get_conments_stars($mc_all,$mc_one,$mc_two,$mc_three,$mc_four,$mc_five);

$smarty->assign('comment_all',  $comment_all); 

if (!$smarty->is_cached('category_discuss.dwt', $cache_id))
{
    $smarty->assign('goods_id', $goods_id);
	
    assign_template();
    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
    
    $categories_pro = get_category_tree_leve_one();
    $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版

    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL
	
    $smarty->assign('helps',           get_shop_help());       // 网店帮助
    //$smarty->assign('top_goods',       get_top10());           // 销售排行
    
    /* 验证码相关设置 */
    if ((intval($_CFG['captcha']) & CAPTCHA_REGISTER) && gd_version() > 0)
    {
        $smarty->assign('enabled_captcha', 1);
        $smarty->assign('rand',            mt_rand());
    }

    $smarty->assign('shop_notice',     $_CFG['shop_notice']);       // 商店公告

}

$discuss_list = get_discuss_all_list($goods_id);
$smarty->assign('discuss_list',       $discuss_list);           

$all_count = get_discuss_type_count($goods_id); //帖子总数
$t_count = get_discuss_type_count($goods_id, 1); //讨论帖总数
$w_count = get_discuss_type_count($goods_id, 2); //问答帖总数
$q_count = get_discuss_type_count($goods_id, 3); //圈子帖总数
$s_count = get_commentImg_count($goods_id); //晒单帖总数

$smarty->assign('all_count',       $all_count);   
$smarty->assign('t_count',       $t_count);    
$smarty->assign('w_count',       $w_count);    
$smarty->assign('q_count',       $q_count);    
$smarty->assign('s_count',       $s_count);  

//热门话题
$discuss_hot = get_discuss_all_list($goods_id, 0, 1, 10, 0, 'dis_browse_num');
$smarty->assign('hot_list',       $discuss_hot);    

$smarty->assign('user_id',       $user_id);  
$smarty->display('category_discuss.dwt', $cache_id);

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

?>