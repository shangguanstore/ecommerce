<?php

/**
 * ECSHOP 管理员信息以及权限管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: privilege.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'login';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
$smarty->assign('seller',   1);
$php_self = get_php_self(1);
$smarty->assign('php_self',     $php_self);
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 退出登录
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'logout')
{
    /* 清除cookie */
    setcookie('ECSCP[stores_id]',   '', 1);
    setcookie('ECSCP[store_user_id]',   '', 1);
    setcookie('ECSCP[seller_pass]', '', 1);

    $sess->destroy_session();
    
    unset($_SESSION['admin_ru_id']);

    $_REQUEST['act'] = 'login';
}

/*------------------------------------------------------ */
//-- 登陆界面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'login')
{
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    
    if ((intval($_CFG['captcha']) & CAPTCHA_ADMIN) && gd_version() > 0)
    {
        $smarty->assign('gd_version', gd_version());
        $smarty->assign('random',     mt_rand());
    }

    $smarty->display('login.dwt');
}

/*------------------------------------------------------ */
//-- 验证登陆信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'signin')
{
    //if (gd_version() > 0 && (intval($_CFG['captcha']) & CAPTCHA_ADMIN))
	if (gd_version() > 0)
    {
        require(ROOT_PATH . '/includes/cls_captcha_verify.php'); //验证码的类 TP
        /* 检查验证码是否正确 */
        $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
            
        $verify = new Verify();
        $captcha_code = $verify->check($captcha, 'admin_login');

        if(!$captcha_code){
            //sys_msg($_LANG['captcha_error'], 1);
			make_json_response('', 0, $_LANG['captcha_error']);
        }
    }
    $_POST['username'] = isset($_POST['stores_user']) ? trim($_POST['stores_user']) : '';
    $_POST['password'] = isset($_POST['stores_pwd']) ? trim($_POST['stores_pwd']) : '';

    $sql="SELECT `ec_salt` FROM ". $ecs->table('store_user') ."WHERE stores_user = '" . $_POST['stores_user']."'";
    $ec_salt =$db->getOne($sql);
	
    if(!empty($ec_salt))
    {
         /* 检查密码是否正确 */
         $sql = "SELECT id,ru_id, stores_user, stores_pwd ,ec_salt,store_id ".
            " FROM " . $ecs->table('store_user') .
            " WHERE stores_user = '" . $_POST['username']. "' AND stores_pwd = '" . md5(md5($_POST['password']).$ec_salt) . "'";
    }
    else
    {
         /* 检查密码是否正确 */
         $sql = "SELECT id,ru_id, stores_user, stores_pwd ,ec_salt,store_id".
            " FROM " . $ecs->table('store_user') .
            " WHERE stores_user = '" . $_POST['username']. "' AND stores_pwd = '" . md5($_POST['password']) . "'";
    }
    $row = $db->getRow($sql);
    
    if ($row)
    {
        // 登录成功
        set_admin_session($row['id'], $row['stores_user'],$row['store_id']);
	    if(empty($row['ec_salt']))
	    {
                $ec_salt=rand(1,9999);
                $new_possword=md5(md5($_POST['password']).$ec_salt);
                $db->query("UPDATE " .$ecs->table('store_user').
             " SET ec_salt='" . $ec_salt . "', stores_pwd='" .$new_possword . "'".
             " WHERE id='$_SESSION[store_user_id]'");
            }

        // 清除购物车中过期的数据
        clear_cart();
        //ecs_header("Location: ./index.php\n");
		make_json_response('', 1, '登陆成功', array('url' => 'index.php'));

        exit;
    }
    else
    {
        //sys_msg($_LANG['login_faild'], 1);
		make_json_response('', 0, $_LANG['login_faild']);
    }
}
/* 清除购物车中过期的数据 */
function clear_cart()
{
    /* 取得有效的session */
    $sql = "SELECT DISTINCT session_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " .
                $GLOBALS['ecs']->table('sessions') . " AS s " .
            "WHERE c.session_id = s.sesskey ";
    $valid_sess = $GLOBALS['db']->getCol($sql);

    // 删除cart中无效的数据
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id NOT " . db_create_in($valid_sess);
    $GLOBALS['db']->query($sql);
	// 删除cart_combo中无效的数据 by mike
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart_combo') .
            " WHERE session_id NOT " . db_create_in($valid_sess);
    $GLOBALS['db']->query($sql);
}

?>
