<?php

/**
 * ECSHOP 程序说明
 * ===========================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: merchants_navigator.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"index");
//admin_priv('navigator');

$exc = new exchange($ecs->table("merchants_nav"), $db, 'id', 'name');

$smarty->assign('menu_select',array('action' => '19_merchants_store', 'current' => '04_merchants_basic_nav'));
$adminru = get_admin_ru_id();
/*------------------------------------------------------ */
//-- 自定义导航栏列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    admin_priv('seller_store_other');//by kong
    $smarty->assign('ur_here', $_LANG['navigator']);
    $smarty->assign('action_link', array('text' => $_LANG['add_new'], 'href' => 'merchants_navigator.php?act=add'));
    $smarty->assign('full_page',  1);

    $navdb = get_nav();
	
	//ecmoban模板堂 --zhuo start
	if($adminru['ru_id'] == 0)
    {
		$smarty->assign('priv_ru',   1);
	}
    else
    {
		$smarty->assign('priv_ru',   0);
	} 	
	//ecmoban模板堂 --zhuo end
	
	//分页
	$page_count_arr = seller_page($navdb,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);
	
    $smarty->assign('navdb',   $navdb['navdb']);
    $smarty->assign('filter',       $navdb['filter']);
    $smarty->assign('record_count', $navdb['record_count']);
    $smarty->assign('page_count',   $navdb['page_count']);


    assign_query_info();
    $smarty->assign('current', 'merchants_navigator');
    $smarty->display('store_navigation.dwt');
}
/*------------------------------------------------------ */
//-- 自定义导航栏列表Ajax
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $navdb = get_nav();

	//分页
	$page_count_arr = seller_page($navdb,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
	
	//ecmoban模板堂 --zhuo start
	if($adminru['ru_id'] == 0)
    {
		$smarty->assign('priv_ru',   1);
	}
    else
    {
		$smarty->assign('priv_ru',   0);
	} 	
	//ecmoban模板堂 --zhuo end
	
    $smarty->assign('navdb',    $navdb['navdb']);
    $smarty->assign('filter',       $navdb['filter']);
    $smarty->assign('record_count', $navdb['record_count']);
    $smarty->assign('page_count',   $navdb['page_count']);

    $sort_flag  = sort_flag($navdb['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $smarty->assign('current', 'merchants_navigator');	
    make_json_result($smarty->fetch('store_navigation.dwt'), '', array('filter' => $navdb['filter'], 'page_count' => $navdb['page_count']));
}
/*------------------------------------------------------ */
//-- 自定义导航栏增加
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{	
    if (empty($_REQUEST['step']))
    {
        $rt = array('act'=>'add');

        $sysmain = get_sysnav();

        $smarty->assign('action_link', array('text' => $_LANG['go_list'], 'href' => 'merchants_navigator.php?act=list'));
        $smarty->assign('ur_here', $_LANG['navigator']);
        assign_query_info();
        $smarty->assign('sysmain',$sysmain);
        $smarty->assign('rt', $rt);
		$smarty->assign('current', 'merchants_navigator');
		$smarty->display('store_navigation_add.dwt');
    }
    elseif ($_REQUEST['step'] == 2)
    {
        $item_name = $_REQUEST['item_name'];
        $item_url = $_REQUEST['item_url'];
        $item_ifshow = $_REQUEST['item_ifshow'];
        $item_opennew = $_REQUEST['item_opennew'];
        $item_type = $_REQUEST['item_type'];
		$item_catId = trim($_REQUEST['item_catId']);
		$item_catId = intval($item_catId);

        $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('merchants_nav') . " WHERE type = '". $item_type ."'");

        $item_vieworder = empty($_REQUEST['item_vieworder']) ? $vieworder+1 : $_REQUEST['item_vieworder'];

        if($item_ifshow == 1 && $item_type == 'middle')
        {
            //如果设置为在中部显示

            $arr = analyse_uri($item_url);  //分析URI
            if($arr)
            {
                //如果为分类
                set_show_in_nav($arr['type'], $arr['id'], 1);   //设置显示
                $sql = "INSERT INTO " . $GLOBALS['ecs']->table('merchants_nav') . " (name,ctype,cid,ifshow,cat_id,vieworder,opennew,url,type,ru_id) VALUES('$item_name','".$arr['type']."','".$arr['id']."','$item_ifshow','$item_catId','$item_vieworder','$item_opennew','$item_url','$item_type', '" .$adminru['ru_id']. "')";
            }
        }

        if(empty($sql))
        {
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('merchants_nav') . " (name,ifshow,vieworder,opennew,url,type,ru_id) VALUES('$item_name','$item_ifshow','$item_vieworder','$item_opennew','$item_url','$item_type', '" .$adminru['ru_id']. "')";
        }
        $db->query($sql);
        clear_cache_files();
        $links[] = array('text' => $_LANG['navigator'], 'href' => 'merchants_navigator.php?act=list');
        $links[] = array('text' => $_LANG['add_new'], 'href' => 'merchants_navigator.php?act=add');
        sys_msg($_LANG['edit_ok'], 0, $links);
    }
}
/*------------------------------------------------------ */
//-- 自定义导航栏编辑
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = $_REQUEST['id'];
    if (empty($_REQUEST['step']))
    {
        $rt = array('act'=>'edit','id'=>$id);
        $row = $db->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('merchants_nav') . " WHERE id='$id'");
        $rt['item_name'] = $row['name'];
        $rt['item_url'] = $row['url'];
        $rt['item_vieworder'] = $row['vieworder'];
        $rt['item_ifshow_'.$row['ifshow']] = 'selected';
        $rt['item_opennew_'.$row['opennew']] = 'selected';
        $rt['item_type_'.$row['type']] = 'selected';
		$rt['item_catId'] = $row['cat_id'];

        $sysmain = get_sysnav();

        $smarty->assign('action_link', array('text' => $_LANG['go_list'], 'href' => 'merchants_navigator.php?act=list'));
        $smarty->assign('ur_here', $_LANG['navigator']);
        assign_query_info();
        $smarty->assign('sysmain',$sysmain);
        $smarty->assign('rt', $rt);
	    $smarty->assign('current', 'merchants_navigator');
        $smarty->display('store_navigation_add.dwt');
    }
    elseif ($_REQUEST['step'] == 2)
    {
        $item_name = $_REQUEST['item_name'];
        $item_url = $_REQUEST['item_url'];
        $item_ifshow = $_REQUEST['item_ifshow'];
        $item_opennew = $_REQUEST['item_opennew'];
        $item_type = $_REQUEST['item_type'];
        $item_vieworder = (int)$_REQUEST['item_vieworder'];
		$item_catId = trim($_REQUEST['item_catId']);
		$item_catId = intval($item_catId);

        $row = $db->getRow("SELECT ctype,cid,ifshow,type FROM " . $GLOBALS['ecs']->table('merchants_nav') . " WHERE id = '$id'");
        $arr = analyse_uri($item_url);

        if($arr)
        {
            //目标为分类
            if($row['ctype'] == $arr['type'] && $row['cid'] == $arr['id'])
            {
                //没有修改分类
                if($item_type != 'middle')
                {
                    //位置不在中部
                    set_show_in_nav($arr['type'], $arr['id'], 0);
                }
            }
            else
            {
                //修改了分类
                if($row['ifshow'] == 1 && $row['type'] == 'middle')
                {
                    //原来在中部显示
                    set_show_in_nav($row['ctype'], $row['cid'], 0); //设置成不显示
                }
                elseif($row['ifshow'] == 0 && $row['type'] == 'middle')
                {
                    //原来不显示
                }
            }

            //分类判断
            if($item_ifshow != is_show_in_nav($arr['type'], $arr['id']) && $item_type == 'middle')
            {
                 set_show_in_nav($arr['type'], $arr['id'], $item_ifshow);
            }
            $sql = "UPDATE " . $GLOBALS['ecs']->table('merchants_nav') .
                " SET name='$item_name',ctype='" . $arr['type'] . "',cid='" . $arr['id'] . "',ifshow='$item_ifshow',vieworder='$item_vieworder',opennew='$item_opennew',url='$item_url',type='$item_type' WHERE id='$id'";
        }
        else
        {
            //目标不是分类
            if($row['ctype'] && $row['cid'])
            {
                //原来是分类
                set_show_in_nav($row['ctype'], $row['cid'], 0);
            }

            $sql = "UPDATE " . $GLOBALS['ecs']->table('merchants_nav') .
                " SET name='$item_name',ctype='',cid='',ifshow='$item_ifshow',cat_id='$item_catId',vieworder='$item_vieworder',opennew='$item_opennew',url='$item_url',type='$item_type' WHERE id='$id'";
        }


        $db->query($sql);
        clear_cache_files();
        $links[] = array('text' => $_LANG['navigator'], 'href' => 'merchants_navigator.php?act=list');
        sys_msg($_LANG['edit_ok'], 0, $links);
    }
}
/*------------------------------------------------------ */
//-- 自定义导航栏删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'del')
{
    $id = (int)$_GET['id'];
    $row = $db->getRow("SELECT ctype,cid,type FROM " . $GLOBALS['ecs']->table('merchants_nav') . " WHERE id = '$id' LIMIT 1");

    if($row['type'] == 'middle' && $row['ctype'] && $row['cid'])
    {
        set_show_in_nav($row['ctype'], $row['cid'], 0);
    }

    $sql = " DELETE FROM " . $GLOBALS['ecs']->table('merchants_nav') . " WHERE id='$id' LIMIT 1";
    $db->query($sql);
    clear_cache_files();
    ecs_header("Location: merchants_navigator.php?act=list\n");
    exit;
}

/*------------------------------------------------------ */
//-- 编辑排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    //check_authz_json('nav');

    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    /* 检查输入的值是否合法 */
    if (!preg_match("/^[0-9]+$/", $order))
    {
        make_json_error(sprintf($_LANG['enter_int'], $order));
    }
    else
    {
        if ($exc->edit("vieworder = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_ifshow')
{
    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    $row = $db->getRow("SELECT type,ctype,cid FROM " . $GLOBALS['ecs']->table('merchants_nav') . " WHERE id = '$id' LIMIT 1");

    if($row['type'] == 'middle' && $row['ctype'] && $row['cid'])
    {
        set_show_in_nav($row['ctype'], $row['cid'], $val);
    }

    if (nav_update($id, array('ifshow' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切换是否新窗口
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_opennew')
{
    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (nav_update($id, array('opennew' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}



function get_nav()
{
	$adminru = get_admin_ru_id();
	
	$where = '';
	if($adminru['ru_id'] > 0){
		$where .= ' where ru_id = ' . $adminru['ru_id'];
	}
	
    $result = get_filter();
    if($result === false)
    {
        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'type DESC, vieworder' : 'type DESC, '.trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);

        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('merchants_nav') . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT id, name, ifshow, vieworder, opennew, url, type, ru_id".
               " FROM ".$GLOBALS['ecs']->table('merchants_nav') . $where .
               " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
               " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $navdb = $GLOBALS['db']->getAll($sql);

    $type = "";
    $navdb2 = array();
    foreach($navdb as $k=>$v)
    {
        if(!empty($type) && $type != $v['type'])
        {
            $navdb2[] = array();
        }
        $navdb2[] = $v;
		
		$data = array('shoprz_brandName', 'shop_class_keyWords', 'shopNameSuffix');
		$shop_info = get_table_date('merchants_shop_information', "user_id = '" .$v['ru_id']. "'", $data);
        $navdb2[$k]['user_name'] = $shop_info['shoprz_brandName'].$shop_info['shopNameSuffix'];
		
        $type = $v['type'];
    }

    $arr = array('navdb' => $navdb2, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/*------------------------------------------------------ */
//-- 排序相关
/*------------------------------------------------------ */
function sort_nav($a,$b)
{
    return  $a['vieworder'] > $b['vieworder'] ? 1 : -1;
}

/*------------------------------------------------------ */
//-- 获得系统列表
/*------------------------------------------------------ */
function get_sysnav()
{
    $adminru = get_admin_ru_id();

    global $_LANG;

    $catlist = cat_list(0, 0, 0, 'merchants_category', array(), 0, $adminru['ru_id']);
    foreach($catlist as $key => $val)
    {
        $val['url'] = build_uri('merchants_store', array('cid' => $val['cat_id'], 'urid' => $adminru['ru_id']), $val['cat_name']);
        $sysmain[] = array('cat_id' => $val['cat_id'], 'cat_name' => $val['cat_name'], 'url' => $val['url']);
    }
    return $sysmain;
}

/*------------------------------------------------------ */
//-- 列表项修改
/*------------------------------------------------------ */
function nav_update($id, $args)
{
    if (empty($args) || empty($id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_nav'), $args, 'update', "id='$id'");
}

/*------------------------------------------------------ */
//-- 根据URI对导航栏项目进行分析，确定其为商品分类还是文章分类
/*------------------------------------------------------ */
function analyse_uri($uri)
{
    $uri = strtolower(str_replace('&amp;', '&', $uri));
    $arr = explode('-', $uri);
    switch($arr[0])
    {
        case 'category' :
            return array('type' => 'c', 'id' => $arr[1]);
        break;
        case 'article_cat' :
            return array('type' => 'a', 'id' => $arr[1]);
        break;
        default:

        break;
    }

    list($fn, $pm) = explode('?', $uri);

    if(strpos($uri, '&') === FALSE)
    {
        $arr = array($pm);
    }
    else
    {
        $arr = explode('&', $pm);
    }
    switch($fn)
    {
        case 'category.php' :
            //商品分类
            foreach($arr as $k => $v)
            {
                list($key, $val) = explode('=', $v);
                if($key == 'id')
                {
                    return array('type' => 'c', 'id'=> $val);
                }
            }
        break;
        case 'article_cat.php'  :
            //文章分类
            foreach($arr as $k => $v)
            {
                list($key, $val) = explode('=', $v);
                if($key == 'id')
                {
                    return array('type' => 'a', 'id'=> $val);
                }
            }
        break;
        default:
            //未知
            return false;
        break;
    }

}

/*------------------------------------------------------ */
//-- 是否显示
/*------------------------------------------------------ */
function is_show_in_nav($type, $id)
{
    if($type == 'c')
    {
        $tablename = $GLOBALS['ecs']->table('category');
    }
    else
    {
        $tablename = $GLOBALS['ecs']->table('article_cat');
    }
    return $GLOBALS['db']->getOne("SELECT show_in_nav FROM $tablename WHERE cat_id = '$id'");
}

/*------------------------------------------------------ */
//-- 设置是否显示
/*------------------------------------------------------ */
function set_show_in_nav($type, $id, $val)
{
    if($type == 'c')
    {
        $tablename = $GLOBALS['ecs']->table('category');
    }
    else
    {
        $tablename = $GLOBALS['ecs']->table('article_cat');
    }
    $GLOBALS['db']->query("UPDATE $tablename SET show_in_nav = '$val' WHERE cat_id = '$id'");
    clear_cache_files();
}
?>
