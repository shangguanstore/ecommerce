<?php

/**
 * ECSHOP 合作伙伴管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: friend_partner.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('partner_list'), $db, 'link_id', 'link_name');

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
//-- 友情链接列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['list_link']);
    $smarty->assign('action_link', array('text' => $_LANG['add_link'], 'href' => 'friend_partner.php?act=add'));
     $smarty->assign('full_page',   1);

    /* 获取友情链接数据 */
    $links_list = get_links_list();

    $smarty->assign('links_list',      $links_list['list']);
    $smarty->assign('filter',          $links_list['filter']);
    $smarty->assign('record_count',    $links_list['record_count']);
    $smarty->assign('page_count',      $links_list['page_count']);

    $sort_flag  = sort_flag($links_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('partner_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    /* 获取友情链接数据 */
    $links_list = get_links_list();

    $smarty->assign('links_list',      $links_list['list']);
    $smarty->assign('filter',          $links_list['filter']);
    $smarty->assign('record_count',    $links_list['record_count']);
    $smarty->assign('page_count',      $links_list['page_count']);

    $sort_flag  = sort_flag($links_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('partner_list.dwt'), '',
        array('filter' => $links_list['filter'], 'page_count' => $links_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加新链接页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    admin_priv('partnerlink');

    $smarty->assign('ur_here',     $_LANG['add_link']);
    $smarty->assign('action_link', array('href'=>'friend_partner.php?act=list', 'text' => $_LANG['list_link']));
    $smarty->assign('action',      'add');
    $smarty->assign('form_act',    'insert');

    assign_query_info();
    $smarty->display('partner_info.dwt');
}

/*------------------------------------------------------ */
//-- 处理添加的链接
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 变量初始化 */
    $link_logo = '';
    $show_order = (!empty($_POST['show_order'])) ? intval($_POST['show_order']) : 0;
    $link_name  = (!empty($_POST['link_name']))  ? sub_str(trim($_POST['link_name']), 250, false) : '';

    /* 查看链接名称是否有重复 */
    if ($exc->num("link_name", $link_name) == 0)
    {
        /* 处理上传的LOGO图片 */
        if ((isset($_FILES['link_img']['error']) && $_FILES['link_img']['error'] == 0) || (!isset($_FILES['link_img']['error']) && isset($_FILES['link_img']['tmp_name']) && $_FILES['link_img']['tmp_name'] != 'none'))
        {
            $img_up_info = @basename($image->upload_image($_FILES['link_img'], 'afficheimg'));
            $link_logo   = DATA_DIR . '/afficheimg/' .$img_up_info;
        }

        /* 使用远程的LOGO图片 */
        if (!empty($_POST['url_logo']))
        {
            if (strpos($_POST['url_logo'], 'http://') === false && strpos($_POST['url_logo'], 'https://') === false)
            {
                $link_logo = 'http://' .trim($_POST['url_logo']);
            }
            else
            {
                $link_logo = trim($_POST['url_logo']);
            }
        }

        /* 如果链接LOGO为空, LOGO为链接的名称 */
        if (((isset($_FILES['upfile_flash']['error']) && $_FILES['upfile_flash']['error'] > 0) || (!isset($_FILES['upfile_flash']['error']) && isset($_FILES['upfile_flash']['tmp_name']) && $_FILES['upfile_flash']['tmp_name'] == 'none')) && empty($_POST['url_logo']))
        {
            $link_logo = '';
        }

        /* 如果友情链接的链接地址没有http://，补上 */
        if (strpos($_POST['link_url'], 'http://') === false && strpos($_POST['link_url'], 'https://') === false)
        {
            $link_url = 'http://' . trim($_POST['link_url']);
        }
        else
        {
            $link_url = trim($_POST['link_url']);
        }
        
        get_oss_add_file(array($link_logo));        

        /* 插入数据 */
        $sql    = "INSERT INTO ".$ecs->table('partner_list')." (link_name, link_url, link_logo, show_order) ".
                  "VALUES ('$link_name', '$link_url', '$link_logo', '$show_order')";
        $db->query($sql);

        /* 记录管理员操作 */
        admin_log($_POST['link_name'], 'add', 'partnerlink');

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'friend_partner.php?act=add';

        $link[1]['text'] = $_LANG['back_list'];
        $link[1]['href'] = 'friend_partner.php?act=list';

        sys_msg($_LANG['add'] . "&nbsp;" .stripcslashes($_POST['link_name']) . " " . $_LANG['attradd_succed'],0, $link);

    }
    else
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg($_LANG['link_name_exist'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 友情链接编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('partnerlink');

    /* 取得友情链接数据 */
    $sql = "SELECT link_id, link_name, link_url, link_logo, show_order ".
           "FROM " .$ecs->table('partner_list'). " WHERE link_id = '".intval($_REQUEST['id'])."'";
    $link_arr = $db->getRow($sql);

    /* 标记为图片链接还是文字链接 */
    if (!empty($link_arr['link_logo']))
    {
        $type      = 'img';
        $link_logo = $link_arr['link_logo'];
    }
    else
    {
        $type      = 'chara';
        $link_logo = '';
    }

    $link_arr['link_name'] = sub_str($link_arr['link_name'], 250, false); // 截取字符串为250个字符避免出现非法字符的情况

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['edit_link']);
    $smarty->assign('action_link', array('href'=>'friend_partner.php?act=list&' . list_link_postfix(), 'text' => $_LANG['list_link']));
    $smarty->assign('form_act',    'update');
    $smarty->assign('action',      'edit');

    $smarty->assign('type',        $type);
    $smarty->assign('link_logo',   $link_logo);
    $smarty->assign('link_arr',    $link_arr);

    assign_query_info();
    $smarty->display('partner_info.dwt');
}

/*------------------------------------------------------ */
//-- 编辑链接的处理页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
    /* 变量初始化 */
    $id         = (!empty($_REQUEST['id']))      ? intval($_REQUEST['id'])      : 0;
    $show_order = (!empty($_POST['show_order'])) ? intval($_POST['show_order']) : 0;
    $link_name  = (!empty($_POST['link_name']))  ? trim($_POST['link_name'])    : '';

    /* 如果有图片LOGO要上传 */
    if ((isset($_FILES['link_img']['error']) && $_FILES['link_img']['error'] == 0) || (!isset($_FILES['link_img']['error']) && isset($_FILES['link_img']['tmp_name']) && $_FILES['link_img']['tmp_name'] != 'none'))
    {
        $img_up_info = @basename($image->upload_image($_FILES['link_img'], 'afficheimg'));
        $link_logo   = ", link_logo = ".'\''. DATA_DIR . '/afficheimg/'.$img_up_info.'\'';
        $linklogo_img = DATA_DIR . '/afficheimg/' . $img_up_info;
    }
    elseif (!empty($_POST['url_logo']))
    {
        $link_logo = ", link_logo = '$_POST[url_logo]'";
        $linklogo_img = $_POST['url_logo'];
    }
    else
    {
        /* 如果是文字链接, LOGO为链接的名称 */
        $link_logo = ", link_logo = ''";
        $linklogo_img = '';
    }

    //如果要修改链接图片, 删除原来的图片
    if (!empty($img_up_info))
    {
        //获取链子LOGO,并删除
        $old_logo = $db->getOne("SELECT link_logo FROM " .$ecs->table('partner_list'). " WHERE link_id = '$id'");
        if ((strpos($old_logo, 'http://') === false) && (strpos($old_logo, 'https://') === false))
        {
            $img_name = basename($old_logo);
            @unlink(ROOT_PATH . DATA_DIR . '/afficheimg/' . $img_name);
            get_oss_del_file(array(DATA_DIR . '/afficheimg/' . $img_name));  
        }
    }

    /* 如果友情链接的链接地址没有http://，补上 */
    if (strpos($_POST['link_url'], 'http://') === false && strpos($_POST['link_url'], 'https://') === false)
    {
        $link_url = 'http://' . trim($_POST['link_url']);
    }
    else
    {
        $link_url = trim($_POST['link_url']);
    }
    
    get_oss_add_file(array($linklogo_img));  

    /* 更新信息 */
    $sql = "UPDATE " .$ecs->table('partner_list'). " SET ".
            "link_name = '$link_name', ".
            "link_url = '$link_url' ".
            $link_logo.','.
            "show_order = '$show_order' ".
            "WHERE link_id = '$id'";

    $db->query($sql);
    /* 记录管理员操作 */
    admin_log($_POST['link_name'], 'edit', 'partnerlink');

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'friend_partner.php?act=list&' . list_link_postfix();

    sys_msg($_LANG['edit'] . "&nbsp;" .stripcslashes($_POST['link_name']) . "&nbsp;" . $_LANG['attradd_succed'],0, $link);
}

/*------------------------------------------------------ */
//-- 删除图标
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'delPic')
{
    check_authz_json('partnerlink');

    $id = intval($_GET['link_id']);
    $result = array('error' => 0, 'content' => '');
    $link_logo = $exc->get_name($id, 'link_logo');
    get_oss_del_file(array($link_logo));
    @unlink(ROOT_PATH . $link_logo);
    $sql = " UPDATE " . $ecs->table('partner_list') . " SET  link_logo = ''  WHERE link_id = '$id' ";

    if (!$db->query($sql))
    {
        $result['error'] = 1;
    }

    make_json_result($result);
    exit;
}
/*------------------------------------------------------ */
//-- 编辑排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_show_order')
{
    check_authz_json('partnerlink');

    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    /* 检查输入的值是否合法 */
    if (!preg_match("/^[0-9]+$/", $order))
    {
        make_json_error(sprintf($_LANG['enter_int'], $order));
    }
    else
    {
        if ($exc->edit("show_order = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
    }
}
/*------------------------------------------------------ */
//-- 删除友情链接
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('partnerlink');

    $id = intval($_GET['id']);

    /* 获取链子LOGO,并删除 */
    $link_logo = $exc->get_name($id, "link_logo");

    get_oss_del_file(array($link_logo));
    @unlink(ROOT_PATH . $link_logo);

    $exc->drop($id);
    clear_cache_files();
    admin_log('', 'remove', 'partnerlink');

    $url = 'friend_partner.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}


/* 获取友情链接数据列表 */
function get_links_list()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'link_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        /* 获得总记录数据 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('partner_list');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取数据 */
        $sql  = 'SELECT link_id, link_name, link_url, link_logo, show_order'.
               ' FROM ' .$GLOBALS['ecs']->table('partner_list').
                " ORDER by $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $list = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        if (empty($rows['link_logo']))
        {
            $rows['link_logo'] = '';
        }
        else
        {
            if ((strpos($rows['link_logo'], 'http://') === false) && (strpos($rows['link_logo'], 'https://') === false))
            {
                $rows['link_logo'] = '../'.$rows['link_logo'];
            }
        }

        $list[] = $rows;
    }

    return array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
