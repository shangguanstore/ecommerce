<?php

/**
 * ECSHOP 用户评论管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: comment_manage.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("comment_seller"), $db, 'sid', 'order_id');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
} 	
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 满意度列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('comment_seller');
    
    $smarty->assign('action_link', array('href' => 'comment_seller.php?act=baseline', 'text' => $_LANG['seller_industry_baseline']));
    
    $smarty->assign('ur_here',      $_LANG['comment_seller_rank']);
    $smarty->assign('full_page',    1);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $list = comment_seller_list(); 

    $smarty->assign('rank_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('comment_seller_rank.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、搜索、排序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
    /* 检查权限 */
    admin_priv('comment_seller');
    
    $list = comment_seller_list();
    
    $smarty->assign('rank_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('comment_seller_rank.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 设置商家评分基线
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'baseline')
{
    /* 检查权限 */
    admin_priv('comment_seller');
    $smarty->assign('action_link', array('href' => 'comment_seller.php?act=list'));
    $smarty->assign('ur_here',  "商品 - ".$_LANG['seller_industry_baseline'] );
    $sql = "SELECT goods, service, shipping FROM " .$GLOBALS['ecs']->table('comment_baseline'). " WHERE 1";
    $baseline = $GLOBALS['db']->getRow($sql);
    
    $smarty->assign('baseline',   $baseline);
    $smarty->assign('form_action',   'insert_update');
    
    assign_query_info();
    $smarty->display('comment_baseline.dwt');
}

/*------------------------------------------------------ */
//-- 添加/编辑商家评分基线
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_update')
{
    /* 检查权限 */
    admin_priv('comment_seller');
    
    $other['goods'] = !empty($_REQUEST['goods_baseline']) ? trim($_REQUEST['goods_baseline']) : '';
    $other['service'] = !empty($_REQUEST['service_baseline']) ? trim($_REQUEST['service_baseline']) : '';
    $other['shipping'] = !empty($_REQUEST['shipping_baseline']) ? trim($_REQUEST['shipping_baseline']) : '';
    
    $sql = "SELECT id FROM " .$GLOBALS['ecs']->table('comment_baseline'). " WHERE 1";
    $res = $GLOBALS['db']->getOne($sql, true);
    
    if($res){
        $db->autoExecute($ecs->table('comment_baseline'), $other, 'UPDATE', " 1 ");
    }else{
        $db->autoExecute($ecs->table('comment_baseline'), $other, 'INSERT');
    }
    
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'comment_seller.php?act=baseline');
    sys_msg($_LANG['success'], 0, $link);
    
    assign_query_info();
    $smarty->display('comment_baseline.dwt');
}

/*------------------------------------------------------ */
//-- 删除满意度
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('comment_seller');

    $id = intval($_GET['id']);
    if ($exc->drop($id))
    {
        admin_log($id,'remove','comment_seller');
        clear_cache_files();
    }

    $url = 'comment_seller.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/**
 * 商家满意度评分
 * @access  public
 * @return  array
 */
function comment_seller_list()
{
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = "1";
    $where .= (!empty($filter['keywords'])) ? " AND (u.user_name LIKE '%" . mysqli_like_quote($filter['keywords']) . "%') " : '';
    
    //管理员查询的权限 -- 店铺查询 start
    $filter['store_search'] = !isset($_REQUEST['store_search']) ? -1 : intval($_REQUEST['store_search']);
    $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
    $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';

    $store_where = '';
    $store_search_where = '';
    if($filter['store_search'] > -1){
       if($ru_id == 0){ 
            if($filter['store_search'] > 0){
                if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }

                if($filter['store_search'] == 1){
                    $where .= " AND s.ru_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }

                if($filter['store_search'] > 1){
                    $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                              " WHERE msi.user_id = s.ru_id $store_where) > 0 ";
                }
            }else{
                $where .= " AND s.ru_id = 0";
            }    
       }
    }
    //管理员查询的权限 -- 店铺查询 end
    
    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('comment_seller'). " AS s ".
            "LEFT JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON u.user_id = s.user_id ".
            "LEFT JOIN ". $GLOBALS['ecs']->table('seller_shopinfo') ." AS sp ON sp.ru_id = s.ru_id ".
            "WHERE $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 获取评论数据 */
    $arr = array();
    $sql  = "SELECT s.*,o.order_sn,u.user_name FROM " .$GLOBALS['ecs']->table('comment_seller'). " AS s " .
            "LEFT JOIN ". $GLOBALS['ecs']->table('order_info') ." AS o ON o.order_id = s.order_id ".
            "LEFT JOIN ". $GLOBALS['ecs']->table('seller_shopinfo') ." AS sp ON sp.ru_id = s.ru_id ".
            "LEFT JOIN ". $GLOBALS['ecs']->table('users') ." AS u ON u.user_id = s.user_id ".
            "WHERE $where ORDER BY $filter[sort_by] $filter[sort_order] ".
            "LIMIT ". $filter['start'] .", $filter[page_size]";
    
    $res  = $GLOBALS['db']->query($sql);
    
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        $row['ru_name'] = get_shop_name($row['ru_id'], 1); 

        $arr[] = $row;
    }

    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>