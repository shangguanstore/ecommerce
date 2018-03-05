<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

$article_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : $_CFG['marticle_id'];

/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('merchants.dwt'))
{
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置
    
    $categories_pro = get_category_tree_leve_one();
    $smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
	
    //ecmoban模板堂 --zhuo start

    $marticle = explode(',',$_CFG['marticle']);

    $article_menu1 = get_merchants_article_menu($marticle[0]);
    $article_menu2 = get_merchants_article_menu($marticle[1]);

    $article_info = get_merchants_article_info($article_id);

    for($i=1;$i<=$_CFG['auction_ad'];$i++){
            $ad_arr .= "'merch".$i.",";
    }

    $smarty->assign('adarr',       $ad_arr); // 分类广告位
    $smarty->assign('article',         $article_info);  // 文章内容
    $smarty->assign('article_menu1',         $article_menu1);  // 文章列表
    $smarty->assign('article_menu2',         $article_menu2);  // 文章列表
    $smarty->assign('article_id',         $article_id);  // 文章ID
    $smarty->assign('marticle',         $marticle[0]); 
    //ecmoban模板堂 --zhuo end

    $smarty->assign('helps',      get_shop_help());       // 网店帮助
   
    /* 页面中的动态内容 */
    assign_dynamic('merchants');
}

$smarty->display('merchants.dwt');

?>