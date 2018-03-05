<?php

/**
 * ECSHOP 属性规格管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: attribute.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
$smarty->assign('menus',$_SESSION['menus']);
$exc = new exchange($ecs->table("attribute"), $db, 'attr_id', 'attr_name');
//echo basename(PHP_SELF,'.php');die;
$smarty->assign('current', basename(PHP_SELF,'.php'));
/*------------------------------------------------------ */
//-- 属性列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('menu_select', array('action'=>'02_cat_and_goods', 'current'=>'08_goods_type'));
    
    $goods_type = isset($_GET['goods_type']) ? intval($_GET['goods_type']) : 0;

    $smarty->assign('ur_here',          $_LANG['09_attribute_list']);
    $smarty->assign('goods_type_list',  goods_type_list($goods_type)); // 取得商品类型
    $smarty->assign('full_page',        1);

    $list = get_attrlist();
    $smarty->assign('attr_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
	
	//ecmoban模板堂 --zhuo start
	$adminru = get_admin_ru_id();
	if($GLOBALS['_CFG']['attr_set_up'] == 0){
		if($adminru['ru_id'] == 0){
			$smarty->assign('action_link',      array('href' => 'attribute.php?act=add&goods_type='.$goods_type , 'text' => $_LANG['10_attribute_add']));
			$smarty->assign('attr_set_up',   1);
		}else{
			$smarty->assign('attr_set_up',   0);
		}
	}elseif($GLOBALS['_CFG']['attr_set_up'] == 1){
		$smarty->assign('action_link',      array('href' => 'attribute.php?act=add&goods_type='.$goods_type , 'text' => $_LANG['10_attribute_add']));
		$smarty->assign('attr_set_up',   1);
	}
	//ecmoban模板堂 --zhuo end

    /* 显示模板 */
    assign_query_info();
    // $smarty->display('attribute_list_old.dwt');
    $smarty->display('attribute_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、翻页
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query')
{
    $list = get_attrlist();
	
	//ecmoban模板堂 --zhuo start
	$adminru = get_admin_ru_id();
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

    $smarty->assign('attr_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('attribute_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑属性
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('attr_manage');
    
    $smarty->assign('menu_select', array('action'=>'02_cat_and_goods', 'current'=>'08_goods_type'));

    /* 添加还是编辑的标识 */
    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('form_act', $is_add ? 'insert' : 'update');

    /* 取得属性信息 */
    if ($is_add)
    {
		$add_edit_cenetent = "暂时没有添加属性权限";
        $goods_type = isset($_GET['goods_type']) ? intval($_GET['goods_type']) : 0;
        $attr = array(
            'attr_id' => 0,
            'cat_id' => $goods_type,
			'attr_cat_type' => 0, //by zhang
            'attr_name' => '',
            'attr_input_type' => 0,
            'attr_index'  => 0,
            'attr_values' => '',
            'attr_type' => 0,
            'is_linked' => 0,
        );
    }
    else
    {
		$add_edit_cenetent = "暂时没有编辑属性权限";
        $sql = "SELECT * FROM " . $ecs->table('attribute') . " WHERE attr_id = '$_REQUEST[attr_id]'";
        $attr = $db->getRow($sql);
    }
	
	//ecmoban模板堂 --zhuo start
	$adminru = get_admin_ru_id();
	if($GLOBALS['_CFG']['attr_set_up'] == 0){
		if($adminru['ru_id'] > 0){
			$links = array(array('href' => 'goods_type.php?act=manage', 'text' => $_LANG['back_list']));
        	sys_msg($add_edit_cenetent, 0, $links);
			exit;
		}
	}
	//ecmoban模板堂 --zhuo end

    $smarty->assign('attr', $attr);
    $smarty->assign('attr_groups', get_attr_groups($attr['cat_id']));

    /* 取得商品分类列表 */
    $smarty->assign('goods_type_list', goods_type_list($attr['cat_id']));

    /* 模板赋值 */
    $smarty->assign('ur_here', $is_add ?$_LANG['10_attribute_add']:$_LANG['52_attribute_add']);
    $smarty->assign('action_link', array('href' => 'attribute.php?act=list', 'text' => $_LANG['09_attribute_list']));

    /* 显示模板 */
    assign_query_info();
    $smarty->display('attribute_info.dwt');
}

/*------------------------------------------------------ */
//-- 插入/更新属性
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('attr_manage');

    /* 插入还是更新的标识 */
    $is_insert = $_REQUEST['act'] == 'insert';

    $cat_id = isset($_REQUEST['cat_id']) && !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;
    $sort_order = isset($_POST['sort_order']) && !empty($_POST['sort_order']) ? $_POST['sort_order'] : 0;

    /* 检查名称是否重复 */
    $exclude = empty($_POST['attr_id']) ? 0 : intval($_POST['attr_id']);
    if (!$exc->is_only('attr_name', $_POST['attr_name'], $exclude, " cat_id = '$cat_id'"))
    {
        sys_msg($_LANG['name_exist'], 1);
    }

    /* 取得属性信息 */
    $attr = array(
        'cat_id'          => $_POST['cat_id'],
        'attr_name'       => $_POST['attr_name'],
		'attr_cat_type'   => $_POST['attr_cat_type'], //by zhang
        'attr_index'      => $_POST['attr_index'],
        'sort_order'      => $sort_order,
        'attr_input_type' => $_POST['attr_input_type'],
        'is_linked'       => $_POST['is_linked'],
        'attr_values'     => isset($_POST['attr_values']) ? $_POST['attr_values'] : '',
       'attr_type'       => empty($_POST['attr_type']) ? '0' : intval($_POST['attr_type']),
        'attr_group'      => isset($_POST['attr_group']) ? intval($_POST['attr_group']) : 0
    );

    /* 入库、记录日志、提示信息 */
    if ($is_insert)
    {
        $db->autoExecute($ecs->table('attribute'), $attr, 'INSERT');

        $attr_id = $db->insert_id();

        $sql = "SELECT MAX(sort_order) AS sort FROM " . $ecs->table('attribute') . " WHERE cat_id = '$cat_id'";
        $sort = $db->getOne($sql);
        
        if (empty($attr['sort_order']) && !empty($sort))
        {
            $attr = array('sort_order' => $attr_id);
            $db->autoExecute($ecs->table('attribute'), $attr, 'UPDATE', "attr_id = '$attr_id'");
        }

        admin_log($_POST['attr_name'], 'add', 'attribute');
        $links = array(
            array('text' => $_LANG['add_next'], 'href' => '?act=add&goods_type=' . $cat_id),
            array('text' => $_LANG['back_list'], 'href' => '?act=list&goods_type=' . $cat_id),
        );
        sys_msg(sprintf($_LANG['add_ok'], $attr['attr_name']), 0, $links);
    }
    else
    {
        $db->autoExecute($ecs->table('attribute'), $attr, 'UPDATE', "attr_id = '$exclude'");
        admin_log($_POST['attr_name'], 'edit', 'attribute');
        $links = array(
            array('text' => $_LANG['back_list'], 'href' => '?act=list&amp;goods_type=' . $cat_id . ''),
        );
        sys_msg(sprintf($_LANG['edit_ok'], $attr['attr_name']), 0, $links);
    }
}
/*------------------------------------------------------ */
//-- ajax  插入 cwl
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'ajaxinsert')
{
    /* 检查权限 */
    admin_priv('attr_manage');
    include(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    /* 插入还是更新的标识 */
    $is_insert = $_REQUEST['act'] == 'ajaxinsert';

    /* 检查名称是否重复 */
    $exclude = empty($_POST['attr_id']) ? 0 : intval($_POST['attr_id']);
    if (!$exc->is_only('attr_name', $_POST['attr_name'], $exclude, " cat_id = '$_POST[cat_id]'"))
    {
        $result['error']=1;
        $result['message']='属性名称重复';
        die($json->encode($result));
    }

    $cat_id = $_REQUEST['cat_id'];

    /* 取得属性信息 */
    $attr = array(
        'cat_id'          => $_POST['cat_id'],
        'attr_name'       => $_POST['attr_name'],
        'attr_cat_type'   => $_POST['attr_cat_type'], //by zhang
        'attr_index'      => $_POST['attr_index'],
        // 'attr_input_type' => $_POST['attr_input_type'],
        'sort_order' => intval($_POST['sort_order']),
        'attr_input_type' => 0,
        'is_linked'       => $_POST['is_linked'],
        'attr_values'     => isset($_POST['attr_values']) ? $_POST['attr_values'] : '',
        // 'attr_type'       => empty($_POST['attr_type']) ? '0' : intval($_POST['attr_type']),
        'attr_type'       => 1,
        'attr_group'      => isset($_POST['attr_group']) ? intval($_POST['attr_group']) : 0
    );

    /* 入库、记录日志、提示信息 */
    if ($is_insert)
    {
        $db->autoExecute($ecs->table('attribute'), $attr, 'INSERT');
        admin_log($_POST['attr_name'], 'add', 'attribute');
        $links = array(
            array('text' => $_LANG['add_next'], 'href' => '?act=add&goods_type=' . $_POST['cat_id']),
            array('text' => $_LANG['back_list'], 'href' => '?act=list'),
        );

        $result['error']=0;
        $result['message']='插入成功';
        die($json->encode($result));
      //  sys_msg(sprintf($_LANG['add_ok'], $attr['attr_name']), 0, $links);
    }
    else
    {
        $result['error']=1;
        $result['message']='插入失败';
        die($json->encode($result));
    }



   /* else
    {
        $db->autoExecute($ecs->table('attribute'), $attr, 'UPDATE', "attr_id = '$_POST[attr_id]'");
        admin_log($_POST['attr_name'], 'edit', 'attribute');
        $links = array(
            array('text' => $_LANG['back_list'], 'href' => '?act=list&amp;goods_type='.$_POST['cat_id'].''),
        );
        sys_msg(sprintf($_LANG['edit_ok'], $attr['attr_name']), 0, $links);
    }*/
}

/*------------------------------------------------------ */
//-- 设置属性颜色  查询现有颜色 by zhang start
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'set_gcolor')
{

	$attr_id = empty($_REQUEST['attr_id']) ? 0 : intval($_REQUEST['attr_id']);

	$SQL="SELECT color_values FROM " .$ecs->table('attribute'). " WHERE attr_id='$attr_id'";

	$list=$db->getOne($SQL); // 查询颜色
	
	$list2=array();
	if(!empty($list)){
		$list=explode("\n",trim($list));

		for ($i=0; $i <count($list); $i++) { 
			if(!stripos($list[$i],"_#")){
				$list[$i]=trim($list[$i])."_#FFFFFF";
			}
				$color=explode("_",$list[$i]);
				$list2[$i]=$color;
		}
	}
        
        $attr_values = get_add_attr_values($attr_id, 1, $list2);
        
        $smarty->assign('attr_values', $attr_values);
	$smarty->assign('attr_id', $attr_id);
	$smarty->assign('ur_here', $_LANG['set_gcolor']);
	$smarty->assign('form_act', 'gcolor_insert');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('set_gcolor.htm');
}

/*------------------------------------------------------ */
//-- 设置属性颜色 修改属性
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'gcolor_insert')
{
	$attr_id = empty($_REQUEST['attr_id']) ? 0 : intval($_REQUEST['attr_id']);
	unset($_GET['attr_id']);
	unset($_GET['act']);
	$str = '';

 	foreach ($_GET as $key_c => $value_c) {
 		if(empty($value_c)){
 			$link[] = array('text' => $_LANG['go_back'], 'href' => 'attribute.php?act=set_gcolor&attr_id=' . $attr_id);
   			sys_msg('请选择颜色', 0, $link);
   			exit;
 		}
 	}
	
	foreach($_GET as $k=>$v) {
		$sql = "UPDATE " .$ecs->table('goods_attr'). " SET color_value = '$v'" . " WHERE attr_id = '$attr_id' AND attr_value = '$k'";
		$db->query($sql);
	}

	foreach($_GET as $k=>$v) {
		$str.= $k."_#".$v."\n";
	}
	$str=strtoupper(trim($str));
 
	$update_color="UPDATE " .$ecs->table('attribute'). " SET `color_values` = '$str' WHERE `attr_id` = '$attr_id'";
	$db -> query($update_color); // 修改颜色
    $link[] = array('text' => $_LANG['go_back'], 'href' => 'attribute.php?act=set_gcolor&attr_id=' . $attr_id);
    sys_msg('属颜色修改成功', 0, $link);
}
//by zhuo end

/*------------------------------------------------------ */
//-- 插入/更新属性图片 
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_img')
{
    /* 检查权限 */
    admin_priv('attr_manage');
    
	$smarty->assign('menu_select', array('action'=>'02_cat_and_goods', 'current'=>'08_goods_type'));
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $attr_name = isset($_REQUEST['attr_name']) ? $_REQUEST['attr_name'] : '';
    
    $attr_values = get_add_attr_values($attr_id);
    $smarty->assign('attr_values', $attr_values);
    $smarty->assign('attr_name', $attr_name);
    $smarty->assign('attr_id', $attr_id);
    
     /* 模板赋值 */
    $smarty->assign('ur_here', "编辑属性图片");
    $smarty->assign('action_link2', array('href' => 'attribute.php?act=edit&attr_id=' . $attr_id, 'text' => $_LANG['go_back']));
    $smarty->assign('action_link', array('href' => 'attribute.php?act=list', 'text' => $_LANG['09_attribute_list']));
    $smarty->assign('form_act', 'insert_img');
        
    /* 显示模板 */
    assign_query_info();
    $smarty->display('attribute_img.dwt');
}

elseif ($_REQUEST['act'] == 'insert_img')
{
    /* 检查权限 */
    admin_priv('attr_manage');
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $attr_name = isset($_REQUEST['attr_name']) ? $_REQUEST['attr_name'] : 0;
    $attr_values = get_add_attr_values($attr_id);

    get_attrimg_insert_update($attr_id, $attr_values);
    
    $link[0] = array('text' => $_LANG['go_back'], 'href' => "attribute.php?act=add_img&attr_id=" .$attr_id. "&attr_name=" . $attr_name);
    $link[1] = array('text' => "返回该属性", 'href' => 'attribute.php?act=edit&attr_id=' . $attr_id);
    $link[2] = array('text' => $_LANG['09_attribute_list'], 'href' => 'attribute.php?act=list');
    sys_msg("编辑成功", 0, $link);
}

/*------------------------------------------------------ */
//-- 删除属性(一个或多个)
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch')
{
    /* 检查权限 */
    admin_priv('attr_manage');

    /* 取得要操作的编号 */
    if (isset($_POST['checkboxes']))
    {
        $count = count($_POST['checkboxes']);
	$ids = isset($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
	
        $sql = "DELETE FROM " . $ecs->table('attribute') . " WHERE attr_id " . db_create_in($ids);
        $db->query($sql);

        $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE attr_id " . db_create_in($ids);
        $db->query($sql);

        /* 记录日志 */
        admin_log('', 'batch_remove', 'attribute');
        clear_cache_files();

        $link[] = array('text' => $_LANG['back_list'], 'href' => 'attribute.php?act=list');
        sys_msg(sprintf($_LANG['drop_ok'], $count), 0, $link);
    }
    else
    {
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'attribute.php?act=list');
        sys_msg($_LANG['no_select_arrt'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑属性名称
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_attr_name')
{
    check_authz_json('attr_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));

    /* 取得该属性所属商品类型id */
    $cat_id = $exc->get_name($id, 'cat_id');

    /* 检查属性名称是否重复 */
    if (!$exc->is_only('attr_name', $val, $id, " cat_id = '$cat_id'"))
    {
        make_json_error($_LANG['name_exist']);
    }

    $exc->edit("attr_name='$val'", $id);

    admin_log($val, 'edit', 'attribute');

    make_json_result(stripslashes($val));
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('attr_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    $exc->edit("sort_order='$val'", $id);

    admin_log(addslashes($exc->get_name($id)), 'edit', 'attribute');

    make_json_result(stripslashes($val));
}
/*------------------------------------------------------ */
//--ajax   删除属性(一个或多个)  cwl
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_del_attr')
{

    include(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    /* 检查权限 */
    admin_priv('attr_manage');

    /* 取得要操作的编号 */
    if (isset($_POST['del_string']))
    {

        $ids   = isset($_POST['del_string']) ? $_POST['del_string'] : '';

       // $arr_conut=(explode(",",$ids));
       // echo $ids;exit;
      //  $count = count($arr_conut);

        $sql = "DELETE FROM " . $ecs->table('attribute') . " WHERE attr_id " . db_create_in($ids);
        $db->query($sql);

        $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE attr_id " . db_create_in($ids);
        $db->query($sql);

        /* 记录日志 */
        admin_log('', 'batch_remove', 'attribute');
        clear_cache_files();


        $result['error']=0;

        die($json->encode($result));
       // $link[] = array('text' => $_LANG['back_list'], 'href' => 'attribute.php?act=list');
        //sys_msg(sprintf($_LANG['drop_ok'], $count), 0, $link);
    }
    else
    {

        $result['error']=1;
        $result['message']=$_LANG['login_failure'];
        die($json->encode($result));
       // $link[] = array('text' => $_LANG['back_list'], 'href' => 'attribute.php?act=list');
       // sys_msg($_LANG['no_select_arrt'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 删除商品属性
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('attr_manage');

    $id = intval($_GET['id']);

    $db->query("DELETE FROM " .$ecs->table('attribute'). " WHERE attr_id='$id'");
    $db->query("DELETE FROM " .$ecs->table('goods_attr'). " WHERE attr_id='$id'");

    $url = 'attribute.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 获取某属性商品数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_attr_num')
{
    check_authz_json('attr_manage');

    $id = intval($_GET['attr_id']);

    $sql = "SELECT COUNT(*) ".
           " FROM " . $ecs->table('goods_attr') . " AS a, ".
           $ecs->table('goods') . " AS g ".
           " WHERE g.goods_id = a.goods_id AND g.is_delete = 0 AND attr_id = '$id' ";

    $goods_num = $db->getOne($sql);

    if ($goods_num > 0)
    {
        $drop_confirm = sprintf($_LANG['notice_drop_confirm'], $goods_num);
    }
    else
    {
        $drop_confirm = $_LANG['drop_confirm'];
    }

    make_json_result(array('attr_id'=>$id, 'drop_confirm'=>$drop_confirm));
}

/*------------------------------------------------------ */
//-- 获得指定商品类型下的所有属性分组
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_attr_groups')
{
    check_authz_json('attr_manage');

    $cat_id = intval($_GET['cat_id']);
    $groups = get_attr_groups($cat_id);

    make_json_result($groups);
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

/**
 * 获取属性列表
 *
 * @return  array
 */
function get_attrlist()
{
	//ecmoban模板堂 --zhuo start
	$adminru = get_admin_ru_id();
	$ruCat = '';
	
	if($GLOBALS['_CFG']['attr_set_up'] == 0){
		if($adminru['ru_id'] > 0){
			$ruCat .= " and t.user_id = 0 ";
		}
	}elseif($GLOBALS['_CFG']['attr_set_up'] == 1){
		if($adminru['ru_id'] > 0){
			$ruCat = " and t.user_id = '" .$adminru['ru_id']. "'";
		}
	}
	//ecmoban模板堂 --zhuo end
	
    /* 查询条件 */
    $filter = array();
    $filter['goods_type'] = empty($_REQUEST['goods_type']) ? 0 : intval($_REQUEST['goods_type']);
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'sort_order' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	if((!empty($filter['goods_type']))){
		$where = " WHERE a.cat_id = '$filter[goods_type]' ";
	}else{
		$ruCat = " where t.user_id = '" .$adminru['ru_id']. "'";
	}
    $where = (!empty($filter['goods_type'])) ? " WHERE a.cat_id = '$filter[goods_type]' " : '';
	$where .= $ruCat;

    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('attribute') . " AS a "." LEFT JOIN " . $GLOBALS['ecs']->table('goods_type') . " AS t ON a.cat_id = t.cat_id "." $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 查询 */
    $sql = "SELECT a.*, t.cat_name " .
            " FROM " . $GLOBALS['ecs']->table('attribute') . " AS a ".
            " LEFT JOIN " . $GLOBALS['ecs']->table('goods_type') . " AS t ON a.cat_id = t.cat_id " . $where .
            " ORDER BY $filter[sort_by] $filter[sort_order] ".
            " LIMIT " . $filter['start'] .", $filter[page_size]";
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['attr_input_type_desc'] = $GLOBALS['_LANG']['value_attr_input_type'][$val['attr_input_type']];
        $row[$key]['attr_values']      = str_replace("\n", ", ", $val['attr_values']);
    }

    $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
?>
