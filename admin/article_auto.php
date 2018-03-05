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
 * $Id: article_auto.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
admin_priv('article_auto');
$smarty->assign('thisfile', 'article_auto.php');
if ($_REQUEST['act'] == 'list')
{
    $goodsdb = get_auto_goods();
    $crons_enable = $db->getOne("SELECT enable FROM " . $GLOBALS['ecs']->table('crons') . " WHERE cron_code='ipdel'");
    $smarty->assign('crons_enable', $crons_enable);
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      $_LANG['article_auto']);
    $smarty->assign('goodsdb',      $goodsdb['goodsdb']);
    $smarty->assign('filter',       $goodsdb['filter']);
    $smarty->assign('record_count', $goodsdb['record_count']);
    $smarty->assign('page_count',   $goodsdb['page_count']);
    $smarty->assign('article_type',      1);
    assign_query_info();
    $smarty->display('goods_auto.dwt');
}
elseif ($_REQUEST['act'] == 'query')
{
    $goodsdb = get_auto_goods();
    $smarty->assign('goodsdb',    $goodsdb['goodsdb']);
    $smarty->assign('filter',       $goodsdb['filter']);
    $smarty->assign('record_count', $goodsdb['record_count']);
    $smarty->assign('page_count',   $goodsdb['page_count']);

    $sort_flag  = sort_flag($goodsdb['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('goods_auto.dwt'), '', array('filter' => $goodsdb['filter'], 'page_count' => $goodsdb['page_count']));
}
elseif ($_REQUEST['act'] == 'del')
{
    $goods_id = (int)$_REQUEST['goods_id'];
    $sql = "DELETE FROM " . $ecs->table('auto_manage') . " WHERE item_id = '$goods_id' AND type = 'article'";
    $db->query($sql);
    $links[] = array('text' => $_LANG['article_auto'], 'href' => 'article_auto.php?act=list');
    sys_msg($_LANG['edit_ok'], 0 ,$links);
}

//批量发布
elseif ($_REQUEST['act'] == 'batch_start')
{
    admin_priv('goods_auto');
    
    if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_select_goods'], 1);
    }

    if($_POST['date'] == '0000-00-00')
    {
        $_POST['date'] = 0;
    }
    else
    {
         $_POST['date'] = local_strtotime(trim($_POST['date']));
    }

    foreach($_POST['checkboxes'] as $id)
    {
        $db->autoReplace($ecs->table('auto_manage'), array('item_id' => $id,'type' => 'article',
            'starttime' => $_POST['date']), array('starttime' =>(string)$_POST['date']));
    }

    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'article_auto.php?act=list');
    sys_msg($_LANG['batch_start_succeed'], 0, $lnk);
}

//批量取消发布
elseif ($_REQUEST['act'] == 'batch_end')
{
    admin_priv('goods_auto');

    if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
    {
        sys_msg($_LANG['no_select_goods'], 1);
    }

    if($_POST['date'] == '0000-00-00')
    {
        $_POST['date'] = 0;
    }
    else
    {
         $_POST['date'] = local_strtotime(trim($_POST['date']));
    }

    foreach($_POST['checkboxes'] as $id)
    {
        $db->autoReplace($ecs->table('auto_manage'), array('item_id' => $id,'type' => 'article',
            'endtime' => $_POST['date']), array('endtime' =>(string)$_POST['date']));
    }

    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'article_auto.php?act=list');
    sys_msg($_LANG['batch_end_succeed'], 0, $lnk);
}

function get_auto_goods()
{
    $where = '';
    if (!empty($_POST['goods_name']))
    {
        $goods_name = trim($_POST['goods_name']);
        $where = " WHERE g.title LIKE '%$goods_name%'";
        $filter['goods_name'] = $goods_name;
    }
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('article') . " g" . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    $goodsdb = array();
    $filter = page_and_size($filter);
    $sql = "SELECT g.*,a.starttime,a.endtime FROM " . $GLOBALS['ecs']->table('article') . " g LEFT JOIN " . $GLOBALS['ecs']->table('auto_manage') . " a ON g.article_id = a.item_id AND a.type='article'" . $where .
           " ORDER BY g. add_time DESC" .
           " LIMIT " . $filter['start'] . ",$filter[page_size]";
    $query = $GLOBALS['db']->query($sql);
    while ($rt = $GLOBALS['db']->fetch_array($query))
    {
        if (!empty($rt['starttime']))
        {
            $rt['starttime'] = local_date('Y-m-d',$rt['starttime']);
        }
        if (!empty($rt['endtime']))
        {
            $rt['endtime'] = local_date('Y-m-d',$rt['endtime']);
        }
        $rt['goods_id'] = $rt['article_id'];
        $rt['goods_name'] = $rt['title'];
        if($rt['cat_id'] > 0){
            $sql = "SELECT cat_name FROM".$GLOBALS['ecs']->table('article_cat')." WHERE cat_id = '".$rt['cat_id']."'";
             $rt['cat_name'] = $GLOBALS['db']->getOne($sql);
        }
        $goodsdb[] = $rt;
    }
    $arr = array('goodsdb' => $goodsdb, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}
?>
