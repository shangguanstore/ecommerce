<?php

/**
 * ECSHOP 礼品卡处理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: bonus.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

/* 初始化$exc对象 */
$exc = new exchange($ecs->table('gift_gard_type'), $db, 'gift_id', 'gift_name');
$cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);

/*------------------------------------------------------ */
//-- 红包类型列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     $_LANG['gift_gard_type_list']);
    $smarty->assign('action_link', array('text' => $_LANG['gift_gard_type_add'], 'href' => 'gift_gard.php?act=add'));
    $smarty->assign('full_page',   1);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);
    
    $list = get_type_list($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
    
    $smarty->assign('type_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('gift_gard_list.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query')
{
    $list = get_type_list($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    $smarty->assign('type_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('gift_gard_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 编辑红包类型名称
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_type_name')
{
    check_authz_json('gift_gard_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv(trim($_POST['val']));

    /* 检查红包类型名称是否重复 */
    if (!$exc->is_only('gift_name', $id, $val))
    {
        make_json_error($_LANG['type_name_exist']);
    }
    else
    {
        $exc->edit("gift_name='$val'", $id);

        make_json_result(stripslashes($val));
    }
}

/*------------------------------------------------------ */
//-- 编辑订单号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_type_money')
{
    check_authz_json('gift_gard_manage');

    $id = intval($_POST['id']);
    $val = floatval($_POST['val']);

    	$sql = "UPDATE " . $ecs->table('user_gift_gard') . " SET express_no='$val' WHERE gift_gard_id='$id'";
    	$up_id = $db->query($sql);
    	
        make_json_result(stripslashes($val));
}

if($_REQUEST['act'] == 'confirm_ship')
{
	check_authz_json('gift_gard_manage');

	$id = intval($_GET['id']);
	$val = floatval($_GET['val']);
	
	if(!empty($id))
	{
		if ($db->getOne("SELECT COUNT(*) FROM " . $ecs->table('user_gift_gard') . " WHERE status = '3' AND gift_gard_id='$id'"))
		{
// 			make_json_error('此订单已经完成，不能取消发货');
		}
		
		elseif($db->getOne("SELECT COUNT(*) FROM " . $ecs->table('user_gift_gard') . " WHERE status = '1' AND gift_gard_id='$id'"))
		{
			$sql = "UPDATE " . $ecs->table('user_gift_gard') . " SET status='2' WHERE gift_gard_id='$id'";
			
			$db->query($sql);
			$val = 2;
                        
		}
		
		elseif ($db->getOne("SELECT COUNT(*) FROM " . $ecs->table('user_gift_gard') . " WHERE status = '2' AND gift_gard_id='$id'"))
		{
			$sql = "UPDATE " . $ecs->table('user_gift_gard') . " SET status='1' WHERE gift_gard_id='$id'";
			$db->query($sql);
			$val = 1;
		}
		
		else
		{
			make_json_error('发货失败');
		}
	}
	else
	{
		make_json_error('订单不存在');
	}
        /*插入操作日志  by kong   start*/
        if($val){
            $db->query(" INSERT INTO".$ecs->table("gift_gard_log")." (`admin_id`,`gift_gard_id`,`delivery_status`,`addtime`,`handle_type`) VALUES ('".$_SESSION['admin_id']."','$id','$val','".time()."','gift_gard')");
        }
        /*by kong end*/
	$url = 'gift_gard.php?act=query_take&' . str_replace('act=confirm_ship', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
/*操作记录日志列表   by kong start*/
if($_REQUEST['act'] == 'handle_log'){
     /* 权限判断 */
    admin_priv('gift_gard_manage');
    $smarty->assign('ur_here',     $_LANG['handle_log']);
    $smarty->assign('action_link', array('text' => $_LANG['take_list'], 'href' => 'gift_gard.php?act=take_list'));

    $id=!empty($_REQUEST['id'])   ? intval($_REQUEST['id']):0;
    $gift_gard_log=get_gift_gard_log($id);
	
	$page_count_arr = seller_page($gift_gard_log,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
    
    $smarty->assign('full_page',    1);
    $smarty->assign('gift_gard_log',   $gift_gard_log['pzd_list']);
    $smarty->assign('filter',       $gift_gard_log['filter']);
    $smarty->assign('record_count', $gift_gard_log['record_count']);
    $smarty->assign('page_count',   $gift_gard_log['page_count']);
    
    $smarty->display("gift_gard_log.dwt");
}

if($_REQUEST['act'] == 'Ajax_handle_log'){
        $id=!empty($_REQUEST['id'])   ? intval($_REQUEST['id']):0;
        $gift_gard_log=get_gift_gard_log($id);
        
	$smarty->assign('gift_gard_log',   $gift_gard_log['pzd_list']);
	$smarty->assign('filter',       $gift_gard_log['filter']);
	$smarty->assign('record_count', $gift_gard_log['record_count']);
	$smarty->assign('page_count',   $gift_gard_log['page_count']);
        

	make_json_result($smarty->fetch('gift_gard_log.htm'), '',
	array('filter' => $gift_gard_log['filter'], 'page_count' => $gift_gard_log['page_count']));
}
/*------------------------------------------------------ */
//-- 编辑订单下限
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_min_amount')
{
    check_authz_json('gift_gard_manage');

    $id = intval($_POST['id']);
    $val = floatval($_POST['val']);

    if ($val < 0)
    {
        make_json_error($_LANG['min_amount_empty']);
    }
    else
    {
        $exc->edit("min_amount='$val'", $id);

        make_json_result(number_format($val, 2));
    }
}

/*------------------------------------------------------ */
//-- 删除红包类型
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'remove')
{
    check_authz_json('gift_gard_manage');

    $id = intval($_GET['id']);

    $exc->drop($id);

    /* 删除礼品卡 */
    $db->query("DELETE FROM " .$ecs->table('user_gift_gard'). " WHERE gift_id = '$id'");

    $url = 'gift_gard.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 红包类型添加页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    admin_priv('gift_gard_manage');
    
    $smarty->assign('menu_select', array('action'=>'03_promotion', 'current'=>'gift_gard_list'));
    
    $smarty->assign('controller','card_add');
    
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('ur_here',      $_LANG['gift_gard_type_add']);
    $smarty->assign('action_link',  array('href' => 'gift_gard.php?act=list', 'text' => $_LANG['gift_gard_type_list']));
    $smarty->assign('action',       'add');

    $smarty->assign('form_act',     'insert');
    $smarty->assign('cfg_lang',     $_CFG['lang']);
    $smarty->assign('cat_list',     cat_list($cat_id));
    $smarty->assign('brand_list',   get_brand_list());

    $next_month = local_strtotime('+1 months');
    $bonus_arr['send_start_date']   = local_date($GLOBALS['_CFG']['time_format']);
    $bonus_arr['use_start_date']    = local_date($GLOBALS['_CFG']['time_format']);
    $bonus_arr['send_end_date']     = local_date($GLOBALS['_CFG']['time_format'], $next_month);
    $bonus_arr['use_end_date']      = local_date($GLOBALS['_CFG']['time_format'], $next_month);

    $smarty->assign('bonus_arr',    $bonus_arr);

    assign_query_info();
    $smarty->display('gift_gard_info.dwt');
}

/*------------------------------------------------------ */
//-- 礼品卡添加页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'gift_add')
{
	admin_priv('gift_gard_manage');

	$gift_id = $_GET['bonus_type'];

	$smarty->assign('lang',         $_LANG);
	$smarty->assign('ur_here',      $_LANG['gift_gard_add']);
	$smarty->assign('action_link',  array('href' => 'gift_gard.php?act=bonus_list&bonus_type=' . $gift_id, 'text' => $_LANG['gift_gard_list']));
	$smarty->assign('action',       'add');

	$smarty->assign('form_act',     'gift_insert');
	$smarty->assign('cfg_lang',     $_CFG['lang']);
	$smarty->assign('cat_list',     cat_list($cat_id));
	$smarty->assign('brand_list',   get_brand_list());
	$smarty->assign('gift_id',   $gift_id);

	assign_query_info();
	$smarty->display('gift_info.htm');
}

/*------------------------------------------------------ */
//-- 礼品卡添加的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'gift_insert')
{
	/* 去掉红包类型名称前后的空格 */
	$gift_sn   = !empty($_POST['gift_sn']) ? trim($_POST['gift_sn']) : '';
	$gift_pwd   = !empty($_POST['gift_pwd']) ? trim($_POST['gift_pwd']) : '';
	$gift_id   = !empty($_POST['type_id']) ? trim($_POST['type_id']) : '0';


	/* 检查礼品卡是否有重复 */
	$sql = "SELECT COUNT(*) FROM " .$ecs->table('user_gift_gard'). " WHERE gift_sn='$gift_sn'";
	if ($db->getOne($sql) > 0)
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG['gift_name_exist'], 0, $link);
	}
	
	$sql = "SELECT COUNT(*) FROM " .$ecs->table('user_gift_gard'). " WHERE gift_password='$gift_pwd'";
	if ($db->getOne($sql) > 0)
	{
		$link[0] = array('text' => '返回重新添加', 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG['gift_pwd_exist'], 0, $link);
	}

	/* 插入数据库。 */
	$sql = "INSERT INTO ".$ecs->table('user_gift_gard')." (gift_sn, gift_password,gift_id)
	VALUES ('$gift_sn',
	'$gift_pwd',
	'$gift_id')";

	$db->query($sql);

	/* 记录管理员操作 */
	admin_log($_POST['gift_sn'], 'add', 'giftgardtype');

    /* 清除缓存 */
    clear_cache_files();

	/* 提示信息 */
	$link[0]['text'] = '返回继续添加';
	$link[0]['href'] = 'gift_gard.php?act=gift_add&bonus_type=' . $gift_id;

	$link[1]['text'] = '礼品卡列表页';
	$link[1]['href'] = 'gift_gard.php?act=bonus_list&bonus_type=' . $gift_id;

	sys_msg($_LANG['add'] . "&nbsp;" .$_POST['gift_sn'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}

/*------------------------------------------------------ */
//-- 礼品卡类型添加的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 去掉红包类型名称前后的空格 */
    $type_name   = !empty($_POST['type_name']) ? trim($_POST['type_name']) : '';
    $gift_number   = !empty($_POST['gift_number']) ? trim($_POST['gift_number']) : '';


    /* 检查类型是否有重复 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('gift_gard_type'). " WHERE gift_name='$type_name'";
    if ($db->getOne($sql) > 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['type_name_exist'], 0, $link);
    }

    /* 获得日期信息 */
    $gift_startdate  = local_strtotime($_POST['use_start_date']);
    $gift_enddate    = local_strtotime($_POST['use_end_date']);

    /* 插入数据库。 */
    $sql = "INSERT INTO ".$ecs->table('gift_gard_type')." (ru_id, gift_name, gift_menory,gift_start_date,gift_end_date, gift_number)
    VALUES ('" .$adminru['ru_id']. "',
            '$type_name',
            '$_POST[type_money]',
            '$gift_startdate',
            '$gift_enddate', '$gift_number')";

    $db->query($sql);
   	$gift_id = $db->insert_id();
    
    /* 生成红包序列号 */
	$num = $db->getOne("SELECT MAX(gift_sn) FROM ". $ecs->table('user_gift_gard'));
   	$num = $num ? floor($num / 1) : 8000000;
   
   for ($i = 0, $j = 0; $i < $gift_number; $i++)
   {
	   $gift_sn = ($num + $i);
	   $gift_pwd = generate_password(6);
	   $db->query("INSERT INTO ".$ecs->table('user_gift_gard')." (gift_sn, gift_password, gift_id) VALUES('$gift_sn', '$gift_pwd', '$gift_id')");
	    
	   $j++;
   }
    
    /* 记录管理员操作 */
    admin_log($_POST['type_name'], 'add', 'giftgardtype');

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $link[0]['text'] = '返回继续添加';
    $link[0]['href'] = 'gift_gard.php?act=add';

    $link[1]['text'] = '礼品卡列表';
    $link[1]['href'] = 'gift_gard.php?act=list';

    sys_msg($_LANG['add'] . "&nbsp;" .$_POST['type_name'] . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}

/*------------------------------------------------------ */
//-- 红包类型编辑页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    admin_priv('gift_gard_manage');
    $smarty->assign('menu_select', array('action'=>'03_promotion', 'current'=>'gift_gard_list'));

    /* 获取红包类型数据 */
    $type_id = !empty($_GET['type_id']) ? intval($_GET['type_id']) : 0;
    $bonus_arr = $db->getRow("SELECT * FROM " .$ecs->table('gift_gard_type'). " WHERE gift_id = '$type_id'");

    $bonus_arr['use_start_date']    = local_date($GLOBALS['_CFG']['time_format'], $bonus_arr['gift_start_date']);
    $bonus_arr['use_end_date']      = local_date($GLOBALS['_CFG']['time_format'], $bonus_arr['gift_end_date']);

    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     '编辑礼品卡类型');
    $smarty->assign('action_link', array('href' => 'gift_gard.php?act=list&' . list_link_postfix(), 'text' => $_LANG['gift_gard_type_list']));
    $smarty->assign('form_act',    'update');
    $smarty->assign('bonus_arr',   $bonus_arr);

    assign_query_info();
    $smarty->display('gift_gard_info.dwt');
}

/*------------------------------------------------------ */
//-- 红包类型编辑的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 获得日期信息 */
    $use_startdate  = local_strtotime($_POST['use_start_date']);
    $use_enddate    = local_strtotime($_POST['use_end_date']);

    /* 对数据的处理 */
    $type_name   = !empty($_POST['type_name'])  ? trim($_POST['type_name'])    : '';
    $type_id     = !empty($_POST['type_id'])    ? intval($_POST['type_id'])    : 0;
    $gift_number     = !empty($_POST['gift_number'])    ? intval($_POST['gift_number'])    : 0;

    $sql = "UPDATE " .$ecs->table('gift_gard_type'). " SET ".
           "gift_name       = '$type_name', ".
           "gift_menory      = '$_POST[type_money]', ".
           "gift_start_date  = '$use_startdate', ".
           "gift_end_date    = '$use_enddate' ".
           "WHERE gift_id   = '$type_id'";

   $db->query($sql);
   
   //再增加的数量
   /* 生成红包序列号 */
   $num = $db->getOne("SELECT MAX(gift_sn) FROM ". $ecs->table('user_gift_gard'));
   $num = $num ? floor($num / 1) : 8000000;
   
   for ($i = 0, $j = 0; $i < $gift_number; $i++)
   {
	   $gift_sn = ($num + $i);
	   $gift_pwd = generate_password(6);
	   $db->query("INSERT INTO ".$ecs->table('user_gift_gard')." (gift_sn, gift_password, gift_id) VALUES('$gift_sn', '$gift_pwd', '$type_id')");
	    
	   $j++;
   }
   
   /* 记录管理员操作 */
   admin_log($_POST['type_name'], 'edit', 'giftgrad');

   /* 清除缓存 */
   clear_cache_files();

   /* 提示信息 */
   $link[] = array('text' => '礼品卡列表页', 'href' => 'gift_gard.php?act=list&' . list_link_postfix());
   sys_msg($_LANG['edit'] .' '.$_POST['type_name'].' '. $_LANG['attradd_succed'], 0, $link);

}

/*------------------------------------------------------ */
//-- 配送商品页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'send')
{
    admin_priv('gift_gard_manage');

    /* 取得参数 */
    $id = !empty($_REQUEST['id'])  ? intval($_REQUEST['id'])  : '';

    assign_query_info();

    $smarty->assign('ur_here',      '配置商品');
    $smarty->assign('action_link',  array('href' => 'gift_gard.php?act=list', 'text' => $_LANG['gift_gard_type_list']));
    
	    /* 查询此红包类型信息 */
	    $bonus_type = $db->GetRow("SELECT gift_id, gift_name FROM ".$ecs->table('gift_gard_type').
	    		" WHERE gift_id='$_REQUEST[id]'");

        /* 查询红包类型的商品列表 */
        $goods_list = get_bonus_goods($_REQUEST['id']);

        /* 查询其他红包类型的商品 */
        $sql = "SELECT goods_id FROM " .$ecs->table('goods').
               " WHERE bonus_type_id > 0 AND bonus_type_id <> '$_REQUEST[id]'";
        $other_goods_list = $db->getCol($sql);
        $smarty->assign('other_goods', join(',', $other_goods_list));

        /* 模板赋值 */
        $smarty->assign('cat_list',    cat_list());
        $smarty->assign('brand_list',  get_brand_list());

        $smarty->assign('bonus_type',  $bonus_type);
        $smarty->assign('goods_list',  $goods_list);

        $smarty->display('gift_gard_by_goods.htm');
}

/*------------------------------------------------------ */
//-- 处理红包的发送页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'send_by_user')
{
    $user_list  = array();
    $start      = empty($_REQUEST['start']) ? 0 : intval($_REQUEST['start']);
    $limit      = empty($_REQUEST['limit']) ? 10 : intval($_REQUEST['limit']);
    $validated_email = empty($_REQUEST['validated_email']) ? 0 : intval($_REQUEST['validated_email']);
    $send_count = 0;

    if (isset($_REQUEST['send_rank']))
    {
        /* 按会员等级来发放红包 */
        $rank_id = intval($_REQUEST['rank_id']);

        if ($rank_id > 0)
        {
            $sql = "SELECT min_points, max_points, special_rank FROM " . $ecs->table('user_rank') . " WHERE rank_id = '$rank_id'";
            $row = $db->getRow($sql);
            if ($row['special_rank'])
            {
                /* 特殊会员组处理 */
                $sql = 'SELECT COUNT(*) FROM ' . $ecs->table('users'). " WHERE user_rank = '$rank_id'";
                $send_count = $db->getOne($sql);
                if($validated_email)
                {
                    $sql = 'SELECT user_id, email, user_name FROM ' . $ecs->table('users').
                            " WHERE user_rank = '$rank_id' AND is_validated = 1".
                            " LIMIT $start, $limit";
                }
                else
                {
                     $sql = 'SELECT user_id, email, user_name FROM ' . $ecs->table('users').
                                " WHERE user_rank = '$rank_id'".
                                " LIMIT $start, $limit";
                }
            }
            else
            {
                $sql = 'SELECT COUNT(*) FROM ' . $ecs->table('users').
                       " WHERE rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']);
                $send_count = $db->getOne($sql);

                if($validated_email)
                {
                    $sql = 'SELECT user_id, email, user_name FROM ' . $ecs->table('users').
                        " WHERE rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']) .
                        " AND is_validated = 1 LIMIT $start, $limit";
                }
                else
                {
                     $sql = 'SELECT user_id, email, user_name FROM ' . $ecs->table('users').
                        " WHERE rank_points >= " . intval($row['min_points']) . " AND rank_points < " . intval($row['max_points']) .
                        " LIMIT $start, $limit";
                }

            }

            $user_list = $db->getAll($sql);
            $count = count($user_list);
        }
    }
    elseif (isset($_REQUEST['send_user']))
    {
        /* 按会员列表发放红包 */
        /* 如果是空数组，直接返回 */
        if (empty($_REQUEST['user']))
        {
            sys_msg($_LANG['send_user_empty'], 1);
        }

        $user_array = (is_array($_REQUEST['user'])) ? $_REQUEST['user'] : explode(',', $_REQUEST['user']);
        $send_count = count($user_array);

        $id_array   = array_slice($user_array, $start, $limit);

        /* 根据会员ID取得用户名和邮件地址 */
        $sql = "SELECT user_id, email, user_name FROM " .$ecs->table('users').
               " WHERE user_id " .db_create_in($id_array);
        $user_list  = $db->getAll($sql);
        $count = count($user_list);
    }

    /* 发送红包 */
    $loop       = 0;
    $bonus_type = bonus_type_info($_REQUEST['id']);

    $tpl = get_mail_template('send_bonus');
    $today = local_date($GLOBALS['_CFG']['time_format'], gmtime());

    foreach ($user_list AS $key => $val)
    {
        /* 发送邮件通知 */
        $smarty->assign('user_name',    $val['user_name']);
        $smarty->assign('shop_name',    $GLOBALS['_CFG']['shop_name']);
        $smarty->assign('send_date',    $today);
        $smarty->assign('sent_date',    $today);
        $smarty->assign('count',        1);
        $smarty->assign('money',        price_format($bonus_type['type_money']));

        $content = $smarty->fetch('str:' . $tpl['template_content']);

        if (add_to_maillist($val['user_name'], $val['email'], $tpl['template_subject'], $content, $tpl['is_html']))
        {
             /* 向会员红包表录入数据 */
            $sql = "INSERT INTO " . $ecs->table('user_bonus') .
                    "(bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed) " .
                    "VALUES ('$_REQUEST[id]', 0, '$val[user_id]', 0, 0, " .BONUS_MAIL_SUCCEED. ")";
            $db->query($sql);
        }
        else
        {
            /* 邮件发送失败，更新数据库 */
            $sql = "INSERT INTO " . $ecs->table('user_bonus') .
                    "(bonus_type_id, bonus_sn, user_id, used_time, order_id, emailed) " .
                    "VALUES ('$_REQUEST[id]', 0, '$val[user_id]', 0, 0, " .BONUS_MAIL_FAIL. ")";
            $db->query($sql);
        }

        if ($loop >= $limit)
        {
            break;
        }
        else
        {
            $loop++;
        }
    }

    //admin_log(addslashes($_LANG['send_bonus']), 'add', 'bonustype');
    if ($send_count > ($start + $limit))
    {
        /*  */
        $href = "bonus.php?act=send_by_user&start=" . ($start+$limit) . "&limit=$limit&id=$_REQUEST[id]&";

        if (isset($_REQUEST['send_rank']))
        {
            $href .= "send_rank=1&rank_id=$rank_id";
        }

        if (isset($_REQUEST['send_user']))
        {
            $href .= "send_user=1&user=" . implode(',', $user_array);
        }

        $link[] = array('text' => $_LANG['send_continue'], 'href' => $href);
    }

    $link[] = array('text' => $_LANG['back_list'], 'href' => 'bonus.php?act=list');

    sys_msg(sprintf($_LANG['sendbonus_count'], $count), 0, $link);
}

/*------------------------------------------------------ */
//-- 发送邮件
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'send_mail')
{
    /* 取得参数：红包id */
    $bonus_id = intval($_REQUEST['bonus_id']);
    if ($bonus_id <= 0)
    {
        die('invalid params');
    }

    /* 取得红包信息 */
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $bonus = bonus_info($bonus_id);
    if (empty($bonus))
    {
        sys_msg($_LANG['bonus_not_exist']);
    }

    /* 发邮件 */
    $count = send_bonus_mail($bonus['bonus_type_id'], array($bonus_id));

    $link[0]['text'] = $_LANG['back_bonus_list'];
    $link[0]['href'] = 'bonus.php?act=bonus_list&bonus_type=' . $bonus['bonus_type_id'];

    sys_msg(sprintf($_LANG['success_send_mail'], $count), 0, $link);
}

/*------------------------------------------------------ */
//-- 按印刷品发放红包
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'send_by_print')
{
    @set_time_limit(0);

    /* 红下红包的类型ID和生成的数量的处理 */
    $bonus_typeid = !empty($_POST['bonus_type_id']) ? $_POST['bonus_type_id'] : 0;
    $bonus_sum    = !empty($_POST['bonus_sum'])     ? $_POST['bonus_sum']     : 1;

    /* 生成红包序列号 */
    $num = $db->getOne("SELECT MAX(bonus_sn) FROM ". $ecs->table('user_bonus'));
    $num = $num ? floor($num / 10000) : 100000;

    for ($i = 0, $j = 0; $i < $bonus_sum; $i++)
    {
        $bonus_sn = ($num + $i) . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $db->query("INSERT INTO ".$ecs->table('user_bonus')." (bonus_type_id, bonus_sn) VALUES('$bonus_typeid', '$bonus_sn')");

        $j++;
    }

    /* 记录管理员操作 */
    admin_log($bonus_sn, 'add', 'userbonus');

    /* 清除缓存 */
    clear_cache_files();

    /* 提示信息 */
    $link[0]['text'] = $_LANG['back_bonus_list'];
    $link[0]['href'] = 'bonus.php?act=bonus_list&bonus_type=' . $bonus_typeid;

    sys_msg($_LANG['creat_bonus'] . $j . $_LANG['creat_bonus_num'], 0, $link);
}

/*------------------------------------------------------ */
//-- 导出线下发放的信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'gen_excel')
{
    @set_time_limit(0);

    /* 获得此线下红包类型的ID */
    $tid  = !empty($_GET['tid']) ? intval($_GET['tid']) : 0;
    $type_name = $db->getOne("SELECT type_name FROM ".$ecs->table('bonus_type')." WHERE type_id = '$tid'");

    /* 文件名称 */
    $bonus_filename = $type_name .'_bonus_list';
    if (EC_CHARSET != 'gbk')
    {
        $bonus_filename = ecs_iconv('UTF8', 'GB2312',$bonus_filename);
    }

    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$bonus_filename.xls");

    /* 文件标题 */
    if (EC_CHARSET != 'gbk')
    {
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['bonus_excel_file']) . "\t\n";
        /* 红包序列号, 红包金额, 类型名称(红包名称), 使用结束日期 */
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['record_id']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['bonus_sn']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['address']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['consignee_name']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['mobile']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['gift_user_name']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['gift_goods_name']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['gift_user_time']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['confirm_ship']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['express_no']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['status']) ."\t\n";
    }
    else
    {
        echo $_LANG['bonus_excel_file'] . "\t\n";
        /* 红包序列号, 红包金额, 类型名称(红包名称), 使用结束日期 */
        echo $_LANG['record_id'] ."\t";
        echo $_LANG['bonus_sn'] ."\t";
        echo $_LANG['address'] ."\t";
        echo $_LANG['consignee_name'] ."\t";
        echo $_LANG['mobile'] ."\t";
        echo $_LANG['gift_user_name'] ."\t";
        echo $_LANG['gift_goods_name'] ."\t";
        echo $_LANG['gift_user_time'] ."\t";
        echo $_LANG['confirm_ship'] ."\t";
        echo $_LANG['express_no'] ."\t";
        echo $_LANG['status'] ."\t\n";
    }

    $val = array();
    $sql = "SELECT ub.bonus_id, ub.bonus_type_id, ub.bonus_sn, bt.type_name, bt.type_money, bt.use_end_date ".
           "FROM ".$ecs->table('user_bonus')." AS ub, ".$ecs->table('bonus_type')." AS bt ".
           "WHERE bt.type_id = ub.bonus_type_id AND ub.bonus_type_id = '$tid' ORDER BY ub.bonus_id DESC";
    $res = $db->query($sql);

    $code_table = array();
    while ($val = $db->fetchRow($res))
    {
        echo $val['bonus_sn'] . "\t";
        echo $val['type_money'] . "\t";
        if (!isset($code_table[$val['type_name']]))
        {
            if (EC_CHARSET != 'gbk')
            {
                $code_table[$val['type_name']] = ecs_iconv('UTF8', 'GB2312', $val['type_name']);
            }
            else
            {
                $code_table[$val['type_name']] = $val['type_name'];
            }
        }
        echo $code_table[$val['type_name']] . "\t";
        echo local_date('Y-m-d', $val['use_end_date']);
        echo "\t\n";
    }
}

/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => $val['shop_price']);
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 添加发放红包的商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add_bonus_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('gift_gard_manage');

    $add_ids    = $json->decode($_GET['add_ids']);
    $args       = $json->decode($_GET['JSON']);
    $type_id = explode(',', trim($args[0]));
    $add_ids_str = implode(',', $add_ids);

    foreach ($type_id AS $key => $val)
    {
    	$sql = "SELECT config_goods_id FROM " . $ecs->table('user_gift_gard') . " WHERE gift_gard_id='$val'";
    	$config_goods = $db->getRow($sql);
    	$add_ids_str .= ',' . $config_goods['config_goods_id'];
    	
        $sql = "UPDATE " .$ecs->table('user_gift_gard'). " SET config_goods_id='$add_ids_str' WHERE gift_gard_id='$val'";
        $db->query($sql, 'SILENT') or make_json_error($db->error());
        
    }

    /* 重新载入 */

//     $gift_gard_arr = explode(',', $type_id);
    $gift_gard_id = $type_id[0];
    $arr = get_bonus_goods($gift_gard_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除发放红包的商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'drop_bonus_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('gift_gard_manage');

    $drop_ids    = $json->decode($_GET['drop_ids']);
    $args       = $json->decode($_GET['JSON']);
    $type_id = explode(',', trim($args[0]));
    

    foreach ($type_id AS $key => $val)
    {
    	$sql = "SELECT config_goods_id FROM " . $ecs->table('user_gift_gard') . " WHERE gift_gard_id='$val'";
    	$config_goods = $db->getRow($sql);
    	$config_goods_arr = explode(',', $config_goods['config_goods_id']);
    	$config_goods_id = array_diff($config_goods_arr,$drop_ids);
    	$config_goods_id = implode(',', $config_goods_id);

    	$db->query("UPDATE " .$ecs->table('user_gift_gard'). " SET config_goods_id='$config_goods_id' WHERE gift_gard_id='$val'");
    }

    /* 重新载入 */
    $gift_gard_id = $type_id[0];
    $arr = get_bonus_goods($gift_gard_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value'  => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索用户
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'search_users')
{
    $keywords = json_str_iconv(trim($_GET['keywords']));

    $sql = "SELECT user_id, user_name FROM " . $ecs->table('users') .
            " WHERE user_name LIKE '%" . mysqli_like_quote($keywords) . "%' OR user_id LIKE '%" . mysqli_like_quote($keywords) . "%'";
    $row = $db->getAll($sql);

    make_json_result($row);
}

/*------------------------------------------------------ */
//--礼品卡列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'bonus_list')
{
    $smarty->assign('menu_select', array('action'=>'03_promotion', 'current'=>'gift_gard_list'));
    
    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      $_LANG['gift_gard_list']);
    $smarty->assign('action_link',   array('href' => 'gift_gard.php?act=list', 'text' => $_LANG['gift_gard_type_list']));

    $list = get_bonus_list($adminru['ru_id']);
    

    /* 赋值是否显示红包序列号 */
    $bonus_type = bonus_type_info(intval($_REQUEST['bonus_type']));
    if ($bonus_type['send_type'] == SEND_BY_PRINT)
    {
        $smarty->assign('show_bonus_sn', 1);
    }

    /* 赋值是否显示发邮件操作和是否发过邮件 */
    elseif ($bonus_type['send_type'] == SEND_BY_USER)
    {
        $smarty->assign('show_mail', 1);
    }

    $smarty->assign('bonus_list',   $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('action_link2',   array('href' => 'gift_gard.php?act=gift_add&bonus_type=' . $list['item'][0]['gift_id'], 'text' => $_LANG['gift_gard_add']));
    $smarty->assign('action_link3',   array('href' => 'gift_gard.php?act=export_gift_gard&bonus_type=' . $list['item'][0]['gift_id'], 'text' => $_LANG['gift_gard_export']));

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('gift_list.dwt');
}

/*------------------------------------------------------ */
//-- 礼品卡兑换列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'take_list')
{
	admin_priv('take_manage');
	$smarty->assign('full_page',    1);
	$smarty->assign('ur_here',      $_LANG['gift_gard_list']);
	$smarty->assign('action_link',   array('href' => 'gift_gard.php?act=take_excel', 'text' => '导出订单'));
        
        $store_list = get_common_store_list();
        $smarty->assign('store_list',        $store_list);
    
	$list = get_bonus_list($adminru['ru_id']);
	
	//分页
	$page_count_arr = seller_page($list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

	/* 赋值是否显示红包序列号 */
	$bonus_type = bonus_type_info(intval($_REQUEST['bonus_type']));
	if ($bonus_type['send_type'] == SEND_BY_PRINT)
	{
		$smarty->assign('show_bonus_sn', 1);
	}

	/* 赋值是否显示发邮件操作和是否发过邮件 */
	elseif ($bonus_type['send_type'] == SEND_BY_USER)
	{
		$smarty->assign('show_mail', 1);
	}

	$smarty->assign('bonus_list',   $list['item']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);
// 	$smarty->assign('action_link2',   array('href' => 'gift_gard.php?act=gift_add&bonus_type=' . $list['item'][0]['gift_id'], 'text' => $_LANG['gift_gard_add']));

	$sort_flag  = sort_flag($list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty->display('take_list.dwt');
}

if ($_REQUEST['act'] == 'query_take')
{
	$list = get_bonus_list($adminru['ru_id']);

	$smarty->assign('bonus_list',   $list['item']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);

	$sort_flag  = sort_flag($list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);

	make_json_result($smarty->fetch('take_list.dwt'), '',
	array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}


/*------------------------------------------------------ */
//-- 红包列表翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query_bonus')
{
    $list = get_bonus_list($adminru['ru_id']);

    /* 赋值是否显示红包序列号 */
    $bonus_type = bonus_type_info(intval($_REQUEST['bonus_type']));
    if ($bonus_type['send_type'] == SEND_BY_PRINT)
    {
        $smarty->assign('show_bonus_sn', 1);
    }

    /* 赋值是否显示发邮件操作和是否发过邮件 */
    elseif ($bonus_type['send_type'] == SEND_BY_USER)
    {
        $smarty->assign('show_mail', 1);
    }

    $smarty->assign('bonus_list',   $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('gift_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 导出已完成订单excel下载
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'take_excel')
{
		$file_name = '提货已完成订单_' . local_date('YmdHis', gmtime());
        $gift_gard_list = get_bonus_list($adminru['ru_id']);
        
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name.xls");

        /* 文件标题 */
        echo ecs_iconv(EC_CHARSET, $file_name) . "\t\n";

        /* 商品名称,订单号,商品数量,销售价格,销售日期 */
        echo ecs_iconv(EC_CHARSET, 'GB2312', '编号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '礼品卡序列号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '送货地址') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '联系人') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '联系电话') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '使用会员') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '提货商品') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '使用时间') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '快递单号') . "\t";
        echo ecs_iconv(EC_CHARSET, 'GB2312', '提货状态') . "\t\n";

        foreach ($gift_gard_list['item'] AS $key => $value)
        {
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_gard_id']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_sn']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['address']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['consignee_name']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['mobile']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['user_name']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['goods_name']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['user_time']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', $value['express_no']) . "\t";
	            echo ecs_iconv(EC_CHARSET, 'GB2312', '已完成') . "\t";
	            echo "\n";
        }
        exit;
}


/*------------------------------------------------------ */
//-- 导出礼品卡excel下载
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'export_gift_gard')
{
	$file_name = '礼品卡_' . local_date('YmdHis', gmtime());
	$gift_list = get_bonus_list($adminru['ru_id']);
	
	header("Content-type: application/vnd.ms-excel; charset=utf-8");
	header("Content-Disposition: attachment; filename=$file_name.xls");

	/* 文件标题 */
	echo ecs_iconv(EC_CHARSET, $file_name) . "\t\n";

	/* 商品名称,订单号,商品数量,销售价格,销售日期 */
	echo ecs_iconv(EC_CHARSET, 'GB2312', '编号') . "\t";
	echo ecs_iconv(EC_CHARSET, 'GB2312', '卡号') . "\t";
	echo ecs_iconv(EC_CHARSET, 'GB2312', '密码') . "\t";
	echo ecs_iconv(EC_CHARSET, 'GB2312', '金额') . "\t";
	echo ecs_iconv(EC_CHARSET, 'GB2312', '卡名称') . "\t\n";

	foreach ($gift_list['item'] AS $key => $value)
	{
		echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_gard_id']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_sn']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_password']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_menory']) . "\t";
		echo ecs_iconv(EC_CHARSET, 'GB2312', $value['gift_name']) . "\t";
		echo "\n";
	}
	exit;
}

/*------------------------------------------------------ */
//--礼品提货列表翻页、排序
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query_take')
{
	$list = get_bonus_list($adminru['ru_id']);

	/* 赋值是否显示红包序列号 */
	$bonus_type = bonus_type_info(intval($_REQsUEST['bonus_type']));
	if ($bonus_type['send_type'] == SEND_BY_PRINT)
	{
		$smarty->assign('show_bonus_sn', 1);
	}

	/* 赋值是否显示发邮件操作和是否发过邮件 */
	elseif ($bonus_type['send_type'] == SEND_BY_USER)
	{
		$smarty->assign('show_mail', 1);
	}

	$smarty->assign('bonus_list',   $list['item']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);

	$sort_flag  = sort_flag($list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);

	make_json_result($smarty->fetch('take_list.dwt'), '',
	array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 删除礼品卡
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_bonus')
{
    check_authz_json('gift_gard_manage');

    $id = intval($_GET['id']);
    
    $sql = "SELECT  COUNT(*) FROM " . $ecs->table('user_gift_gard') . " WHERE gift_gard_id='$id' AND user_id > 0";
    $num = $db->getOne($sql);
    if($num > 0)
    {
    	$db->query("UPDATE " .$ecs->table('user_gift_gard'). "SET is_delete = 0  WHERE gift_gard_id='$id'");
    }
    else
    {
    	$db->query("DELETE FROM " .$ecs->table('user_gift_gard'). " WHERE gift_gard_id='$id'");
    }

    $url = 'gift_gard.php?act=query_bonus&' . str_replace('act=remove_bonus', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除提货
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove_take')
{
	check_authz_json('gift_gard_manage');

	$id = intval($_GET['id']);

	$db->query("UPDATE " .$ecs->table('user_gift_gard'). " SET goods_id=0, user_id=0, address='', consignee_name='', mobile='', status=0, express_no='' WHERE gift_gard_id='$id'");
        
        $db->query("DELETE FROM " . $ecs->table('gift_gard_log') . " WHERE gift_gard_id  = '$id' AND handle_type='gift_gard'" );//删除操作记录  bu kong
        
	$url = 'gift_gard.php?act=query_take&' . str_replace('act=remove_take', '', $_SERVER['QUERY_STRING']);

	ecs_header("Location: $url\n");
	exit;
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'batch')
{
    /* 检查权限 */
    admin_priv('gift_gard_manage');
    $smarty->assign('menu_select', array('action'=>'03_promotion', 'current'=>'gift_gard_list'));

    /* 去掉参数：红包类型 */
    $bonus_type_id = intval($_REQUEST['bonus_type']);

    /* 取得选中的红包id */
    if (isset($_POST['checkboxes']))
    {
        $bonus_id_list = $_POST['checkboxes'];

        /* 删除红包 */
        if (isset($_POST['drop']))
        {
        	foreach($bonus_id_list as $key => $val)
        	{
        		 $sql = "SELECT  COUNT(*) FROM " . $ecs->table('user_gift_gard') . " WHERE gift_gard_id='$val' AND user_id > 0";
			    $num = $db->getOne($sql);
			    if($num > 0)
			    {
        			$db->query("UPDATE " .$ecs->table('user_gift_gard'). "SET is_delete = 0  WHERE gift_gard_id='$val'");
        		}
        		else
        		{
        			$db->query("DELETE FROM " .$ecs->table('user_gift_gard'). " WHERE gift_gard_id='$val'");
        		}
        	}

            admin_log(count($bonus_id_list), 'remove', 'userbonus');

            clear_cache_files();

            $link[] = array('text' => $_LANG['back_gift_list'],
                'href' => 'gift_gard.php?act=bonus_list&bonus_type='. $bonus_type_id);
            sys_msg(sprintf($_LANG['batch_gift_success'], count($bonus_id_list)), 0, $link);
        }

        /* 配置商品 */
        elseif (isset($_POST['configure_goods']))
        {
        	assign_query_info();
        	
        	$smarty->assign('ur_here',      '配置商品');
        	$smarty->assign('action_link',  array('href' => 'gift_gard.php?act=bonus_list&bonus_type=' . $bonus_type_id, 'text' => $_LANG['gift_gard_list']));
        	
        	/* 查询此红包类型信息 */
        	$bonus_type = $db->GetRow("SELECT gift_id, gift_name FROM ".$ecs->table('gift_gard_type').
        			" WHERE gift_id='$_REQUEST[id]'");

            $bonus_type = $bonus_type ? $bonus_type : $_REQUEST['bonus_type'];

        	
        	/* 查询红包类型的商品列表 */
        	if(count($bonus_id_list) == 1)
        	{
        		$goods_list = get_bonus_goods($bonus_id_list[0]);
        	}
        	
        	/* 查询其他红包类型的商品 */
        	$sql = "SELECT goods_id FROM " .$ecs->table('goods').
        	" WHERE bonus_type_id > 0 AND bonus_type_id <> '$_REQUEST[id]'";
        	$other_goods_list = $db->getCol($sql);
        	$smarty->assign('other_goods', join(',', $other_goods_list));
        	
        	/* 模板赋值 */
        	$smarty->assign('cat_list',    cat_list());
        	$smarty->assign('brand_list',  get_brand_list());
        	
        	$smarty->assign('bonus_type',  $bonus_type);
        	$smarty->assign('goods_list',  $goods_list);
        	$smarty->assign('gift_goods_id', implode(',', $bonus_id_list));
        	
        	$smarty->display('gift_gard_by_goods.dwt');
        }
    }
    else
    {
        sys_msg($_LANG['no_select_bonus'], 1);
    }
}

/**
 * 获取红包类型列表
 * @access  public
 * @return void
 */
function get_type_list($ru_id)
{
    /* 获得所有红包类型的发放数量 */
    $sql = "SELECT gift_id, COUNT(*) AS sent_count".
            " FROM " .$GLOBALS['ecs']->table('gift_gard_type') .
            " GROUP BY gift_id";
    $res = $GLOBALS['db']->query($sql);

    $sent_arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $sent_arr[$row['gift_id']] = $row['sent_count'];
    }

    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keyword']      = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        
        /* 查询条件 */
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'gift_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        
        $where = " WHERE 1 ";
        if($ru_id)
        {
            $where .= " AND ru_id = '$ru_id'";
        }
        
        $where .= (!empty($filter['keyword'])) ? " AND (ggt.gift_name LIKE '%" . mysqli_like_quote($filter['keyword']) . "%')" : '';
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = !isset($_REQUEST['store_search']) ? -1 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] > -1){
           if($ru_id == 0){ 
                if($filter['store_search'] > 0){
                    if($_REQUEST['store_type']){
                        $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                    }

                    if($filter['store_search'] == 1){
                        $where .= " AND ggt.ru_id = '" .$filter['merchant_id']. "' ";
                    }elseif($filter['store_search'] == 2){
                        $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                    }elseif($filter['store_search'] == 3){
                        $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                    }

                    if($filter['store_search'] > 1){
                        $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                                  " WHERE msi.user_id = ggt.ru_id $store_where) > 0 ";
                    }
                }else{
                    $where .= " AND ggt.ru_id = 0";
                }    
           }
        }
        //管理员查询的权限 -- 店铺查询 end
        
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('gift_gard_type') ." AS ggt". $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        
        /* 分页大小 */
        $filter = page_and_size($filter);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('gift_gard_type') ." AS ggt". " $where ORDER BY $filter[sort_by] $filter[sort_order]";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['send_by'] = $GLOBALS['_LANG']['send_by'][$row['send_type']];
        $row['send_count'] = isset($sent_arr[$row['type_id']]) ? $sent_arr[$row['type_id']] : 0;      
        $row['shop_name'] = get_shop_name($row['ru_id'], 1);
        
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('user_gift_gard') . " WHERE gift_id=$row[gift_id]";
        $row['gift_count'] = $GLOBALS['db']->getOne($sql);
        $row['effective_date'] = local_date($GLOBALS['_CFG']['time_format'], $row['gift_start_date']) . '--' . local_date($GLOBALS['_CFG']['time_format'], $row['gift_end_date']);

        $arr[] = $row;
    }

    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 查询红包类型的商品列表
 *
 * @access  public
 * @param   integer $type_id
 * @return  array
 */
function get_bonus_goods($type_id)
{

	$type_arr = $GLOBALS['db']->getRow("SELECT config_goods_id FROM " . $GLOBALS['ecs']->table('user_gift_gard') . " WHERE gift_gard_id='$type_id'");
	
    $sql = "SELECT goods_id, goods_name FROM " .$GLOBALS['ecs']->table('goods').
            " WHERE goods_id " . db_create_in($type_arr[config_goods_id]);
    $row = $GLOBALS['db']->getAll($sql);

    return $row;
}

/**
 * 获取用户红包列表
 * @access  public
 * @param   $page_param
 * @return void
 */
function get_bonus_list($ru_id)
{
    /* 查询条件 */
    $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
            if(!empty($filter['keywords']))
            {
                    $srarch_where = " AND ub.gift_sn='$filter[keywords]' OR ub.address='$filter[keywords]' OR ub.mobile='$filter[keywords]' OR ub.consignee_name='$filter[keywords]'";
                    $srarch_where2 = " AND ub.gift_sn='$filter[keywords]' OR ub.address LIKE " . "'%$filter[keywords]%'" . " OR ub.mobile='$filter[keywords]' OR ub.consignee_name='$filter[keywords]'";
            }
            else
            {
                    $srarch_where = '';
                    $srarch_where2 = '';
            }
    }
    
    $where = " WHERE 1 ";
    
    /* 查询条件 */
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'ub.user_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['bonus_type'] = empty($_REQUEST['bonus_type']) ? 0 : intval($_REQUEST['bonus_type']);
    $where_count = empty($filter['bonus_type']) ? '' : " AND ub.gift_id='$filter[bonus_type]'";
    $where .= empty($filter['bonus_type']) ? '' : " AND ub.gift_id='$filter[bonus_type]'";
    
    if($_REQUEST['act'] == 'bonus_list' || $_REQUEST['act'] == 'query_bonus' || $_REQUEST['act'] == 'export_gift_gard')
    {
    	$delete_where = " AND ub.is_delete = 1";
    	$delete_where2 = " AND ub.is_delete = 1";
    	$filter['sort_by'] = 'ub.gift_gard_id';
    }
    
    if(empty($_REQUEST['bonus_type']))
    {
    	$where .= " AND ub.status > 0";
    }
    
    if($ru_id)
    {
        $where .= " AND bt.ru_id = '$ru_id'";
    }
    
    //管理员查询的权限 -- 店铺查询 start
    $filter['store_search'] = !isset($_REQUEST['store_search']) ? -1 : intval($_REQUEST['store_search']);
    $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
    $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';

    $store_where = '';
    $store_search_where = '';
    if($filter['store_search'] > -1){
       if($ru_id == 0){ 
            if($filter['store_search'] > 0){
                if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }

                if($filter['store_search'] == 1){
                    $where .= " AND bt.ru_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }

                if($filter['store_search'] > 1){
                    $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                              " WHERE msi.ru_id = bt.ru_id $store_where) > 0 ";
                }
            }else{
                $where .= " AND bt.ru_id = 0";
            }    
       }
    }
    //管理员查询的权限 -- 店铺查询 end
    
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('user_gift_gard') ." AS ub". 
            " LEFT JOIN " .$GLOBALS['ecs']->table('gift_gard_type'). " AS bt ON ub.gift_id = bt.gift_id" .
            $where . $where_count . $delete_where . $srarch_where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);
    
    /* 分页大小 */
    $filter = page_and_size($filter);
    if($_REQUEST['act'] == 'export_gift_gard' || $_REQUEST['act'] == 'take_excel')
    {
    	$filter['sort_by'] = 'gift_gard_id';
    	$filter[page_size] = $filter['record_count'];
    }

    $sql = "SELECT ub.*, bt.ru_id, u.user_name, u.email, o.goods_name, bt.gift_name, bt.gift_menory ".
          " FROM ".$GLOBALS['ecs']->table('user_gift_gard'). " AS ub ".
          " LEFT JOIN " .$GLOBALS['ecs']->table('gift_gard_type'). " AS bt ON bt.gift_id=ub.gift_id ".
          " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=ub.user_id ".
          " LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS o ON o.goods_id=ub.goods_id $where $delete_where2 $srarch_where2 ".
          " ORDER BY ".$filter['sort_by']." ".$filter['sort_order'].
          " LIMIT ". $filter['start'] .", $filter[page_size]";
    
    $row = $GLOBALS['db']->getAll($sql);

    foreach ($row AS $key => $val)
    {
        $row[$key]['emailed'] = $GLOBALS['_LANG']['mail_status'][$row[$key]['emailed']];
        
        if(!empty($val['user_time']))
        {
            $row[$key]['user_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['user_time']);
        }
        else
        {
            $row[$key]['user_time'] = '';
        }
        
        $row[$key]['shop_name'] = get_shop_name($val['ru_id'], 1);
    }
    

    $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    
    return $arr;
}

/**
 * 取得红包类型信息
 * @param   int     $bonus_type_id  红包类型id
 * @return  array
 */
function bonus_type_info($bonus_type_id)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('gift_gard_type') .
            " WHERE gift_id = '$bonus_type_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 发送红包邮件
 * @param   int     $bonus_type_id  红包类型id
 * @param   array   $bonus_id_list  红包id数组
 * @return  int     成功发送数量
 */
function send_bonus_mail($bonus_type_id, $bonus_id_list)
{
    /* 取得红包类型信息 */
    $bonus_type = bonus_type_info($bonus_type_id);
    if ($bonus_type['send_type'] != SEND_BY_USER)
    {
        return 0;
    }

    /* 取得属于该类型的红包信息 */
    $sql = "SELECT b.bonus_id, u.user_name, u.email " .
            "FROM " . $GLOBALS['ecs']->table('user_bonus') . " AS b, " .
                $GLOBALS['ecs']->table('users') . " AS u " .
            " WHERE b.user_id = u.user_id " .
            " AND b.bonus_id " . db_create_in($bonus_id_list) .
            " AND b.order_id = 0 " .
            " AND u.email <> ''";
    $bonus_list = $GLOBALS['db']->getAll($sql);
    if (empty($bonus_list))
    {
        return 0;
    }

    /* 初始化成功发送数量 */
    $send_count = 0;

    /* 发送邮件 */
    $tpl   = get_mail_template('send_bonus');
    $today = local_date($GLOBALS['_CFG']['date_format']);
    foreach ($bonus_list AS $bonus)
    {
        $GLOBALS['smarty']->assign('user_name',    $bonus['user_name']);
        $GLOBALS['smarty']->assign('shop_name',    $GLOBALS['_CFG']['shop_name']);
        $GLOBALS['smarty']->assign('send_date',    $today);
        $GLOBALS['smarty']->assign('sent_date',    $today);
        $GLOBALS['smarty']->assign('count',        1);
        $GLOBALS['smarty']->assign('money',        price_format($bonus_type['type_money']));

        $content = $GLOBALS['smarty']->fetch('str:' . $tpl['template_content']);
        if (add_to_maillist($bonus['user_name'], $bonus['email'], $tpl['template_subject'], $content, $tpl['is_html'], false))
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
                    " SET emailed = '" . BONUS_MAIL_SUCCEED . "'" .
                    " WHERE bonus_id = '$bonus[bonus_id]'";
            $GLOBALS['db']->query($sql);
            $send_count++;
        }
        else
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('user_bonus') .
                    " SET emailed = '" . BONUS_MAIL_FAIL . "'" .
                    " WHERE bonus_id = '$bonus[bonus_id]'";
            $GLOBALS['db']->query($sql);
        }
    }

    return $send_count;
}

function add_to_maillist($username, $email, $subject, $content, $is_html)
{
    $time = time();
    $content = addslashes($content);
    $template_id = $GLOBALS['db']->getOne("SELECT template_id FROM " . $GLOBALS['ecs']->table('mail_templates') . " WHERE template_code = 'send_bonus'");
    $sql = "INSERT INTO "  . $GLOBALS['ecs']->table('email_sendlist') . " ( email, template_id, email_content, pri, last_send) VALUES ('$email', $template_id, '$content', 1, '$time')";
    $GLOBALS['db']->query($sql);
    return true;
}

/**
 * 生成礼品卡密码
 * @param int $length
 * @return string
 * 
 * @author guan
 */
function generate_password( $length = 6 ) {
    // 密码字符集，可任意添加你需要的字符
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    
    if($GLOBALS['db']->getOne("SELECT * FROM " . $GLOBALS['ecs']->table('user_gift_gard') ." WHERE gift_password='$password'"))
    {
    	$password = generate_password(6);
    }
    else
    {
    	return $password;
    }
}
/*操作日志  分页  by kong */
function get_gift_gard_log($id = 0){
    $result = get_filter();
    if ($result === false)
    {
        if($id > 0){
            $filter['id'] =$id;
        }
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('gift_gard_log')." WHERE gift_gard_id = '".$filter['id']."' AND handle_type='gift_gard'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        $sql="SELECT a.id,a.addtime,b.user_name,a.delivery_status,a.gift_gard_id FROM".$GLOBALS['ecs']->table('gift_gard_log'). " AS a LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." AS b ON a.admin_id = b.user_id WHERE a.gift_gard_id = '".$filter['id']."' AND a.handle_type='gift_gard'  ORDER BY a.addtime DESC LIMIT " . $filter['start'] . "," . $filter['page_size'];
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
        $row = $GLOBALS['db']->getAll($sql);

        foreach($row as $k=>$v){
            if($v['addtime']   > 0 ){
                $row[$k]['add_time']=date("Y-m-d  H:i:s",$v['addtime']);
            }
            if($v['delivery_status'] == 1){
                $row[$k]['delivery_status']='未发货';
            }elseif($v['delivery_status'] == 2){
               $row[$k]['delivery_status']='已发货'; 
            }
            if($v['gift_gard_id']){
                $row[$k]['gift_sn']=$GLOBALS['db']->getOne(" SELECT gift_sn FROM ".$GLOBALS['ecs']->table('user_gift_gard')." WHERE gift_gard_id = '".$v['gift_gard_id']."'");
            }
        }
        $arr = array('pzd_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        return $arr;
}
?>