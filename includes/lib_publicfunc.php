<?php

/**
 * ecmoban 公用函数库
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_common.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/*获取当前商家的等级*/
function get_seller_grade_rank($ru_id){
    $sql = "SELECT sg.goods_sun, sg.seller_temp, sg.favorable_rate, sg.give_integral, sg.rank_integral, sg.pay_integral FROM " .$GLOBALS['ecs']->table('merchants_grade'). " AS mg, " . 
            $GLOBALS['ecs']->table('seller_grade'). " AS sg " .
            " WHERE mg.grade_id = sg.id AND ru_id = '$ru_id' LIMIT 1";
    
    $res = $GLOBALS['db']->getRow($sql);
    
    $res['favorable_rate'] = !empty($res['favorable_rate']) ? $res['favorable_rate']/100 : 1;
    $res['give_integral'] = !empty($res['give_integral']) ? $res['give_integral']/100 : 1;
    $res['rank_integral'] = !empty($res['rank_integral']) ? $res['rank_integral']/100 : 1;
    $res['pay_integral'] = !empty($res['pay_integral']) ? $res['pay_integral']/100 : 1;
    
    return $res;
}

/**
 * 申请日志列表
 */
function get_account_log_list($ru_id, $type = 0){
    require_once(ROOT_PATH . 'includes/lib_order.php');
    
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = !isset($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        
        $filter['order_sn'] = !isset($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
        $filter['out_up'] = !isset($_REQUEST['out_up']) ? 0 : intval($_REQUEST['out_up']);
        $filter['log_type'] = !isset($_REQUEST['log_type']) ? 0 : intval($_REQUEST['log_type']);
        $filter['handler'] = !isset($_REQUEST['handler']) ? 0 : intval($_REQUEST['handler']);
        
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'sal.log_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        
        $filter['act_type'] = !isset($_REQUEST['act_type']) ? 'detail' : $_REQUEST['act_type'];
        $filter['ru_id'] = !isset($_REQUEST['ru_id']) ? $ru_id : intval($_REQUEST['ru_id']);
        
        $ex_where = ' WHERE 1 ';
        
        //订单编号
        if($filter['order_sn']){
            $ex_where .= " AND (sal.apply_sn = '" .$filter['order_sn']. "'";
            $ex_where .= " OR ";
            $ex_where .= " (SELECT order_sn FROM " .$GLOBALS['ecs']->table("order_info"). " AS oi WHERE sal.order_id = oi.order_id LIMIT 1) = '" .$filter['order_sn']. "')";
        }
        
        //收入/支出
        if($filter['out_up']){
            if($filter['out_up'] != 4){
                $ex_where .= " AND (sal.log_type > '" .$filter['out_up']. "' OR sal.log_type =  '" .$filter['out_up']. "')";
            }else{
                $ex_where .= " AND sal.log_type = '" .$filter['out_up']. "'";
            }
        }
        
        //待处理
        if($filter['handler']){
            if($filter['handler'] == 1){
                $ex_where .= " AND sal.is_paid = 1";
            }else{
                $ex_where .= " AND sal.is_paid = 0";
            }
        }
        
        //类型
        if($filter['log_type']){
            $ex_where .= " AND sal.log_type = '" .$filter['log_type']. "'";
        }
    
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = empty($_REQUEST['store_search']) ? 0 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';

        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] !=0){
           if($ru_id == 0){ 

               if($_REQUEST['store_type']){
                    $store_search_where = "AND mis.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }

                if($filter['store_search'] == 1){
                    $ex_where .= " AND mis.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND mis.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND mis.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }

                if($filter['store_search'] > 1){
                    $ex_where .= " AND mis.user_id > 0 $store_where ";
                }
           }
        }
        //管理员查询的权限 -- 店铺查询 end

        $type = implode(',', $type);
        
        if($filter['ru_id']){
            $ex_where .= " AND sal.ru_id = '" .$filter['ru_id']. "'";
        }

        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('seller_account_log') ." AS sal ". 
                " LEFT JOIN " . $GLOBALS['ecs']->table('merchants_shop_information')  ." AS mis ON sal.ru_id = mis.user_id ". 
                " $ex_where AND sal.log_type IN($type)";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT sal.* FROM " . $GLOBALS['ecs']->table('seller_account_log') . " AS sal " .
                " LEFT JOIN " . $GLOBALS['ecs']->table('merchants_shop_information') . " AS mis ON sal.ru_id = mis.user_id " .
                " $ex_where AND sal.log_type IN($type)" .
                " ORDER BY " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }    
    
    $res = $GLOBALS['db']->getAll($sql);
    
    $arr = array();
    for($i=0; $i<count($res); $i++){
        $res[$i]['shop_name'] = get_shop_name($res[$i]['ru_id'], 1);
        $order = order_info($res[$i]['order_id']);
        $res[$i]['order_sn'] = $order['order_sn'];
        $res[$i]['amount'] = price_format($res[$i]['amount'], false);
        $res[$i]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $res[$i]['add_time']);
        $res[$i]['payment_info'] = payment_info($res[$i]['pay_id']);
    }
    
    $arr = array('log_list' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 申请日志详细信息
 */
function get_account_log_info($log_id){
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('seller_account_log'). " WHERE log_id = '$log_id'";
    $res = $GLOBALS['db']->getRow($sql);
    
    if($res){
        $res['shop_name'] = get_shop_name($res['ru_id'], 1);
        $res['payment_info'] = payment_info($res['pay_id']);
    }
    
    return $res;
}

/**
 * 查询所有商家分类
 */
function get_seller_category() {
    $sql = "SELECT c.*, (SELECT c2.cat_name FROM " . $GLOBALS['ecs']->table("category") . " AS c2 WHERE c2.cat_id = c.parent_id LIMIT 1) AS parent_name " .
            " FROM " . $GLOBALS['ecs']->table("merchants_category") . " AS mc," .
            $GLOBALS['ecs']->table("category") . " AS c " .
            " WHERE 1 AND mc.cat_id = c.cat_id";
    $res = $GLOBALS['db']->getAll($sql);

    $chid_level = 0;
    $level = 1;

    $arr = array();
    if ($res) {
        foreach ($res as $key => $row) {
            $arr[$key]['cat_id'] = $row['cat_id'];
            $arr[$key]['cat_name'] = $row['cat_name'];
            $arr[$key]['parent_id'] = $row['parent_id'];

            $arr[$key]['keywords'] = $row['keywords'];
            $arr[$key]['cat_desc'] = $row['cat_desc'];
            $arr[$key]['sort_order'] = $row['sort_order'];
            $arr[$key]['measure_unit'] = $row['measure_unit'];
            $arr[$key]['show_in_nav'] = $row['show_in_nav'];
            $arr[$key]['style'] = $row['style'];
            $arr[$key]['grade'] = $row['grade'];
            $arr[$key]['filter_attr'] = $row['filter_attr'];
            $arr[$key]['is_top_style'] = $row['is_top_style'];
            $arr[$key]['top_style_tpl'] = $row['top_style_tpl'];
            $arr[$key]['cat_icon'] = $row['cat_icon'];
            $arr[$key]['is_top_show'] = $row['is_top_show'];
            $arr[$key]['category_links'] = $row['category_links'];
            $arr[$key]['category_topic'] = $row['category_topic'];
            $arr[$key]['pinyin_keyword'] = $row['pinyin_keyword'];
            $arr[$key]['cat_alias_name'] = $row['cat_alias_name'];
            $arr[$key]['template_file'] = $row['template_file'];

            $arr[$key]['parent_name'] = $row['parent_name'];

            $sql = "SELECT * FROM " . $GLOBALS['ecs']->table("merchants_category") . " WHERE cat_id = '" . $row['parent_id'] . "'";
            if ($GLOBALS['db']->getOne($sql, true)) {



                $cat_level = get_seller_cat_level($row['parent_id']);
                if ($cat_level['parent_id'] != 0) {
                    $chid = get_seller_cat_level($cat_level['parent_id']);
                    if ($chid) {
                        $chid_level += 1;
                    }
                }

                $arr[$key]['level'] = $level + $chid_level;
            } else {
                $arr[$key]['level'] = 0;
            }

            $cat_level = array('一', '二', '三', '四', '五', '六', '气', '八', '九', '十');
            $arr[$key]['belongs'] = $cat_level[$arr[$key]['level']] . "级";

            if ($arr[$key]['level'] == 0) {
                $row['parent_id'] = 0;
            }
        }
    }

    return $arr;
}

/**
 * 查询商家分类是否存在上一级
 */
function get_seller_cat_level($parent_id = 0, $level = 1) {
    $sql = "SELECT c.cat_id, c.cat_name, c.parent_id FROM " . $GLOBALS['ecs']->table("merchants_category") . " AS mc," .
            $GLOBALS['ecs']->table("category") . " AS c" .
            " WHERE mc.cat_id = c.cat_id AND c.cat_id = '$parent_id' LIMIT 1";
    $row = $GLOBALS['db']->getRow($sql);

    return $row;
}

/*
 * 商家分类
 * 获取上下级分类列表 by wu
 * $cat_id      分类id
 * $relation    关系 0:自己 1:上级 2:下级
 * $self        是否包含自己 true:包含 false:不包含
 */

function get_seller_select_category($cat_id = 0, $relation = 0, $self = true, $user_id = 0) {
    //静态数组	
    static $cat_list = array();
    $cat_list[] = intval($cat_id);
    
    if($user_id){
        $where = " AND user_id = '$user_id'";
    }

    if ($relation == 0) {
        return $cat_list;
    } elseif ($relation == 1) {
        $sql = " select parent_id from " . $GLOBALS['ecs']->table('merchants_category') . " where cat_id='" . $cat_id . "' $where";
        $parent_id = $GLOBALS['db']->getOne($sql);
        if (!empty($parent_id)) {
            get_seller_select_category($parent_id, $relation, $self, $user_id);
        }
        //删除自己
        if ($self == false) {
            unset($cat_list[0]);
        }
        $cat_list[] = 0;
        //去掉重复，主要是0
        return array_reverse(array_unique($cat_list));
    } elseif ($relation == 2) {
        $sql = " select cat_id from " . $GLOBALS['ecs']->table('merchants_category') . " where parent_id='" . $cat_id . "' $where";
        $child_id = $GLOBALS['db']->getCol($sql);
        if (!empty($child_id)) {
            foreach ($child_id as $key => $val) {
                get_seller_select_category($val, $relation, $self, $user_id);
            }
        }
        //删除自己
        if ($self == false) {
            unset($cat_list[0]);
        }
        return $cat_list;
    }
}

/*
 * 平台分类
 * 获取当级分类列表 by wu
 * $cat_id      分类id
 * $relation    关系 0:自己 1:上级 2:下级
 */
function get_seller_category_list($cat_id = 0, $relation = 0, $user_id = 0) {
    
    $where = '';
    if($user_id){
        $where .= " AND user_id = '$user_id'";
    }
    
    if ($relation == 0) {
        $parent_id = $GLOBALS['db']->getOne(" SELECT parent_id FROM " . $GLOBALS['ecs']->table('merchants_category') . " WHERE cat_id = '$cat_id' $where");
    } elseif ($relation == 1) {
        $parent_id = $GLOBALS['db']->getOne(" SELECT parent_id FROM " . $GLOBALS['ecs']->table('merchants_category') . " WHERE cat_id = '$cat_id' $where");
    } elseif ($relation == 2) {
        $parent_id = $cat_id;
    }

    $parent_id = empty($parent_id) ? 0 : $parent_id;
    $category_list = $GLOBALS['db']->getAll(" SELECT cat_id, cat_name FROM " . $GLOBALS['ecs']->table('merchants_category') . " WHERE parent_id = '$parent_id' $where");
    foreach ($category_list as $key => $val) {
        if ($cat_id == $val['cat_id']) {
            $is_selected = 1;
        } else {
            $is_selected = 0;
        }
        $category_list[$key]['is_selected'] = $is_selected;
    }
    return $category_list;
}

//设置默认筛选 by wu
function set_default_filter($goods_id = 0, $cat_id = 0, $user_id = 0, $cat_type_show = 0)
{
    //分类导航
    if ($cat_id) {
        $parent_cat_list = get_select_category($cat_id, 1, true, $user_id);
        $filter_category_navigation = get_array_category_info($parent_cat_list);
        $GLOBALS['smarty']->assign('filter_category_navigation', $filter_category_navigation);
    }
    
    if($user_id){
        $seller_shop_cat = seller_shop_cat($user_id);
    }else{
        $seller_shop_cat = array();
    }

    $GLOBALS['smarty']->assign('filter_category_list', get_category_list($cat_id, 0, $seller_shop_cat, $user_id, 2)); //分类列表
    $GLOBALS['smarty']->assign('filter_brand_list', search_brand_list($goods_id)); //品牌列表
    $GLOBALS['smarty']->assign('cat_type_show', $cat_type_show); //平台分类

    return true;
}

function set_seller_default_filter($goods_id = 0, $cat_id = 0, $user_id = 0) {
    //分类导航
    if ($cat_id > 0) {
        $seller_parent_cat_list = get_seller_select_category($cat_id, 1, true, $user_id);
        $seller_filter_category_navigation = get_seller_array_category_info($seller_parent_cat_list);
        
        $GLOBALS['smarty']->assign('seller_filter_category_navigation', $seller_filter_category_navigation);
    }
   
    $GLOBALS['smarty']->assign('seller_filter_category_list', get_seller_category_list($cat_id, 0, $user_id)); //分类列表
    $GLOBALS['smarty']->assign('seller_cat_type_show', 1); //商家分类

    return true;
}

//给出cat_id,返回逐级分类 by wu
function get_seller_every_category($cat_id = 0) {
    $parent_cat_list = get_seller_category_array($cat_id, 1, true);
    $filter_category_navigation = get_seller_array_category_info($parent_cat_list);
    $cat_nav = "";
    if ($filter_category_navigation) {
        foreach ($filter_category_navigation as $key => $val) {
            if ($key == 0) {
                $cat_nav .= $val['cat_name'];
            } elseif ($key > 0) {
                $cat_nav .= " > " . $val['cat_name'];
            }
        }
    }

    return $cat_nav;
}

//通过分类id，获取一个数组包含所有父级元素 by wu
function get_seller_category_array($cat_id = 0, $relation = 0, $self = true) {
    $cat_list[] = intval($cat_id);

    if ($relation == 0) {
        return $cat_list;
    } elseif ($relation == 1) {
        do {
            $sql = " select parent_id from " . $GLOBALS['ecs']->table('merchants_category') . " where cat_id='" . $cat_id . "' ";
            $parent_id = $GLOBALS['db']->getOne($sql);
            if (!empty($parent_id)) {
                $cat_list[] = $parent_id;
                $cat_id = $parent_id;
            }
        } while (!empty($parent_id));

        //删除自己
        if ($self == false) {
            unset($cat_list[0]);
        }
        $cat_list[] = 0;
        //去掉重复，主要是0
        return array_reverse(array_unique($cat_list));
    } elseif ($relation == 2) {
        
    }
}

//获取数组中分类信息 by wu
function get_seller_array_category_info($arr = array()) { 
    if ($arr) {
        $sql = " SELECT cat_id, cat_name FROM " . $GLOBALS['ecs']->table('merchants_category') . " WHERE cat_id " . db_create_in($arr);
        return $GLOBALS['db']->getAll($sql);
    } else {
        return false;
    }
}

/**
 * 商家入驻分类
 */
function seller_shop_cat($user_id = 0){
    $seller_shop_cat = '';
    if($user_id){
        $sql = "SELECT user_shopMain_category FROM " .$GLOBALS['ecs']->table('merchants_shop_information'). " WHERE user_id = '$user_id'";
        $seller_shop_cat = $GLOBALS['db']->getOne($sql, true);
    }
    
    $arr = array();
    $arr['parent'] = '';
    if($seller_shop_cat){
        $seller_shop_cat = explode("-", $seller_shop_cat);
        
        foreach($seller_shop_cat as $key=>$row){
            if($row){
                $cat = explode(":", $row);
                $arr[$key]['cat_id'] = $cat[0];
                $arr[$key]['cat_tree'] = $cat[1];
                
                $arr['parent'] .= $cat[0] . ",";
                
                if($cat[1]){
                    $arr['parent'] .= $cat[1] . ",";
                }
            }
        }
    }
    
    $arr['parent'] = substr($arr['parent'], 0, -1);
    
    return $arr;
}

/**
 * 获得商品分类的所有信息
 *
 * @param   integer     $cat_id     指定的分类ID
 *
 * @return  mix
 */
function get_seller_cat_info($cat_id) {
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('merchants_category') . " WHERE cat_id = '$cat_id' LIMIT 1";
    $row = $GLOBALS['db']->getRow($sql);
    
    if($row){
        $row['is_show_merchants'] = $row['is_show'];
    }
    
    return $row;
}

/**
 * 获取商品信息
 */
function get_admin_goods_info($goods_id = 0, $select = array()) {

    if ($select) {
        $select = implode(",", $select);
    } else {
        $select = "*";
    }

    $sql = "SELECT $select FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = '$goods_id' LIMIT 1";
    $row = $GLOBALS['db']->getRow($sql);
    
    if($row['user_cat']){
        $cat_info = get_seller_cat_info($row['user_cat']);
        $row['user_cat_name'] = $cat_info['cat_name'];
    }
    
    return $row;
}

//给出cat_id,返回逐级分类 by wu
function get_every_category($cat_id = 0) {
    $parent_cat_list = get_category_array($cat_id, 1, true);
    $filter_category_navigation = get_array_category_info($parent_cat_list);
    $cat_nav = "";
    if ($filter_category_navigation) {
        foreach ($filter_category_navigation as $key => $val) {
            if ($key == 0) {
                $cat_nav .= $val['cat_name'];
            } elseif ($key > 0) {
                $cat_nav .= " > " . $val['cat_name'];
            }
        }
    }

    return $cat_nav;
}

//通过分类id，获取一个数组包含所有父级元素 by wu
function get_category_array($cat_id = 0, $relation = 0, $self = true) {
    $cat_list[] = intval($cat_id);

    if ($relation == 0) {
        return $cat_list;
    } elseif ($relation == 1) {
        do {
            $sql = " SELECT parent_id FROM " . $GLOBALS['ecs']->table('category') . " WHERE cat_id='" . $cat_id . "' ";
            $parent_id = $GLOBALS['db']->getOne($sql);
            if (!empty($parent_id)) {
                $cat_list[] = $parent_id;
                $cat_id = $parent_id;
            }
        } while (!empty($parent_id));

        //删除自己
        if ($self == false) {
            unset($cat_list[0]);
        }
        $cat_list[] = 0;
        //去掉重复，主要是0
        return array_reverse(array_unique($cat_list));
    } elseif ($relation == 2) {
        
    }
}

//获取数组中分类信息 by wu
function get_array_category_info($arr = array()) {
    if ($arr) {
        $arr = get_del_str_comma($arr);
        $sql = " SELECT cat_id, cat_name FROM " . $GLOBALS['ecs']->table('category') . " WHERE cat_id " . db_create_in($arr);
        $category_list = $GLOBALS['db']->getAll($sql);
        
        foreach ($category_list as $key => $val) {
            $category_list[$key]['url'] = build_uri('category', array('cid' => $val['cat_id']), $val['cat_name']);
        }
        return $category_list;
    } else {
        return false;
    }
}

/**
 * 商品详情分类
 */
function get_add_edit_goods_cat_list($goods_id = 0, $cat_id = 0, $table = 'category', $sin_prefix = '', $user_id = 0, $seller_shop_cat = array()){
    
    //关联商品
    if (empty($sin_prefix)) {
        $select_category_rel = '';
        $select_category_rel .= insert_select_category(0, 0, 0, 'cat_id1', 1, $table, $seller_shop_cat);
        $GLOBALS['smarty']->assign($sin_prefix . 'select_category_rel', $select_category_rel);
    }

    //配件
    if (empty($sin_prefix)) {
        $select_category_pak = '';
        $select_category_pak .= insert_select_category(0, 0, 0, 'cat_id2', 1, $table, $seller_shop_cat);
        $GLOBALS['smarty']->assign($sin_prefix . 'select_category_pak', $select_category_pak);
    }

    /**
     * 商品分类
     * 添加商品
     */
    if ($_REQUEST['act'] == 'add') {
        $select_category_html = '';
        
        if($sin_prefix){
            $select_category_html .= insert_seller_select_category(0, 0, 0, 'user_cat', 0, $table, array(), $user_id);
        }else{
            $select_category_html .= insert_select_category(0, 0, 0, 'cat_id', 0, $table, $seller_shop_cat);
        }

        $GLOBALS['smarty']->assign($sin_prefix . 'select_category_html', $select_category_html);
    }
    
    /**
     * 编辑商品
     */
    else if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'copy') 
    {
        $goods = get_admin_goods_info($goods_id, array('cat_id', 'user_cat'));
        
        $select_category_html = '';
        
        if($sin_prefix){
            $parent_cat_list = get_seller_select_category($cat_id, 1, true, $user_id);
            $cat_id = $goods['user_cat'];
        }else{
            $parent_cat_list = get_select_category($cat_id, 1, true);
            $cat_id = $goods['cat_id'];
        }
        
        for ($i = 0; $i < count($parent_cat_list); $i++) {
            
            if($sin_prefix){
                $select_category_html .= insert_seller_select_category(pos($parent_cat_list), next($parent_cat_list), $i, 'user_cat', 0, $table, array(), $user_id);
            }else{
                $select_category_html .= insert_select_category(pos($parent_cat_list), next($parent_cat_list), $i, 'cat_id', 0, $table, $seller_shop_cat);
            }
        }
        $GLOBALS['smarty']->assign($sin_prefix . 'select_category_html', $select_category_html);
        $parent_and_rank = empty($cat_id) ? '0_0' : $cat_id . '_' . (count($parent_cat_list) - 2);
        $GLOBALS['smarty']->assign($sin_prefix . 'parent_and_rank', $parent_and_rank);
    }
}

/**
 * 会员信息
 */
function get_admin_user_info($id = 0){
    $sql  = 'SELECT u.user_id, u.email, u.user_name, u.user_money, u.mobile_phone, u.pay_points, nick_name'.
            ' FROM ' .$GLOBALS['ecs']->table('users'). ' AS u ' .
            " WHERE u.user_id = '$id'";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 查询当前商品属性
 */
function get_dialog_goods_attr_type($attr_id = 0, $goods_id = 0){
    $sql = "SELECT goods_attr_id, attr_id, attr_value FROM " .$GLOBALS['ecs']->table('goods_attr'). " WHERE attr_id = '$attr_id' AND goods_id = '$goods_id' ORDER BY attr_sort";
    $res = $GLOBALS['db']->getAll($sql);
    
    if($res){
        foreach($res as $key=>$row)
        {
            if ($goods_id) {
                $res[$key]['is_selected'] = 1;
            }
            else {
                $res[$key]['is_selected'] = 0;
            }
        }
    }
    
    return $res;
}
function seller_grade_list()
{
    $sql = "SELECT user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') . " WHERE merchants_audit = 1 ORDER BY user_id ASC";
    return $GLOBALS['db']->getAll($sql);
}

function get_pin_regions()
{
    $arr = array();
    $letters = range('A', 'Z');
    $pin_regions = read_static_cache('pin_regions', '/data/sc_file/');

    if ($pin_regions !== false) {
        foreach ($letters as $key => $row ) {
            foreach ($pin_regions as $pk => $prow ) {
                if ($row == $prow['initial']) {
                    $arr[$row][$pk] = $prow;
                }
            }

            if ($arr[$row]) {
                $arr[$row] = get_array_sort($arr[$row], 'region_id');
            }
        }
    }

    ksort($arr);
    return $arr;
}
function get_updel_goods_attr($goods_id = 0)
{
    $admin_id = get_admin_id();

    if ($admin_id) {
        if ($goods_id) {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('goods_attr') . " SET goods_id = '$goods_id' WHERE admin_id = '$admin_id' AND goods_id = 0";
        }
        else {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE admin_id = '$admin_id' AND goods_id = 0";
        }

        $GLOBALS['db']->query($sql);
    }
}

function get_goods_attr_nameId($goods_id = 0, $attr_id = 0, $attr_value = '', $select = 'goods_attr_id', $type = 0)
{
    if ($type == 1) {
        $sql = "SELECT $select FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_id = '$goods_id' AND goods_attr_id = '$attr_id'";
    }
    else {
        $sql = "SELECT $select FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_id = '$goods_id' AND attr_id = '$attr_id'  AND attr_value = '$attr_value'";
    }
    return $GLOBALS['db']->getOne($sql);
}

function get_warehouse_area_attr_price_insert($warehouse_area, $goods_id, $goods_attr_id, $table)
{
    $arr = array();

    if (is_array($warehouse_area))
    {
        for ($i = 0; count($warehouse_area) > $i; $i++) {
            if (!empty($warehouse_area[$i])) {
                $parent = array('goods_id' => $goods_id, 'goods_attr_id' => $goods_attr_id);

                if ($table == 'warehouse_attr') {
                    $where = " AND warehouse_id = '" . $warehouse_area[$i] . "'";
                    $parent['warehouse_id'] = $warehouse_area[$i];
                    $parent['attr_price'] = $_POST['attr_price_' . $warehouse_area[$i]];
                }
                else if ($table == 'warehouse_area_attr') {
                    $where = " AND area_id = '" . $warehouse_area[$i] . "'";
                    $parent['area_id'] = $warehouse_area[$i];
                    $parent['attr_price'] = $_POST['attrPrice_' . $warehouse_area[$i]];
                }

                $sql = "SELECT id FROM " . $GLOBALS['ecs']->table($table) . " WHERE goods_id = '$goods_id' AND goods_attr_id = '$goods_attr_id' $where";
                $id = $GLOBALS['db']->getOne($sql);

                if ($id > 0) {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($table), $parent, 'UPDATE', "goods_id = '$goods_id' and goods_attr_id = '$goods_attr_id' $where");
                }
                else {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($table), $parent, 'INSERT');
                }
            }
        }
    }
    else if (is_array($goods_attr_id)) {
        for ($i = 0; count($goods_attr_id) > $i; $i++) {
            if (!empty($goods_attr_id[$i])) {
                $parent = array('goods_id' => $goods_id, 'goods_attr_id' => $goods_attr_id[$i]);

                if ($table == 'warehouse_attr') {
                    $where = " AND warehouse_id = '$warehouse_area'";
                    $parent['warehouse_id'] = $warehouse_area;
                    $parent['attr_price'] = $_POST['attr_price_' . $goods_attr_id[$i]];
                }
                else if ($table == 'warehouse_area_attr') {
                    $where = " AND area_id = '$warehouse_area'";
                    $parent['area_id'] = $warehouse_area;
                    $parent['attr_price'] = $_POST['attrPrice_' . $goods_attr_id[$i]];
                }

                $sql = "SELECT id FROM " . $GLOBALS['ecs']->table($table) . " WHERE goods_id = '$goods_id' AND goods_attr_id = '" . $goods_attr_id[$i] . "' $where";
                $id = $GLOBALS['db']->getOne($sql);

                if ($id > 0) {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($table), $parent, 'UPDATE', "goods_id = '$goods_id' and goods_attr_id = '" . $goods_attr_id[$i] . "' $where");
                }
                else {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table($table), $parent, 'INSERT');
                }
            }
        }
    }
}
?>