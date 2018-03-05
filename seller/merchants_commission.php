<?php

/**
 * ECSHOP 管理中心服务站管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: wanglei $
 * $Id: suppliers_server.php 15013 2009-05-13 09:31:42Z wanglei $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"merchants_commission");
define('SUPPLIERS_ACTION_LIST', 'delivery_view,back_view');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

$smarty->assign('menu_select',array('action' => '17_merchants', 'current' => '03_merchants_commission'));
/*------------------------------------------------------ */
//-- 服务站列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     admin_priv('merchants_commission');
	 
	 $smarty->assign('current','merchants_commission_list');
	 
    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['brokerage_amount_list']); // 当前导航
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);
	 
    $result = merchants_commission_list();
	
	//分页
	$page_count_arr = seller_page($result,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
	
    /* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = " . $_SESSION['seller_id']);
	
    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str != 'all')
    {
    	/* 查询 */
        $smarty->assign('no_all',        0); 
        $ser_name = $_LANG['suppliers_list_server'];
    }else{
        /* 查询 */
        $smarty->assign('no_all',        1); 
        $smarty->assign('action_link', array('href' => 'merchants_commission.php?act=add', 'text' => $_LANG['add_suppliers_server']));
        $smarty->assign('action_link2', array('href' => 'merchants_percent.php?act=list', 'text' => $_LANG['suppliers_percent_list']));
        $smarty->assign('action_link3', array('href' => 'javascript:download_list();', 'text' => $_LANG['export_all_suppliers']));//liu
        $ser_name = $_LANG['suppliers_server_center'];
    }
    $smarty->assign('full_page',        1); // 翻页参数
    $smarty->assign('merchants_commission_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_suppliers_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('merchants_commission_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('merchants_commission');
	
	/* 获得该管理员的权限 */
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = " . $_SESSION['seller_id']);
	
    /* 如果被编辑的管理员拥有了all这个权限，将不能编辑 */
    if ($priv_str != 'all')
    {
        $smarty->assign('no_all',        0); 
        $ser_name = $_LANG['suppliers_list_server'];
    }else{
        $smarty->assign('no_all',        1); 
    }
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);
	
	/* 查询 */
   	$result = merchants_commission_list();
	
	//分页
	$page_count_arr = seller_page($result,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    $smarty->assign('merchants_commission_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('merchants_commission_list.dwt'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}

/*------------------------------------------------------ */
//-- 商家订单别表
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'order_list')
{
    /* 检查权限 */
    admin_priv('merchants_commission');
	
    $user_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;	 
    $_SESSION['order_userId'] = $user_id;

    $date = array('suppliers_percent');
    $percent_id = get_table_date('merchants_server', "user_id = '$user_id' ", $date, $sqlType = 2);

    $date = array('percent_value');
    $percent_value = get_table_date('merchants_percent', "percent_id = '$percent_id'", $date, $sqlType = 2) . '%';
    $smarty->assign('percent_value',   $percent_value);

    if($adminru['ru_id'] > 0){
        $smarty->assign('no_all', 0);
    }else{
        $smarty->assign('no_all', 1);
    }

    /* 模板赋值 */
    $smarty->assign('action_link', array('href' => 'javascript:order_downloadList();', 'text' => $_LANG['export_merchant_commission']));//liu
    $smarty->assign('ur_here', $_LANG['brokerage_order_list']); // 当前导航
    $smarty->assign('full_page',        1); // 翻页参数    
    $page_count_arr = seller_page($goods_list, $_REQUEST['page']);
    $smarty->assign('page_count_arr', $page_count_arr);
    
    $order_list = merchants_order_list($user_id);
    $smarty->assign('user_id',   $user_id);	//liu  供批量结算用
    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $smarty->assign('server_id', '<img src="images/sort_desc.gif">');
	
    /* 显示模板 */
    assign_query_info();
    $smarty->display('merchants_order_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'order_query')
{
	
    check_authz_json('merchants_commission');
	
    $user_id = $_SESSION['order_userId'];
    $order_list = merchants_order_list($user_id);

    $date = array('suppliers_percent');
    $percent_id = get_table_date('merchants_server', "user_id = '$user_id' ", $date, $sqlType = 2);

    $date = array('percent_value');
    $percent_value = get_table_date('merchants_percent', "percent_id = '$percent_id'", $date, $sqlType = 2) . '%';
    $smarty->assign('percent_value',   $percent_value);
	
    $smarty->assign('order_list',   $order_list['orders']);
    $smarty->assign('filter',       $order_list['filter']);
    $smarty->assign('record_count', $order_list['record_count']);
    $smarty->assign('page_count',   $order_list['page_count']);
    $page_count_arr = seller_page($result, $_REQUEST['page']);
    $smarty->assign('page_count_arr', $page_count_arr);
    
    /* 排序标记 */
    $sort_flag  = sort_flag($order_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('merchants_order_list.dwt'), '',
        array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}
/*------------------------------------------------------ */
//--Excel文件下载 商家佣金明细
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'merchant_download')
{
    
    $filename = date('YmdHis') . ".csv";
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=" . $filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    
    $merchants_order_list = merchants_order_list();

    echo order_download_list($merchants_order_list['orders']);
    
    echo ecs_iconv(EC_CHARSET, 'GB2312', '佣金总金额：'); 
    echo ecs_iconv(EC_CHARSET, 'GB2312', $merchants_order_list['orders']['brokerage_amount']['all_price']). "\t\n";
    echo ecs_iconv(EC_CHARSET, 'GB2312', '已结算：');
    echo ecs_iconv(EC_CHARSET, 'GB2312', $merchants_order_list['orders']['brokerage_amount']['is_settlement_price']) . "\t\n";
    echo ecs_iconv(EC_CHARSET, 'GB2312', '未结算：');
    echo ecs_iconv(EC_CHARSET, 'GB2312', $merchants_order_list['orders']['brokerage_amount']['no_settlement_price']) . "\t\n";
        
    exit;
}
/*------------------------------------------------------ */
//--商家订单列表  操作日志 by kong 
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'handle_log'){
    /* 权限判断 */
    admin_priv('merchants_commission');
    $smarty->assign('ur_here',     $_LANG['handle_log']);
    $user_id=!empty($_REQUEST['user_id'])   ? intval($_REQUEST['user_id']):0;
    $smarty->assign('action_link', array('text' => $_LANG['brokerage_order_list'], 'href' => 'merchants_commission.php?act=order_list&id='.$user_id));
    
    $id=!empty($_REQUEST['id'])   ? intval($_REQUEST['id']):0;
    $gift_gard_log=get_gift_gard_log($id);
    
    $smarty->assign('full_page',    1);
    $smarty->assign('gift_gard_log',   $gift_gard_log['pzd_list']);
    $smarty->assign('filter',       $gift_gard_log['filter']);
    $smarty->assign('record_count', $gift_gard_log['record_count']);
    $smarty->assign('page_count',   $gift_gard_log['page_count']);
    $smarty->assign('order_id',   $id);
    
    $smarty->display("merchants_order_log.dwt");
}
/*------------------------------------------------------ */
//--商家订单列表  操作日志 ajax by kong 
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'Ajax_handle_log'){
    
        $id=!empty($_REQUEST['id'])   ? intval($_REQUEST['id']):0;
        $gift_gard_log=get_gift_gard_log($id);
        
	$smarty->assign('gift_gard_log',   $gift_gard_log['pzd_list']);
	$smarty->assign('filter',       $gift_gard_log['filter']);
	$smarty->assign('record_count', $gift_gard_log['record_count']);
	$smarty->assign('page_count',   $gift_gard_log['page_count']);

	make_json_result($smarty->fetch('merchants_order_log.dwt'), '',
	array('filter' => $gift_gard_log['filter'], 'page_count' => $gift_gard_log['page_count']));
}
function order_download_list($result) {
    if(empty($result)) {
        return i("没有符合您要求的数据！^_^");
    }

    $data = i('订单编号,下单时间,收货人,总金额,退款金额,有效分成金额,订单状态,佣金金额,结算状态'."\n");
    $count = count($result);
    
    for ($i = 0; $i < $count; $i++) {
        $order_sn = i($result[$i]['order_sn']);
        $short_order_time = i($result[$i]['short_order_time']);
        $consignee =  i($result[$i]['consignee']) ."". i($result[$i]['address']);
        $total_fee_price = i($result[$i]['total_fee_price']);
        $return_amount_price = i($result[$i]['return_amount_price']);
        $brokerage_amount_price = i($result[$i]['brokerage_amount_price']);
        $effective_amount_price = i($result[$i]['effective_amount_price']);
        $is_settlement = i($result[$i]['settlement_status']);
        $status = i($result[$i]['ordersTatus']);
        
        $data .= $order_sn . ',' . $short_order_time .','. $consignee . ',' .
                $total_fee_price . ',' . $return_amount_price . ',' . $brokerage_amount_price . ',' .
                $status . ',' . $effective_amount_price . ',' . $is_settlement . "\n";
    }
    return $data;
}
/**
 *  获取商家佣金列表
 *
 * @access  public
 * @param  is_pagination  是否分页
 *
 * @return void
 */
function merchants_commission_list()  
{
    $adminru = get_admin_ru_id();
    
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 's.server_id' : trim($_REQUEST['sort_by']);  
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);  

        $where = 'WHERE 1 ';

        if($adminru['ru_id'] > 0){
            $where .= " AND s.user_id = '" .$adminru['ru_id']. "'";
        }
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = empty($_REQUEST['store_search']) ? 0 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        $store_search_where = '';
        if($filter['store_search'] !=0){
           if($adminru['ru_id'] == 0){ 
               
               if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }
               
                if($filter['store_search'] == 1){
                    $where .= " AND mis.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $where .= " AND mis.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $where .= " AND mis.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }
           }
        }
        
        $where .= " AND mis.merchants_audit = 1 ";
        //管理员查询的权限 -- 店铺查询 end
        
        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('merchants_server') . " as s " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('merchants_shop_information'). " as mis on s.user_id = mis.user_id " . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
		
        /* 查询 */  //ecmoban模板堂 -zhuo suppliers_sn		
        $sql = "SELECT u.user_name, mis.*, msf.*, s.server_id, s.user_id, s.suppliers_desc, s.suppliers_percent  
                FROM " . $GLOBALS['ecs']->table("merchants_server") . " as s " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('merchants_shop_information'). " as mis on s.user_id = mis.user_id " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " as u on s.user_id = u.user_id " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('merchants_steps_fields'). " as msf on s.user_id = msf.user_id " .
				 " $where " ." group by s.user_id ". 
				 " ORDER BY " . $filter['sort_by'] . " " .$filter['sort_order'];	
			$sql .= " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);
    $count = count($row);
    for ($i=0; $i<$count; $i++)
    {
        $row[$i]['server_id'] = $row[$i]['server_id'];
        $valid = get_nerchants_order_valid_refund($row[$i]['user_id']); //订单有效总额
        $row[$i]['order_valid_total'] = price_format($valid['total_fee']);

        $refund = get_nerchants_order_valid_refund($row[$i]['user_id'], 1); //订单退款总额
        $row[$i]['order_refund_total'] = price_format($refund['total_fee']); 
        $row[$i]['store_name'] = get_shop_name($row[$i]['user_id'], 1);
        
        $is_settlement = merchants_is_settlement($row[$i]['user_id'],1);//已结算佣金金额  liu
        $row[$i]['is_settlement'] = price_format($is_settlement); 
        
        $no_settlement = merchants_is_settlement($row[$i]['user_id'],0);//未结算佣金金额  liu
        $row[$i]['no_settlement'] = price_format($no_settlement);
        
        $row[$i]['order_valid_total'] = price_format($valid['total_fee']);

        $row[$i]['total_fee_price'] = number_format($valid['total_fee'], 2, '.', '');
        $row[$i]['total_fee_refund'] = number_format($refund['total_fee'], 2, '.', '');
        $row[$i]['is_settlement_price'] = $is_settlement;//已结算佣金金额  liu
        $row[$i]['no_settlement_price'] = $no_settlement;//未结算佣金金额  liu        
        $sql = "SELECT ss.shop_name, ss.shop_address, ss.mobile, ".
               "concat(IFNULL(p.region_name, ''), '  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region ".
               "FROM " . $GLOBALS['ecs']->table('seller_shopinfo') . " AS ss ".
               "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS p ON ss.province = p.region_id ".
               "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS t ON ss.city = t.region_id ".
               "LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS d ON ss.district = d.region_id  ".
               "WHERE ss.ru_id = '" . $row[$i]['user_id'] . "' LIMIT 1";
        $seller_shopinfo = $GLOBALS['db']->getRow($sql);

        if ($seller_shopinfo['shop_name'])
        {
            $row[$i]['companyName'] = $seller_shopinfo['shop_name'];
            $row[$i]['company_adress'] = '[' . $seller_shopinfo['region'] . '] ' . $seller_shopinfo['shop_address'];
        }

        if ($seller_shopinfo['mobile'])
        {
            $row[$i]['company_contactTel'] = $seller_shopinfo['mobile'];
        }
        else
        {
            $row[$i]['company_contactTel'] = $row[$i]['contactPhone'];
        }
		
    }
	
    $arr = array('result' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    //get_print_r($arr);
    return $arr;
}

//佣金百分比
function get_suppliers_percent(){
	$sql = "select percent_id, percent_value from " . $GLOBALS['ecs']->table('merchants_percent') . " where 1 order by sort_order asc";
	$res = $GLOBALS['db']->getAll($sql);
	
	return $res;
}

/**
 *  获取商家佣金列表
 *
 * @access  public
 * @param   is_pagination  是否分页
 *
 * @return void
 */
function merchants_order_list()  
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['id'] = empty($_REQUEST['id']) ? 0 : trim($_REQUEST['id']);  
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'o.order_id' : trim($_REQUEST['sort_by']);  
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']); 
		
		/* 过滤信息 */
        $filter['start_time'] = empty($_REQUEST['start_time']) ? '' : local_strtotime(trim($_REQUEST['start_time']));
        $filter['end_time'] = empty($_REQUEST['end_time']) ? '' : local_strtotime(trim($_REQUEST['end_time'])); 
        $filter['state'] = isset($_REQUEST['state']) ? trim($_REQUEST['state']) : ''; //liu 新增状态
		
        $where = 'WHERE 1';
		if(isset($filter['state'])&&$filter['state'] != '')// liu  判断是否有结算状态查询
		{
			$where .= " AND is_settlement = '".$filter['state']."' ";
		}
		
        if(!empty($filter['start_time'])){
                $where .= " AND o.add_time >= '" .$filter['start_time'] . "' ";
        }

        if(!empty($filter['end_time'])){
                $where .= " AND o.add_time <= '" .$filter['end_time']. "' ";
        }

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }
        $where .= order_query_sql('finished', 'o.');
        $where .= " and (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " as oi2 where oi2.main_order_id = o.order_id) = 0 ";  //主订单下有子订单时，则主订单不显示
        $where .= " and og.ru_id = '" .$filter['id']. "' group by o.order_id ";

        /* 记录总数 */
        $sql = "SELECT o.order_id ".
                " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". 
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_goods'). " AS og ON o.order_id=og.order_id ". 
               $where;
				
        $filter['record_count']   = count($GLOBALS['db']->getAll($sql));
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
		
		/* 查询 */
        $sql = "SELECT og.ru_id, o.order_id, o.main_order_id, o.order_sn, o.add_time, o.order_status, o.shipping_status, o.order_amount, o.money_paid, o.is_delete, o.is_settlement," .
                    "o.shipping_time, o.auto_delivery_time, o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, " .
                    "(" . order_commission_field('o.') . ") AS total_fee, " .
                    "IFNULL(u.user_name, '" .$GLOBALS['_LANG']['anonymous']. "') AS buyer ".
                " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". 
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_goods'). " AS og ON o.order_id=og.order_id ". 
               $where .
                " ORDER BY $filter[sort_by] $filter[sort_order] ";
        $sql .= " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    
    $row = $GLOBALS['db']->getAll($sql);

    $count = count($row);
    for ($i=0; $i<$count; $i++)
    { 
        $row[$i]['formated_order_amount'] = price_format($row[$i]['order_amount'], true);
        $row[$i]['formated_money_paid'] = price_format($row[$i]['money_paid'], true);
        $row[$i]['formated_total_fee'] = price_format($row[$i]['total_fee'], true);
        $row[$i]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $row[$i]['add_time']);
        $row[$i]['ordersTatus'] = $GLOBALS['_LANG']['os'][$row[$i]['order_status']] ."|". $GLOBALS['_LANG']['ps'][$row[$i]['pay_status']] ."|". $GLOBALS['_LANG']['ss'][$row[$i]['shipping_status']];
	
        if($row[$i]['is_settlement'])
        {
            $row[$i]['settlement_status'] = "已结算";
        }
        else
        {
            $row[$i]['settlement_status'] = "未结算";
        }
     
        $date = array('suppliers_percent');
        $percent_id = get_table_date('merchants_server', "user_id = '" .$row[$i]['ru_id']. "' ", 2);

        $date = array('percent_value');
        $percent_value = get_table_date('merchants_percent', "percent_id = '$percent_id'", $date, 2);

        if($percent_value == 0){
                $percent_value = 1;
        }else{
                $percent_value = $percent_value/100;
        }
        
        $row[$i]['consignee'] = "【" .$row[$i]['consignee']. "】";
 
        $row[$i]['return_amount'] = get_order_return_list($row[$i]['order_id']);
        $row[$i]['return_amount'] = !empty($row[$i]['return_amount']) ? $row[$i]['return_amount'] : "0.00";
        
        $row[$i]['formated_return_amount'] = price_format($row[$i]['return_amount'], true);
        $row[$i]['formated_brokerage_amount'] = price_format(($row[$i]['total_fee']-$row[$i]['return_amount']) * $percent_value, true);
        $row[$i]['formated_effective_amount'] = price_format($row[$i]['total_fee']-$row[$i]['return_amount'], true);
        
        $row[$i]['total_fee_price'] = $row[$i]['total_fee'];
        $row[$i]['return_amount_price'] = $row[$i]['return_amount'];
        $row[$i]['effective_amount_price'] = ($row[$i]['total_fee']-$row[$i]['return_amount']) * $percent_value;
        $row[$i]['brokerage_amount_price'] = $row[$i]['total_fee']-$row[$i]['return_amount'];
        
        $row['brokerage_amount']['ru_id'] = $row[$i]['ru_id'];
        $row['all_brokerage_amount'] += ($row[$i]['total_fee']-$row[$i]['return_amount']) * $percent_value;
    }

    if($count){
        $row['brokerage_amount']['all'] = price_format($row['all_brokerage_amount'], true);//改 liu 
        $row['brokerage_amount']['all_price'] = $row['all_brokerage_amount'];
        unset($row['all_brokerage_amount']);//删除总金额的原始变量 liu
        
        $is_settlement = merchants_is_settlement($row['brokerage_amount']['ru_id'],1);//已结算佣金金额  liu
        $no_settlement = merchants_is_settlement($row['brokerage_amount']['ru_id'],0);//未结算佣金金额  liu

        $row['brokerage_amount']['is_settlement'] = price_format($is_settlement, true);//liu
        $row['brokerage_amount']['no_settlement'] = price_format($no_settlement, true);//liu
        
        $row['brokerage_amount']['is_settlement_price'] = $is_settlement;//liu
        $row['brokerage_amount']['no_settlement_price'] = $no_settlement;//liu
    }
    
    $arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    
    return $arr;
}

//商家订单有效金额和退款金额
function get_nerchants_order_valid_refund($ru_id, $type = 0){
    
    $where = order_query_sql('finished', 'oi.');
    if($type == 1){
        $sql = "SELECT SUM(oreturn.actual_return) AS total_fee FROM " .$GLOBALS['ecs']->table('order_info'). " as oi," .$GLOBALS['ecs']->table('order_return'). " as oreturn " .  " WHERE 1" . 
                " $where AND oi.order_id = oreturn.order_id AND oreturn.back = 1 AND (SELECT og.ru_id FROM " . $GLOBALS['ecs']->table('order_goods') .' as og' . " WHERE og.order_id = oi.order_id limit 0, 1) = '$ru_id'" . 
                " AND (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " as oi2 where oi2.main_order_id = oi.order_id limit 0, 1) = 0";  //主订单下有子订单时，则主订单不显示;
        
        $res = $GLOBALS['db']->getRow($sql);
    }else{
        $total_fee = "SUM((" . order_amount_field('oi.') . ")) AS total_fee ";

        $sql = "SELECT oi.order_id, oi.order_sn, $total_fee  FROM " .$GLOBALS['ecs']->table('order_info'). " as oi " . 
            " WHERE 1 $where AND (SELECT og.ru_id FROM " . $GLOBALS['ecs']->table('order_goods') .' as og' . " WHERE og.order_id = oi.order_id limit 0, 1) = '$ru_id'" . 
            " AND (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " as oi2 where oi2.main_order_id = oi.order_id limit 0, 1) = 0";  //主订单下有子订单时，则主订单不显示;
        
        $res = $GLOBALS['db']->getRow($sql);
    }
    
    return $res;
}

/*
 * 查询订单退款 
 * $type 0-单条数据 1-多条数据
 */
function get_order_return_list($order_id, $type = 0){
    $sql = "SELECT SUM(actual_return) FROM " .$GLOBALS['ecs']->table('order_return'). " WHERE order_id = '$order_id'";
    $actual_return = $GLOBALS['db']->getOne($sql);
    
    return $actual_return;
}
/**
 *  获取选中的订单佣金列表 liu
 *
 * @access  public
 * @param
 *
 * @return void
 */
function merchants_order_list_checked($user_id = 0 ,$checked_id = 0)  
{
		$where = 'WHERE 1';
		$where .= " and o.is_settlement = 0 ";
		$where .= " and o.order_id ".$checked_id;
        $where .= order_query_sql('finished', 'o.');
        $where .= " and (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " as oi2 where oi2.main_order_id = o.order_id) = 0 ";  //主订单下有子订单时，则主订单不显示
        $where .= " and og.ru_id = '$user_id' group by o.order_id ";

		
		/* 查询 */
        $sql = "SELECT og.ru_id, o.user_id, o.order_id, o.main_order_id, o.order_sn, o.add_time, o.order_status, o.shipping_status, o.order_amount, o.money_paid, o.is_delete, o.is_settlement," .
                    "o.shipping_time, o.auto_delivery_time, o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, " .
                    "(" . order_amount_field('o.') . ") AS total_fee, " .
                    "IFNULL(u.user_name, '" .$GLOBALS['_LANG']['anonymous']. "') AS buyer ".
                " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". 
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_goods'). " AS og ON o.order_id=og.order_id ". 
                 " LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS g ON og.goods_id=g.goods_id ". 
               $where;
    $row = $GLOBALS['db']->getAll($sql);
	return $row;
}
/*
* 获取商家已/未结算佣金
*/
function merchants_is_settlement($ru_id = 0,$state = 1)  
{
        $where = 'WHERE 1';
		$where .= " and o.is_settlement = '$state' ";
        $where .= order_query_sql('finished', 'o.');
        $where .= " and (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " as oi2 where oi2.main_order_id = o.order_id) = 0 ";  //主订单下有子订单时，则主订单不显示
        $where .= " and og.ru_id = '$ru_id' group by o.order_id ";
		
		/* 查询 */
        $sql = "SELECT og.ru_id, o.order_id, o.main_order_id, o.order_sn, o.add_time, o.order_status, o.shipping_status, o.order_amount, o.money_paid, o.is_delete, o.is_settlement," .
                    "o.shipping_time, o.auto_delivery_time, o.pay_status, o.consignee, o.address, o.email, o.tel, o.extension_code, o.extension_id, " .
                    "(" . order_amount_field('o.') . ") AS total_fee, " .
                    "IFNULL(u.user_name, '" .$GLOBALS['_LANG']['anonymous']. "') AS buyer ".
                " FROM " . $GLOBALS['ecs']->table('order_info') . " AS o " .
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". 
                " LEFT JOIN " .$GLOBALS['ecs']->table('order_goods'). " AS og ON o.order_id=og.order_id ". 
                 " LEFT JOIN " .$GLOBALS['ecs']->table('goods'). " AS g ON og.goods_id=g.goods_id ". $where; 

    $row = $GLOBALS['db']->getAll($sql);
	$count = count($row);
    for ($i=0; $i<$count; $i++)
    {
        $row[$i]['formated_order_amount'] = price_format($row[$i]['order_amount'], true);
        $row[$i]['formated_money_paid'] = price_format($row[$i]['money_paid'], true);
        $row[$i]['formated_total_fee'] = price_format($row[$i]['total_fee'], true);
        $row[$i]['short_order_time'] = local_date($GLOBALS['_CFG']['time_format'], $row[$i]['add_time']);
		
        $date = array('suppliers_percent');
        $percent_id = get_table_date('merchants_server', "user_id = '" .$row[$i]['ru_id']. "' ", $date, $sqlType = 2);

        $date = array('percent_value');
        $percent_value = get_table_date('merchants_percent', "percent_id = '$percent_id'", $date, $sqlType = 2);

        if($percent_value == 0){
                $percent_value = 1;
        }else{
                $percent_value = $percent_value/100;
        }
 
        $row[$i]['return_amount'] = get_order_return_list($row[$i]['order_id']);
        $row[$i]['formated_return_amount'] = price_format($row[$i]['return_amount'], true);
        $row[$i]['formated_brokerage_amount'] = price_format(($row[$i]['total_fee']-$row[$i]['return_amount']) * $percent_value, true);
        $row[$i]['formated_effective_amount'] = price_format($row[$i]['total_fee']-$row[$i]['return_amount'], true);
        $row['all_brokerage_amount'] += ($row[$i]['total_fee']-$row[$i]['return_amount']) * $percent_value;
    }

    return number_format($row['all_brokerage_amount'], 2, '.', '');
}
/*
* 获得字符串内的数字
*/
 function findNum($str=''){
	$str=trim($str);
	if(empty($str)){
		return '';
	}
	$temp=array('1','2','3','4','5','6','7','8','9','0');
	$result='';
	for($i=0;$i<strlen($str);$i++){
		if(in_array($str[$i],$temp)){
			$result.=$str[$i];
		}
	}
	if($result == '000'){
		$result = 0;
	}
	return $result;
}
/*
*  字符串转换已结算未结算
*/
function changeSettlement($str){
	if($str=='0'){
		$str = '未结算';
	}else{
		$str = '已结算';
	}
	return $str;
}

function commission_download_list($result) {
    if(empty($result)) {
        return i("没有符合您要求的数据！^_^");
    }

    $data = i('商家名称,店铺名称,公司名称,公司地址,联系方式,订单有效总金额,订单退款总金额,已结算订单金额,未结算订单金额'."\n");
    $count = count($result);
    
    for ($i = 0; $i < $count; $i++) {
        $user_name = i($result[$i]['user_name']);
        $store_name = i($result[$i]['store_name']);
        $companyName = i($result[$i]['companyName']);
        $company_adress = i($result[$i]['company_adress']);
        $company_contactTel = i($result[$i]['company_contactTel']);
        $order_valid_total = i($result[$i]['total_fee_price']);
        $order_refund_total = i($result[$i]['total_fee_refund']);
        $is_settlement = i($result[$i]['is_settlement_price']);
        $no_settlement = i($result[$i]['no_settlement_price']);

        $data .= $user_name . ',' . $store_name .','. $companyName . ',' .
                $company_adress . ',' . $company_contactTel . ',' . $order_valid_total . ',' .
                $order_refund_total . ',' . $is_settlement . ',' . $no_settlement . "\n";
    }
    return $data;
}
function i($strInput) {
    return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
}
/*操作日志  分页  by kong */
function get_gift_gard_log($id = 0){
    $result = get_filter();
    if ($result === false)
    {
        if($id > 0){
            $filter['id'] =$id;
        }
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('gift_gard_log')." WHERE gift_gard_id = '".$filter['id']."'  AND handle_type='toggle_on_settlement'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        $sql="SELECT a.id,a.addtime,b.user_name,a.delivery_status,a.gift_gard_id FROM".$GLOBALS['ecs']->table('gift_gard_log'). " AS a LEFT JOIN ".$GLOBALS['ecs']->table('admin_user')." AS b ON a.admin_id = b.user_id WHERE a.gift_gard_id = '".$filter['id']."' AND a.handle_type='toggle_on_settlement'  ORDER BY a.addtime DESC LIMIT " . $filter['start'] . "," . $filter['page_size'];
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
            if($v['delivery_status'] == 0){
                $row[$k]['delivery_status']=$GLOBALS['_LANG']['no_settlement'];
            }elseif($v['delivery_status'] == 1){
               $row[$k]['delivery_status']=$GLOBALS['_LANG']['is_settlement']; 
            }
            if($v['gift_gard_id']){
                $row[$k]['gift_sn']=$GLOBALS['db']->getOne(" SELECT order_sn FROM ".$GLOBALS['ecs']->table('order_info')." WHERE order_id = '".$v['gift_gard_id']."'");
            }
        }
        $arr = array('pzd_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        return $arr;
}
?>