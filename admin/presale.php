<?php

/**
 * ECSHOP 管理中心预售商品管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: li $
 * $Id: presale.php 17217 2015-11-8 li $
 */

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');

/* 检查权限 */
admin_priv('presale');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

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
//-- 预售活动列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 模板赋值 */
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      $_LANG['presale_list']);
    $smarty->assign('action_link',  array('href' => 'presale.php?act=add', 'text' => $_LANG['add_presale']));
    
    if($adminru['ru_id'] == 0){
            $smarty->assign('presale_cat_link',  array('href' => 'presale_cat.php?act=list', 'text' => '预售分类列表'));
    }
    
    $list = presale_list($adminru['ru_id']);
	
    $smarty->assign('presale_list',   $list['item']);
    $smarty->assign('filter',           $list['filter']);
    $smarty->assign('record_count',     $list['record_count']);
    $smarty->assign('page_count',       $list['page_count']);
    
    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    /* 显示商品列表页面 */
    assign_query_info();
    $smarty->display('presale_list.dwt');
}

elseif ($_REQUEST['act'] == 'query')
{
    $list = presale_list($adminru['ru_id']);

    $smarty->assign('presale_list', $list['item']);
    $smarty->assign('filter',         $list['filter']);
    $smarty->assign('record_count',   $list['record_count']);
    $smarty->assign('page_count',     $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('presale_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加/编辑预售活动
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
    /* 初始化/取得预售活动信息 */
    if ($_REQUEST['act'] == 'add') {
        $presale = array(
            'cid' => 0,
            'start_time' => date('Y-m-d H:i:s', time() + 86400),
            'end_time' => date('Y-m-d H:i:s', time() + 4 * 86400),
            'pay_start_time' => date('Y-m-d H:i:s', time() + 4 * 86400 + 1),
            'pay_end_time' => date('Y-m-d H:i:s', time() + 6 * 86400)
        );
        $smarty->assign('ur_here', $_LANG['add_presale']);
    } else {
        $presale_id = intval($_REQUEST['id']);
        if ($presale_id <= 0) {
            die('invalid param');
        }
        $presale = presale_info($presale_id);
        $smarty->assign('ur_here', '编辑预售活动');
    }
    $smarty->assign('presale', $presale);

    /* 模板赋值 */
    $smarty->assign('action_link', list_link($_REQUEST['act'] == 'add'));

    $cat_select = presale_cat_list(0, $presale['cid'], false, 0, true, '', 1);
    /* 缩进分类 */
    foreach ($cat_select as $k => $v) {
        if ($v['level']) {
            $level = '';
            for ($i = 0; $i < $v['level']; $i++) {
                $level .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $cat_select[$k]['name'] = $level . $v['name'];
        }
    }
    $smarty->assign('cat_select', $cat_select);

    set_default_filter(); //设置默认筛选
    $smarty->assign('ru_id', $adminru['ru_id']);

    /* 显示模板 */
    assign_query_info();
    $smarty->display('presale_info.dwt');
}

/*------------------------------------------------------ */
//-- 添加/编辑预售活动的提交
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] =='insert_update')
{

    /* 取得预售活动id */
    $presale_id = intval($_POST['act_id']);
    if (isset($_POST['finish']) || isset($_POST['succeed']) || isset($_POST['fail']) || isset($_POST['mail']))
    {
        if ($presale_id <= 0)
        {
            sys_msg($_LANG['error_presale'], 1);
        }
        $presale = presale_info($presale_id);
        if (empty($presale))
        {
            sys_msg($_LANG['error_presale'], 1);
        }
    }

    if (isset($_POST['finish']))
    {
        /* 判断订单状态 */
        if ($presale['status'] != GBS_UNDER_WAY)
        {
            sys_msg($_LANG['error_status'], 1);
        }

        /* 结束预售活动，修改结束时间为当前时间 */
        $sql = "UPDATE " . $ecs->table('presale_activity') .
                " SET end_time = '" . gmtime() . "' " .
                "WHERE act_id = '$presale_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'presale.php?act=list', 'text' => $_LANG['back_list'])
        );
        sys_msg($_LANG['edit_success'], 0, $links);
    }
    elseif (isset($_POST['succeed']))
    {
        /* 设置活动成功 */

        /* 判断订单状态 */
        if ($presale['status'] != GBS_FINISHED)
        {
            sys_msg($_LANG['error_status'], 1);
        }

        /* 如果有订单，更新订单信息 */
        if ($presale['total_order'] > 0)
        {
            /* 查找该预售活动的已确认或未确认订单（已取消的就不管了） */
            $sql = "SELECT order_id " .
                    "FROM " . $ecs->table('order_info') .
                    " WHERE extension_code = 'presale' " .
                    "AND extension_id = '$presale_id' " .
                    "AND (order_status = '" . OS_CONFIRMED . "' or order_status = '" . OS_UNCONFIRMED . "')";
            $order_id_list = $db->getCol($sql);

            /* 更新订单商品价 */
            $final_price = $presale['trans_price'];
            $sql = "UPDATE " . $ecs->table('order_goods') .
                    " SET goods_price = '$final_price' " .
                    "WHERE order_id " . db_create_in($order_id_list);
            $db->query($sql);

            /* 查询订单商品总额 */
            $sql = "SELECT order_id, SUM(goods_number * goods_price) AS goods_amount " .
                    "FROM " . $ecs->table('order_goods') .
                    " WHERE order_id " . db_create_in($order_id_list) .
                    " GROUP BY order_id";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $order_id = $row['order_id'];
                $goods_amount = floatval($row['goods_amount']);

                /* 取得订单信息 */
                $order = order_info($order_id);

                /* 判断订单是否有效：余额支付金额 + 已付款金额 >= 保证金 */
                if ($order['surplus'] + $order['money_paid'] >= $presale['deposit'])
                {
                    /* 有效，设为已确认，更新订单 */

                    // 更新商品总额
                    $order['goods_amount'] = $goods_amount;

                    // 如果保价，重新计算保价费用
                    if ($order['insure_fee'] > 0)
                    {
                        $shipping = shipping_info($order['shipping_id']);
                        $order['insure_fee'] = shipping_insure_fee($shipping['shipping_code'], $goods_amount, $shipping['insure']);
                    }

                    // 重算支付费用
                    $order['order_amount'] = $order['goods_amount'] + $order['shipping_fee']
                        + $order['insure_fee'] + $order['pack_fee'] + $order['card_fee']
                        - $order['money_paid'] - $order['surplus'];
                    if ($order['order_amount'] > 0)
                    {
                        $order['pay_fee'] = pay_fee($order['pay_id'], $order['order_amount']);
                    }
                    else
                    {
                        $order['pay_fee'] = 0;
                    }

                    // 计算应付款金额
                    $order['order_amount'] += $order['pay_fee'];

                    // 计算付款状态
                    if ($order['order_amount'] > 0)
                    {
                        $order['pay_status'] = PS_UNPAYED;
                        $order['pay_time'] = 0;
                    }
                    else
                    {
                        $order['pay_status'] = PS_PAYED;
                        $order['pay_time'] = gmtime();
                    }

                    // 如果需要退款，退到帐户余额
                    if ($order['order_amount'] < 0)
                    {
                        // todo （现在手工退款）
                    }

                    // 订单状态
                    $order['order_status'] = OS_CONFIRMED;
                    $order['confirm_time'] = gmtime();

                    // 更新订单
                    $order = addslashes_deep($order);
                    update_order($order_id, $order);
                }
                else
                {
                    /* 无效，取消订单，退回已付款 */

                    // 修改订单状态为已取消，付款状态为未付款
                    $order['order_status'] = OS_CANCELED;
                    $order['to_buyer'] = $_LANG['cancel_order_reason'];
                    $order['pay_status'] = PS_UNPAYED;
                    $order['pay_time'] = 0;

                    /* 如果使用余额或有已付款金额，退回帐户余额 */
                    $money = $order['surplus'] + $order['money_paid'];
                    if ($money > 0)
                    {
                        $order['surplus'] = 0;
                        $order['money_paid'] = 0;
                        $order['order_amount'] = $money;

                        // 退款到帐户余额
                        order_refund($order, 1, $_LANG['cancel_order_reason'] . ':' . $order['order_sn']);
                    }

                    /* 更新订单 */
                    $order = addslashes_deep($order);
                    update_order($order['order_id'], $order);
                }
            }
        }

        /* 修改预售活动状态为成功 */
        $sql = "UPDATE " . $ecs->table('presale_activity') .
                " SET is_finished = '" . GBS_SUCCEED . "' " .
                "WHERE act_id = '$presale_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'presale.php?act=list', 'text' => $_LANG['back_list'])
        );
        sys_msg($_LANG['edit_success'], 0, $links);
    }
    elseif (isset($_POST['fail']))
    {
        /* 设置活动失败 */

        /* 判断订单状态 */
        if ($presale['status'] != GBS_FINISHED)
        {
            sys_msg($_LANG['error_status'], 1);
        }

        /* 如果有有效订单，取消订单 */
        if ($presale['valid_order'] > 0)
        {
            /* 查找未确认或已确认的订单 */
            $sql = "SELECT * " .
                    "FROM " . $ecs->table('order_info') .
                    " WHERE extension_code = 'presale' " .
                    "AND extension_id = '$presale_id' " .
                    "AND (order_status = '" . OS_CONFIRMED . "' OR order_status = '" . OS_UNCONFIRMED . "') ";
            $res = $db->query($sql);
            while ($order = $db->fetchRow($res))
            {
                // 修改订单状态为已取消，付款状态为未付款
                $order['order_status'] = OS_CANCELED;
                $order['to_buyer'] = $_LANG['cancel_order_reason'];
                $order['pay_status'] = PS_UNPAYED;
                $order['pay_time'] = 0;

                /* 如果使用余额或有已付款金额，退回帐户余额 */
                $money = $order['surplus'] + $order['money_paid'];
                if ($money > 0)
                {
                    $order['surplus'] = 0;
                    $order['money_paid'] = 0;
                    $order['order_amount'] = $money;

                    // 退款到帐户余额
                    order_refund($order, 1, $_LANG['cancel_order_reason'] . ':' . $order['order_sn'], $money);
                }

                /* 更新订单 */
                $order = addslashes_deep($order);
                update_order($order['order_id'], $order);
            }
        }

        /* 修改预售活动状态为失败，记录失败原因（活动说明） */
        $sql = "UPDATE " . $ecs->table('presale_activity') .
                " SET is_finished = '" . GBS_FAIL . "', " .
                    "act_desc = '$_POST[act_desc]' " .
                "WHERE act_id = '$presale_id' LIMIT 1";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $links = array(
            array('href' => 'presale.php?act=list', 'text' => $_LANG['back_list'])
        );
        sys_msg($_LANG['edit_success'], 0, $links);
    }
    elseif (isset($_POST['mail']))
    {
        /* 发送通知邮件 */

        /* 判断订单状态 */
        if ($presale['status'] != GBS_SUCCEED)
        {
            sys_msg($_LANG['error_status'], 1);
        }

        /* 取得邮件模板 */
        $tpl = get_mail_template('presale');

        /* 初始化订单数和成功发送邮件数 */
        $count = 0;
        $send_count = 0;

        /* 取得有效订单 */
        $sql = "SELECT o.consignee, o.add_time, g.goods_number, o.order_sn, " .
                    "o.order_amount, o.order_id, o.email " .
                "FROM " . $ecs->table('order_info') . " AS o, " .
                    $ecs->table('order_goods') . " AS g " .
                "WHERE o.order_id = g.order_id " .
                "AND o.extension_code = 'presale' " .
                "AND o.extension_id = '$presale_id' " .
                "AND o.order_status = '" . OS_CONFIRMED . "'";
        $res = $db->query($sql);
        while ($order = $db->fetchRow($res))
        {
            /* 邮件模板赋值 */
            $smarty->assign('consignee',    $order['consignee']);
            $smarty->assign('add_time',     local_date($_CFG['time_format'], $order['add_time']));
            $smarty->assign('goods_name',   $presale['goods_name']);
            $smarty->assign('goods_number', $order['goods_number']);
            $smarty->assign('order_sn',     $order['order_sn']);
            $smarty->assign('order_amount', price_format($order['order_amount']));
            $smarty->assign('shop_url',     $ecs->url() . 'user.php?act=order_detail&order_id='.$order['order_id']);
            $smarty->assign('shop_name',    $_CFG['shop_name']);
            $smarty->assign('send_date',    local_date($GLOBALS['_CFG']['time_format'], gmtime()));

            /* 取得模板内容，发邮件 */
            $content = $smarty->fetch('str:' . $tpl['template_content']);
            if (send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
            {
                $send_count++;
            }
            $count++;
        }

        /* 提示信息 */
        sys_msg(sprintf($_LANG['mail_result'], $count, $send_count));
    }
    else
    {
        /* 保存预售信息 */
        $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0)
        {
            sys_msg($_LANG['error_goods_null']);
        }
        $info = goods_presale($goods_id);
        if ($info && $info['act_id'] != $presale_id)
        {
            sys_msg($_LANG['error_goods_exist']);
        }

        $goods_name = $db->getOne("SELECT goods_name FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'");

        $act_name = empty($_POST['act_name']) ? $goods_name : sub_str($_POST['act_name'], 0, 255, false);

        $deposit = floatval($_POST['deposit']);
        
        if ($deposit < 0)
        {
            $deposit = 0;
        }

        /* 检查开始时间和结束时间是否合理 */
        $start_time = local_strtotime($_POST['start_time']);
        $end_time = local_strtotime($_POST['end_time']);
        $pay_start_time = local_strtotime($_POST['pay_start_time']);//liu
        $pay_end_time = local_strtotime($_POST['pay_end_time']);//liu
        if ($start_time >= $end_time||$pay_start_time >= $pay_end_time||$end_time>$pay_start_time)//change liu
        {
            sys_msg($_LANG['invalid_time']);
        }

	$adminru = get_admin_ru_id(); //ecmoban模板堂 --zhuo

        $presale = array(
            'act_name'   => $act_name,
            'act_desc'   => $_POST['act_desc'],
            'cid'   => intval($_POST['cid']),
            'goods_id'   => $goods_id,
            'user_id'   => $adminru['ru_id'], //ecmoban模板堂 --zhuo
            'goods_name' => $goods_name,
            'start_time'    => $start_time,
            'end_time'      => $end_time,
            'pay_start_time'    => $pay_start_time,//liu
            'pay_end_time'      => $pay_end_time,//liu
            'deposit'           => $deposit
        );
        /* 清除缓存 */
        clear_cache_files();

        /* 保存数据 */
        if ($presale_id > 0)
        {
            /* update */
            $db->autoExecute($ecs->table('presale_activity'), $presale, 'UPDATE', "act_id = '$presale_id'");

            /* log */
            admin_log(addslashes($goods_name) . '[' . $presale_id . ']', 'edit', 'presale');

            /* todo 更新活动表 */

            /* 提示信息 */
            $links = array(
                array('href' => 'presale.php?act=list&' . list_link_postfix(), 'text' => $_LANG['back_list'])
            );
            sys_msg($_LANG['edit_success'], 0, $links);
        }
        else
        {
            /* insert */
            $db->autoExecute($ecs->table('presale_activity'), $presale, 'INSERT');

            /* log */
            admin_log(addslashes($goods_name), 'add', 'presale');

            /* 提示信息 */
            $links = array(
                array('href' => 'presale.php?act=add', 'text' => $_LANG['continue_add']),
                array('href' => 'presale.php?act=list', 'text' => $_LANG['back_list'])
            );
            sys_msg($_LANG['add_success'], 0, $links);
        }
    }
}

/*------------------------------------------------------ */
//-- 批量删除预售活动
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_drop')
{
    if (isset($_POST['checkboxes']))
    {
        $del_count = 0; //初始化删除数量
        foreach ($_POST['checkboxes'] AS $key => $id)
        {
            /* 取得预售活动信息 */
            $presale = presale_info($id);

            /* 如果预售活动已经有订单，不能删除 */
            if ($presale['valid_order'] <= 0)
            {
                /* 删除预售活动 */
                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('presale_activity') .
                        " WHERE act_id = '$id' LIMIT 1";
                $GLOBALS['db']->query($sql, 'SILENT');

                admin_log(addslashes($presale['goods_name']) . '[' . $id . ']', 'remove', 'presale');
                $del_count++;
            }
        }

        /* 如果删除了预售活动，清除缓存 */
        if ($del_count > 0)
        {
            clear_cache_files();
        }

        $links[] = array('text' => $_LANG['back_list'], 'href'=>'presale.php?act=list');
        sys_msg(sprintf($_LANG['batch_drop_success'], $del_count), 0, $links);
    }
    else
    {
        $links[] = array('text' => $_LANG['back_list'], 'href'=>'presale.php?act=list');
        sys_msg($_LANG['no_select_presale'], 0, $links);
    }
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'search_goods')
{
    check_authz_json('presale');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
    $arr    = get_goods_list($filter);

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 获取本店价
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_price')
{
    check_authz_json('presale');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $goods_id = $json->decode($_GET['goods_id']);
    $shop_price    = get_shop_price($goods_id);
    make_json_result($shop_price);
}

/*------------------------------------------------------ */
//-- 编辑保证金
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit_deposit')
{
    check_authz_json('presale');

    $id = intval($_POST['id']);
    $val = floatval($_POST['val']);

    $sql = "UPDATE " . $ecs->table('presale_activity') .
            " SET deposit = '" . $val . "'" .
            " WHERE act_id = '$id'";
    $db->query($sql);

    clear_cache_files();

    make_json_result(number_format($val, 2));
}

/*------------------------------------------------------ */
//-- 删除预售活动
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('presale');

    $id = intval($_GET['id']);

    /* 取得预售活动信息 */
    $presale = presale_info($id);

    /* 如果预售活动已经有订单，不能删除 */
    if ($presale['valid_order'] > 0)
    {
        make_json_error($_LANG['error_exist_order']);
    }

    /* 删除预售活动 */
    $sql = "DELETE FROM " . $ecs->table('presale_activity') . " WHERE act_id = '$id' LIMIT 1";
    $db->query($sql);

    admin_log(addslashes($presale['goods_name']) . '[' . $id . ']', 'remove', 'presale');

    clear_cache_files();

    $url = 'presale.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*
 * 取得预售活动列表
 * @return   array
 */
function presale_list($ru_id)
{
    $result = get_filter();
    $where = "";
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keyword']      = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'ga.act_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		
        $where = (!empty($filter['keyword'])) ? " AND (ga.goods_name LIKE '%" . mysqli_like_quote($filter['keyword']) . "%')" : '';
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = !isset($_REQUEST['store_search']) ? -1 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        //ecmoban模板堂 --zhuo start
        if($ru_id > 0){
            $where .= " and ga.user_id = '$ru_id'";
        }
        //ecmoban模板堂 --zhuo end
        
        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] > -1){
           if($ru_id == 0){ 
                if($filter['store_search'] > 0){
                    if($_REQUEST['store_type']){
                        $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                    }

                    if($filter['store_search'] == 1){
                        $where .= " AND ga.user_id = '" .$filter['merchant_id']. "' ";
                    }elseif($filter['store_search'] == 2){
                        $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                    }elseif($filter['store_search'] == 3){
                        $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                    }

                    if($filter['store_search'] > 1){
                        $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                                  " WHERE msi.user_id = ga.user_id $store_where) > 0 ";
                    }
                }else{
                    $where .= " AND ga.user_id = 0";
                }    
           }
        }
        //管理员查询的权限 -- 店铺查询 end

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('presale_activity') ." AS ga ".
                " WHERE 1 $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询 */
        $sql = "SELECT ga.* ".
                "FROM " . $GLOBALS['ecs']->table('presale_activity') ." AS ga ".
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
        $stat = presale_stat($row['act_id'], $row['deposit']);
        $arr = array_merge($row, $stat);

        $status = presale_status($arr);

        $arr['start_time']  = local_date($GLOBALS['_CFG']['date_format'], $arr['start_time']);
        $arr['end_time']    = local_date($GLOBALS['_CFG']['date_format'], $arr['end_time']);
        $arr['pay_start_time']  = local_date($GLOBALS['_CFG']['date_format'], $arr['pay_start_time']);
        $arr['pay_end_time']    = local_date($GLOBALS['_CFG']['date_format'], $arr['pay_end_time']);
        $arr['cur_status']  = $GLOBALS['_LANG']['gbs'][$status];
		
        $arr['shop_name'] = get_shop_name($row['user_id'], 1);
        
        $list[] = $arr;
    }
    $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得某商品的预售活动
 * @param   int     $goods_id   商品id
 * @return  array
 */
function goods_presale($goods_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('presale_activity') .
            " WHERE goods_id = '$goods_id' " .
            " AND start_time <= " . gmtime() .
            " AND end_time >= " . gmtime();

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true)
{
    $href = 'presale.php?act=list';
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    return array('href' => $href, 'text' => $GLOBALS['_LANG']['presale_list']);
}

/*
* 获取商品的本店售价，供参考预售商品定金参考比对
*/
function get_shop_price($goods_id){
	$sql = " SELECT shop_price FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id = '$goods_id' ";
	return $GLOBALS['db']->getOne($sql);
}

?>