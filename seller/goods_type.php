<?php

/**
 * ECSHOP 商品类型管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_type.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"goods");
$exc = new exchange($ecs->table("goods_type"), $db, 'cat_id', 'cat_name');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

$smarty->assign('current', basename(PHP_SELF,'.php'));

/*------------------------------------------------------ */
//-- 管理界面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'manage')
{
    assign_query_info();

    $smarty->assign('ur_here',          $_LANG['08_goods_type']);
    $smarty->assign('full_page',        1);

    $good_type_list = get_goodstype($adminru['ru_id']);
    $good_in_type = '';

    $smarty->assign('goods_type_arr',   $good_type_list['type']);
    $smarty->assign('filter',       $good_type_list['filter']);
    $smarty->assign('record_count', $good_type_list['record_count']);
    $smarty->assign('page_count',   $good_type_list['page_count']);
	
	//分页
	$page_count_arr = seller_page($good_type_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    $query = $db->query("SELECT a.cat_id FROM " . $ecs->table('attribute') . " AS a RIGHT JOIN " . $ecs->table('goods_attr') . " AS g ON g.attr_id = a.attr_id GROUP BY a.cat_id");
     while ($row = $db->fetchRow($query))
    {
        $good_in_type[$row['cat_id']]=1;
    }
    $smarty->assign('good_in_type', $good_in_type);
	
    //ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['attr_set_up'] == 0){
            if($adminru['ru_id'] == 0){
                    $smarty->assign('action_link',      array('text' => $_LANG['new_goods_type'], 'href' => 'goods_type.php?act=add'));
                    $smarty->assign('attr_set_up',   1);
            }else{
                    $smarty->assign('attr_set_up',   0);
            }
    }elseif($GLOBALS['_CFG']['attr_set_up'] == 1){
            $smarty->assign('action_link',      array('text' => $_LANG['new_goods_type'], 'href' => 'goods_type.php?act=add'));
            $smarty->assign('attr_set_up',   1);
    }
    //ecmoban模板堂 --zhuo end

    // $smarty->display('goods_type_old.dwt');
    $smarty->display('goods_type.dwt');
}

/*------------------------------------------------------ */
//-- 获得列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $good_type_list = get_goodstype($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($good_type_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
	
    //ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['attr_set_up'] == 0){
            if($adminru['ru_id'] == 0){
                    $smarty->assign('attr_set_up',   1);
            }else{
                    $smarty->assign('attr_set_up',   0);
            }
    }elseif($GLOBALS['_CFG']['attr_set_up'] == 1){
            $smarty->assign('attr_set_up',   1);
    }
    //ecmoban模板堂 --zhuo end

    $smarty->assign('goods_type_arr',   $good_type_list['type']);
    $smarty->assign('filter',       $good_type_list['filter']);
    $smarty->assign('record_count', $good_type_list['record_count']);
    $smarty->assign('page_count',   $good_type_list['page_count']);

    make_json_result($smarty->fetch('goods_type.dwt'), '',
        array('filter' => $good_type_list['filter'], 'page_count' => $good_type_list['page_count']));
}

/*------------------------------------------------------ */
//-- 修改商品类型名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_type_name')
{
    check_authz_json('goods_type');

    $type_id   = !empty($_POST['id'])  ? intval($_POST['id']) : 0;
    $type_name = !empty($_POST['val']) ? json_str_iconv(trim($_POST['val']))  : '';
    //$ru_id=$adminru['ru_id'];
    /* 检查名称是否重复 */

   // $is_only = $exc->is_only('cat_name', $type_name, $type_id);

    //editor    cwl   很奇怪为什么 原来版本会限制  类型名称  不能重复，这么多店铺肯定会有重复的类型名称.而且插入的时候不限制，修改的时候反而限制   所以我只是限制 自己uid的店铺内  不能有重复的类型名称   start

     //= "SELECT COUNT(*) FROM  .$table. WHERE cat_name = '尺寸'  AND '  cat_id   != '46'";


    //$sql ="select count(*) from ".$GLOBALS['ecs']->table('goods_type') . "WHERE   cat_name  = '".$type_name."'  AND  cat_id   = '$type_id'   AND  user_id   = '$ru_id'" ;//查看该商户的  当前修改的类型名称是否重复
    //$is_only = $GLOBALS['db']->getOne($sql);
    //echo $is_only;exit;
    //editor    cwl  end

    if ($is_only)
    {
        $exc->edit("cat_name='$type_name'", $type_id);

        admin_log($type_name, 'edit', 'goods_type');

        make_json_result(stripslashes($type_name));
    }
    else
    {
        make_json_error($_LANG['repeat_type_name']);
    }
}

/*------------------------------------------------------ */
//-- 切换启用状态
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'toggle_enabled')
{
    check_authz_json('goods_type');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("enabled='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 添加商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('goods_type');
	
	$smarty->assign('menu_select', array('action'=>'02_cat_and_goods', 'current'=>'08_goods_type'));
	
    //ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['attr_set_up'] == 0){
            if($adminru['ru_id'] > 0){
                    $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
            sys_msg('暂时没有添加属性权限', 0, $links);
                    exit;
            }
    }
    //ecmoban模板堂 --zhuo end

    $smarty->assign('ur_here',     $_LANG['new_goods_type']);
    $smarty->assign('action_link', array('href'=>'goods_type.php?act=manage', 'text' => $_LANG['goods_type_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'insert');
    $smarty->assign('goods_type',  array('enabled' => 1));

    assign_query_info();
    $smarty->display('goods_type_info.dwt');
}

elseif ($_REQUEST['act'] == 'insert')
{
    //$goods_type['cat_name']   = trim_right(sub_str($_POST['cat_name'], 60));
    //$goods_type['attr_group'] = trim_right(sub_str($_POST['attr_group'], 255));

    //include(ROOT_PATH . 'includes/cls_json.php');

    //$json   = new JSON;

    $goods_type['cat_name']   = sub_str($_POST['cat_name'], 60);
    $goods_type['attr_group'] = sub_str($_POST['attr_group'], 255);
    $goods_type['enabled']    = intval($_POST['enabled']);
	
    //ecmoban模板堂 --zhuo start
    $goods_type['user_id']    = $adminru['ru_id'];
    //ecmoban模板堂 --zhuo end

    if ($db->autoExecute($ecs->table('goods_type'), $goods_type) !== false)
    {
        $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));

        //$result['error']=0;
        //$result['message']='新建成功';
        //die($json->encode($result));

        sys_msg($_LANG['add_goodstype_success'], 0, $links);
    }
    else
    {
        //$result['error']=1;
        //$result['message']='新建失败';
        //die($json->encode($result));

        sys_msg($_LANG['add_goodstype_failed'], 1);
    }
}

/*------------------------------------------------------ */
//-- 编辑商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
	$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '08_goods_type'));
    $goods_type = get_goodstype_info(intval($_GET['cat_id']));

    if (empty($goods_type))
    {
        sys_msg($_LANG['cannot_found_goodstype'], 1);
    }

    admin_priv('goods_type');
	
    //ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['attr_set_up'] == 0){
            if($adminru['ru_id'] > 0){
                    $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
            sys_msg('暂时没有添加属性权限', 0, $links);
                    exit;
            }
    }
    //ecmoban模板堂 --zhuo end

    $smarty->assign('ur_here',     $_LANG['edit_goods_type']);
    $smarty->assign('action_link', array('href'=>'goods_type.php?act=manage', 'text' => $_LANG['goods_type_list']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'update');
    $smarty->assign('goods_type',  $goods_type);

    assign_query_info();
    $smarty->display('goods_type_info.dwt');
}

elseif ($_REQUEST['act'] == 'update')
{
    $goods_type['cat_name']   = sub_str($_POST['cat_name'], 60);
    $goods_type['attr_group'] = sub_str($_POST['attr_group'], 255);
    $goods_type['enabled']    = intval($_POST['enabled']);
    $cat_id                   = intval($_POST['cat_id']);
    $old_groups               = get_attr_groups($cat_id);

    if ($db->autoExecute($ecs->table('goods_type'), $goods_type, 'UPDATE', "cat_id='$cat_id'") !== false)
    {
        /* 对比原来的分组 */
        $new_groups = explode("\n", str_replace("\r", '', $goods_type['attr_group']));  // 新的分组

        foreach ($old_groups AS $key=>$val)
        {
            $found = array_search($val, $new_groups);

            if ($found === NULL || $found === false)
            {
                /* 老的分组没有在新的分组中找到 */
                update_attribute_group($cat_id, $key, 0);
            }
            else
            {
                /* 老的分组出现在新的分组中了 */
                if ($key != $found)
                {
                    update_attribute_group($cat_id, $key, $found); // 但是分组的key变了,需要更新属性的分组
                }
            }
        }

        $links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
        sys_msg($_LANG['edit_goodstype_success'], 0, $links);
    }
    else
    {
        sys_msg($_LANG['edit_goodstype_failed'], 1);
    }
}

/*------------------------------------------------------ */
//-- 删除商品类型
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('goods_type');


    $id = intval($_GET['id']);

    $name = $exc->get_name($id);

    if ($exc->drop($id))
    {
        //include(ROOT_PATH . 'includes/cls_json.php');

        //$json   = new JSON;
        admin_log(addslashes($name), 'remove', 'goods_type');

        /* 清除该类型下的所有属性 */
        $sql = "SELECT attr_id FROM " .$ecs->table('attribute'). " WHERE cat_id = '$id'";
        $arr = $db->getCol($sql);

        $GLOBALS['db']->query("DELETE FROM " .$ecs->table('attribute'). " WHERE attr_id " . db_create_in($arr));
        $GLOBALS['db']->query("DELETE FROM " .$ecs->table('goods_attr'). " WHERE attr_id " . db_create_in($arr));
	$url = "goods_type.php?act=query&" . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header('Location: {$url}\n');
	die();


    }
    else
    {
        	make_json_error($_LANG['remove_failed']);
    }
}

/**
 * 获得所有商品类型
 *
 * @access  public
 * @return  array
 */
function get_goodstype($ru_id)
{
    //ecmoban模板堂 --zhuo start
    $where = " WHERE 1 ";

    if ($GLOBALS['_CFG']['attr_set_up'] == 0)
    {
        $where .= " AND t.user_id = 0 ";
    }
    elseif ($GLOBALS['_CFG']['attr_set_up'] == 1)
    {
        $where .= " AND t.user_id = '$ru_id'";
    }
    //ecmoban模板堂 --zhuo end
	
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        if (!empty($_GET['is_ajax']) && ($_GET['is_ajax'] == 1))
        {
            $_REQUEST['keyword'] = json_str_iconv($_REQUEST['keyword']);
        }

        $filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);

        if ($filter['keyword'])
        {
            $where .= " AND t.cat_name LIKE '%" . mysqli_like_quote($filter['keyword']) . "%' ";
        }

        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ". $GLOBALS['ecs']->table('goods_type'). " AS t ".
               "LEFT JOIN ". $GLOBALS['ecs']->table('attribute'). " AS a ON a.cat_id=t.cat_id ".
               $where . "GROUP BY t.cat_id ";
        $filter['record_count'] = count($GLOBALS['db']->getAll($sql));

        $filter = page_and_size($filter);

        /* 查询记录 */
        $sql = "SELECT t.*, COUNT(a.cat_id) AS attr_count ".
               "FROM ". $GLOBALS['ecs']->table('goods_type'). " AS t ".
               "LEFT JOIN ". $GLOBALS['ecs']->table('attribute'). " AS a ON a.cat_id=t.cat_id ".
               $where . "GROUP BY t.cat_id " .
               'LIMIT ' . $filter['start'] . ',' . $filter['page_size'];
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $all = $GLOBALS['db']->getAll($sql);

    foreach ($all AS $key=>$val)
    {
        $all[$key]['attr_group'] = strtr($val['attr_group'], array("\r" => '', "\n" => ", "));
        $all[$key]['user_name'] = get_shop_name($val['user_id'], 1);
    }

    return array('type' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

/**
 * 获得指定的商品类型的详情
 *
 * @param   integer     $cat_id 分类ID
 *
 * @return  array
 */
function get_goodstype_info($cat_id)
{
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('goods_type'). " WHERE cat_id='$cat_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 更新属性的分组
 *
 * @param   integer     $cat_id     商品类型ID
 * @param   integer     $old_group
 * @param   integer     $new_group
 *
 * @return  void
 */
function update_attribute_group($cat_id, $old_group, $new_group)
{
    $sql = "UPDATE " . $GLOBALS['ecs']->table('attribute') .
            " SET attr_group='$new_group' WHERE cat_id='$cat_id' AND attr_group='$old_group'";
    $GLOBALS['db']->query($sql);
}

?>
