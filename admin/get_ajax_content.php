<?php

/*
 * ECSHOP 获取ajax数据
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

$_REQUEST['act'] = trim($_REQUEST['act']);

$data = array('error' => 0, 'message' => '', 'content' => '');

/* ------------------------------------------------------ */
//-- 获取下级分类列表
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_select_category') {
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $child_cat_id = empty($_REQUEST['child_cat_id']) ? 0 : intval($_REQUEST['child_cat_id']);
    $cat_level = empty($_REQUEST['cat_level']) ? 0 : intval($_REQUEST['cat_level']);
    $select_jsId = empty($_REQUEST['select_jsId']) ? 'cat_parent_id' : trim($_REQUEST['select_jsId']);
    $type = empty($_REQUEST['type']) ? 0 : intval($_REQUEST['type']);

    $content = insert_select_category($cat_id, $child_cat_id, $cat_level, $select_jsId, $type);
    if (!empty($content)) {
        $data['error'] = 1;
        $data['content'] = $content;
    }

    die(json_encode($data));
}

/* ------------------------------------------------------ */
//-- 获取筛选分类列表
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'filter_category') {
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $cat_type_show = empty($_REQUEST['cat_type_show']) ? 0 : intval($_REQUEST['cat_type_show']);
    $user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    //上级分类列表
    if($cat_type_show == 1){
        $parent_cat_list = get_seller_select_category($cat_id, 1, true, $user_id);
        $filter_category_navigation = get_seller_array_category_info($parent_cat_list);
    }else{
        $parent_cat_list = get_select_category($cat_id, 1, true);
        $filter_category_navigation = get_array_category_info($parent_cat_list);
    }
    
    $cat_nav = "";
    if ($filter_category_navigation) {
        foreach ($filter_category_navigation as $key => $val) {
            if ($key == 0) {
                $cat_nav .= $val['cat_name'];
            } elseif ($key > 0) {
                $cat_nav .= " > " . $val['cat_name'];
            }
        }
    } else {
        $cat_nav = "请选择分类";
    }
    $result['cat_nav'] = $cat_nav;

    //分类级别
    $cat_level = count($parent_cat_list);
    
    if($cat_type_show == 1){
        if ($cat_level <= 3) {
            $filter_category_list = get_seller_category_list($cat_id, 2, $user_id);
        } else {
            $filter_category_list = get_seller_category_list($cat_id, 0, $user_id);
            $cat_level -= 1;
        }
    }else{
        if ($cat_level <= 3) {
            $filter_category_list = get_category_list($cat_id, 2);
        } else {
            $filter_category_list = get_category_list($cat_id, 0);
            $cat_level -= 1;
        }
    }
    
    $smarty->assign('user_id', $user_id); //分类等级
    if ($user_id)
    {
        $smarty->assign('seller_cat_type_show', $cat_type_show);//分类等级
    }
    else
    {
        $smarty->assign('cat_type_show', $cat_type_show); //分类等级
    }

    $smarty->assign('filter_category_level', $cat_level); //分类等级
    
    if ($cat_type_show) {
        $smarty->assign('seller_filter_category_navigation', $filter_category_navigation);
        $smarty->assign('seller_filter_category_list', $filter_category_list);
        $result['content'] = $smarty->fetch('templates/library/filter_category_seller.lbi');
    } else {
        $smarty->assign('filter_category_navigation', $filter_category_navigation);
        $smarty->assign('filter_category_list', $filter_category_list);
        $result['content'] = $smarty->fetch('templates/library/filter_category.lbi');
    }

    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 获取品牌列表
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'search_brand_list') {
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');
    $smarty->assign('filter_brand_list', search_brand_list($goods_id));
    $result['content'] = $smarty->fetch('templates/library/search_brand_list.lbi');
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 获取筛选商品列表
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'filter_list') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $search_type = empty($_REQUEST['search_type']) ? '' : trim($_REQUEST['search_type']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    //筛选商品
    if ($search_type == "goods") {
        $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $brand_id = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
        $keyword = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);

        $filters['cat_id'] = $cat_id;
        $filters['brand_id'] = $brand_id;
        $filters['keyword'] = $keyword;
        $filters['sel_mode'] = 0;
        $filters['brand_keyword'] = "";
        $filters['exclude'] = "";
        $filters = $json->decode(json_encode($filters));

        $arr = get_goods_list($filters);
        $opt = array();

        foreach ($arr AS $key => $val) {
            $opt[] = array('value' => $val['goods_id'],
                'text' => $val['goods_name'],
                'data' => $val['shop_price']);
        }
        $filter_list = $opt;
    }

    //筛选文章
    elseif ($search_type == "article") {
        $title = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);

        $where = " WHERE cat_id > 0 ";
        if (!empty($title)) {
            $keyword = trim($filters['title']);
            $where .= " AND title LIKE '%" . mysqli_like_quote($title) . "%' ";
        }

        $sql = 'SELECT article_id, title FROM ' . $ecs->table('article') . $where .
                'ORDER BY article_id DESC LIMIT 50';
        $res = $db->query($sql);
        $arr = array();

        while ($row = $db->fetchRow($res)) {
            $arr[] = array('value' => $row['article_id'], 'text' => $row['title'], 'data' => '');
        }
        $filter_list = $arr;
    }

    //筛选地区
    elseif ($search_type == "area") {
        $ra_id = empty($_REQUEST['keyword']) ? 0 : intval($_REQUEST['keyword']);

        $arr = get_areaRegion_info_list($ra_id);
        $opt = array();

        foreach ($arr AS $key => $val) {
            $opt[] = array('value' => $val['region_id'],
                'text' => $val['region_name'],
                'data' => 0);
        }
        $filter_list = $opt;
    }

    //筛选商品类型
    elseif ($search_type == "goods_type") {
        $cat_id = empty($_REQUEST['keyword']) ? 0 : intval($_REQUEST['keyword']);

        $goods_fields = my_array_merge($_LANG['custom'], get_attributes($cat_id));
        $opt = array();

        foreach ($goods_fields AS $key => $val) {
            $opt[] = array('value' => $key,
                'text' => $val,
                'data' => 0);
        }
        $filter_list = $opt;
    }

    /* ------------------------------------------------------ */
    //-- 搜索内容
    /* ------------------------------------------------------ */ 
    elseif ($search_type == 'get_content') {
        $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $brand_id = empty($_REQUEST['brand_id']) ? 0 : intval($_REQUEST['brand_id']);
        $keyword = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);

        $filters['cat_id'] = $cat_id;
        $filters['brand_id'] = $brand_id;
        $filters['keyword'] = $keyword;
        $filters = $json->decode(json_encode($filters));

        $arr = get_brandlist($filters);
        $opt = array();

        foreach ($arr AS $key => $val) {
            $opt[] = array(
                'value' => $val['brand_id'],
                'text' => $val['brand_name'],
                'data' => $val['brand_id']
            );
        }

        $filter_list = $opt;
    }

    $smarty->assign('filter_list', $filter_list);
    $result['content'] = $smarty->fetch('templates/library/move_left.lbi');
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 增加一个楼层内容
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_floor_content') {
    $fittings = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $value = empty($_REQUEST['group']) ? '' : trim($_REQUEST['group']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $value = explode("|", $value);
    $filename = $value[0];
    $cat_id = $value[1];
    $region = $value[2];
    $curr_template = $_CFG['template'];

    $sql = "select cat_name from " . $GLOBALS['ecs']->table('category') . " where cat_id = '$cat_id'";
    $cat_name = $GLOBALS['db']->getOne($sql, true);

    foreach ($fittings AS $val) {
        $brand_name = $GLOBALS['db']->getOne("SELECT brand_name FROM " . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$val' LIMIT 1");

        $sql = "select fb_id from " . $GLOBALS['ecs']->table('floor_content') . " where brand_id = '$val' AND filename = '$filename' AND id = '$cat_id' AND region = '$region' AND theme = '$curr_template'";
        if (!$GLOBALS['db']->getOne($sql)) {
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('floor_content') .
                    "(filename, region, id, id_name, brand_id, brand_name, theme) " .
                    "VALUES('$filename', '$region', '$cat_id', '$cat_name', '$val','$brand_name','$curr_template')"; //by mike add
            $GLOBALS['db']->query($sql, 'SILENT');
        }
    }

    $arr = get_floor_content($curr_template, $filename, $cat_id, $region);
    $options = array();

    foreach ($arr AS $val) {
        $options[] = array(
            'value' => $val['fb_id'],
            'text' => '[' . $val['id_name'] . ']' . $val['brand_name'],
            'data' => $val['id']
        );
    }

    $smarty->assign('filter_result', $options);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 删除一个楼层内容
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_floor_content') {
    $fb_id = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $curr_template = $_CFG['template'];
    $options = array();

    if (count($fb_id) > 0) {
        $fb_id = implode(',', $fb_id);
        $sql = "SELECT filename,region,id FROM " . $GLOBALS['ecs']->table('floor_content') . " WHERE fb_id " . db_create_in($fb_id) . " LIMIT 1";
        $floor_info = $GLOBALS['db']->getRow($sql);

        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('floor_content') . " WHERE fb_id " . db_create_in($fb_id);
        $GLOBALS['db']->query($sql);

        $arr = get_floor_content($curr_template, $floor_info['filename'], $floor_info['id'], $floor_info['region']);

        foreach ($arr AS $val) {
            $options[] = array(
                'value' => $val['fb_id'],
                'text' => '[' . $val['id_name'] . ']' . $val['brand_name'],
                'data' => $val['id']
            );
        }
    }

    $smarty->assign('filter_result', $options);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 把商品加入关联
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_link_goods') {
    $linked_array = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $is_double = empty($_REQUEST['is_single']) ? 1 : 0;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    foreach ($linked_array AS $val) {
        if ($is_double) {
            /* 双向关联 */
            $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                    "VALUES ('$val', '$goods_id', '$is_double', '$_SESSION[admin_id]')";
            $db->query($sql, 'SILENT');
        }

        $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                "VALUES ('$goods_id', '$val', '$is_double', '$_SESSION[admin_id]')";
        $db->query($sql, 'SILENT');
    }

    $linked_goods = get_linked_goods($goods_id);
    $options = array();

    foreach ($linked_goods AS $val) {
        $options[] = array('value' => $val['goods_id'],
            'text' => $val['goods_name'],
            'data' => '');
    }

    $smarty->assign('filter_result', $options);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 删除关联商品
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_link_goods') {
    $drop_goods = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $drop_goods_ids = db_create_in($drop_goods);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $is_signle = empty($_REQUEST['is_single']) ? 0 : 1;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    if (!$is_signle) {
        $sql = "DELETE FROM " . $ecs->table('link_goods') .
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    } else {
        $sql = "UPDATE " . $ecs->table('link_goods') . " SET is_double = 0 " .
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    }
    if ($goods_id == 0) {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $sql = "DELETE FROM " . $ecs->table('link_goods') .
            " WHERE goods_id = '$goods_id' AND link_goods_id " . $drop_goods_ids;
    if ($goods_id == 0) {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $linked_goods = get_linked_goods($goods_id);
    $options = array();

    foreach ($linked_goods AS $val) {
        $options[] = array(
            'value' => $val['goods_id'],
            'text' => $val['goods_name'],
            'data' => '');
    }

    $smarty->assign('filter_result', $options);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 增加一个配件
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_group_goods') {
    $fittings = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $price = empty($_REQUEST['price2']) ? 0 : floatval($_REQUEST['price2']);
    $group_id = empty($_REQUEST['group2']) ? 1 : intval($_REQUEST['group2']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $sql = "select count(*) from " . $ecs->table('group_goods') . " where parent_id = '$goods_id' and group_id = '$group_id' and admin_id = '" . $_SESSION['admin_id'] . "'";
    $groupCount = $db->getOne($sql);

    $message = "";
    if ($groupCount < 1000) {
        foreach ($fittings AS $val) {
            $sql = "SELECT id FROM " . $ecs->table('group_goods') . " WHERE parent_id = '$goods_id' AND goods_id = '$val' AND group_id = '$group_id'";
            if (!$db->getOne($sql)) {
                $sql = "INSERT INTO " . $ecs->table('group_goods') . " (parent_id, goods_id, goods_price, admin_id, group_id) " .
                        "VALUES ('$goods_id', '$val', '$price', '$_SESSION[admin_id]', '$group_id')"; //by mike add
                $db->query($sql, 'SILENT');
            }
        }


        $error = 0;
    } else {
        $error = 1;
        $message = "一组配件只能添加五个商品，如需添加则删除该组其它配件商品";
    }

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['goods_id'],
            'text' => '[' . $val['group_name'] . ']' . $val['goods_name'],
            'data' => '');
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 删除一个配件
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_group_goods') {
    $fittings = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $price = empty($_REQUEST['price2']) ? 0 : floatval($_REQUEST['price2']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $sql = "DELETE FROM " . $ecs->table('group_goods') .
            " WHERE parent_id='$goods_id' AND " . db_create_in($fittings, 'goods_id');
    if ($goods_id == 0) {
        $sql .= " AND admin_id = '$_SESSION[admin_id]'";
    }
    $db->query($sql);

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['goods_id'],
            'text' => '[' . $val['group_name'] . ']' . $val['goods_name'],
            'data' => '');
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 添加关联文章
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_goods_article') {
    $articles = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    foreach ($articles AS $val) {
        $sql = "INSERT INTO " . $ecs->table('goods_article') . " (goods_id, article_id, admin_id) " .
                "VALUES ('$goods_id', '$val', '$_SESSION[admin_id]')";
        $db->query($sql);
    }

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['article_id'],
            'text' => $val['title'],
            'data' => '');
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 删除关联文章
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_goods_article') {
    $articles = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE " . db_create_in($articles, "article_id") . " AND goods_id = '$goods_id'";
    $db->query($sql);

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['article_id'],
            'text' => $val['title'],
            'data' => '');
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 增加一个关联地区
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_area_goods') {
    $fittings = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = '$goods_id'";
    $ru_id = $GLOBALS['db']->getOne($sql);

    foreach ($fittings AS $val) {
        $sql = "INSERT INTO " . $ecs->table('link_area_goods') . " (goods_id, region_id, ru_id) " .
                "VALUES ('$goods_id', '$val', '$ru_id')";
        $db->query($sql, 'SILENT');
    }

    $arr = get_area_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['region_id'],
            'text' => $val['region_name'],
            'data' => 0);
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 把商品加入商品柜
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_win_goods') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $linked_array = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $id = empty($_REQUEST['win_id']) ? 0 : intval($_REQUEST['win_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $sql = "select win_goods from " . $GLOBALS['ecs']->table('seller_shopwindow') . " where id='$id'";
    $win_goods = $GLOBALS['db']->getOne($sql);
    
    foreach ($linked_array AS $val) {
        if (!strstr($win_goods, $val) && !empty($val)) {
            $win_goods.=!empty($win_goods) ? ',' . $val : $val;
        }
    }
    
    $sql = "update " . $GLOBALS['ecs']->table('seller_shopwindow') . " set win_goods='$win_goods' where id='$id'";
    $GLOBALS['db']->query($sql);

    $win_goods = get_win_goods($id);

    $smarty->assign('filter_result', $win_goods);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 把商品移除商品柜
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_win_goods') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $drop_goods = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $id = empty($_REQUEST['win_id']) ? 0 : intval($_REQUEST['win_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $win_goods = $GLOBALS['db']->getOne("select win_goods from " . $GLOBALS['ecs']->table('seller_shopwindow') . " where id='$id'");
    $win_goods_arr = explode(',', $win_goods);

    foreach ($drop_goods AS $val) {
        if (strstr($win_goods, $val) && !empty($val)) {
            $key = array_search($val, $win_goods_arr);
            if ($key !== false) {
                array_splice($win_goods_arr, $key, 1);
            }
        }
    }
    $new_win_goods = '';
    foreach ($win_goods_arr AS $val) {
        if (!strstr($new_win_goods, $val) && !empty($val)) {
            $new_win_goods.=!empty($new_win_goods) ? ',' . $val : $val;
        }
    }

    $sql = "update " . $GLOBALS['ecs']->table('seller_shopwindow') . " set win_goods='$new_win_goods' where id='$id'";
    $GLOBALS['db']->query($sql);

    $win_goods = get_win_goods($id);

    $smarty->assign('filter_result', $win_goods);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 删除一个关联地区
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_area_goods') {
    $drop_goods = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $drop_goods_ids = db_create_in($drop_goods);
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $sql = "DELETE FROM " . $ecs->table('link_area_goods') . " WHERE region_id" . $drop_goods_ids . " and goods_id = '$goods_id'";
    if ($goods_id == 0) {
        $adminru = get_admin_ru_id();
        $ru_id = $adminru['ru_id'];

        $sql .= " AND ru_id = '$ru_id'";
    }
    $db->query($sql);

    $arr = get_area_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val) {
        $opt[] = array('value' => $val['region_id'],
            'text' => $val['region_name'],
            'data' => 0);
    }

    $smarty->assign('filter_result', $opt);
    $result['content'] = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    die(json_encode($result));
}

/* ------------------------------------------------------ */
//-- 添加统一详情商品
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_link_desc') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $linked_array = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    get_add_edit_link_desc($linked_array, 0, $id);
    $linked_goods = get_linked_goods_desc();

    $options = array();
    foreach ($linked_goods AS $val) {
        $options[] = array('value' => $val['goods_id'],
            'text' => $val['goods_name'],
            'data' => '');
    }

    $smarty->assign('filter_result', $options);
    $content = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    make_json_result($content);
}

/* ------------------------------------------------------ */
//-- 删除统一详情商品
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'drop_link_desc') {
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $drop_goods = empty($_REQUEST['value']) ? array() : explode(",", $_REQUEST['value']);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    get_add_edit_link_desc($drop_goods, 1, $id);
    $linked_goods = get_linked_goods_desc();

    $options = array();
    foreach ($linked_goods AS $val) {
        $options[] = array(
            'value' => $val['goods_id'],
            'text' => $val['goods_name'],
            'data' => '');
    }

    if (empty($linked_goods)) {
        $sql = "delete from " . $ecs->table('link_desc_temporary') . " where 1";
        $db->query($sql);
    }

    $smarty->assign('filter_result', $options);
    $content = $smarty->fetch('templates/library/move_right.lbi');

    clear_cache_files();
    make_json_result($content);
}

/* ------------------------------------------------------ */
//-- 上传图片
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'upload_img') {
    include_once(ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);
    require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

    $act_type = empty($_REQUEST['type']) ? '' : trim($_REQUEST['type']);
    $id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);

    $result = array('error' => 0, 'pic' => '', 'name' => '');
    $typeArr = array("jpg", "png", "gif", "jepg"); //允许上传文件格式

    if (isset($_POST)) {
        $name = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        $name_tmp = $_FILES['file']['tmp_name'];
        if (empty($name)) {
            $result['error'] = "您还未选择图片！";
        }
        $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型		
        if (!in_array($type, $typeArr)) {
            $result['error'] = "清上传jpg,jepg,png或gif类型的图片！";
        }
    }

    if ($act_type == 'goods_img') {
        /* 开始处理 start */
        $_FILES['goods_img'] = $_FILES['file'];
        $proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0) ? false : true;
        $_POST['auto_thumb'] = 1; //自动生成缩略图
        $_REQUEST['goods_id'] = $id;
        $goods_id = $id;
        /* 开始处理 end */

        /* 处理商品图片 */
        $goods_img = '';  // 初始化商品图片
        $goods_thumb = '';  // 初始化商品缩略图
        $original_img = '';  // 初始化原始图片
        $old_original_img = '';  // 初始化原始图片旧图
        // 如果上传了商品图片，相应处理
        if (($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') or ( ($_POST['goods_img_url'] != $_LANG['lab_picture_url'] && $_POST['goods_img_url'] != 'http://') && $is_url_goods_img = 1)) {

            if (empty($is_url_goods_img)) {
                $original_img = $image->upload_image($_FILES['goods_img']); // 原始图片
            } elseif (copy(trim($_POST['goods_img_url']), ROOT_PATH . 'temp/' . basename($_POST['goods_img_url']))) {
                $original_img = 'temp/' . basename($_POST['goods_img_url']);
            }

            if ($original_img === false) {
                sys_msg($image->error_msg(), 1, array(), false);
            }
            $goods_img = $original_img;   // 商品图片

            /* 复制一份相册图片 */
            /* 添加判断是否自动生成相册图片 */
            if ($_CFG['auto_generate_gallery']) {
                $img = $original_img;   // 相册图片
                $pos = strpos(basename($img), '.');
                $newname = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                if (!copy('../' . $img, '../' . $newname)) {
                    sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                }
                $img = $newname;

                $gallery_img = $img;
                $gallery_thumb = $img;
            }

            // 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
            if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img) {

                if (empty($is_url_goods_img)) {
                    // 如果设置大小不为0，缩放图片
                    if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0) {
                        $goods_img = $image->make_thumb('../' . $goods_img, $GLOBALS['_CFG']['image_width'], $GLOBALS['_CFG']['image_height']);
                        if ($goods_img === false) {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                    }

                    /* 添加判断是否自动生成相册图片 */
                    if ($_CFG['auto_generate_gallery']) {
                        $newname = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                        if (!copy('../' . $img, '../' . $newname)) {
                            sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                        }
                        $gallery_img = $newname;
                    }

                    // 加水印
                    if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark'])) {
                        if ($image->add_watermark('../' . $goods_img, '', $GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false) {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                        /* 添加判断是否自动生成相册图片 */
                        if ($_CFG['auto_generate_gallery']) {
                            if ($image->add_watermark('../' . $gallery_img, '', $GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false) {
                                sys_msg($image->error_msg(), 1, array(), false);
                            }
                        }
                    }
                }

                // 相册缩略图
                /* 添加判断是否自动生成相册图片 */
                if ($_CFG['auto_generate_gallery']) {
                    if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0) {
                        $gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'], $GLOBALS['_CFG']['thumb_height']);
                        if ($gallery_thumb === false) {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                    }
                }
            }
        }


        // 是否上传商品缩略图
        if (isset($_FILES['goods_thumb']) && $_FILES['goods_thumb']['tmp_name'] != '' &&
                isset($_FILES['goods_thumb']['tmp_name']) && $_FILES['goods_thumb']['tmp_name'] != 'none') {
            // 上传了，直接使用，原始大小
            $goods_thumb = $image->upload_image($_FILES['goods_thumb']);
            if ($goods_thumb === false) {
                sys_msg($image->error_msg(), 1, array(), false);
            }
        } else {
            // 未上传，如果自动选择生成，且上传了商品图片，生成所略图
            if ($proc_thumb && isset($_POST['auto_thumb']) && !empty($original_img)) {
                // 如果设置缩略图大小不为0，生成缩略图
                if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0) {
                    $goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'], $GLOBALS['_CFG']['thumb_height']);
                    if ($goods_thumb === false) {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                } else {
                    $goods_thumb = $original_img;
                }
            }
        }
        
        /* 重新格式化图片名称 */
        $original_img = reformat_image_name('goods', $goods_id, $original_img, 'source');
        $goods_img = reformat_image_name('goods', $goods_id, $goods_img, 'goods');
        $goods_thumb = reformat_image_name('goods_thumb', $goods_id, $goods_thumb, 'thumb'); //111
        //将数据保存返回 by wu
        $result['data'] = array(
            'original_img' => $original_img,
            'goods_img' => $goods_img,
            'goods_thumb' => $goods_thumb
        );

        //OSS上传 by wu
        get_oss_add_file($result['data']);

        /* 如果有图片，把商品图片加入图片相册 */
        if (isset($img)) {
            /* 重新格式化图片名称 */
            if (empty($is_url_goods_img)) {
                $img = reformat_image_name('gallery', $goods_id, $img, 'source');
                $gallery_img = reformat_image_name('gallery', $goods_id, $gallery_img, 'goods');
            } else {
                $img = $url_goods_img;
                $gallery_img = $url_goods_img;
            }

            $gallery_thumb = reformat_image_name('gallery_thumb', $goods_id, $gallery_thumb, 'thumb');
            
            $gallery_count = get_goods_gallery_count($goods_id);
            $img_desc = $gallery_count + 1;
            
            $sql = "INSERT INTO " . $ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
                    "VALUES ('$goods_id', '$gallery_img', $img_desc, '$gallery_thumb', '$img')";
            $db->query($sql);
            
            get_oss_add_file(array($gallery_img, $gallery_thumb, $img));
            
            $result['img_desc'] = $img_desc;
        }

        /* 结束处理 start */
        $pic_name = "";
        $pic_url = '../' . $goods_img;
        $upload_status = 1;
        /* 结束处理 end */
    } elseif ($act_type == 'gallery_img') {
        /* 开始处理 start */
        $_FILES['img_url'] = array(
            'name' => array($_FILES['file']['name']),
            'type' => array($_FILES['file']['type']),
            'tmp_name' => array($_FILES['file']['tmp_name']),
            'error' => array($_FILES['file']['error']),
            'size' => array($_FILES['file']['size'])
        );
        $_REQUEST['goods_id_img'] = $id;
        $_REQUEST['img_desc'] = array(array(''));
        $_REQUEST['img_file'] = array(array(''));
        /* 开始处理 end */

        $goods_id = !empty($_REQUEST['goods_id_img']) ? intval($_REQUEST['goods_id_img']) : 0;
        $img_desc = !empty($_REQUEST['img_desc']) ? $_REQUEST['img_desc'] : array();
        $img_file = !empty($_REQUEST['img_file']) ? $_REQUEST['img_file'] : array();
        $php_maxsize = ini_get('upload_max_filesize');
        $htm_maxsize = '2M';
        if ($_FILES['img_url']) {
            foreach ($_FILES['img_url']['error'] AS $key => $value) {
                if ($value == 0) {
                    if (!$image->check_img_type($_FILES['img_url']['type'][$key])) {
                        $result['error'] = '1';
                        $result['massege'] = sprintf($_LANG['invalid_img_url'], $key + 1);
                    } else {
                        $goods_pre = 1;
                    }
                } elseif ($value == 1) {
                    $result['error'] = '1';
                    $result['massege'] = sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize);
                } elseif ($_FILES['img_url']['error'] == 2) {
                    $result['error'] = '1';
                    $result['massege'] = sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize);
                }
            }
        }
        
        $gallery_count = get_goods_gallery_count($goods_id);
        $result['img_desc'] = $gallery_count + 1;
    
        handle_gallery_image_add($goods_id, $_FILES['img_url'], $img_desc, $img_file, '', '', 'ajax', $result['img_desc']);
        clear_cache_files();
        if ($goods_id > 0) {
            /* 图片列表 */
            $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id' ORDER BY img_desc ASC";
        } else {
            $img_id = $_SESSION['thumb_img_id' . $_SESSION['admin_id']];
            $where = '';
            if ($img_id) {
                $where = "AND img_id " . db_create_in($img_id) . "";
            }
            $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id='' $where ORDER BY img_desc ASC";
        }
        $img_list = $db->getAll($sql);
        /* 格式化相册图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0)) {
            foreach ($img_list as $key => $gallery_img) {
                $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
            }
        } else {
            foreach ($img_list as $key => $gallery_img) {
                $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
        }
        $goods['goods_id'] = $goods_id;
        $smarty->assign('img_list', $img_list);
        $img_desc = array();
        foreach ($img_list as $k => $v) {
            $img_desc[] = $v['img_desc'];
        }
        $img_default = min($img_desc);
        $min_img_id = $db->getOne(" SELECT img_id   FROM " . $ecs->table("goods_gallery") . " WHERE goods_id = '$goods_id' AND img_desc = '$img_default' ORDER BY img_desc   LIMIT 1");
        $smarty->assign('min_img_id', $min_img_id);
        $smarty->assign('goods', $goods);

        /* 结束处理 start */
        $this_img_info = $GLOBALS['db']->getRow(" SELECT * FROM " . $GLOBALS['ecs']->table('goods_gallery') . " ORDER BY img_id DESC LIMIT 1 ");
        $result['img_id'] = $this_img_info['img_id'];
        $pic_name = "";
        $pic_url = '../' . $this_img_info['thumb_url'];
        $upload_status = 1;
        $result["external_url"] = '';
        /* 结束处理 end */
    }

    if ($upload_status) { //临时文件转移到目标文件夹
        $result['error'] = 0;
        $result['pic'] = $pic_url;
        $result['name'] = $pic_name;
    } else {
        $result['error'] = "上传有误，清检查服务器配置！";
    }
    die(json_encode($result));
}
/*------------------------------------------------------ */
//-- AJAX刷新管理员的权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_allot')
{
    
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/priv_action.php');
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    check_authz_json('users_merchants_priv');

    /*获取默认等级ID*/
    $sql = "SELECT id FROM".$ecs->table("seller_grade")."WHERE is_default = 1";
    $default_grade_id = $db->getOne($sql);
    
    $grade_id=!empty($_REQUEST['grade_id'])   ? $_REQUEST['grade_id'] : $default_grade_id;
    $smarty->assign("grade_id",$grade_id);
    
    /*获取全部等级*/
    $sql = "SELECT grade_name , id FROM".$ecs->table("seller_grade");
    $seller_grade = $db->getAll($sql);
    $smarty->assign("seller_grade",$seller_grade);
    
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('merchants_privilege'). " WHERE grade_id='$grade_id'");

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
                 " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }
	
	if($priv_arr){
		$db_create_in = array_keys($priv_arr);
	}else{
		$db_create_in = '';
	}

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
           " WHERE parent_id " .db_create_in($db_create_in);
    $result = $db->query($sql);
    while ($priv = $db->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }
	
	if($priv_arr){
		// 将同一组的权限使用 "," 连接起来，供JS全选 ecmoban模板堂 --zhuo
		foreach ($priv_arr AS $action_id => $action_group)
		{
			if($action_group['priv']){
				$priv = @array_keys($action_group['priv']);
				$priv_arr[$action_id]['priv_list'] = join(',', $priv);
                                if(!empty($action_group['priv'])){
                                    foreach ($action_group['priv'] AS $key => $val)
                                    {
                                            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
                                    }
                                }
				
			}
		}
	}else{
		$priv_arr = array();
	}

    /* 赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['allot_priv']);
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('form_act',    'update_allot');
    
    $content = $smarty->fetch('templates/library/ajax_allot.lbi');
    die(json_encode($content));
}
/* ------------------------------------------------------ */
//-- 函数相关
/* ------------------------------------------------------ */

/**
 * 检查图片网址是否合法
 *
 * @param string $url 网址
 *
 * @return boolean
 */
function goods_parse_url($url) {
    $parse_url = @parse_url($url);
    return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}

/* 获取区域商品 */

function get_area_goods($goods_id) {
    $sql = "select rw.region_id, rw.region_name from " . $GLOBALS['ecs']->table('link_area_goods') . " as lag" .
            " left join " . $GLOBALS['ecs']->table('region_warehouse') . " as rw on lag.region_id = rw.region_id" .
            " where lag.goods_id = '$goods_id'";

    return $GLOBALS['db']->getAll($sql);
}

/* 获取地区列表 */

function get_areaRegion_info_list($ra_id) {

    if ($ra_id > 0) {
        $where_raId = " and mr.ra_id = '$ra_id'";
    }

    $sql = "select rw.region_id, rw.region_name from " . $GLOBALS['ecs']->table('merchants_region_info') . " as mr " .
            " left join " . $GLOBALS['ecs']->table('region') . " as r on mr.region_id = r.region_id" .
            " left join " . $GLOBALS['ecs']->table('region_warehouse') . " as rw on r.region_id = rw.regionId" .
            " where 1" . $where_raId;
    return $GLOBALS['db']->getAll($sql);
}

//获取橱窗商品
function get_win_goods($id) {
    $adminru = get_admin_ru_id();
    $sql = "select id,win_goods from " . $GLOBALS['ecs']->table('seller_shopwindow') . " where id='$id' and ru_id='" . $adminru['ru_id'] . "'";

    $win_info = $GLOBALS['db']->getRow($sql);

    if ($win_info['id'] > 0) {
        $goods_ids = $win_info['win_goods'];
        $goods = array();
        if ($goods_ids) {
            $sql = "select goods_id,goods_name from " . $GLOBALS['ecs']->table('goods') . " where user_id='" . $adminru['ru_id'] . "' and goods_id in ($goods_ids)";
            $goods = $GLOBALS['db']->getAll($sql);
        }
        //return $goods;
        $opt = array(); //by wu

        foreach ($goods AS $val) {
            $opt[] = array('value' => $val['goods_id'],
                'text' => $val['goods_name'],
                'data' => '');
        }
        return $opt;
    } else {
        return 'no_cc';
    }
}

/**
 * 数组合并
 *
 * @param array $array1 数组1
 * @param array $array2 数组2
 *
 * @return array
 */
function my_array_merge($array1, $array2) {
    $new_array = $array1;
    foreach ($array2 as $key => $val) {
        $new_array[$key] = $val;
    }
    return $new_array;
}

/**
 * 获取商品类型属性
 *
 * @param int $cat_id 商品类型ID
 *
 * @return array
 */
function get_attributes($cat_id = 0) {
    $sql = "SELECT `attr_id`, `cat_id`, `attr_name` FROM " . $GLOBALS['ecs']->table('attribute') . " ";
    if (!empty($cat_id)) {
        $cat_id = intval($cat_id);
        $sql .= " WHERE `cat_id` = '{$cat_id}' ";
    }
    $sql .= " ORDER BY `cat_id` ASC, `attr_id` ASC ";
    $attributes = array();
    $query = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($query)) {
        $attributes[$row['attr_id']] = $row['attr_name'];
    }
    return $attributes;
}

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist($filters) {

    $cat_id = !empty($filters->cat_id) ? intval($filters->cat_id) : 0;
    $keyword = !empty($filters->keyword) ? trim($filters->keyword) : '';
    $brand_id = !empty($filters->brand_id) ? intval($filters->brand_id) : 0;

    $children = cat_list($cat_id, 1);
    $children = arr_foreach($children);

    if ($children) {
        $children = implode(",", $children) . "," . $cat_id;
        $children = get_children($children, 0, 1);
    } else {
        $children = "g.cat_id IN ($cat_id)";
    }

    $where = '1';
    if (!empty($keyword)) {
        if (strtoupper(EC_CHARSET) == 'GBK') {
            $keyword = iconv("UTF-8", "gb2312", $keyword);
        }

        $where .= " AND brand_name like '%{$keyword}%'";
    }

    if (!empty($brand_id)) {
        $where .= " AND b.brand_id = '$brand_id' ";
    } else {

        /**
         * 当前分类下的所有子分类
         * 返回一维数组
         */
        $cat_keys = get_array_keys_cat($cat_id);

        $where .= " AND $children OR " . 'gc.cat_id ' . db_create_in(array_unique(array_merge(array($cat_id), $cat_keys)));
    }

    /* 获取分类下平台品牌 */
    $sql = "SELECT b.brand_id, b.brand_name, b.brand_logo, COUNT(*) AS goods_num " .
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.brand_id = b.brand_id AND g.user_id = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
            " WHERE $where AND b.is_show = 1 " .
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";

    $brands_list = $GLOBALS['db']->getAll($sql);

    /* 获取分类下商家品牌 */
    $sql = "SELECT b.brand_id, b.brand_name , COUNT(*) AS goods_num, g.user_id " .
            "FROM " . $GLOBALS['ecs']->table('brand') . "AS b  " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('link_brand') . "AS lb ON lb.brand_id = b.brand_id " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('merchants_shop_brand') . "AS msb ON msb.bid = lb.bid AND msb.audit_status = 1 AND msb.is_show = 1 " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON g.brand_id = msb.bid AND g.user_id > 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 " .
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods_cat') . " AS gc ON g.goods_id = gc.goods_id " .
            " WHERE $where AND b.is_show = 1 " .
            "GROUP BY b.brand_id HAVING goods_num > 0 ORDER BY b.sort_order, b.brand_id ASC";

    $msb_brands_list = $GLOBALS['db']->getAll($sql);

    if ($msb_brands_list) {
        $new_array = array_merge($brands_list, $msb_brands_list);
        $new = unique_arr($new_array);
        $brands_list = $new;
    }

    $brands = array();
    foreach ($brands_list AS $key => $val) {
        $brands[$key]['brand_id'] = $val['brand_id'];
        $brands[$key]['brand_name'] = $val['brand_name'];
    }

    return $brands;
}

function get_floor_content($curr_template, $filename, $id = 0, $region = '') {
    $where = " where 1 ";
    if (!empty($id)) {
        $where.=" and id='$id'";
    }
    if (!empty($region)) {
        $where.=" and region='$region'";
    }
    $sql = "select * from " . $GLOBALS['ecs']->table('floor_content') . $where . " and filename='$filename' and theme='$curr_template'";
    $row = $GLOBALS['db']->getAll($sql);

    return $row;
}

?>