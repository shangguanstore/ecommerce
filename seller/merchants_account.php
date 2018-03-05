<?php
/**
 * ECSHOP 商家资金管理
 * ============================================================================
 * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: order.php 17219 2011-01-27 10:49:19Z yehuaixiao $
 */
define('IN_ECS', true);
require (dirname(__FILE__) . '/includes/init.php');
require (ROOT_PATH . 'includes/lib_order.php');
$adminru = get_admin_ru_id();

if ($adminru['ru_id'] == 0)
{
	$smarty->assign('priv_ru', 1);
}
else
{
	$smarty->assign('priv_ru', 0);
}

admin_priv('seller_account');

if (!isset($_REQUEST['submit_act']))
{
	if (!isset($_REQUEST['act_type']))
	{
		$Loaction = "merchants_account.php?act=account_manage&act_type=account";
		ecs_header("Location: $Loaction\n");
	}

	$tab_menu = array();

	if ($_REQUEST['act_type'] == 'account')
	{
		$tab_menu[] = array("curr" => 1, "text" => $_LANG['01_seller_account'], "href" => "merchants_account.php?act=account_manage&act_type=account");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['02_seller_deposit'], "href" => "merchants_account.php?act=account_manage&act_type=deposit");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['03_top_up'], "href" => "merchants_account.php?act=account_manage&act_type=topup");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['04_seller_detail'], "href" => "merchants_account.php?act=account_manage&act_type=detail");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['05_seller_account_log'], "href" => "merchants_account.php?act=account_manage&act_type=account_log");
	}
	elseif ($_REQUEST['act_type'] == "deposit")
	{
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['01_seller_account'], "href" => "merchants_account.php?act=account_manage&act_type=account");
		$tab_menu[] = array("curr" => 1, "text" => $_LANG['02_seller_deposit'], "href" => "merchants_account.php?act=account_manage&act_type=deposit");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['03_top_up'], "href" => "merchants_account.php?act=account_manage&act_type=topup");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['04_seller_detail'], "href" => "merchants_account.php?act=account_manage&act_type=detail");
		$tab_menu[] = array("curr" => 0, "text" => $_LANG['05_seller_account_log'], "href" => "merchants_account.php?act=account_manage&act_type=account_log");
	}
	else
	{
		if (($_REQUEST['act_type'] == "topup") || ($_REQUEST['act_type'] == "topup_pay"))
		{
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['01_seller_account'], "href" => "merchants_account.php?act=account_manage&act_type=account");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['02_seller_deposit'], "href" => "merchants_account.php?act=account_manage&act_type=deposit");
			$tab_menu[] = array("curr" => 1, "text" => $_LANG['03_top_up'], "href" => "merchants_account.php?act=account_manage&act_type=topup");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['04_seller_detail'], "href" => "merchants_account.php?act=account_manage&act_type=detail");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['05_seller_account_log'], "href" => "merchants_account.php?act=account_manage&act_type=account_log");
		}
		elseif ($_REQUEST['act_type'] == "detail")
		{
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['01_seller_account'], "href" => "merchants_account.php?act=account_manage&act_type=account");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['02_seller_deposit'], "href" => "merchants_account.php?act=account_manage&act_type=deposit");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['03_top_up'], "href" => "merchants_account.php?act=account_manage&act_type=topup");
			$tab_menu[] = array("curr" => 1, "text" => $_LANG['04_seller_detail'], "href" => "merchants_account.php?act=account_manage&act_type=detail");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['05_seller_account_log'], "href" => "merchants_account.php?act=account_manage&act_type=account_log");
		}
		elseif ($_REQUEST['act_type'] == "account_log") {
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['01_seller_account'], "href" => "merchants_account.php?act=account_manage&act_type=account");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['02_seller_deposit'], "href" => "merchants_account.php?act=account_manage&act_type=deposit");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['03_top_up'], "href" => "merchants_account.php?act=account_manage&act_type=topup");
			$tab_menu[] = array("curr" => 0, "text" => $_LANG['04_seller_detail'], "href" => "merchants_account.php?act=account_manage&act_type=detail");
			$tab_menu[] = array("curr" => 1, "text" => $_LANG['05_seller_account_log'], "href" => "merchants_account.php?act=account_manage&act_type=account_log");
		}
	}

	$smarty->assign("tab_menu", $tab_menu);
}

if ($_REQUEST['act'] == "account_manage")
{
	$smarty->assign("full_page", 1);
	$users_real = get_users_real($adminru['ru_id'], 1);
	$smarty->assign("real", $users_real);
	assign_query_info();

	if ($_REQUEST['act_type'] == "account")
	{
		if (intval($_CFG['sms_signin']) > 0)
		{
			$sms_security_code = $_SESSION['sms_security_code'] = rand(1000, 9999);
			$smarty->assign("sms_security_code", $sms_security_code);
			$smarty->assign("enabled_sms_signin", 1);
		}

		if (!$users_real)
		{
			$smarty->assign("form_act", "insert");
		}
		else
		{
			$smarty->assign("form_act", "update");
		}

		$smarty->display("merchants_account.dwt");
	}
	elseif ($_REQUEST['act_type'] == "deposit")
	{
		if (!$users_real)
		{
			$link[0] = array("href" => "merchants_account.php?act=account_manage&act_type=account", "text" => $_LANG['01_seller_account']);
			sys_msg($_LANG['account_noll'], 0, $link);
		}
		else if ($users_real['review_status'] != 1) {
			$link[0] = array("href" => "merchants_account.php?act=account_manage&act_type=account", "text" => $_LANG['01_seller_account']);
			sys_msg($_LANG['label_status'], 0, $link);
		}

		$smarty->assign("form_act", "deposit_insert");
		$seller_shopinfo = get_seller_shopinfo($adminru['ru_id'], array("seller_money"));
		$smarty->assign("seller_shopinfo", $seller_shopinfo);
		$smarty->display("merchants_deposit.dwt");
	}
	elseif ($_REQUEST['act_type'] == "topup")
	{
		$smarty->assign("form_act", "topup_insert");
		$payment_list = available_payment_list(0);

		foreach ($payment_list as $key => $payment )
		 {
			if (substr($payment['pay_code'], 0, 4) == "pay_")
			{
				unset($payment_list[$key]);
				continue;
			}
		}

		$smarty->assign("pay", $payment_list);
		$seller_shopinfo = get_seller_shopinfo($adminru['ru_id'], array("seller_money"));
		$smarty->assign("seller_shopinfo", $seller_shopinfo);
		$user_money = $db->getOne("SELECT user_money FROM " . $ecs->table("users") . " WHERE user_id='" . $adminru['ru_id'] . "'");
		$smarty->assign("user_money", $user_money);
		$smarty->display("merchants_topup.dwt");
	}
	elseif ($_REQUEST['act_type'] == "detail")
	{
		$list = get_account_log_list($adminru['ru_id'], array(2, 3, 4));
		$log_list = $list['log_list'];
		$smarty->assign("log_list", $log_list);
		$smarty->assign("filter", $list['filter']);
		$smarty->assign("record_count", $list['record_count']);
		$smarty->assign("page_count", $list['page_count']);
		$smarty->display("merchants_detail.dwt");
	}
	elseif ($_REQUEST['act_type'] == "account_log")
	{
		$list = get_account_log_list($adminru['ru_id'], array(1, 4));
		$log_list = $list['log_list'];
		$smarty->assign("log_list", $log_list);
		$smarty->assign("filter", $list['filter']);
		$smarty->assign("record_count", $list['record_count']);
		$smarty->assign("page_count", $list['page_count']);
		$smarty->display("merchants_account_log.dwt");
	}
	elseif ($_REQUEST['act_type'] == "topup_pay")
	{
		include_once (ROOT_PATH . "includes/lib_payment.php");
		$log_id = (isset($_REQUEST['log_id']) ? intval($_REQUEST['log_id']) : 0);
		$sql = "SELECT * FROM " . $ecs->table("seller_account_log") . " WHERE log_id = '$log_id' LIMIT 1";
		$account_log = $db->getRow($sql);
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table("pay_log") . " WHERE order_id = '$log_id' AND order_type = '" . PAY_TOPUP . "' LIMIT 1";
		$pay_log = $db->getRow($sql);
		$payment_info = array();
		$payment_info = payment_info($account_log['pay_id']);
		$payment = unserialize_config($payment_info['pay_config']);
		$payment_info['pay_fee'] = pay_fee($account_log['pay_id'], $account_log['amount'], 0);
		$apply_info['order_amount'] = $account_log['amount'] + $payment_info['pay_fee'];
		$apply_info['order_sn'] = $account_log['apply_sn'];
		$apply_info['user_id'] = $account_log['ru_id'];
		$apply_info['surplus_amount'] = $account_log['amount'];
		$apply_info['log_id'] = $pay_log['log_id'];

		if ($payment_info['pay_code'] == "balance")
		{
			$user_money = $db->getOne("SELECT user_money FROM " . $ecs->table("users") . " WHERE user_id='" . $account_log['ru_id'] . "'");

			if ($user_money >= $account_log['amount'])
			{
				$sql = " UPDATE " . $ecs->table("seller_shopinfo") . " SET seller_money = seller_money + " . $account_log['amount'] . " WHERE ru_id = '" . $account_log['ru_id'] . "'";
				$db->query($sql);
				$sql = " UPDATE " . $ecs->table("seller_account_log") . " SET is_paid = 1, pay_time = '" . gmtime() . "' WHERE log_id = '$log_id'";
				$db->query($sql);
				require (ROOT_PATH . "includes/lib_clips.php");
				$sql = " UPDATE " . $ecs->table("users") . " SET user_money = user_money - " . $account_log['amount'] . " WHERE user_id = '" . $account_log['ru_id'] . "'";
				$db->query($sql);
				$change_desc = $_LANG['label_seller_topup'] . $account_log['apply_sn'];
				$user_account_log = array("user_id" => $account_log['ru_id'], "user_money" => "-" . $account_log['amount'], "change_desc" => $change_desc, "process_type" => 0, "payment" => $payment_info['pay_name'], "change_time" => gmtime(), "change_type" => 2);
				$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table("account_log"), $user_account_log, "INSERT");
				$sql = "UPDATE " . $ecs->table("pay_log") . "SET is_paid = 1 WHERE order_id = '$log_id' AND order_type = '" . PAY_TOPUP . "'";
				$db->query($sql);
				$link[0] = array("href" => "merchants_account.php?act=account_manage&act_type=topup", "text" => $_LANG['topup_account_ok']);
				sys_msg($_LANG['deposit_account_ok'], 0, $link);
			}
			else
			{
				sys_msg("您的余额已不足,请选择其他付款方式!");
			}
		}
		else 
		{
			include_once (ROOT_PATH . "includes/modules/payment/" . $payment_info['pay_code'] . ".php");
			$pay_obj = new $payment_info['pay_code']();
			$payment_info['pay_button'] = $pay_obj->get_code($apply_info, $payment);
		}

		$smarty->assign("payment", $payment_info);
		$smarty->assign("order", $apply_info);
		$smarty->assign("amount", $account_log['amount']);
		$smarty->display("seller_done.dwt");
	}
}
elseif ($_REQUEST['act'] == "query")
{
	if ($_REQUEST['act_type'] == "detail")
	{
		$list = get_account_log_list($adminru['ru_id'], array(2, 3, 4));
		$fetch = "merchants_detail";
	}
	elseif ($_REQUEST['act_type'] == "account_log")
	{
		$list = get_account_log_list($adminru['ru_id'], array(1, 4));
		$fetch = "merchants_account_log";
	}

	if (($_REQUEST['act_type'] == "detail") || ($_REQUEST['act_type'] == "account_log"))
	{
		$smarty->assign("log_list", $list['log_list']);
		$smarty->assign("filter", $list['filter']);
		$smarty->assign("record_count", $list['record_count']);
		$smarty->assign("page_count", $list['page_count']);
		$sort_flag = sort_flag($list['filter']);
		$smarty->assign($sort_flag['tag'], $sort_flag['img']);
		make_json_result($smarty->fetch($fetch . ".dwt"), "", array("filter" => $list['filter'], "page_count" => $list['page_count']));
	}
}
elseif ($_REQUEST['act'] == "account_edit")
{
	$is_insert = (isset($_REQUEST['form_act']) ? trim($_REQUEST['form_act']) : "");
	$other['real_name'] = (isset($_REQUEST['real_name']) ? addslashes(trim($_REQUEST['real_name'])) : "");
	$other['self_num'] = (isset($_REQUEST['self_num']) ? addslashes(trim($_REQUEST['self_num'])) : "");
	$other['bank_name'] = (isset($_REQUEST['bank_name']) ? addslashes(trim($_REQUEST['bank_name'])) : "");
	$other['bank_card'] = (isset($_REQUEST['bank_card']) ? addslashes(trim($_REQUEST['bank_card'])) : "");
	$other['bank_mobile'] = (isset($_REQUEST['mobile_phone']) ? addslashes(trim($_REQUEST['mobile_phone'])) : "");
	$other['mobile_code'] = (isset($_REQUEST['mobile_code']) ? intval($_REQUEST['mobile_code']) : "");
	$other['user_type'] = 1;
	$other['user_id'] = $adminru['ru_id'];
	$link[0] = array("href" => "merchants_account.php?act=account_manage&act_type=account", "text" => $_LANG['01_seller_account']);

	if ($_SESSION['sms_mobile_code'] != $other['mobile_code']) {
		sys_msg($_LANG['mobile_code_error'], 0, $link);
		exit();
	}

	if ($is_insert == "insert") {
		$other['add_time'] = gmtime();
		$db->autoExecute($ecs->table("users_real"), $other, "INSERT");
	}
	else {
		$db->autoExecute($ecs->table("users_real"), $other, "UPDTAE", "user_id = '" . $adminru['ru_id'] . "' AND user_type = 1");
	}

	sys_msg($is_insert ? $_LANG['add_account_ok'] : $_LANG['edit_account_ok'], 0, $link);
}
elseif ($_REQUEST['act'] == "deposit_insert")
{
	$other['amount'] = (isset($_REQUEST['deposit']) ? floatval(trim($_REQUEST['deposit'])) : 0);
	$other['seller_note'] = (isset($_REQUEST['deposit_note']) ? addslashes(trim($_REQUEST['deposit_note'])) : 0);
	$other['real_id'] = (isset($_REQUEST['real_id']) ? intval($_REQUEST['real_id']) : 0);
	$other['add_time'] = gmtime();
	$other['log_type'] = 1;
	$other['ru_id'] = $adminru['ru_id'];
	$other['deposit_mode'] = (isset($_REQUEST['deposit_mode']) ? intval($_REQUEST['deposit_mode']) : 0);
	$db->autoExecute($ecs->table("seller_account_log"), $other, "INSERT");
	$link[0] = array("href" => "merchants_account.php?act=account_manage&act_type=account_log", "text" => $_LANG['05_seller_account_log']);
	sys_msg($_LANG['deposit_account_ok'], 0, $link);
}
elseif ($_REQUEST['act'] == "topup_insert")
{
	include_once (ROOT_PATH . "/includes/cls_image.php");
	include_once (ROOT_PATH . "/includes/lib_clips.php");
	$image = new cls_image($_CFG['bgcolor']);
	$nowTime = gmtime();
	$other['amount'] = (isset($_REQUEST['topup_account']) ? floatval(trim($_REQUEST['topup_account'])) : 0);
	$other['seller_note'] = (isset($_REQUEST['topup_note']) ? addslashes(trim($_REQUEST['topup_note'])) : 0);
	$other['pay_id'] = (isset($_REQUEST['pay_id']) ? intval($_REQUEST['pay_id']) : 0);
	$other['add_time'] = $nowTime;
	$other['log_type'] = 3;
	$other['ru_id'] = $adminru['ru_id'];
	$certificate_img = (isset($_FILES['certificate_img']) ? $_FILES['certificate_img'] : array());

	if ($certificate_img['name']) {
		$other['certificate_img'] = $image->upload_image("", "seller_account", "", 1, $certificate_img['name'], $certificate_img['type'], $certificate_img['tmp_name'], $certificate_img['error'], $certificate_img['size']);
	}

	$other['apply_sn'] = get_order_sn();
	$other['pay_time'] = $nowTime;
	$db->autoExecute($ecs->table("seller_account_log"), $other, "INSERT");
	$log_id = $db->insert_id();
	insert_pay_log($log_id, $other['amount'], PAY_TOPUP);
	$Loaction = "merchants_account.php?act=account_manage&act_type=topup_pay&log_id=" . $log_id;
	ecs_header("Location: $Loaction\n");
}
elseif ($_REQUEST['act'] == "del_pay")
{
	$nowTime = gmtime();
	$log_id = (isset($_REQUEST['log_id']) ? intval($_REQUEST['log_id']) : 0);
	$sql = "DELETE FROM " . $ecs->table("seller_account_log") . " WHERE log_id = '$log_id'";
	$db->query($sql);
	$Loaction = "merchants_account.php?act=account_manage&act_type=detail";
	ecs_header("Location: $Loaction\n");
}

?>
