<?php

/** 
 * ECSHOP 管理中心优惠活动管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: favourable.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_goods.php');

include_once(ROOT_PATH . '/includes/cls_image.php'); 
$image = new cls_image($_CFG['bgcolor']);
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"bonus");
$exc = new exchange($ecs->table('favourable_activity'), $db, 'act_id', 'act_name');

$adminru = get_admin_ru_id();

//ecmoban模板堂 --zhuo start
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
} 	
//ecmoban模板堂 --zhuo end

$smarty->assign('controller', basename(PHP_SELF,'.php'));
/*------------------------------------------------------ */
//-- 活动列表页
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    admin_priv('favourable');

    /* 模板赋值 */
    $smarty->assign('full_page',   1);
    $smarty->assign('ur_here',     $_LANG['favourable_list']);
    $smarty->assign('action_link', array('href' => 'favourable.php?act=add', 'text' => $_LANG['add_favourable']));

    $list = favourable_list($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    $smarty->assign('favourable_list', $list['item']);
    $smarty->assign('filter',          $list['filter']);
    $smarty->assign('record_count',    $list['record_count']);
    $smarty->assign('page_count',      $list['page_count']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('favourable_list.dwt');
}

/*------------------------------------------------------ */
//-- 分页、排序、查询
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $list = favourable_list($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    $smarty->assign('favourable_list', $list['item']);
    $smarty->assign('filter',          $list['filter']);
    $smarty->assign('record_count',    $list['record_count']);
    $smarty->assign('page_count',      $list['page_count']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('favourable_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('favourable');

    $id = intval($_GET['id']);
    $favourable = favourable_info($id);
    if (empty($favourable))
    {
        make_json_error($_LANG['favourable_not_exist']);
    }
    $name = $favourable['act_name'];
    get_del_batch('', $id, array('activity_thumb'), 'act_id', 'favourable_activity', 1); //删除图片
    
    $exc->drop($id);

    /* 记日志 */
    admin_log($name, 'remove', 'favourable');

    /* 清除缓存 */
    clear_cache_files();

    $url = 'favourable.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 取得要操作的记录编号 */
    if (empty($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_record_selected']);
    }
    else
    {
        /* 检查权限 */
        admin_priv('favourable');

        $ids = $_POST['checkboxes'];

        if (isset($_POST['drop']))
        {
            get_del_batch($ids, '', array('activity_thumb'), 'act_id', 'favourable_activity', 1);
            
            /* 删除记录 */
            $sql = "DELETE FROM " . $ecs->table('favourable_activity') .
                    " WHERE act_id " . db_create_in($ids);
            $db->query($sql);

            /* 记日志 */
            admin_log('', 'batch_remove', 'favourable');

            /* 清除缓存 */
            clear_cache_files();

            $links[] = array('text' => $_LANG['back_favourable_list'], 'href' => 'favourable.php?act=list&' . list_link_postfix());
            sys_msg($_LANG['batch_drop_ok']);
        }
    }
}

/*------------------------------------------------------ */
//-- 修改排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('favourable');

    $id  = intval($_POST['id']);
    $val = intval($_POST['val']);

    $sql = "UPDATE " . $ecs->table('favourable_activity') .
            " SET sort_order = '$val'" .
            " WHERE act_id = '$id' LIMIT 1";
    $db->query($sql);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 添加、编辑
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('favourable');

    $smarty->assign('menu_select', array('action'=>'03_promotion', 'current'=>'12_favourable'));
    
    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_action', $is_add ? 'insert' : 'update');

    /* 初始化、取得优惠活动信息 */
    if ($is_add)
    {
	$ru_id = $adminru['ru_id']; //ecmoban模板堂 --zhuo
        $favourable = array(
            'act_id'        => 0,
            'act_name'      => '',
            'start_time'    => date('Y-m-d H:i:s', time() + 86400),
            'end_time'      => date('Y-m-d H:i:s', time() + 4 * 86400),
            'user_rank'     => '',
            'act_range'     => FAR_ALL,
            'act_range_ext' => '',
            'min_amount'    => 0,
            'max_amount'    => 0,
            'act_type'      => FAT_GOODS,
            'act_type_ext'  => 0,
            'user_id'       => $ru_id, //ecmoban模板堂 --zhuo
            'gift'          => array()
        );
    }
    else
    {
        if (empty($_GET['id']))
        {
            sys_msg('invalid param');
        }
        $id = intval($_GET['id']);
        $favourable = favourable_info($id);
        if (empty($favourable))
        {
            sys_msg($_LANG['favourable_not_exist']);
        }
		
        $ru_id = $favourable['user_id']; //ecmoban模板堂 --zhuo
    }
    
    $smarty->assign('favourable', $favourable);

    /* 取得用户等级 */
    $user_rank_list = array();
    $user_rank_list[] = array(
        'rank_id'   => 0,
        'rank_name' => $_LANG['not_user'],
        'checked'   => strpos(',' . $favourable['user_rank'] . ',', ',0,') !== false
    );
    $sql = "SELECT rank_id, rank_name FROM " . $ecs->table('user_rank');
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        $row['checked'] = strpos(',' . $favourable['user_rank'] . ',', ',' . $row['rank_id']. ',') !== false;
        $user_rank_list[] = $row;
    }
    $smarty->assign('user_rank_list', $user_rank_list);

    /* 取得优惠范围 */
    $act_range_ext = array();
    if ($favourable['act_range'] != FAR_ALL && !empty($favourable['act_range_ext']))
    {
        if ($favourable['act_range'] == FAR_CATEGORY)
        {
            $sql = "SELECT cat_id AS id, cat_name AS name FROM " . $ecs->table('category') .
                " WHERE cat_id " . db_create_in($favourable['act_range_ext']);
        }
        elseif ($favourable['act_range'] == FAR_BRAND)
        {
            //ecmoban模板堂 --zhuo satrt
            if($ru_id > 0){
                    $sql = "SELECT bid AS id, brandName AS name FROM " . $ecs->table('merchants_shop_brand') .
                            " WHERE bid " . db_create_in($favourable['act_range_ext']) . " AND user_id = '$ru_id' AND audit_status = 1";
            }else{
                    $sql = "SELECT brand_id AS id, brand_name AS name FROM " . $ecs->table('brand') .
                            " WHERE brand_id " . db_create_in($favourable['act_range_ext']);
            }
            //ecmoban模板堂 --zhuo end	
        }
        else
        {
            $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $ecs->table('goods') .
                " WHERE goods_id " . db_create_in($favourable['act_range_ext']);
        }
        $act_range_ext = $db->getAll($sql);
    }
    $smarty->assign('act_range_ext', $act_range_ext);

    /* 赋值时间控件的语言 */
    $smarty->assign('cfg_lang', $_CFG['lang']);

    /* 显示模板 */
    if ($is_add)
    {
        $smarty->assign('ur_here', $_LANG['add_favourable']);
    }
    else
    {
        $smarty->assign('ur_here', $_LANG['edit_favourable']);
    }
    $href = 'favourable.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['favourable_list']));
    assign_query_info();
    $smarty->display('favourable_info.dwt');
}

/*------------------------------------------------------ */
//-- 添加、编辑后提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('favourable');

    $ru_id = isset($_REQUEST['ru_id']) ? intval($_REQUEST['ru_id']) : 0;
    
    /* 是否添加 */
    $is_add = $_REQUEST['act'] == 'insert';

    // 验证商品是否只参与一个活动 by qin
    $now = gmtime();
    $act_id = intval($_POST['id']);
    $act_range = intval($_POST['act_range']);
    $act_range_ext = intval($_POST['act_range_ext']);
    
    // 按分类优惠活动包含的所有商品
    $act_range_ext_cat = get_act_range_ext(FAR_CATEGORY, $act_id);
    $goods_list_cat = get_range_goods(FAR_CATEGORY, $act_range_ext_cat, 'cat_id');
    
    // 按品牌优惠活动包含的所有商品
    $act_range_ext_brand = get_act_range_ext(FAR_BRAND, $act_id);
    $goods_list_brand = get_range_goods(FAR_BRAND, $act_range_ext_brand, 'brand_id');
    
    // 按商品优惠活动包含的所有商品
    $act_range_ext_goods = get_act_range_ext(FAR_GOODS, $act_id);
    $goods_list_goods = get_range_goods(FAR_GOODS, $act_range_ext_goods, 'goods_id');
    
//    print_arr($goods_list_goods);
    switch ($act_range)
    {
        case 0:// 全部商品
            
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('favourable_activity'). " WHERE start_time <= '$now' AND end_time >= '$now' ";
            $num = $GLOBALS['db']->getOne($sql);
            if ($num)
            {
                sys_msg('商品已经参与其他优惠活动', 1);
            }
            break;
        case 1: // 按分类
            $goods_list_cat_new = get_range_goods(FAR_CATEGORY, $_POST['act_range_ext'], 'cat_id');
            $arr = array_intersect($goods_list_cat, $goods_list_cat_new);
//            print_arr($goods_list_brand);
            $arr1 = array_intersect($goods_list_brand, $goods_list_cat_new);
            $arr2 = array_intersect($goods_list_goods, $goods_list_cat_new);
            if ($arr || $arr1 || $arr2)
            {
                sys_msg('分类商品已经参与其他优惠活动', 1);
            }
            break;
        case 2: // 按品牌
            $goods_list_brand_new = get_range_goods(FAR_BRAND, $_POST['act_range_ext'], 'brand_id');
//            print_arr($goods_list_brand_new);
            $arr = array_intersect($goods_list_cat, $goods_list_brand_new);
            $arr1 = array_intersect($goods_list_brand, $goods_list_brand_new);
            $arr2 = array_intersect($goods_list_brand_new, $goods_list_goods);
//            print_arr($arr2);
            if ($arr || $arr1 || $arr2)
            {
                sys_msg('品牌商品已经参与其他优惠活动', 1);
            }
            break;
        case 3: // 按商品
            $goods_list_goods_new = get_range_goods(FAR_GOODS, $_POST['act_range_ext'], 'goods_id');
//            print_arr($_POST['act_range_ext']);
            $arr = array_intersect($goods_list_cat, $goods_list_goods_new);
            $arr1 = array_intersect($goods_list_brand, $goods_list_goods_new);
            $arr2 = array_intersect($goods_list_goods, $goods_list_goods_new);
//            print_arr($arr2);
            if ($arr || $arr1 || $arr2)
            {
                sys_msg('商品已经参与其他优惠活动', 1);
            }
            break;

        default:
            break;
    }
    
    /* 检查名称是否重复 */
    $act_name = sub_str($_POST['act_name'], 255, false);
    if (!$exc->is_only('act_name', $act_name, intval($_POST['id'])))
    {
        sys_msg($_LANG['act_name_exists']);
    }

    /* 检查享受优惠的会员等级 */
    if (!isset($_POST['user_rank']))
    {
        sys_msg($_LANG['pls_set_user_rank']);
    }

    /* 检查优惠范围扩展信息 */
    if (intval($_POST['act_range']) > 0 && !isset($_POST['act_range_ext']))
    {
        sys_msg($_LANG['pls_set_act_range']);
    }

    /* 检查金额上下限 */
    $min_amount = floatval($_POST['min_amount']) >= 0 ? floatval($_POST['min_amount']) : 0;
    $max_amount = floatval($_POST['max_amount']) >= 0 ? floatval($_POST['max_amount']) : 0;
    if ($max_amount > 0 && $min_amount > $max_amount)
    {
        sys_msg($_LANG['amount_error']);
    }

    /* 取得赠品 */
    $gift = array();
    if (intval($_POST['act_type']) == FAT_GOODS && isset($_POST['gift_id']))
    {
        foreach ($_POST['gift_id'] as $key => $id)
        {
            $gift[] = array('id' => $id, 'name' => $_POST['gift_name'][$key], 'price' => $_POST['gift_price'][$key]);
        }
    }
    
    /* 提交值 */
    $favourable = array(
        'act_id'        => intval($_POST['id']),
        'act_name'      => $act_name,
        'start_time'    => local_strtotime($_POST['start_time']),
        'end_time'      => local_strtotime($_POST['end_time']),
        'user_rank'     => isset($_POST['user_rank']) ? join(',', $_POST['user_rank']) : '0',
        'act_range'     => intval($_POST['act_range']),
        'act_range_ext' => intval($_POST['act_range']) == 0 ? '' : join(',', $_POST['act_range_ext']),
        'min_amount'    => floatval($_POST['min_amount']),
        'max_amount'    => floatval($_POST['max_amount']),
        'act_type'      => intval($_POST['act_type']),
        'act_type_ext'  => floatval($_POST['act_type_ext']),
        'gift'          => serialize($gift),
        'userFav_type'  => intval($_POST['userFav_type'])
    );
    if ($favourable['act_type'] == FAT_GOODS)
    {
        $favourable['act_type_ext'] = round($favourable['act_type_ext']);
    }
    
    $activity_thumb = $image->upload_image($_FILES['activity_thumb'], 'activity_thumb');  //图片存放地址
    
    get_oss_add_file(array($activity_thumb));
        
    /* 保存数据 */
    if ($is_add)
    {
        //ecmoban模板堂 -- zhuo
        $favourable['user_id']          =   $adminru['ru_id'];
	$favourable['activity_thumb']   =   $activity_thumb;
        $db->autoExecute($ecs->table('favourable_activity'), $favourable, 'INSERT');
        $favourable['act_id'] = $db->insert_id();
    }
    else
    {
        if(!empty($activity_thumb)){
            $favourable['activity_thumb'] = $activity_thumb;
        }
        $db->autoExecute($ecs->table('favourable_activity'), $favourable, 'UPDATE', "act_id = '$favourable[act_id]'");
    }

    /* 记日志 */
    if ($is_add)
    {
        admin_log($favourable['act_name'], 'add', 'favourable');
    }
    else
    {
        admin_log($favourable['act_name'], 'edit', 'favourable');
    }

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    if ($is_add)
    {
        $links = array(
            array('href' => 'favourable.php?act=add', 'text' => $_LANG['continue_add_favourable']),
            array('href' => 'favourable.php?act=list', 'text' => $_LANG['back_favourable_list'])
        );
        sys_msg($_LANG['add_favourable_ok'], 0, $links);
    }
    else
    {
        $links = array(
            array('href' => 'favourable.php?act=edit&id=' . $favourable['act_id'] . "&ru_id=" . $ru_id, 'text' => $_LANG['edit_favourable']),
            array('href' => 'favourable.php?act=list&' . list_link_postfix(), 'text' => $_LANG['back_favourable_list'])
        );
        sys_msg($_LANG['edit_favourable_ok'], 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 删除活动图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_thumb')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $act_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $ru_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;

    get_del_batch('', $act_id, array('activity_thumb'), 'act_id', 'favourable_activity', 1); //删除图片
    $sql = "UPDATE " .$ecs->table('favourable_activity'). " SET activity_thumb = '' WHERE act_id = '$act_id'";
    $db->query($sql);
    
    $link= array(array('text' => $_LANG['edit_favourable'], 'href' => 'favourable.php?act=edit&id=' . $act_id . "&ru_id=" . $ru_id), array('text' => $_LANG['favourable_list'], 'href' => 'favourable.php?act=list'));
    sys_msg($_LANG['drop_activity_thumb_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search')
{
    /* 检查权限 */
    check_authz_json('favourable');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
    $filter->keyword = json_str_iconv($filter->keyword);
	$ru_id = $filter->ru_id; //ecmoban模板堂 --zhuo
	
    if ($filter->act_range == FAR_ALL)
    {
        $arr[0] = array(
            'id'   => 0,
            'name' => $_LANG['js_languages']['all_need_not_search']
        );
    }
    elseif ($filter->act_range == FAR_CATEGORY)
    {
		//ecmoban模板堂 --zhuo satrt
        if($ru_id > 0){	
			$arr = get_user_cat_list($ru_id);
			$arr = get_user_cat_search($ru_id, $filter->keyword, $arr);
			$arr = array_values($arr);
		}else{
			$sql = "SELECT c.cat_id AS id, c.cat_name AS name FROM " . $ecs->table('category') . " as c " . 
					" WHERE c.cat_name LIKE '%" . mysqli_like_quote($filter->keyword) . "%' " .
					"AND (SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('merchants_category') . " as mc WHERE mc.cat_id = c.cat_id) < 1 " . 
					"LIMIT 50";
			$arr = $db->getAll($sql);
		}
		//ecmoban模板堂 --zhuo end
    }
    elseif ($filter->act_range == FAR_BRAND)
    {
		//ecmoban模板堂 --zhuo satrt
		if($ru_id > 0){
			$sql = "SELECT bid AS id, brandName AS name FROM " . $ecs->table('merchants_shop_brand') .
					" WHERE brandName LIKE '%" . mysqli_like_quote($filter->keyword) . "%' AND user_id = '$ru_id' AND audit_status = 1 LIMIT 50";
			$arr = $db->getAll($sql);	
		}else{
			$sql = "SELECT brand_id AS id, brand_name AS name FROM " . $ecs->table('brand') .
            		" WHERE brand_name LIKE '%" . mysqli_like_quote($filter->keyword) . "%' LIMIT 50";
        	$arr = $db->getAll($sql);
		}	
		//ecmoban模板堂 --zhuo end
    }
    else
    {
        $sql = "SELECT goods_id AS id, goods_name AS name FROM " . $ecs->table('goods') .
            " WHERE (goods_name LIKE '%" . mysqli_like_quote($filter->keyword) . "%'" .
            " OR goods_sn LIKE '%" . mysqli_like_quote($filter->keyword) . "%')  AND user_id = '$ru_id' LIMIT 50";
			
        $arr = $db->getAll($sql);
    }
    if (empty($arr))
    {
        $arr = array(0 => array(
            'id'   => 0,
            'name' => $_LANG['search_result_empty']
        ));
    }

    make_json_result($arr);
}

/*
 * 取得优惠活动列表
 * @return   array
 */
function favourable_list($ru_id)
{
	
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['is_going']   = empty($_REQUEST['is_going']) ? 0 : 1;
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'fa.act_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['use_type']    = empty($_REQUEST['use_type']) ? 0 : intval($_REQUEST['use_type']);
        
        $where = "";
        //ecmoban模板堂 --zhuo start
        if($filter['use_type'] == 1){ //自营
            $where .= " AND fa.user_id = 0 AND fa.userFav_type = 0";
        }else if($filter['use_type'] == 2){ //商家   
            $where .= " AND fa.user_id > 0 AND fa.userFav_type = 0";
        }else if($filter['use_type'] == 3){ //全场    
            $where .= " AND fa.userFav_type = 1";
        }else if($filter['use_type'] == 4){ //商家自主使用    
            $where .= " AND fa.user_id = '$ru_id' AND fa.userFav_type = 0";
        }else{
            if($ru_id > 0){
                $where .= " AND (fa.user_id = '$ru_id' OR fa.userFav_type = 1)";
            }
        }
        //ecmoban模板堂 --zhuo end
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = empty($_REQUEST['store_search']) ? 0 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] !=0){
           if($ru_id == 0){ 
               
               if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }
               
                if($filter['store_search'] == 1){
                    $where .= " AND fa.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }
                
                if($filter['store_search'] > 1){
                    $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                              " WHERE msi.user_id = fa.user_id $store_where) > 0 ";
                }
           }
        }
        //管理员查询的权限 -- 店铺查询 end
        
        if (!empty($filter['keyword']))
        {
            $where .= " AND fa.act_name LIKE '%" . mysqli_like_quote($filter['keyword']) . "%'";
        }
        if ($filter['is_going'])
        {
            $now = gmtime();
            $where .= " AND fa.start_time <= '$now' AND fa.end_time >= '$now' ";
        }

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('favourable_activity') ." AS fa ".
                " WHERE 1 $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT fa.* ".
                "FROM " . $GLOBALS['ecs']->table('favourable_activity') ." AS fa ".
                " WHERE 1 $where ".
                " ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->query($sql);

    $list = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['start_time']  = local_date('Y-m-d H:i:s', $row['start_time']);
        $row['end_time']    = local_date('Y-m-d H:i:s', $row['end_time']);
        $row['user_name'] = get_shop_name($row['user_id'], 1); //ecmoban模板堂 --zhuo

        $list[] = $row;
    }

    return array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


//查询商家所有分类 ecmoban模板堂 --zhuo start
function get_user_cat_list($ru_id){
	$sql = "SELECT user_shopMain_category FROM " .$GLOBALS['ecs']->table('merchants_shop_information'). " WHERE user_id = '$ru_id'";
	$user_cat = $GLOBALS['db']->getOne($sql);
	
	$arr = $new_arr = array();
	if(!empty($user_cat)){
		$user_cat = explode("-", $user_cat);
		
		foreach($user_cat as $key=>$row){
			$arr[$key] = explode(":", $row);
		}
		
		foreach($arr as $key=>$row){
			foreach($row as $ck=>$rows){
				if($ck > 0){
					$arr[$key][$ck] = explode(",", $rows);
				}
			}
		}
		
		$arr = get_level_three_cat1($arr);
		$arr = arr_foreach($arr);
		$arr = array_unique($arr);
		
		foreach($arr as $key=>$row){
			$new_arr[$key]['id'] 	= $row;
			$new_arr[$key]['name'] 	= $GLOBALS['db']->getOne("SELECT cat_name as name FROM " .$GLOBALS['ecs']->table('category'). " WHERE cat_id = '$row'");
		}
		
		$new_arr = get_array_sort($new_arr, 'id');
		return $new_arr;
	}
} 

function get_level_three_cat1($arr){
	$new_arr = array();
	
	foreach($arr as $key=>$row){
		$new_arr[$key]['cat'] = $row[0];
		$new_arr[$key]['cat_child'] = $row[1];
		$new_arr[$key]['cat_child_three'] = get_level_three_cat2($row[1]);
	}
	
	foreach($new_arr as $key=>$row){
		$new_arr[$key] = array_values($row);
	}
	
	return $new_arr;
}

function get_level_three_cat2($arr){
	$new_arr = array();
	
	foreach($arr as $key=>$row){
		$new_arr[$key] = get_cat_list_three($row);
	}
	
	$new_arr = arr_foreach($new_arr);
	return $new_arr;
}

function get_cat_list_three($arr){
	$res = $GLOBALS['db']->getAll("SELECT cat_id FROM " .$GLOBALS['ecs']->table('category'). " WHERE parent_id = '$arr'");
	
	$arr = array();
	foreach($res as $key=>$row){
		$arr[$key] = $row['cat_id'];
	}
	
	return $arr;	
}

function get_user_cat_search($ru_id, $keyword = '', $arr = array()){
	
	$sql = "SELECT mc.cat_id as id, c.cat_name as name FROM " .$GLOBALS['ecs']->table('merchants_category'). " as mc, " .$GLOBALS['ecs']->table('category'). " as c " . 
			" WHERE mc.cat_id = c.cat_id AND user_id = '$ru_id'"; 	
	$res = $GLOBALS['db']->getAll($sql);
	
	$arr = array_values($arr);
	
	if($res){
		$arr = array_merge($arr, $res);
	}
	
	$new_arr = array();
	if(!empty($keyword)){
		foreach($arr as $key=>$row){
			$pos  =  strpos ($row['name'] ,  $keyword);
			if ($pos ===  false) {
				unset($row);
			}else{
				$new_arr[$key] = $row;
			}
		}
	}else{
		$new_arr = $arr;
	}
	
	return $new_arr;
}
//查询商家所有分类 ecmoban模板堂 --zhuo end

// 同一类型优惠范围（分类或品牌） -qin
function get_act_range_ext($act_range, $act_id)
{
    if ($act_range > 0)
    {
        $a_range = " AND act_range = '$act_range' ";
    }
    $now = gmtime();
    // 商家id
    $user_id = $GLOBALS['db']->getOne("SELECT ru_id FROM ".$GLOBALS['ecs']->table('admin_user')." WHERE user_id = '$_SESSION[seller_id]' ");
    $sql = "SELECT act_range_ext " .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE start_time <= '$now' AND end_time >= '$now' " . $a_range ." AND act_id <> '$act_id' AND user_id = '$user_id' ";
    $res = $GLOBALS['db']->getAll($sql);
    $arr=array();
    foreach ($res as $key => $row)
    {
        $arr = array_merge($arr, explode(',', $row['act_range_ext']));
    }
    
//    print_arr($arr);
    return array_unique($arr);
}

// 获取分类或品牌下得所有商品
function get_range_goods($act_range, $act_range_ext_list, $create_in)
{
    if (empty($act_range_ext_list))
    {
        return array();
    }
    
    switch ($act_range)
    {
        case FAR_CATEGORY:
            $id_list = array();
            foreach ($act_range_ext_list as $id)
            {
                $cat_keys = get_array_keys_cat(intval($id));
                $id_list = array_merge($id_list, $cat_keys);
            }
            break;
        case FAR_BRAND:
            $id_list = $act_range_ext_list;
            break;
        case FAR_GOODS:
            $id_list = $act_range_ext_list;
            break;

        default:
            break;
    }
    
    $sql = "SELECT goods_id FROM ".$GLOBALS['ecs']->table('goods')." WHERE ".  db_create_in($id_list, $create_in);
    $res = $GLOBALS['db']->query($sql);
    $arr_goods_id = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr_goods_id[] = $row['goods_id'];
    }
    return $arr_goods_id;
}
?>