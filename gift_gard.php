<?php

/**
 * ECMOBAN 礼品卡
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
  * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: gift_gard.php 17217 2011-01-19 06:29:08Z zhuo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/*------------------------------------------------------ */
//-- act 操作项的初始化
/*------------------------------------------------------ */

$categories_pro = get_category_tree_leve_one();
$smarty->assign('categories_pro',  $categories_pro); // 分类树加强版


require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo

//ecmoban模板堂 --zhuo start
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end

$user_id = empty($_SESSION['user_id']) ? 0 :  $_SESSION['user_id'];
          
if (empty($_REQUEST['act']))
{
    if(isset($_SESSION['gift_sn']) && $_SESSION['gift_sn']){
        $_REQUEST['act'] = 'list';
    }else{
        $_REQUEST['act'] = 'gift_login';
    }
}

if($_REQUEST['act'] == 'gift_login')
{
	assign_template();

	$cat_id       = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
	$position = assign_ur_here('gift_gard');
	$smarty->assign('page_title',       $position['title']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
        
        $captcha = intval($_CFG['captcha']);
        if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
        {
            $GLOBALS['smarty']->assign('enabled_captcha', 1);
            $GLOBALS['smarty']->assign('rand', mt_rand());
        }

	$smarty->assign('categories',       get_categories_tree($cat_id)); // 分类树
	$smarty->assign('helps',            get_shop_help());              // 网店帮助
	//$smarty->assign('top_goods',        get_top10());                  // 销售排行
	$smarty->display('gift_gard_login.dwt');
}

if($_REQUEST['act'] == 'check_gift')
{

        if(!$user_id)
        {
            ecs_header("Location: user.php\n");
            exit;
        }
        
	$gift_card = isset($_POST['gift_card']) ? trim($_POST['gift_card']) : '';
	$gift_pwd = isset($_POST['gift_pwd']) ? trim($_POST['gift_pwd']) : '';
	$captcha_str = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        
        if(isset($_POST['captcha']))
        {
            if (empty($captcha_str))
            {
                    show_message($_LANG['cmt_lang']['captcha_not_null'], $_LANG['relogin_lnk'], 'javascript:history.go(-1);', 'error');
            }
        
            if (($captcha_str & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha_str & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
            {

                $verify = new Verify();
                $captcha_code = $verify->check($captcha_str, 'captcha_login');

                if(!$captcha_code){
                    show_message($_LANG['invalid_captcha'], $_LANG['relogin_lnk'], 'javascript:history.go(-1);', 'error');
                }
            }
        }
		
	if (check_gift_login($gift_card, $gift_pwd))
	{
		ecs_header("Location: gift_gard.php?act=list\n");
		exit;
	}
	else
	{
		show_message('礼品卡卡号或密码错误', $_LANG['relogin_lnk'], 'gift_gard.php', 'error');
	}
}

if($_REQUEST['act'] == 'exit_gift')
{
	/* 摧毁cookie */
	$time = time() - 3600;
	setcookie("gift_sn",  '', $time);
	$_SESSION['gift_sn'] = null;
	ecs_header("Location: index.php\n");
	exit;
}


/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/*------------------------------------------------------ */
//-- 礼品卡商品列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 初始化分页信息 */
    $page         = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
    $size         = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;
    $cat_id       = isset($_REQUEST['cat_id']) && intval($_REQUEST['cat_id']) > 0 ? intval($_REQUEST['cat_id']) : 0;
    $gift_sn      = isset($_SESSION['gift_sn']) && empty($_SESSION['gift_sn']) ? "" : addslashes($_SESSION['gift_sn']);


    /* 排序、显示方式以及类型 */
    $default_display_type      = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
    $default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
    $default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'gift_id' : 'gift_id';

    $sort    = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('gift_gard_id'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
    $order   = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
    $display = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
    $display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
//     setcookie('ECS[display]', $display, gmtime() + 86400 * 7);

    /* 页面的缓存ID */
    $cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
        $_CFG['lang']));

    if (!$smarty->is_cached('gift_gard_list.dwt', $cache_id))
    {
        /* 如果页面没有被缓存则重新获取页面的内容 */

        $children = get_children($cat_id);
        
		$cat_select = array('cat_name', 'keywords', 'cat_desc', 'style', 'grade', 'filter_attr', 'parent_id');
        $cat = get_cat_info($cat_id, $cat_select);   // 获得分类的相关信息

        if (!empty($cat))
        {
            $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
            $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
        }

        assign_template();

        $position = assign_ur_here('gift_gard');
        $smarty->assign('page_title',       $position['title']);    // 页面标题
        $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

        $smarty->assign('categories',       get_categories_tree());        // 分类树
        $smarty->assign('helps',            get_shop_help());              // 网店帮助
        //$smarty->assign('top_goods',        get_top10());                  // 销售排行
        $smarty->assign('promotion_info',   get_promotion_info());         // 促销活动信息
		

        /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
        $smarty->assign('country_list',       get_regions());
        $smarty->assign('shop_country',       $_CFG['shop_country']);
        $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
        
        $count = get_gift_goods_count();
        $max_page = ($count> 0) ? ceil($count / $size) : 1;
        if ($page > $max_page)
        {
            $page = $max_page;
        }
        $goodslist = gift_get_goods($size, $page);

        //查询卡内金额
        
        $sql = "SELECT gift_id FROM " . $ecs->table('user_gift_gard') . " WHERE gift_sn='$gift_sn'";
        $gift = $db->getRow($sql);
        
        $sql = "SELECT gift_menory FROM " . $ecs->table('gift_gard_type') . " WHERE gift_id = '" . $gift["gift_id"] . "'";
        $gift_menory = $db->getRow($sql);
        
        $smarty->assign('gift_menory', $gift_menory['gift_menory']);
        $smarty->assign('gift_sn',       $_SESSION['gift_sn']);
        $smarty->assign('goods_list',       $goodslist);
        $smarty->assign('category',         $cat_id);
        $smarty->assign('integral_max',     $integral_max);
        $smarty->assign('integral_min',     $integral_min);


        assign_pager('gift_gard',            $cat_id, $count, $size, $sort, $order, $page, '', ''); // 分页
        assign_dynamic('gift_gard_list'); // 动态内容
    }

    $smarty->display('gift_gard_list.dwt', $cache_id);
}

elseif ($_REQUEST['act'] == 'take_view')
{ 
	$goods_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$gift_sn = isset($_SESSION['gift_sn']) && empty($_SESSION['gift_sn']) ? '' : addslashes($_SESSION['gift_sn']);

	if($gift_sn)
	{
		$pwd = $db->getRow("SELECT * FROM " . $ecs->table('user_gift_gard') . " WHERE gift_sn='$gift_sn' AND is_delete = 1");
		if(check_gift_login($gift_sn, $pwd['gift_password']))
		{
			$_SESSION['gift_sn'] = '';
			ecs_header("Location: gift_gard.php?act=gift_login\n");
			exit;
		}
	}
	

	if(empty($goods_id))
	{
		ecs_header("Location: gift_gard.php?act=list\n");
		exit;
	}
	
	include_once('includes/lib_transaction.php');

	assign_template();
	
	/* 取得国家列表、商店所在国家、商店所在国家的省列表 */
        $smarty->assign('country_list',       get_regions());
        $smarty->assign('shop_country',       $_CFG['shop_country']);
        $smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));
        
        
        $smarty->assign('goods_id', $goods_id);
        
        $position = assign_ur_here('gift_gard');
        $smarty->assign('page_title',       $position['title']);    // 页面标题
        $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

        $smarty->assign('categories',       get_categories_tree());        // 分类树
        $smarty->assign('helps',            get_shop_help());              // 网店帮助
        //$smarty->assign('top_goods',        get_top10());                  // 销售排行
        $smarty->assign('promotion_info',   get_promotion_info());         // 促销活动信息
	$smarty->display('take_view.dwt');
}

elseif($_REQUEST['act'] == 'check_take')
{
	$goods_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	$gift_sn = isset($_SESSION['gift_sn']) && empty($_SESSION['gift_sn']) ? '' : addslashes($_SESSION['gift_sn']);

	if($gift_sn)
	{
		$pwd = $db->getRow("SELECT * FROM " . $ecs->table('user_gift_gard') . " WHERE gift_sn= '$gift_sn' AND is_delete = 1");
		if(!check_gift_login($_SESSION['gift_sn'], $pwd['gift_password']))
		{
			$_SESSION['gift_sn'] = '';
			show_message('礼品卡已使用', '去礼品卡登录页', 'gift_gard.php',  'error');
			exit;
		}
	}
	else
	{
		show_message('礼品卡已过期', '返回上一步', 'gift_gard.php',  'error');
	}
	

	$sql = "SELECT gift_menory FROM " . $ecs->table('gift_gard_type') . " WHERE gift_id='" . $pwd["gift_id"] . "'";
	$gift_type = $db->getRow($sql);
	
	if(empty($goods_id))
	{
		ecs_header("Location: gift_gard.php?act=list\n");
		exit;
	}

	$user_time = gmtime();
	$country = empty($_POST['country']) ? 0 : intval($_POST['country']);	
	$country = $db->getRow("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id='$country' LIMIT 1"); //国家
	$province = empty($_POST['province']) ? 0 : intval($_POST['province']);
	$province = $db->getRow("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id='$province' LIMIT 1");//省份
	$city = empty($_POST['city']) ? 0 : intval($_POST['city']);
	$city = $db->getRow("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id='$city' LIMIT 1");//城市
	$district = empty($_POST['district']) ? 0 : intval($_POST['district']);
	$city = $db->getRow("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id='$district' LIMIT 1");//区域
	$street = empty($_POST['street']) ? 0 : intval($_POST['street']);
	$street = $db->getRow("SELECT region_name FROM " . $ecs->table('region') . " WHERE region_id='$street' LIMIT 1");//街道

	$desc_address   = empty($_POST['desc_address']) ? '0' : addslashes(trim($_POST['desc_address']));
	$consignee = empty($_POST['consignee']) ? '0' : addslashes(trim($_POST['consignee']));
	$mobile = empty($_POST['mobile']) ? '0' : addslashes(trim($_POST['mobile']));
	$shipping_time = empty($_POST['shipping_time']) ? '0' : addslashes(trim($_POST['shipping_time']));
	$address = '[' . $country['region_name'] . ' ' . $province['region_name'] . ' ' . $city['region_name'] . ' ' . $district['region_name'] . ' ' . $street['region_name'] . '] '. $desc_address;
	
	
	if(empty($country) || empty($province) || empty($city) || empty($district) || empty($desc_address) || empty($consignee) || empty($mobile))
	{
		show_message('请填写完整的收获信息，以免货物不能到达您的手中，谢谢配合', '返回重新提货', 'gift_gard.php',  'error');
	}
	
	$sql = "UPDATE " . $ecs->table('user_gift_gard')  . " SET user_id='$user_id', goods_id='$goods_id', user_time='$user_time', address='$address', consignee_name='$consignee', mobile='$mobile', shipping_time='$shipping_time', status='1'  WHERE gift_sn='$_SESSION[gift_sn]'";
	
	if($db->query($sql))
	{
 		$_SESSION['gift_sn'] = "";
                show_message('提货成功', '去我的提货', 'user.php?act=take_list',  'success');
	}
	else
	{
		show_message('提货失败', '重新提货', 'gift_gard.php',  'error');
	}
	
}


/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得礼品卡下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function gift_get_goods($size, $page)
{
	$sql = 'SELECT config_goods_id,gift_id FROM ' . $GLOBALS['ecs']->table('user_gift_gard') . " WHERE gift_sn='$_SESSION[gift_sn]' AND is_delete = 1";
	$config_goods = $GLOBALS['db']->getRow($sql);
	
	$config_goods_arr = explode(',', $config_goods['config_goods_id']);
	
	$sql = "SELECT goods_id, goods_name, shop_price, goods_thumb FROM " . $GLOBALS['ecs']->table('goods') . "WHERE goods_id " . db_create_in($config_goods_arr);
	
    /* 获得商品列表 */
    $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $arr[$row['goods_id']]['goods_id']              = $row['goods_id'];
        $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        $arr[$row['goods_id']]['shop_price']  = $row['shop_price'];
        $arr[$row['goods_id']]['goods_thumb'] = $row['goods_thumb'];
//         $arr[$row['goods_id']]['gift_end_date']              = local_date('Y/m/d', $row['gift_end_date']);
    }
    
    return $arr;
}

/**
 * 获得礼品卡总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_gift_goods_count()
{
    $sql = 'SELECT config_goods_id FROM ' . $GLOBALS['ecs']->table('user_gift_gard') . " WHERE gift_sn='$_SESSION[gift_sn]' AND is_delete = 1";
	$config_goods = $GLOBALS['db']->getRow($sql);
	
	$config_goods_arr = explode(',', $config_goods['config_goods_id']);
	
	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods') . "WHERE goods_id " . db_create_in($config_goods_arr);
	
	
    /* 返回商品总数 */
    return $GLOBALS['db']->getOne($sql);
}



/**
 *  用户登录函数
 *
 * @access  public
 * @param   string  $username
 * @param   string  $password
 *
 * @return void
 */
function check_gift_login($gift_sn, $gift_pwd, $remember = null)
{
	if(empty($gift_pwd) || empty($gift_sn))
	{
		return false;
	}
	

	$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('user_gift_gard') . " WHERE gift_sn = '$gift_sn' AND goods_id = 0 AND is_delete = 1";
	if(!($GLOBALS['db']->getOne($sql)))
	{
		$_SESSION['gift_sn'] = '';
		show_message('礼品卡已使用', '返回礼品卡登录页', 'gift_gard.php', 'error');
		return false;
	}
	
	$sql = "SELECT " . 'gift_gard_id, gift_id' .
	" FROM " . $GLOBALS['ecs']->table('user_gift_gard').
	" WHERE gift_sn = '$gift_sn' AND gift_password = '$gift_pwd' AND is_delete = 1";
	$result = $GLOBALS['db']->getRow($sql);
	if(empty($result))
	{
		$_SESSION['gift_sn'] = '';
		show_message('密码错误', '返回礼品卡登录页', 'gift_gard.php', 'error');
		return false;
	}
	
	$sql = "SELECT gift_end_date, gift_start_date FROM " . $GLOBALS['ecs']->table('gift_gard_type') . " WHERE gift_id = $result[gift_id]";
	$row = $GLOBALS['db']->getRow($sql);
	$time = gmtime();
	if($row['gift_end_date'] <= $time)
	{
		$_SESSION['gift_sn'] = '';
		show_message('礼品卡已过期, 有效期至' . local_date('Y-m-d H:i:s',$row['gift_end_date']), '返回礼品卡登录页', 'gift_gard.php', 'error');
		return false;
	}
	elseif ($row['gift_start_date'] >= $time)
	{
		$_SESSION['gift_sn'] = '';
		show_message('礼品卡开始使用日期为' . local_date('Y-m-d H:i:s',$row['gift_start_date']), '返回礼品卡登录页', 'gift_gard.php', 'error');
		return false;
	}
	
	if ($result)
	{	
		//清除缓存
		clear_all_files();
		$_SESSION['gift_sn'] = $gift_sn;
        $time = time() + 3600 * 24 * 15;
        setcookie("gift_sn", $gift_sn, $time);
		return true;
	}
	else
	{
			$_SESSION['gift_sn'] = '';
		return false;
	}
}

?>
