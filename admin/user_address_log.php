<?php

/**
 * ECSHOP 会员收货地址管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: users.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 用户收货地址日志列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $sql = "SELECT rank_id, rank_name, min_points FROM ".$ecs->table('user_rank')." ORDER BY min_points ASC ";
    $rs = $db->query($sql);

    $ranks = array();
    while ($row = $db->FetchRow($rs))
    {
        $ranks[$row['rank_id']] = $row['rank_name'];
    }

    $smarty->assign('user_ranks',   $ranks);
    $smarty->assign('ur_here',      $_LANG['03_users_list']);

    $address_list = user_address_list_log();	

    $smarty->assign('address_list',    $address_list['address_list']);
    $smarty->assign('filter',       $address_list['filter']);
    $smarty->assign('record_count', $address_list['record_count']);
    $smarty->assign('page_count',   $address_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('user_address_list_log.dwt');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $address_list = user_address_list_log();

    $smarty->assign('address_list',    $address_list['address_list']);
    $smarty->assign('filter',       $address_list['filter']);
    $smarty->assign('record_count', $address_list['record_count']);
    $smarty->assign('page_count',   $address_list['page_count']);

    $sort_flag  = sort_flag($address_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_address_list_log.dwt'), '', array('filter' => $address_list['filter'], 'page_count' => $address_list['page_count']));
}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('users_manage');
	
	/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    $smarty->assign('country_list',       get_regions());
	
	/* 获得用户收货人信息 */
    $consignee = get_consignee_log($_REQUEST['address_id'],$_REQUEST['user_id']);
	
	$country_list = get_regions_log(0,0);
	$province_list = get_regions_log(1,$consignee['country']);
	$city_list     = get_regions_log(2,$consignee['province']);
	$district_list = get_regions_log(3,$consignee['city']);
	
	$sn = 0;
	$smarty->assign('country_list',    $country_list);
	$smarty->assign('province_list',    $province_list);
        $smarty->assign('city_list',        $city_list);
        $smarty->assign('district_list',    $district_list);
	$smarty->assign('sn',    $sn);
	$smarty->assign('consignee',    $consignee);
	$smarty->assign('address_id',    $_REQUEST['address_id']);
	$smarty->assign('user_id',    $_REQUEST['user_id']);


    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('action_link',      array('text' => $_LANG['03_users_list'], 'href'=>'user_address_log.php?act=list'));
    $smarty->assign('form_action',      'update');

    $smarty->display('user_address_log_info.dwt');
}

/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $consignee = empty($_POST['consignee']) ? '' : trim($_POST['consignee']);
	$country = isset($_POST['country']) ? $_POST['country'] : 0;
	$province = isset($_POST['province']) ? $_POST['province'] : 0;
	$city = isset($_POST['city']) ? $_POST['city'] : 0;
	$district = isset($_POST['district']) ? $_POST['district'] : 0;
	$address = empty($_POST['address']) ? '' : trim($_POST['address']);
	$tel = empty($_POST['tel']) ? '' : trim($_POST['tel']);
	$mobile = empty($_POST['mobile']) ? '' : trim($_POST['mobile']);
	$email = empty($_POST['email']) ? '' : trim($_POST['email']);
	$zipcode = empty($_POST['zipcode']) ? '' : trim($_POST['zipcode']);
	$sign_building = empty($_POST['sign_building']) ? '' : trim($_POST['sign_building']);
	$best_time = empty($_POST['best_time']) ? '' : trim($_POST['best_time']);
	$audit = isset($_POST['audit']) ? $_POST['audit'] : 0;
	
	$address_id = isset($_POST['address_id']) ? $_POST['address_id'] : 0;
	$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
	
	$other['consignee'] = $consignee;
	$other['country'] = $country;
	$other['province'] = $province;
	$other['city'] = $city;
	$other['district'] = $district;
	$other['address'] = $address;
	$other['tel'] = $tel;
	$other['mobile'] = $mobile;
	$other['email'] = $email;
	$other['zipcode'] = $zipcode;
	$other['sign_building'] = $sign_building;
	$other['best_time'] = $best_time;
	$other['audit'] = $audit;
	$other['update_time'] = gmtime();
	
	//更新到收货地址表中
	$db->autoExecute($ecs->table('user_address'), $other, 'UPDATE', "address_id = '$address_id' and user_id = '$user_id'");
	
	//更新收货地址日志表
	$db->autoExecute($ecs->table('user_address'), $other, 'UPDATE', "address_id = '$address_id' and user_id = '$user_id'");
	$address_log_up = $_LANG['update_success'];

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'user_address_log.php?act=list';
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';

    sys_msg($address_log_up, 0, $links);

}

/*------------------------------------------------------ */
//-- 批量删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch_remove')
{
    /* 检查权限 */
    admin_priv('users_drop');

    if (isset($_POST['checkboxes']))
    {
       
		get_delete_address_log($_POST['checkboxes']);
		
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'user_address_log.php?act=list');
		
		$count = count($_POST['checkboxes']);
        sys_msg(sprintf($_LANG['batch_remove_success'], $count), 0, $lnk);
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'user_address_log.php?act=list');
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}

/*------------------------------------------------------ */
//-- 删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_drop');
	
	$address['address_id'] = $_GET['id'];

	get_delete_address_log($address,1);
	
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'user_address_log.php?act=list');
    sys_msg($_LANG['remove_success'], 0, $link);
}

/**
 *  返回用户收货地址列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_address_list_log()
{
    $result = get_filter();
    if ($result === false)
    {
		/* 过滤条件 */
        $filter['consignee'] = empty($_REQUEST['consignee']) ? '' : trim($_REQUEST['consignee']);
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['consignee'] = json_str_iconv($filter['consignee']);
        }
		$filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
		$filter['mobile'] = empty($_REQUEST['mobile']) ? '' : trim($_REQUEST['mobile']);
		
        $filter['sort_by']    = "a.address_id";
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $ex_where = ' WHERE 1 ';
		
		if($filter['consignee']){
			$ex_where .= " AND a.consignee = '" .$filter['consignee']. "'";
		}
		if($filter['user_name']){
			$ex_where .= " AND u.user_name = '" .$filter['user_name']. "'";
		}
		if($filter['mobile']){
			$ex_where .= " AND a.mobile = '" .$filter['mobile']. "'";
		}

        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_address') . " as a ". 
									"left join " . $GLOBALS['ecs']->table('users') . " as u on a.user_id = u.user_id " . $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT concat(IFNULL(c.region_name, ''), '  ', IFNULL(p.region_name, ''), " .
					"'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region, u.user_name, a.address_id, a.user_id, a.consignee, a.email, a.country, a.province, a.city, a.district, a.address, a.zipcode, a.tel, a.mobile, a.sign_building, a.best_time, a.audit ".
                " FROM " . $GLOBALS['ecs']->table('user_address') . " as a left join" . 
				
				$GLOBALS['ecs']->table('users') . " as u on a.user_id = u.user_id " . 
				
				"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS c ON a.country = c.region_id " .
				"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS p ON a.province = p.region_id " .
				"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS t ON a.city = t.region_id " .
				"LEFT JOIN " . $GLOBALS['ecs']->table('region') . " AS d ON a.district = d.region_id " .
				
				$ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $address_list = $GLOBALS['db']->getAll($sql);

    $count = count($address_list);
    for ($i=0; $i<$count; $i++)
    {
        $address_list[$i]['best_time'] = $address_list[$i]['best_time'];
		$address_list[$i]['userUp_time'] = local_date("Y-m-d H:i:s",$address_list[$i]['userUp_time']);
    }

    $arr = array('address_list' => $address_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}


//ecmoban模板堂 --zhuo start
/**
 * 取得收货人地址列表
 * @param   int     $user_id    用户编号
 * @return  array
 */
function get_consignee_log($address_id = 0,$user_id = 0)
{
    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('user_address') .
            " WHERE user_id = '$user_id' and address_id = '$address_id'";

    return $GLOBALS['db']->getRow($sql);
}

/**
 * 获得指定国家的所有省份
 *
 * @access      public
 * @param       int     country    国家的编号
 * @return      array
 */
function get_regions_log($type = 0, $parent = 0)
{
    $sql = 'SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region') .
            " WHERE region_type = '$type' AND parent_id = '$parent'";

    return $GLOBALS['db']->GetAll($sql);
}

//批量删除会员
function get_delete_address_log($address_id = array(),$open = 0){
	if($open == 1){
		$sql = "delete from " .$GLOBALS['ecs']->table('user_address'). " where address_id = " . $address_id['address_id'];
		$GLOBALS['db']->query($sql);
	}else{
		if(count($address_id) > 0){
			for($i=0;$i<count($address_id);$i++){
				$sql = "delete from " .$GLOBALS['ecs']->table('user_address'). " where address_id = " . $address_id[$i];
				$GLOBALS['db']->query($sql);
			}
		}
	}
	
	if($open == 1){
		$sql = "delete from " .$GLOBALS['ecs']->table('user_address'). " where address_id = " . $address_id['address_id'];
		$GLOBALS['db']->query($sql);
	}else{
		if(count($address_id) > 0){
			for($i=0;$i<count($address_id);$i++){
				$sql = "delete from " .$GLOBALS['ecs']->table('user_address'). " where address_id = " . $address_id[$i];
				$GLOBALS['db']->query($sql);
			}
		}
	}
}
//ecmoban模板堂 --zhuo end

?>