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
$cmt->id   = !empty($_GET['id'])   ? json_str_iconv($_GET['id'])   : 0;
$cmt->type = !empty($_GET['type']) ? intval($_GET['type']) : 0;
$cmt->page = isset($_GET['page'])   && intval($_GET['page'])  > 0 ? intval($_GET['page'])  : 1;


if ($result['error'] == 0)
{
    $id = explode("|", $cmt->id);

    $goods_id = $id[0];
    $dis_type = $id[1];
    $revType = $id[2];
    $sort = $id[3];
    
    if($revType){
        $size = 10;
    }else{
        $size = 40;
    }
    
    if(!$sort){
        $sort = 'add_time';
    }
    
    $discuss_list = get_discuss_all_list($goods_id, $dis_type, $cmt->page, $size, $revType, $sort);
    $smarty->assign('discuss_list',     $discuss_list);
    
    if($revType){
        if($dis_type == 4){
            $all_count = get_commentImg_count($goods_id); //帖子总数
        }else{
            $all_count = get_discuss_type_count($goods_id, $revType); //帖子总数
        }
        $smarty->assign('all_count',       $all_count);   
        $smarty->assign('goods_id',       $goods_id);   
        $result['content'] = $smarty->fetch("library/comments_discuss_list1.lbi");
    }else{
        $result['content'] = $smarty->fetch("library/comments_discuss_list2.lbi");
    }
}

echo $json->encode($result);

?>