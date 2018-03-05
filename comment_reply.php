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

$json   = new JSON;
$result = array('error' => 0, 'message' => '', 'content' => '');

$cmt = new stdClass();
$cmt->id   = !empty($_GET['id'])   ? json_str_iconv($_GET['id'])   : 0;
$cmt->type = !empty($_GET['type']) ? intval($_GET['type']) : 0;
$cmt->page = isset($_GET['page'])   && intval($_GET['page'])  > 0 ? intval($_GET['page'])  : 1;
$cmt->libType = isset($_GET['libType'])   && intval($_GET['libType'])  > 0 ? intval($_GET['libType'])  : 0;

if ($result['error'] == 0)
{
    $id = explode("|", $cmt->id);

    $goods_id = $id[0];
    $comment_id = $id[1];
    
    if($cmt->libType == 1){
        $comment_reply = get_reply_list($goods_id, $comment_id, $cmt->type, $cmt->page, $cmt->libType, 10);
    }else{
        $comment_reply = get_reply_list($goods_id, $comment_id, $cmt->type, $cmt->page, $cmt->libType);
    }

    $smarty->assign('comment_type', $cmt->type);
    $smarty->assign('goods_id',     $goods_id);
    $smarty->assign('comment_id',   $comment_id);
    $smarty->assign('reply_list',   $comment_reply['reply_list']);
    $smarty->assign('reply_pager',  $comment_reply['reply_pager']);
    $smarty->assign('reply_count',  $comment_reply['reply_count']);
    $smarty->assign('reply_size',  $comment_reply['reply_size']);
    
    $result['comment_id'] = $comment_id;
    
    if($cmt->libType == 1){
        $result['content'] = $smarty->fetch("library/comment_repay.lbi");
    }else{
        $result['content'] = $smarty->fetch("library/comment_reply.lbi");
    }
    
}

echo $json->encode($result);
?>