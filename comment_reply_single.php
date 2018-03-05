<?php

/**
 * ECSHOP 提交用户评论
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: comment.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');
if (!isset($_REQUEST['cmt']) && !isset($_REQUEST['act']))
{
    /* 只有在没有提交评论内容以及没有act的情况下才跳转 */
    ecs_header("Location: ./\n");
    exit;
}
$_REQUEST['cmt'] = isset($_REQUEST['cmt']) ? json_str_iconv($_REQUEST['cmt']) : '';

$json   = new JSON;
$result = array('error' => 0, 'message' => '', 'content' => '');


$cmt = new stdClass();
$cmt->id   = isset($_GET['id'])   ? intval($_GET['id'])   : 0;
$cmt->libType   = isset($_GET['libType'])   ? intval($_GET['libType'])   : 0;
$cmt->type = isset($_GET['type']) ? intval($_GET['type']) : 0;
$cmt->page = isset($_GET['page'])   && intval($_GET['page'])  > 0 ? intval($_GET['page']) : 1;


if ($result['error'] == 0)
{
	if($cmt->libType == 1){
		
		$reply_list = single_show_reply_list($cmt->id, $cmt->page);
		
		$smarty->assign('comment_list', $reply_list['comment_list']);
		$smarty->assign('reply_paper', $reply_list['reply_paper']);
		
		$result['content'] = $smarty->fetch("library/comment_reply_show.lbi");
	}else{
		$single_reply = assign_comments_single_reply($cmt->id, $cmt->type, $cmt->page);
		$reply_comments = $single_reply['reply_comments'];
		$reply_paper = $single_reply['reply_paper'];
		
		$smarty->assign('reply_comments',        $reply_comments);
		$smarty->assign('reply_paper',        $reply_paper);
	
		$result['comment_id'] = $cmt->id;
		$result['content'] = $smarty->fetch("library/comment_reply_list.lbi");
	}
}

echo $json->encode($result);

?>