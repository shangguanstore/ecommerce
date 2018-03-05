<?php

/**
 * ECSHOP 会员管理程序
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

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 用户帐号列表
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
    $smarty->assign('action_link',  array('text' => $_LANG['04_users_add'], 'href'=>'users.php?act=add'));
	
    //ecmoban模板堂 --zhuo start 会员导出
    $smarty->assign('action_link2',  array('text' => $_LANG['12_users_export'], 'href'=>'javascript:download_userlist();'));
    //ecmoban模板堂 --zhuo end 会员导出
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');

    assign_query_info();
    $smarty->display('users_list.dwt');
}

/*------------------------------------------------------ */
//-- ajax返回用户列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $user_list = user_list();

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('filter',       $user_list['filter']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('page_count',   $user_list['page_count']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $sort_flag  = sort_flag($user_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('users_list.dwt'), '', array('filter' => $user_list['filter'], 'page_count' => $user_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $user = array(  'rank_points'   => $_CFG['register_points'],
                    'pay_points'    => $_CFG['register_points'],
                    'sex'           => 0,
                    'credit_line'   => 0
                    );
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);
    $smarty->assign('extend_info_list', $extend_info_list);
     /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    
    $smarty->assign('ur_here',          $_LANG['04_users_add']);
    $smarty->assign('action_link',      array('text' => $_LANG['11_users_add'], 'href'=>'mc_user.php'));
    $smarty->assign('action_link2',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list'));
    $smarty->assign('form_action',      'insert');
    $smarty->assign('user',             $user);
    $smarty->assign('special_ranks',    get_rank_list(true));
    
    /*获取从1956年到先前的年月日数组*/
    $select_date = array();
    $select_date['year'] = range(1956,date(Y));
    $select_date['month'] = range(1,12);
    $select_date['day'] = range(1,31);
    $smarty->assign("select_date",$select_date);
    
    assign_query_info();
    $smarty->display('user_add.dwt');
}

/*------------------------------------------------------ */
//-- 添加会员帐号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);
    $user_registerMode = array('email' => $email, 'register_mode' => 0);
    
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';
    
    $users =& init_users();

    if (!$users->add_user($username, $password, $user_registerMode))
    {
        /* 插入会员数据失败 */
        if ($users->error == ERR_INVALID_USERNAME)
        {
            $msg = $_LANG['username_invalid'];
        }
        elseif ($users->error == ERR_USERNAME_NOT_ALLOW)
        {
            $msg = $_LANG['username_not_allow'];
        }
        elseif ($users->error == ERR_USERNAME_EXISTS)
        {
            $msg = $_LANG['username_exists'];
        }
        elseif ($users->error == ERR_INVALID_EMAIL)
        {
            $msg = $_LANG['email_invalid'];
        }
        elseif ($users->error == ERR_EMAIL_NOT_ALLOW)
        {
            $msg = $_LANG['email_not_allow'];
        }
        elseif ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            //die('Error:'.$users->error_msg());
        }
        sys_msg($msg, 1);
    }

    /* 注册送积分 */
    if (!empty($GLOBALS['_CFG']['register_points']))
    {
        log_account_change($_SESSION['user_id'], 0, 0, $GLOBALS['_CFG']['register_points'], $GLOBALS['_CFG']['register_points'], $_LANG['register_points']);
    }

    /*把新注册用户的扩展信息插入数据库*/
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    $extend_field_str = '';    //生成扩展字段的内容字符串
    $user_id_arr = $users->get_profile_by_name($username);
    foreach ($fields_arr AS $val)
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(!empty($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];
            $extend_field_str .= " ('" . $user_id_arr['user_id'] . "', '" . $val['id'] . "', '" . $temp_field_content . "'),";
        }
    }
    $extend_field_str = substr($extend_field_str, 0, -1);

    if ($extend_field_str)      //插入注册扩展数据
    {
        $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . ' (`user_id`, `reg_field_id`, `content`) VALUES' . $extend_field_str;
        $db->query($sql);
    }

    /* 更新会员的其它信息 */
    $other =  array();
    $other['credit_line'] = $credit_line;
    $other['user_rank']  = $rank;
    $other['sex']        = $sex;
    $other['birthday']   = $birthday;
    $other['reg_time'] = local_strtotime(local_date('Y-m-d H:i:s'));

    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';
    
    $other['passwd_question']   = $sel_question;
    $other['passwd_answer']  = $passwd_answer;
    
    //验证手机号
    if(!empty($other['mobile_phone']))
    {
        $sql = "SELECT user_id FROM " . $ecs->table('users') ." WHERE mobile_phone = '$other[mobile_phone]'";
        if($db->getOne($sql) > 0)
        {
            sys_msg('该手机号已存在！', 1);
        }
    }
    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

    /* 记录管理员操作 */
    admin_log($_POST['username'], 'add', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['add_success'], htmlspecialchars(stripslashes($_POST['username']))), 0, $link);

}

/*------------------------------------------------------ */
//-- 编辑用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'edit')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "SELECT u.user_name, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , " .
            "u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn, u.office_phone, u.home_phone, u.mobile_phone, ".
            "u.question, u.answer" . 
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);
    $row['user_name'] = addslashes($row['user_name']);
    $users  =& init_users();
    $user   = $users->get_user_info($row['user_name']);

    $sql = "SELECT u.user_id, u.sex, u.birthday, u.pay_points, u.rank_points, u.user_rank , u.user_money, u.frozen_money, u.credit_line, u.parent_id, u2.user_name as parent_username, u.qq, u.msn,
            u.office_phone, u.home_phone, u.mobile_phone,".
            "u.passwd_question, u.passwd_answer" . 
        " FROM " .$ecs->table('users'). " u LEFT JOIN " . $ecs->table('users') . " u2 ON u.parent_id = u2.user_id WHERE u.user_id='$_GET[id]'";

    $row = $db->GetRow($sql);

    if ($row)
    {
        $user['user_id']        = $row['user_id'];
        $user['sex']            = $row['sex'];
        $user['birthday']       = date($row['birthday']);
        if($user['birthday']){
            $birthday               = explode("-", $user['birthday']);
            $user['year']           = intval($birthday[0]);
            $user['month']          = intval($birthday[1]);
            $user['day']            = intval($birthday[2]);
        }
        $user['pay_points']     = $row['pay_points'];
        $user['rank_points']    = $row['rank_points'];
        $user['user_rank']      = $row['user_rank'];
        $user['user_money']     = $row['user_money'];
        $user['frozen_money']   = $row['frozen_money'];
        $user['credit_line']    = $row['credit_line'];
        $user['formated_user_money'] = price_format($row['user_money']);
        $user['formated_frozen_money'] = price_format($row['frozen_money']);
        $user['parent_id']      = $row['parent_id'];
        $user['parent_username']= $row['parent_username'];
        $user['qq']             = $row['qq'];
        $user['msn']            = $row['msn'];
        $user['office_phone']   = $row['office_phone'];
        $user['home_phone']     = $row['home_phone'];
        $user['mobile_phone']   = $row['mobile_phone'];
        $user['passwd_question']   = $row['passwd_question'];
        $user['passwd_answer']   = $row['passwd_answer'];
    }
    else
    {
          $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
          sys_msg($_LANG['username_invalid'], 0, $links);
     }
     
     /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    
    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user[user_id]";
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }

    $smarty->assign('extend_info_list', $extend_info_list);

    /* 当前会员推荐信息 */
    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    if(empty($affiliate['config']['separate_by']))
    {
        //推荐注册分成
        $affdb = array();
        $num = count($affiliate['item']);
        $up_uid = "'$_GET[id]'";
        for ($i = 1 ; $i <=$num ;$i++)
        {
            $count = 0;
            if ($up_uid)
            {
                $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
                $query = $db->query($sql);
                $up_uid = '';
                while ($rt = $db->fetch_array($query))
                {
                    $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                    $count++;
                }
            }
            $affdb[$i]['num'] = $count;
        }
        if ($affdb[1]['num'] > 0)
        {
            $smarty->assign('affdb', $affdb);
        }
    }
    $smarty->assign('full_page',  1);
    $smarty->assign('action_link2',      array('text' => $_LANG['03_users_list'], 'href'=>'users.php?act=list'));
     /*获取从1956年到先前的年月日数组*/
    $select_date = array();
    $select_date['year'] = range(1956,date(Y));
    $select_date['month'] = range(1,12);
    $select_date['day'] = range(1,31);
    $smarty->assign("select_date",$select_date);
    $smarty->assign("user_id",$user['user_id']);
    assign_query_info();
    $smarty->assign('ur_here',          $_LANG['users_edit']);
    $smarty->assign('user',             $user);
    $smarty->assign('form_action',      'update');
    $smarty->assign('special_ranks',    get_rank_list(true));
    $smarty->display('user_list_edit.dwt');
}

/*------------------------------------------------------ */
//-- 更新用户帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'update')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $username = empty($_POST['username']) ? '' : trim($_POST['username']);
    $password = empty($_POST['password']) ? '' : trim($_POST['password']);
    $email = empty($_POST['email']) ? '' : trim($_POST['email']);
    $sex = empty($_POST['sex']) ? 0 : intval($_POST['sex']);
    $sex = in_array($sex, array(0, 1, 2)) ? $sex : 0;
    $birthday = $_POST['birthdayYear'] . '-' .  $_POST['birthdayMonth'] . '-' . $_POST['birthdayDay'];
    $rank = empty($_POST['user_rank']) ? 0 : intval($_POST['user_rank']);
    $credit_line = empty($_POST['credit_line']) ? 0 : floatval($_POST['credit_line']);
    $id = empty($_POST['id']) ? 0 : intval($_POST['id']);
    
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';

    $users  =& init_users();
    
    if (!$users->edit_user(array('user_id'=>$id, 'username'=>$username, 'password'=>$password, 'email'=>$email, 'gender'=>$sex, 'bday'=>$birthday ), 1))
    {
        if ($users->error == ERR_EMAIL_EXISTS)
        {
            $msg = $_LANG['email_exists'];
        }
        else
        {
            $msg = $_LANG['edit_user_failed'];
        }
        sys_msg($msg, 1);
    }
    if(!empty($password))
    {
			$sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_name= '".$username."'";
			$db->query($sql);
	}
    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);
    $user_id_arr = $users->get_profile_by_name($username);
    $user_id = $user_id_arr['user_id'];

    foreach ($fields_arr AS $val)       //循环更新扩展用户信息
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(isset($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr($_POST[$extend_field_index], 0, 99) : $_POST[$extend_field_index];

            $sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            if ($db->getOne($sql))      //如果之前没有记录，则插入
            {
                $sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            }
            else
            {
                $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
            }
            $db->query($sql);
        }
    }


    /* 更新会员的其它信息 */
    $other =  array();
    $other['credit_line'] = $credit_line;
    $other['user_rank'] = $rank;

    $other['msn'] = isset($_POST['extend_field1']) ? htmlspecialchars(trim($_POST['extend_field1'])) : '';
    $other['qq'] = isset($_POST['extend_field2']) ? htmlspecialchars(trim($_POST['extend_field2'])) : '';
    $other['office_phone'] = isset($_POST['extend_field3']) ? htmlspecialchars(trim($_POST['extend_field3'])) : '';
    $other['home_phone'] = isset($_POST['extend_field4']) ? htmlspecialchars(trim($_POST['extend_field4'])) : '';
    $other['mobile_phone'] = isset($_POST['extend_field5']) ? htmlspecialchars(trim($_POST['extend_field5'])) : '';
    
    $other['passwd_question']   = $sel_question;
    $other['passwd_answer']  = $passwd_answer;
    
    //验证手机是否存在
    if(!empty($other['mobile_phone']))
    {
        $sql = "SELECT user_id FROM ". $ecs->table('users') ." WHERE mobile_phone = '$other[mobile_phone]' AND user_id != '$id'";
        if($db->getOne($sql) > 0)
        {
            sys_msg('该手机号已存在！', 1);
        }
    }

    $db->autoExecute($ecs->table('users'), $other, 'UPDATE', "user_name = '$username'");

    /* 记录管理员操作 */
    admin_log($username, 'edit', 'users');

    /* 提示信息 */
    $links[0]['text']    = $_LANG['goto_list'];
    $links[0]['href']    = 'users.php?act=list&' . list_link_postfix();
    $links[1]['text']    = $_LANG['go_back'];
    $links[1]['href']    = 'javascript:history.back()';

    sys_msg($_LANG['update_success'], 0, $links);

}

/*------------------------------------------------------ */
//-- 切换是否验证
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_validated')
{
    check_authz_json('users_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (user_update($id, array('is_validated' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
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
		/* 只有超级管理员才可以删除商家会员 by wu */
		$priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('admin_user'). " WHERE user_id = '$_SESSION[admin_id]'");
		if ($priv_str != 'all')
		{
			foreach($_POST['checkboxes'] as $key=>$val)
			{
				$sql = "SELECT id FROM " .$GLOBALS['ecs']->table('seller_shopinfo'). " WHERE ru_id = '$val'";
				$shopinfo = $GLOBALS['db']->getOne($sql);
				if(!empty($shopinfo))
				{
					unset($_POST['checkboxes'][$key]);
				}
			}    
		}
		
        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id " . db_create_in($_POST['checkboxes']);
        $col = $db->getCol($sql);
        $usernames = implode(',',addslashes_deep($col));
        $count = count($col);
        /* 通过插件来删除用户 */
        $users =& init_users();
        $users->remove_user($col);

        admin_log($usernames, 'batch_remove', 'users');

        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg(sprintf($_LANG['batch_remove_success'], $count), 0, $lnk);
    }
    else
    {
        $lnk[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
        sys_msg($_LANG['no_select_user'], 0, $lnk);
    }
}

elseif ($_REQUEST['act'] == 'main_user') {
    require_once(ROOT_PATH . '/includes/lib_base.php');
    $data = read_static_cache('main_user_str');

    if ($data === false) {
        include_once(ROOT_PATH . 'includes/cls_transport.php');
        
        $ecs_version = VERSION;
        $ecs_lang = $_CFG['lang'];
        $ecs_release = RELEASE;
        $php_ver = PHP_VERSION;
        $mysql_ver = $db->version();
        $ecs_charset = strtoupper(EC_CHARSET);
        
        $scount = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('seller_shopinfo')); //会员数量
        $no_main_order = " WHERE 1 AND (select count(*) from " .$GLOBALS['ecs']->table('order_info'). " AS oi2 WHERE oi2.main_order_id = o.order_id) = 0 ";  //主订单下有子订单时，则主订单不显示
        $sql = 'SELECT COUNT(*) AS oCount, IFNULL(SUM(order_amount), 0) AS oAmount FROM ' . $ecs->table('order_info') ." AS o ". $no_main_order;
        $order['stats'] = $db->getRow($sql);
        $ocount = $order['stats']['oCount']; //订单数量
        $oamount = $order['stats']['oAmount']; //总销售金额
        
        $goods['total'] = $db->GetOne('SELECT COUNT(*) FROM ' . $ecs->table('goods') .
                ' WHERE is_delete = 0 AND is_alone_sale = 1 AND is_real = 1');
        $gcount = $goods['total']; //商品数量
        $ecs_user = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('users')); //会员数量
        
        $ecs_template = $db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . ' WHERE code = \'template\''); //当前使用模板
        $style = $db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . ' WHERE code = \'stylename\'');  //当前模板样式
        if ($style == '') {
            $style = '0';
        }
        $ecs_style = $style;
        $shop_url = urlencode($ecs->url()); //当前url
        
        $httpData = array(
            'domain'            =>  $ecs->get_domain(), //当前域名
            'url'               =>  urldecode($shop_url), //当前url
            'ver'           	=>  $ecs_version,
            'lang'          	=>  $ecs_lang,
            'release'           =>  $ecs_release,
            'php_ver'           =>  $php_ver,
            'mysql_ver'         =>  $mysql_ver,
            'ocount'            =>  $ocount,
            'oamount'           =>  $oamount,
            'gcount'            =>  $gcount,
            'scount'            =>  $scount,
            'charset'           =>  $ecs_charset,
            'usecount'          =>  $ecs_user,
            'template'          =>  $ecs_template,
            'style'             =>  $ecs_style
        );

        $Http = new Http();
        $Http->doPost('http://ecshop.ecmoban.com/dsc_checkver.php', $httpData);
        write_static_cache('main_user_str', $httpData);
    }
}

/*------------------------------------------------------ */
//-- 删除会员帐号
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_drop');
    
    $user_id = intval($_GET['id']);

    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$user_id'";
    $username = $db->getOne($sql);

    /* 只有超级管理员才可以删除商家会员 by wu */
    $sql = "SELECT shop_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') . " WHERE user_id = '$user_id'";
    
    if($GLOBALS['db']->getOne($sql)){
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'users.php?act=list');
        sys_msg(sprintf($_LANG['remove_seller_fail'], $username, $user_id), 0, $link);
    }
    
    /* 通过插件来删除用户 */
    $users =& init_users();
    $users->remove_user($username); //已经删除用户所有数据

    /* 记录管理员操作 */
    admin_log(addslashes($username), 'remove', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['remove_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//--  收货地址查看
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'address_list')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $sql = "SELECT a.*, c.region_name AS country_name, p.region_name AS province, ct.region_name AS city_name, d.region_name AS district_name ".
           " FROM " .$ecs->table('user_address'). " as a ".
           " LEFT JOIN " . $ecs->table('region') . " AS c ON c.region_id = a.country " .
           " LEFT JOIN " . $ecs->table('region') . " AS p ON p.region_id = a.province " .
           " LEFT JOIN " . $ecs->table('region') . " AS ct ON ct.region_id = a.city " .
           " LEFT JOIN " . $ecs->table('region') . " AS d ON d.region_id = a.district " .
           " WHERE user_id='$id'";
    $address = $db->getAll($sql);
    $smarty->assign('address',          $address);
    $smarty->assign("user_id",$id);
    $smarty->assign('form_action','address_list');
    $smarty->assign('full_page',  1);
    $smarty->assign('ur_here',          $_LANG['address_list']);
    if($id > 0){
        $smarty->assign('action_link2',  array('text' => $_LANG['address_list'], 'href'=>'users.php?act=list'));
    }
    
    assign_query_info();
    $smarty->display('user_list_edit.dwt');
}

/*------------------------------------------------------ */
//-- 脱离推荐关系
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'remove_parent')
{
    /* 检查权限 */
    admin_priv('users_manage');

    $sql = "UPDATE " . $ecs->table('users') . " SET parent_id = 0 WHERE user_id = '" . $_GET['id'] . "'";
    $db->query($sql);

    /* 记录管理员操作 */
    $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '" . $_GET['id'] . "'";
    $username = $db->getOne($sql);
    admin_log(addslashes($username), 'edit', 'users');

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'users.php?act=list');
    sys_msg(sprintf($_LANG['update_success'], $username), 0, $link);
}

/*------------------------------------------------------ */
//-- 查看用户推荐会员列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'aff_list')
{
    /* 检查权限 */
    admin_priv('users_manage');
    $smarty->assign('ur_here',      $_LANG['03_users_list']);

    $auid = $_GET['auid'];
    $user_list['user_list'] = array();

    $affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
    $smarty->assign('affiliate', $affiliate);

    empty($affiliate) && $affiliate = array();

    $num = count($affiliate['item']);
    $up_uid = "'$auid'";
    $all_count = 0;
    for ($i = 1; $i<=$num; $i++)
    {
        $count = 0;
        if ($up_uid)
        {
            $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
            $query = $db->query($sql);
            $up_uid = '';
            while ($rt = $db->fetch_array($query))
            {
                $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                $count++;
            }
        }
        $all_count += $count;

        if ($count)
        {
            $sql = "SELECT user_id, user_name, '$i' AS level, email, is_validated, user_money, frozen_money, rank_points, pay_points, reg_time ".
                    " FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_id IN($up_uid)" .
                    " ORDER by level, user_id";
            $user_list['user_list'] = array_merge($user_list['user_list'], $db->getAll($sql));
        }
    }

    $temp_count = count($user_list['user_list']);
    for ($i=0; $i<$temp_count; $i++)
    {
        $user_list['user_list'][$i]['reg_time'] = local_date($_CFG['date_format'], $user_list['user_list'][$i]['reg_time']);
    }

    $user_list['record_count'] = $all_count;

    $smarty->assign('user_list',    $user_list['user_list']);
    $smarty->assign('record_count', $user_list['record_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('action_link',  array('text' => $_LANG['back_note'], 'href'=>"users.php?act=edit&id=$auid"));

    assign_query_info();
    $smarty->display('affiliate_list.htm');
}

//ecmoban模板堂 --zhuo start 会员导出
elseif ($_REQUEST['act'] == 'export'){
    $filename = date('YmdHis').".csv";
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=".$filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    
    $user_list = user_list();
    echo user_date($user_list['user_list']);
    exit;
}

function user_date($result) {
    if(empty($result)) {
        return i("没有符合您要求的数据！^_^");
    }
    $data = i('编号,会员名称,商家名称,联系方式,邮件地址,是否已验证,可用资金,冻结资金,等级积分,消费积分,注册日期'."\n");
    $count = count($result);
    for($i = 0 ; $i < $count ;  $i++) {
        
        if(empty($result[$i]['ru_name'])){
            $result[$i]['ru_name'] = "商城会员";
        }
        
        $data .= i($result[$i]['user_id']).','.
                i($result[$i]['user_name']).','.i($result[$i]['ru_name']).','.
                i($result[$i]['mobile_phone']).','.i($result[$i]['email']).','.
                i($result[$i]['is_validated']).','.i($result[$i]['user_money']).','.
                i($result[$i]['frozen_money']).','.i($result[$i]['rank_points']).','.
                i($result[$i]['pay_points']).','.i($result[$i]['reg_time'])."\n";
    }
    return $data;
}
function i($strInput) {
    return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
}
//ecmoban模板堂 --zhuo 会员导出 end

/**
 *  返回用户列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function user_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $filter['rank'] = empty($_REQUEST['rank']) ? 0 : intval($_REQUEST['rank']);
        $filter['pay_points_gt'] = empty($_REQUEST['pay_points_gt']) ? 0 : intval($_REQUEST['pay_points_gt']);
        $filter['pay_points_lt'] = empty($_REQUEST['pay_points_lt']) ? 0 : intval($_REQUEST['pay_points_lt']);
        $filter['mobile_phone'] = empty($_REQUEST['mobile_phone']) ? 0 : addslashes($_REQUEST['mobile_phone']);
        $filter['email'] = empty($_REQUEST['email']) ? 0 : addslashes($_REQUEST['email']);

        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'u.user_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);

        $ex_where = ' WHERE 1 ';
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = empty($_REQUEST['store_search']) ? 0 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] !=0){
           if($ru_id == 0){ 
               
               if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }
               
                if($filter['store_search'] == 1){
                    $ex_where .= " AND u.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }
                
                if($filter['store_search'] > 1){
                    $ex_where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                              " WHERE msi.user_id = u.user_id $store_where) > 0 ";
                }
           }
        }
        //管理员查询的权限 -- 店铺查询 end
        
        if ($filter['keywords'])
        {
            $ex_where .= " AND u.user_name LIKE '%" . mysqli_like_quote($filter['keywords']) ."%'";
        }
        
        if ($filter['mobile_phone'])
        {
            $ex_where .= " AND u.mobile_phone = '" . $filter['mobile_phone'] ."'";
        }
        
        if ($filter['email'])
        {
            $ex_where .= " AND u.email = '" . $filter['email'] ."'";
        }
        
        if ($filter['rank'])
        {
            $sql = "SELECT min_points, max_points, special_rank FROM ".$GLOBALS['ecs']->table('user_rank')." WHERE rank_id = '$filter[rank]'";
            $row = $GLOBALS['db']->getRow($sql);
            if ($row['special_rank'] > 0)
            {
                /* 特殊等级 */
                $ex_where .= " AND u.user_rank = '$filter[rank]' ";
            }
            else
            {
                $ex_where .= " AND u.rank_points >= " . intval($row['min_points']) . " AND u.rank_points < " . intval($row['max_points']);
            }
        }
        if ($filter['pay_points_gt'])
        {
             $ex_where .=" AND u.pay_points >= '$filter[pay_points_gt]' ";
        }
        if ($filter['pay_points_lt'])
        {
            $ex_where .=" AND u.pay_points < '$filter[pay_points_lt]' ";
        }

        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('users') ." AS u ". $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT u.user_id, u.user_name, u.nick_name, u.mobile_phone, u.email, u.is_validated, u.user_money, u.frozen_money, u.rank_points, u.pay_points, u.reg_time,rank_points ".
                " FROM " . $GLOBALS['ecs']->table('users') ." AS u ". $ex_where .
                " ORDER by " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $user_list = $GLOBALS['db']->getAll($sql);

    $count = count($user_list);
    for ($i=0; $i<$count; $i++)
    {
        $user_list[$i]['ru_name'] = get_shop_name($user_list[$i]['user_id'], 1); //ecmoban模板堂 --zhuo
        $user_list[$i]['reg_time'] = local_date($GLOBALS['_CFG']['date_format'], $user_list[$i]['reg_time']);
        $user_list[$i]['rank_name'] = $GLOBALS['db']->getOne("SELECT rank_name FROM".$GLOBALS['ecs']->table('user_rank')." WHERE min_points <=  '".$user_list[$i]['rank_points']."' AND max_points >= '".$user_list[$i]['rank_points']."'");
        if($user_list[$i]['rank_name'] == ''){
            $user_list[$i]['rank_name'] = '无等级';
        }
    }

    $arr = array('user_list' => $user_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

/**
 * 添加会员
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function user_update($user_id, $args)
{
    if (empty($args) || empty($user_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $args, 'update', "user_id='$user_id'");
}
?>