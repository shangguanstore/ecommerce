<?php 

/**
 * ECSHOP 众筹分类管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: zc_category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("category"), $db, 'cat_id', 'cat_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') {
    /* 权限检查 */
    admin_priv('zc_category_manage');

    /* 修改 by wu */
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $sql = " SELECT * FROM " . $GLOBALS['ecs']->table('zc_category') . " WHERE parent_id = '$parent_id' ORDER BY sort_order, cat_id ";
    $cat_list = $GLOBALS['db']->getAll($sql);

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['02_crowdfunding_cat']);
    $smarty->assign('action_link', array('href' => 'zc_category.php?act=add', 'text' => $_LANG['add_zc_category']));
    $smarty->assign('full_page', 1);
    $smarty->assign('cat_info', $cat_list);

    /* 列表页面 */
    assign_query_info();
    $smarty->display('zc_category_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'query') {
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $sql = " SELECT * FROM " . $GLOBALS['ecs']->table('zc_category') . " WHERE parent_id = '$parent_id' ORDER BY sort_order, cat_id ";
    $cat_list = $GLOBALS['db']->getAll($sql);

    $smarty->assign('cat_info', $cat_list);

    make_json_result($smarty->fetch('zc_category_list.dwt'));
}

/*------------------------------------------------------ */
//-- 添加分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限检查 */
    admin_priv('zc_category_manage');
	
	$parent_id = empty($_REQUEST['parent_id'])? 0:intval($_REQUEST['parent_id']); //by wu

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['add_zc_category']);
    $smarty->assign('action_link',  array('href' => 'zc_category.php?act=list', 'text' => $_LANG['02_crowdfunding_cat']));

    $smarty->assign('cat_select',   zc_cat_list(0, $parent_id, false));
    $smarty->assign('form_act',     'insert');
    $smarty->assign('cat_info',     array('is_show' => 1,'is_group_show' => 0,'is_search_show' => 0,'is_search_show_layout' => 1)); //ecmoban模板堂 --zhuo

    /* 显示页面 */
    assign_query_info();
    $smarty->display('zc_category_info.dwt');
}

/*------------------------------------------------------ */
//-- 处理添加分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限检查 */
    admin_priv('zc_category_manage');
	
	$parent_id = empty($_REQUEST['parent_id'])? 0:intval($_REQUEST['parent_id']); //by wu

    /* 初始化变量 */
    $cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();

    if (cat_exists($cat['cat_name'], $cat['parent_id']))
    {
        /* 同级别下不能有重复的分类名称 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['catname_exist'], 0, $link);
    }
    /* 入库的操作 */
    if ($db->autoExecute($ecs->table('zc_category'), $cat) !== false)
    {
        $cat_id = $db->insert_id();
        //insert_cat_recommend($cat['cat_recommend'], $cat_id);

        admin_log($_POST['cat_name'], 'add', 'zc_category');   // 记录管理员操作
        clear_cache_files();    // 清除缓存

        /*添加链接*/
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'zc_category.php?act=add&parent_id='.$parent_id;

        $link[1]['text'] = $_LANG['go_list'];
        $link[1]['href'] = 'zc_category.php?act=list&parent_id='.$parent_id;

        sys_msg('分类添加成功', 0, $link);
    }
 }
 
/*------------------------------------------------------ */
//-- 编辑分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
	/* 权限检查 */
    admin_priv('zc_category_manage');
	
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['edit_zc_category']);
    $smarty->assign('action_link', array('text' => $_LANG['02_crowdfunding_cat'], 'href' => 'zc_category.php?act=list'));

    $smarty->assign('cat_info',    $cat_info);
    $smarty->assign('form_act',    'update');
    $smarty->assign('cat_select',  zc_cat_list(0, $cat_info['parent_id'], false));

    /* 显示页面 */
    assign_query_info();
    $smarty->display('zc_category_info.dwt');
}

/*------------------------------------------------------ */
//-- 更新分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 权限检查 */
    admin_priv('zc_category_manage');

    /* 初始化变量 */
    $cat_id              = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = !empty($_POST['parent_id'])    ? intval($_POST['parent_id'])  : 0;
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();

    /* 判断分类名是否重复 */

    if ($cat['cat_name'] != $old_cat_name)
    {
        if (cat_exists($cat['cat_name'],$cat['parent_id'], $cat_id))
        {
           $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
           sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }

    /* 判断上级目录是否合法 */
    $children = array_keys(zc_cat_list($cat_id, 0, false));     // 获得当前分类的所有下级分类
    if (in_array($cat['parent_id'], $children))
    {
        /* 选定的父类是当前分类或当前分类的下级分类 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['prev_category_wrong'], 0, $link);
    }

    if ($db->autoExecute($ecs->table('zc_category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
    {

        //更新首页推荐
		//insert_cat_recommend($cat['cat_recommend'], $cat_id);
        /* 更新分类信息成功 */
        clear_cache_files(); // 清除缓存
        admin_log($_POST['cat_name'], 'edit', 'zc_category'); // 记录管理员操作

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_list'], 'href' => 'zc_category.php?act=list');
        sys_msg($_LANG['edit_success'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 删除分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('zc_category_manage');

    /* 初始化分类ID并取得分类名称 */
    $cat_id   = intval($_GET['id']);
    $cat_name = $db->getOne('SELECT cat_name FROM ' .$ecs->table('zc_category'). " WHERE cat_id='$cat_id'");

    /* 当前分类下是否有子分类 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('zc_category'). " WHERE parent_id='$cat_id'");
	
	$goods_count = 0; //初始化分类下商品数量
	
    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' .$ecs->table('zc_project'). " WHERE cat_id='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0)
    {
        /* 删除分类 */
        $sql = 'DELETE FROM ' .$ecs->table('zc_category'). " WHERE cat_id = '$cat_id'";
        if ($db->query($sql))
        {
            clear_cache_files();
            admin_log($cat_name, 'remove', 'zc_category');
        }
    }
    else
    {
        make_json_error($cat_name .' '. $_LANG['cat_isleaf']);
    }

    $url = 'zc_category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 分类排序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('zc_category_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (zc_cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

//更新分类信息
function zc_cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('zc_category'), $args, 'update', "cat_id='$cat_id'");
}

/**
 * 过滤和排序所有分类，返回一个带有缩进级别的数组
 *
 * @access  private
 * @param   int     $cat_id     上级分类ID
 * @param   array   $arr        含有所有分类的数组
 * @param   int     $level      级别
 * @return  void
 */
function zc_cat_options($spec_cat_id, $arr)
{
    static $cat_options = array();

    if (isset($cat_options[$spec_cat_id]))
    {
        return $cat_options[$spec_cat_id];
    }

    if (!isset($cat_options[0]))
    {
        $level = $last_cat_id = 0;
        $options = $cat_id_array = $level_array = array();
        $data = read_static_cache('zc_cat_option_static');
        if ($data === false)
        {
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $cat_id = $value['cat_id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }

                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);

                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_cat_id  = $cat_id;
                        $cat_id_array = array($cat_id);
                        $level_array[$last_cat_id] = ++$level;
                        continue;
                    }

                    if ($value['parent_id'] == $last_cat_id)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);

                        if ($value['has_children'] > 0)
                        {
                            if (end($cat_id_array) != $last_cat_id)
                            {
                                $cat_id_array[] = $last_cat_id;
                            }
                            $last_cat_id    = $cat_id;
                            $cat_id_array[] = $cat_id;
                            $level_array[$last_cat_id] = ++$level;
                        }
                    }
                    elseif ($value['parent_id'] > $last_cat_id)
                    {
                        break;
                    }
                }

                $count = count($cat_id_array);
                if ($count > 1)
                {
                    $last_cat_id = array_pop($cat_id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_cat_id != end($cat_id_array))
                    {
                        $last_cat_id = end($cat_id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_cat_id = 0;
                        $cat_id_array = array();
                        continue;
                    }
                }

                if ($last_cat_id && isset($level_array[$last_cat_id]))
                {
                    $level = $level_array[$last_cat_id];
                }
                else
                {
                    $level = 0;
                }
            }
            //如果数组过大，不采用静态缓存方式
            if (count($options) <= 2000)
            {
                write_static_cache('zc_cat_option_static', $options);
            }
        }
        else
        {
            $options = $data;
        }
        $cat_options[0] = $options;
    }
    else
    {
        $options = $cat_options[0];
    }

    if (!$spec_cat_id)
    {
        return $options;
    }
    else
    {
        if (empty($options[$spec_cat_id]))
        {
            return array();
        }

        $spec_cat_id_level = $options[$spec_cat_id]['level'];

        foreach ($options AS $key => $value)
        {
            if ($key != $spec_cat_id)
            {
                unset($options[$key]);
            }
            else
            {
                break;
            }
        }

        $spec_cat_id_array = array();
        foreach ($options AS $key => $value)
        {
            if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                ($spec_cat_id_level > $value['level']))
            {
                break;
            }
            else
            {
                $spec_cat_id_array[$key] = $value;
            }
        }
        $cat_options[$spec_cat_id] = $spec_cat_id_array;

        return $spec_cat_id_array;
    }
}
?>