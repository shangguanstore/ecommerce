<?php

/**
 * ECSHOP 弹窗管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: brand.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . '/includes/cls_json.php');
$json = new JSON;

/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'operate') {
    $result = array('dialog_type' => '', 'app' => '', 'content' => '');
	
    $dialog_type = empty($_REQUEST['dialog_type'])? '':trim($_REQUEST['dialog_type']);
	$app = empty($_REQUEST['app'])? '':trim($_REQUEST['app']);
	$message = empty($_REQUEST['message'])? '':trim($_REQUEST['message']);
	
    $smarty->assign("dialog_type", $dialog_type);
	$smarty->assign("app", $app);
	$smarty->assign("message", $message);
	
    $result['dialog_type'] = $dialog_type;
	$result['app'] = $app;
    $result['content'] = $GLOBALS['smarty']->fetch('dialog.dwt');
    die($json->encode($result));
}


?>
