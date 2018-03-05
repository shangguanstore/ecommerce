<?php

/**
 * ECSHOP 管理中心公用文件
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: init.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

define('ECS_ADMIN', true);


error_reporting(E_ALL);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 初始化设置 */
@ini_set('memory_limit',          '1024M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

if (file_exists('../data/config.php'))
{
    include('../data/config.php');
}
else
{
    include('../includes/config.php');
}

/* 取得当前ecshop所在的根目录 */
if(!defined('SELLER_PATH'))
{
    define('SELLER_PATH','seller');
}

define('ROOT_PATH', str_replace(SELLER_PATH . '/includes/init.php', '', str_replace('\\', '/', __FILE__)));

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

if (isset($_SERVER['PHP_SELF']))
{
    define('PHP_SELF', $_SERVER['PHP_SELF']);
}
else
{
    define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');

//by guan start
require(ROOT_PATH . 'includes/cls_pinyin.php');
require(ROOT_PATH . 'includes/lib_scws.php');
//by guan end

require(ROOT_PATH . SELLER_PATH . '/includes/lib_main.php');
require(ROOT_PATH . SELLER_PATH . '/includes/cls_exchange.php');
require(ROOT_PATH . 'includes/Http.class.php'); //ecmoban模板堂 --zhuo
require(ROOT_PATH . 'includes/lib_ecmoban.php'); //ecmoban模板堂 --zhuo
require(ROOT_PATH . 'includes/lib_ecmobanFunc.php'); //ecmoban模板堂 --zhuo
require(ROOT_PATH . 'includes/lib_publicfunc.php'); //ecmoban模板堂 --zhuo

require(ROOT_PATH . 'data/sms_config.php'); //ecmoban模板堂 --zhuo 短信语言包模板

/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 对路径进行安全处理 */
if (strpos(PHP_SELF, '.php/') !== false)
{
    ecs_header("Location:" . substr(PHP_SELF, 0, strpos(PHP_SELF, '.php/') + 4) . "\n");
    exit();
}

/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$err = new ecs_error('message.htm');

/* 初始化session */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ECSCP_SELLER_ID');

/* 初始化 action */
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'login' || $_REQUEST['act'] == 'logout' || $_REQUEST['act'] == 'signin') &&
    strpos(PHP_SELF, '/privilege.php') === false)
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'forget_pwd' || $_REQUEST['act'] == 'reset_pwd' || $_REQUEST['act'] == 'get_pwd') &&
    strpos(PHP_SELF, '/get_password.php') === false)
{
    $_REQUEST['act'] = '';
}

$sel_config = get_shop_config_val('open_memcached');
//ecmoban模板堂 --zhuo memcached start
if($sel_config['open_memcached'] == 1){
    require(ROOT_PATH . 'includes/cls_cache.php');
    require(ROOT_PATH . 'data/cache_config.php');
    $cache = new cls_cache($cache_config);
}
//ecmoban模板堂 --zhuo memcached end

/* 载入系统参数 */
$_CFG = load_config();
$_CFG['editing_tools'] = 'seller_ueditor'; //修改编辑器目录 by wu

// TODO : 登录部分准备拿出去做，到时候把以下操作一起挪过去
if ($_REQUEST['act'] == 'captcha')
{
    require(ROOT_PATH . '/includes/cls_captcha_verify.php'); //验证码的类 TP
    $code_config = array(
        'imageW' => "120", //验证码图片宽度  
        'imageH' => "36", //验证码图片高度  
        'fontSize' => "18", //验证码字体大小
        'length' => "4", //验证码位数
        'useNoise' => false, //关闭验证码杂点
    );
    $code_config['seKey'] = 'admin_login';
    $img = new Verify($code_config);
    $img->entry();
    exit;
}

require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/common_merchants.php');
require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/log_action.php');

if (file_exists(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/' . basename(PHP_SELF)))
{
    include(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/admin/' . basename(PHP_SELF));
}

if (!file_exists('../temp/caches'))
{
    @mkdir('../temp/caches', 0777);
    @chmod('../temp/caches', 0777);
}

if (!file_exists('../temp/compiled/'. SELLER_PATH))
{
    @mkdir('../temp/compiled/'. SELLER_PATH, 0777);
    @chmod('../temp/compiled/'. SELLER_PATH, 0777);
}

clearstatcache();

/* 如果有新版本，升级 */
if (!isset($_CFG['dsc_version']))
{
    $_CFG['dsc_version'] = 'v1.8';
}

if (preg_replace('/(?:\.|\s+)[a-z]*$/i', '', $_CFG['dsc_version']) != preg_replace('/(?:\.|\s+)[a-z]*$/i', '', VERSION)
        && file_exists('../upgrade/index.php'))
{
    // 转到升级文件
    ecs_header("Location: ../upgrade/index.php\n");

    exit;
}

/* 创建 Smarty 对象。*/
require(ROOT_PATH . 'includes/cls_template.php');
$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . SELLER_PATH . '/templates';
$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/'. SELLER_PATH;
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}


$smarty->assign('lang', $_LANG);
$smarty->assign('help_open', $_CFG['help_open']);

if(isset($_CFG['enable_order_check']))  // 为了从旧版本顺利升级到2.5.0
{
    $smarty->assign('enable_order_check', $_CFG['enable_order_check']);
}
else
{
    $smarty->assign('enable_order_check', 0);
}

/* 验证通行证信息 */
if(isset($_GET['ent_id']) && isset($_GET['ent_ac']) &&  isset($_GET['ent_sign']) && isset($_GET['ent_email']))
{
    $ent_id = trim($_GET['ent_id']);
    $ent_ac = trim($_GET['ent_ac']);
    $ent_sign = trim($_GET['ent_sign']);
    $ent_email = trim($_GET['ent_email']);
    $certificate_id = trim($_CFG['certificate_id']);
    $domain_url = $ecs->url();
    $token=$_GET['token'];
    if($token==md5(md5($_CFG['token']).$domain_url.SELLER_PATH))
    {
        require(ROOT_PATH . 'includes/cls_transport.php');
        $t = new transport('-1',5);
        $apiget = "act=ent_sign&ent_id= $ent_id & certificate_id=$certificate_id";

        $t->request('http://cloud.ecshop.com/api.php', $apiget);
        $db->query('UPDATE '.$ecs->table('shop_config') . ' SET value = "'. $ent_id .'" WHERE code = "ent_id"');
        $db->query('UPDATE '.$ecs->table('shop_config') . ' SET value = "'. $ent_ac .'" WHERE code = "ent_ac"');
        $db->query('UPDATE '.$ecs->table('shop_config') . ' SET value = "'. $ent_sign .'" WHERE code = "ent_sign"');
        $db->query('UPDATE '.$ecs->table('shop_config') . ' SET value = "'. $ent_email .'" WHERE code = "ent_email"');
        clear_cache_files();
        ecs_header("Location: ./index.php\n");
    }
}

/* 验证管理员身份 */
if ((!isset($_SESSION['seller_id']) || intval($_SESSION['seller_id']) <= 0) &&
    $_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
	$_REQUEST['act'] != 'check_user_name' && $_REQUEST['act'] != 'check_user_password' && //by wu
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    /* session 不存在，检查cookie */
    if (!empty($_COOKIE['ECSCP']['seller_id']) && !empty($_COOKIE['ECSCP']['seller_pass']))
    {
        // 找到了cookie, 验证cookie信息
        $sql = 'SELECT user_id, user_name, password, action_list, last_login ' .
                ' FROM ' .$ecs->table('admin_user') .
                " WHERE user_id = '" . intval($_COOKIE['ECSCP']['seller_id']) . "'";
        $row = $db->GetRow($sql);

        if (!$row)
        {
            // 没有找到这个记录
            setcookie($_COOKIE['ECSCP']['seller_id'],   '', 1);
            setcookie($_COOKIE['ECSCP']['seller_pass'], '', 1);

            if (!empty($_REQUEST['is_ajax']))
            {
                make_json_error($_LANG['priv_error']);
            }
            else
            {
                ecs_header("Location: privilege.php?act=login\n");
            }

            exit;
        }
        else
        {
            // 检查密码是否正确
            if (md5($row['password'] . $_CFG['hash_code']) == $_COOKIE['ECSCP']['seller_pass'])
            {
                !isset($row['last_time']) && $row['last_time'] = '';
                set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['last_time']);

                // 更新最后登录时间和IP
                $db->query('UPDATE ' . $ecs->table('admin_user') .
                            " SET last_login = '" . gmtime() . "', last_ip = '" . real_ip() . "'" .
                            " WHERE user_id = '" . $_SESSION['seller_id'] . "'");
            }
            else
            {
                setcookie($_COOKIE['ECSCP']['seller_id'],   '', 1);
                setcookie($_COOKIE['ECSCP']['seller_pass'], '', 1);

                if (!empty($_REQUEST['is_ajax']))
                {
                    make_json_error($_LANG['priv_error']);
                }
                else
                {
                    ecs_header("Location: privilege.php?act=login\n");
                }

                exit;
            }
        }
    }
    else
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}

$smarty->assign('token', $_CFG['token']);

if ($_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    
    $admin_path = preg_replace('/:\d+/', '', $ecs->seller_url()) . SELLER_PATH;//重置路径
    if (!empty($_SERVER['HTTP_REFERER']) &&
        strpos(preg_replace('/:\d+/', '', $_SERVER['HTTP_REFERER']), $admin_path) === false)
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}

//ecmoban模板堂 --zhuo start   
if(isset($_SESSION['seller_name'])){
    $admin_sql = "select user_id from " .$GLOBALS['ecs']->table('admin_user'). " where user_name = '" .$_SESSION['seller_name']. "'";
    $uid = $GLOBALS['db']->getOne($admin_sql);

    $uname = '';
    if($_SESSION['seller_id'] > 0 && $_SESSION['seller_id'] != $uid){
        $admin_sql = "select user_name from " .$GLOBALS['ecs']->table('admin_user'). " where user_id = '" .$_SESSION['seller_id']. "'";
        $uname = $GLOBALS['db']->getOne($admin_sql);
        $_SESSION['seller_name']  = $uname;
    }
}
//ecmoban模板堂 --zhuo end   


/* 管理员登录后可在任何页面使用 act=phpinfo 显示 phpinfo() 信息 */
if ($_REQUEST['act'] == 'phpinfo' && function_exists('phpinfo'))
{
    phpinfo();

    exit;
}

//header('Cache-control: private');
header('content-type: text/html; charset=' . EC_CHARSET);
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 判断是否支持gzip模式 */
/*if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}*/

//by wu
include_once('includes/inc_priv.php');	
include_once('includes/inc_menu.php');

set_seller_menu(); //顶部菜单
get_menu_name(); //当前页面
get_user_menu_pro(); //快捷菜单
unset($modules, $purview); //用完后清空，避免影响其他功能

//设置商家菜单
function set_seller_menu()
{
	//include_once('includes/inc_priv.php');	
	//include_once('includes/inc_menu.php');	
	//include_once(ROOT_PATH . 'languages/zh_cn/admin/common_seller.php');	
	global $modules, $purview, $_LANG;
       
	//菜单排序
   foreach ($modules AS $key => $value) {
       ksort($modules[$key]);
   }
   ksort($modules);	
	
	//商家权限
	$action_list = explode(',', $_SESSION['seller_action_list']);
	
	//权限子菜单
	$action_menu = array();
	foreach($purview as $key=>$val)
	{
		if(is_array($val))
		{
			foreach($val as $k=>$v)
			{
				if(in_array($v, $action_list))
				{
					$action_menu[$key] = $v;
				}
			}
		}
		else
		{
			if(in_array($val, $action_list))
			{
				$action_menu[$key] = $val;
			}			
		}
	} 
	
	//匹配父菜单
	foreach($modules as $key=>$val)
	{
		foreach($val as $k=>$v)
		{
			if(!array_key_exists($k, $action_menu))
			{
				unset($modules[$key][$k]);
			}
		}
		
		if(empty($modules[$key]))
		{
			unset($modules[$key]);
		}
	}

	//菜单赋值
	$menu = array();
	$i = 0;
	foreach($modules as $key=>$val)
	{
		$menu[$i] = array(
			'action'   => $key,
			'label'    => get_menu_url(reset($val), $_LANG[$key]),
			'url'      => get_menu_url(reset($val)),
			'children' => array()
		);
		
		foreach($val as $k=>$v)
		{
			$menu[$i]['children'][] = array(
				'action'   => $k,
				'label'    => get_menu_url($v, $_LANG[$k]),
				'url'      => get_menu_url($v),
				'status'   => get_user_menu_status($k)
			);
		}
		
		$i++;
	}
	$GLOBALS['smarty']->assign('seller_menu', $menu);
}

function get_menu_url($url = '', $name = '')
{
	if($url)
	{
		$url_arr = explode('?', $url);
		if(!$url_arr[0] || !is_file($url_arr[0]))
		{
			$url = '';
			if($name)
			{
				$name = '<span style="text-decoration: line-through; color:#ccc; ">' . $name . '</span>';	
			}			
		}		
	}
	
	if($name)
	{
		return $name;
	}
	else
	{
		return $url;
	}	
}

function get_menu_name()
{
	//include_once('includes/inc_menu.php');
    //include_once('includes/inc_priv.php');
	global $modules, $purview;	
	
    @$url = basename($_SERVER['PHP_SELF'])."?".$_SERVER["QUERY_STRING"];
	if($url)
	{
		//过滤多余的查询
		$url = str_replace('&uselastfilter=1', '', $url);
		$menu_arr = get_menu_arr($url, $modules);
		if($menu_arr)
		{
			$GLOBALS['smarty']->assign('menu_select', $menu_arr);
			return $menu_arr;
		}
	}
	return false;
}

function get_menu_arr($url = '', $list = array())
{
	static $menu_arr = array();
	static $menu_key = NULL;
	foreach($list as $key=>$val)
	{
		if(is_array($val))
		{
			$menu_key = $key;
			get_menu_arr($url, $val);
		}
		else
		{
			if($val == $url)
			{   
				$menu_arr['action'] = $menu_key;
				$menu_arr['current'] = $key;
			}
		}
	}
	return $menu_arr;
}

//获取快捷菜单详细列表信息
function get_user_menu_pro()
{
	$user_menu_pro = array();
	$user_menu_arr = get_user_menu_list();
	if($user_menu_arr)
	{
		foreach($user_menu_arr as $key=>$val)
		{
			$user_menu_pro[$key] = get_menu_info($val);
		}
		$GLOBALS['smarty']->assign('user_menu_pro', $user_menu_pro);
		return $user_menu_pro;
	}
	return false;
}


//返回快捷菜单列表
function get_user_menu_list()
{
	$adminru = get_admin_ru_id();
	if($adminru['ru_id'] > 0)
	{
		$sql = " SELECT user_menu FROM " . $GLOBALS['ecs']->table('seller_shopinfo') . " WHERE ru_id = '".$adminru['ru_id']."' ";
		$user_menu_str = $GLOBALS['db']->getOne($sql);
		if($user_menu_str)
		{
			$user_menu_arr = explode(',', $user_menu_str);
			return $user_menu_arr;
		}		
	}
	return false;	
}

//返回快捷菜单选中状态
function get_user_menu_status($action = '')
{
	$user_menu_arr = get_user_menu_list();
	if($user_menu_arr && in_array($action, $user_menu_arr))
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

//根据action获取菜单名称和url
function get_menu_info($action = '')
{
	global $modules, $_LANG;
	foreach($modules as $key=>$val)
	{
		foreach($val as $k=>$v)
		{
			if($k == $action)
			{
				$user_info = array(
					'action' => $k,
					'label' => $_LANG[$k],
					'url' => $v);
				return $user_info;
			}			
		}
	}
	return false;
}

if(isset($_SESSION['admin_ru_id']) && $_SESSION['admin_ru_id']){
    $smarty->assign('ru_id', $_SESSION['admin_ru_id']);
}
?>
