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

if (empty($_REQUEST['act']))
{
    /*
     * act 参数为空
     * 默认为添加评论内容
     */
    $cmt  = $json->decode($_REQUEST['cmt']);
    $cmt->page = 1;
    $cmt->id   = !empty($cmt->id)   ? intval($cmt->id) : 0;
    $cmt->type = !empty($cmt->type) ? intval($cmt->type) : 0;

    if (empty($cmt) || !isset($cmt->type) || !isset($cmt->id))
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['invalid_comments'];
    }
    elseif (!is_email($cmt->email))
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['error_email'];
    }
    else
    {
        if ((intval($_CFG['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
        {
            /* 检查验证码 */
            include_once('includes/cls_captcha.php');

            $validator = new captcha();
            if (!$validator->check_word($cmt->captcha))
            {
                $result['error']   = 1;
                $result['message'] = $_LANG['invalid_captcha'];
            }
            else
            {
                $factor = intval($_CFG['comment_factor']);
                if ($cmt->type == 0 && $factor > 0)
                {
                    /* 只有商品才检查评论条件 */
                    switch ($factor)
                    {
                        case COMMENT_LOGIN :
                            if ($_SESSION['user_id'] == 0)
                            {
                                $result['error']   = 1;
                                $result['message'] = $_LANG['comment_login'];
                            }
                            break;

                        case COMMENT_CUSTOM :
                            if ($_SESSION['user_id'] > 0)
                            {
                                $sql = "SELECT o.order_id FROM " . $ecs->table('order_info') . " AS o ".
                                       " WHERE user_id = '" . $_SESSION['user_id'] . "'".
                                       " AND (o.order_status = '" . OS_CONFIRMED . "' or o.order_status = '" . OS_SPLITED . "') ".
                                       " AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') ".
                                       " AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') ".
                                       " LIMIT 1";


                                 $tmp = $db->getOne($sql);
                                 if (empty($tmp))
                                 {
                                    $result['error']   = 1;
                                    $result['message'] = $_LANG['comment_custom'];
                                 }
                            }
                            else
                            {
                                $result['error'] = 1;
                                $result['message'] = $_LANG['comment_custom'];
                            }
                            break;
                        case COMMENT_BOUGHT :
                            if ($_SESSION['user_id'] > 0)
                            {
                                $sql = "SELECT o.order_id".
                                       " FROM " . $ecs->table('order_info'). " AS o, ".
                                       $ecs->table('order_goods') . " AS og ".
                                       " WHERE o.order_id = og.order_id".
                                       " AND o.user_id = '" . $_SESSION['user_id'] . "'".
                                       " AND og.goods_id = '" . $cmt->id . "'".
                                       " AND (o.order_status = '" . OS_CONFIRMED . "' or o.order_status = '" . OS_SPLITED . "') ".
                                       " AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') ".
                                       " AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') ".
                                       " LIMIT 1";
                                 $tmp = $db->getOne($sql);
                                 if (empty($tmp))
                                 {
                                    $result['error']   = 1;
                                    $result['message'] = $_LANG['comment_brought'];
                                 }
                            }
                            else
                            {
                                $result['error']   = 1;
                                $result['message'] = $_LANG['comment_brought'];
                            }
                    }
                }

                /* 无错误就保存留言 */
                if (empty($result['error']))
                {
                    add_comment($cmt);
                }
            }
        }
        else
        {
            /* 没有验证码时，用时间来限制机器人发帖或恶意发评论 */
            if (!isset($_SESSION['send_time']))
            {
                $_SESSION['send_time'] = 0;
            }

            $cur_time = gmtime();
            if (($cur_time - $_SESSION['send_time']) < 30) // 小于30秒禁止发评论
            {
                $result['error']   = 1;
                $result['message'] = $_LANG['cmt_spam_warning'];
            }
            else
            {
                $factor = intval($_CFG['comment_factor']);
                if ($cmt->type == 0 && $factor > 0)
                {
                    /* 只有商品才检查评论条件 */
                    switch ($factor)
                    {
                        case COMMENT_LOGIN :
                            if ($_SESSION['user_id'] == 0)
                            {
                                $result['error']   = 1;
                                $result['message'] = $_LANG['comment_login'];
                            }
                            break;

                        case COMMENT_CUSTOM :
                            if ($_SESSION['user_id'] > 0)
                            {
                                $sql = "SELECT o.order_id FROM " . $ecs->table('order_info') . " AS o ".
                                       " WHERE user_id = '" . $_SESSION['user_id'] . "'".
                                       " AND (o.order_status = '" . OS_CONFIRMED . "' or o.order_status = '" . OS_SPLITED . "') ".
                                       " AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') ".
                                       " AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') ".
                                       " LIMIT 1";


                                 $tmp = $db->getOne($sql);
                                 if (empty($tmp))
                                 {
                                    $result['error']   = 1;
                                    $result['message'] = $_LANG['comment_custom'];
                                 }
                            }
                            else
                            {
                                $result['error'] = 1;
                                $result['message'] = $_LANG['comment_custom'];
                            }
                            break;

                        case COMMENT_BOUGHT :
                            if ($_SESSION['user_id'] > 0)
                            {
                                $sql = "SELECT o.order_id".
                                       " FROM " . $ecs->table('order_info'). " AS o, ".
                                       $ecs->table('order_goods') . " AS og ".
                                       " WHERE o.order_id = og.order_id".
                                       " AND o.user_id = '" . $_SESSION['user_id'] . "'".
                                       " AND og.goods_id = '" . $cmt->id . "'".
                                       " AND (o.order_status = '" . OS_CONFIRMED . "' or o.order_status = '" . OS_SPLITED . "') ".
                                       " AND (o.pay_status = '" . PS_PAYED . "' OR o.pay_status = '" . PS_PAYING . "') ".
                                       " AND (o.shipping_status = '" . SS_SHIPPED . "' OR o.shipping_status = '" . SS_RECEIVED . "') ".
                                       " LIMIT 1";
                                 $tmp = $db->getOne($sql);
                                 if (empty($tmp))
                                 {
                                    $result['error']   = 1;
                                    $result['message'] = $_LANG['comment_brought'];
                                 }
                            }
                            else
                            {
                                $result['error']   = 1;
                                $result['message'] = $_LANG['comment_brought'];
                            }
                    }
                }
                /* 无错误就保存留言 */
                if (empty($result['error']))
                {
                    add_comment($cmt);
                    $_SESSION['send_time'] = $cur_time;
                }
            }
        }
    }
}
/*------------------------------------------------------ */
//-- 无刷新上传图片ajax
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='ajax_return_images'){

    $img_file = isset($_FILES['filename']) ? $_FILES['filename'] : array();
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    $goods_id = isset($_GET['goods_id']) ? intval($_GET['goods_id']) : 0;
    $user_id = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
    $sessid = isset($_GET['sessid']) ? trim($_GET['sessid']) : '';
    
    $sql = "SELECT count(*) FROM ". $ecs->table('sessions') ." WHERE userid = '$user_id' AND sesskey='$sessid'";

    if (!empty($user_id) && $db->getOne($sql) > 0) {
        include_once(ROOT_PATH . '/includes/cls_image.php');
        $image = new cls_image($_CFG['bgcolor']);

        $img_file = $image->upload_image($img_file, 'cmt_img/'.date('Ym')); //原图
        if($img_file === false){
            $result['error'] = 1;
            $result['msg'] = $image->error_msg();
            die($json->encode($result));
        }

        $img_thumb = $image->make_thumb($img_file, $GLOBALS['_CFG']['single_thumb_width'], $GLOBALS['_CFG']['single_thumb_height'], DATA_DIR . '/cmt_img/'.date('Ym').'/thumb/'); //缩略图
        
        get_oss_add_file(array($img_file, $img_thumb));
        
        $return = array(
            'order_id' => $order_id,
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'comment_img' => $img_file,
            'img_thumb' => $img_thumb
        );

        $sql = "select count(*) from " . $ecs->table('comment_img') . " where user_id = '$user_id' and order_id = '$order_id' and goods_id = '$goods_id'";
        $img_count = $db->getOne($sql);

        if ($img_count < 10 && $img_file) {
            $db->autoExecute($ecs->table('comment_img'), $return, 'INSERT');
        } else {
            $result['error'] = 1;
            $result['msg'] = '最多可传10张图！';
            die($json->encode($result));
        }
    } else {
        $result['error'] = 2;
        $result['msg'] = '请您先登录网站！';
        die($json->encode($result));
    }

    $sql = "select id, comment_img, img_thumb from " . $ecs->table('comment_img') . " where user_id = '$user_id' and order_id = '$order_id' and goods_id = '$goods_id' and comment_id = 0 order by id desc";
    $img_list = $db->getAll($sql);
    
    $result['imglist_count'] = count($img_list);
    $result['currentImg_path'] = $img_list[0]['comment_img'];
    $result['currentImg_id'] = $img_list[0]['id'];
    $smarty->assign('img_list', $img_list);
    $result['content'] = $smarty->fetch("library/comment_image.lbi");

    die($json->encode($result));
}
/*------------------------------------------------------ */
//-- 删除晒单照片
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='del_pictures'){
    $img_id = isset($_REQUEST['cur_imgId']) ? intval($_REQUEST['cur_imgId']) : 0;
    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    
    if(empty($_SESSION['user_id']) || !$img_id){
        $result['error'] = 1;
    }
    $img_list = array();
    
    $sql = "select id, comment_img, img_thumb from " .$ecs->table('comment_img'). " where user_id = '" .$_SESSION['user_id']. "' and order_id = '$order_id' and goods_id = '$goods_id' order by id desc";
    $img_list = $db->getAll($sql);

    foreach($img_list as $key=>$val){
       
        if($img_id == $val['id']){
            $sql = "delete from " .$ecs->table('comment_img'). " where id = '$img_id'";
            $db->query($sql);
            unset($img_list[$key]);
            
            get_oss_del_file(array($val['comment_img'], $val['img_thumb']));
            
            @unlink(ROOT_PATH . $val['comment_img']);
            @unlink(ROOT_PATH . $val['img_thumb']);
        }
        
    }

    $smarty->assign('img_list',        $img_list);
    $result['content'] = $smarty->fetch("library/comment_image.lbi");

    die($json->encode($result));
}
/*------------------------------------------------------ */
//-- 晒单照片列表
/*------------------------------------------------------ */
elseif($_REQUEST['act']=='ajax_return_images_list'){
	
	$sql = "select id, comment_img, img_thumb from " .$ecs->table('comment_img'). " where user_id = '" .$_SESSION['user_id']. "' and order_id = '$order_id' and goods_id = '$goods_id' order by id desc";
	$img_list = $db->getAll($sql);
	
	if($img_list){
		$smarty->assign('img_list',        $img_list);
		$result['content'] = $smarty->fetch("library/comment_image.lbi");
	}else{
		$result['error'] = 1;
	}
	
	die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 晒单入库处理
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'comm_order_goods')
{
    include_once('includes/cls_json.php');
    $json  = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $_POST['cmt']=strip_tags(urldecode($_POST['cmt']));
    $_POST['cmt'] = json_str_iconv($_POST['cmt']);
    
    if (empty($_POST['cmt']))
    {
        $result['error'] = 1;
        die($json->encode($result));
    }

    $cmt = $json->decode($_POST['cmt']);
    
    $user_id = $_SESSION['user_id'];
    $comment_id = isset($cmt->comment_id) ? intval($cmt->comment_id) : 0;
    $rank = isset($cmt->comment_rank) ? intval($cmt->comment_rank) : 5;
    $rank_server = 5;
    $rank_delivery = 5;
    $content = isset($cmt->content) ? htmlspecialchars(trim($cmt->content)) : '';
    $order_id = isset($cmt->order_id) ? intval($cmt->order_id) : 0;
    $goods_id = isset($cmt->goods_id) ? intval($cmt->goods_id) : 0;
    $goods_tag = isset($cmt->impression) ? trim($cmt->impression) : '';
    $sign = isset($cmt->sign) ? trim($cmt->sign) : 0;
    $result['sign'] = $sign;
    $rec_id = isset($cmt->rec_id) ? intval($cmt->rec_id) : 0;
            
    $addtime = gmtime();
    $ip = real_ip();
    
    $captcha_str = isset($cmt->captcha) ? htmlspecialchars(trim($cmt->captcha)) : '';
    
    /* 验证码检查 */
    if (intval($_CFG['captcha']) && gd_version() > 0)
    {
        $verify = new Verify();
        $captcha_code = $verify->check($captcha_str, 'user_comment', $rec_id);
        
        if(!$captcha_code){
            $result['error'] = 1;
            $result['message'] = $_LANG['invalid_captcha'];
            die($json->encode($result));
        }
    }
    
    $sql = "select user_id from " .$ecs->table('goods'). " where goods_id = '$goods_id'";
    $ru_id = $db->getOne($sql);

    if($comment_id == 0){
        $status = 1 - $GLOBALS['_CFG']['comment_check'];
        $sql = "INSERT INTO " . $ecs->table('comment') . "(comment_type, id_value, email, user_name, content, comment_rank, comment_server, comment_delivery, add_time, ip_address
                    , status, parent_id, user_id, single_id, order_id, goods_tag, ru_id)VALUES('0', '$goods_id', ' $_SESSION[email]', '$_SESSION[user_name]', '$content', '$rank', $rank_server, $rank_delivery, '$addtime', '$ip', '$status', '0', '$_SESSION[user_id]', '0', '$order_id', '$goods_tag', '$ru_id')";
        if($db->query($sql))
        {
            //更新comment_img
            $comment_id = $db->insert_id();
            $sql = "UPDATE ". $ecs->table('comment_img') ." SET comment_id = '$comment_id' WHERE order_id = '$order_id' AND goods_id = '$goods_id' AND user_id = '$user_id'" ;
            $db->query($sql);
            //更新评论数量
            $sql = "UPDATE ". $ecs->table('goods') ." SET comments_number = comments_number + 1 WHERE goods_id = '$goods_id'";
            $db->query($sql);
            
            $result['message'] = $GLOBALS['_CFG']['comment_check'] ? $_LANG['cmt_submit_wait'] : $_LANG['cmt_submit_done'];
            $result['message_type'] = '评论信息';
        }
    }else{
        $sql = "UPDATE ". $ecs->table('comment_img') ." SET comment_id = '$comment_id' WHERE order_id = '$order_id' AND goods_id = '$goods_id' AND user_id = '$user_id' AND comment_id = 0" ;
        $db->query($sql);

        $result['message'] = '晒单成功';
        $result['message_type'] = '晒单信息';
    }
    
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 评论商家满意度
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'satisfaction_degree')
{
    include_once('includes/cls_json.php');
    $json  = new JSON;
    $result = array('error' => 0, 'msg' => '', 'content' => '');
    
    $user_id = $_SESSION['user_id'];
    $_POST['rank']=strip_tags(urldecode($_POST['rank']));
    $_POST['rank'] = json_str_iconv($_POST['rank']);
    
    if (empty($_POST['rank']))
    {
        $result['error'] = 1;
        $result['msg'] = '提交参数有误！';
        die($json->encode($result));
    }
    if(empty($user_id)){
        $result['error'] = 1;
        $result['msg'] = '请登录后，评价！';
        die($json->encode($result));
    }

    $cmt = $json->decode($_POST['rank']);
    
    $order_id = isset($cmt->order_id) ? intval($cmt->order_id) : 0;
    $desc_rank = isset($cmt->desc_rank) ? intval($cmt->desc_rank) : 5;
    $service_rank = isset($cmt->service_rank) ? intval($cmt->service_rank) : 5;
    $delivery_rank = isset($cmt->delivery_rank) ? intval($cmt->delivery_rank) : 5;
    $sender_rank = isset($cmt->sender_rank) ? trim($cmt->sender_rank) : '';
    $addtime = gmtime();
    //商家id
    $sql = "SELECT ru_id FROM ". $ecs->table('order_goods') ." WHERE order_id = '$order_id' LIMIT 1";
    $ru_id = $db->getOne($sql);
    
    $sql = "INSERT INTO " . $ecs->table('comment_seller') . "(user_id, ru_id, order_id, desc_rank, service_rank, delivery_rank, sender_rank, add_time )VALUES('$user_id', '$ru_id', '$order_id', ' $desc_rank', '$service_rank', '$delivery_rank', '$sender_rank', '$addtime')";
    $result = $db->query($sql);
    if(!$result)
    {
        $result['error'] = 1;
        $result['msg'] = '提交参数有误！';
    }
    die($json->encode($result));
}elseif($_REQUEST['act'] == 'comment_all' || $_REQUEST['act'] == 'comment_good' || $_REQUEST['act'] == 'comment_middle' || $_REQUEST['act'] == 'comment_short' || $_REQUEST['act'] == 'gotopage'){    
    /*
     * act 参数不为空
     * 默认为评论内容列表
     * 根据 _GET 创建一个静态对象
     */
    $cmt = new stdClass();
    $cmt->id   = !empty($_GET['id'])   ? htmlspecialchars($_GET['id'])   : 0;
    $cmt->type = !empty($_GET['type']) ? intval($_GET['type']) : 0;
    $cmt->page = isset($_GET['page'])   && intval($_GET['page'])  > 0 ? intval($_GET['page'])  : 1;
    
    $id = explode("|", $cmt->id);
    
    $goods_id = $id[0];
    $cmtType = $id[1];
    
    $comments = assign_comment($goods_id, $cmt->type, $cmt->page, $cmtType);
    
    $smarty->assign('comment_type', $cmt->type);
    $smarty->assign('id',           $cmt->id);
    $smarty->assign('username',     $_SESSION['user_name']);
    $smarty->assign('email',        $_SESSION['email']);
    $smarty->assign('comments',     $comments['comments']);
    $smarty->assign('pager',        $comments['pager']);

    $smarty->assign('count',        $comments['count']);
    $smarty->assign('size',        $comments['size']);

    $result['content'] = $smarty->fetch("library/comments_list.lbi");
    echo $json->encode($result);
}
/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 添加评论内容
 *
 * @access  public
 * @param   object  $cmt
 * @return  void
 */
function add_comment($cmt)
{
    /* 评论是否需要审核 */
    $status = 1 - $GLOBALS['_CFG']['comment_check'];

    $user_id = empty($_SESSION['user_id']) ? 0 : $_SESSION['user_id'];
    $email = empty($cmt->email) ? $_SESSION['email'] : trim($cmt->email);
    $user_name = empty($cmt->username) ? $_SESSION['user_name'] : '';
    $email = htmlspecialchars($email);
    $user_name = htmlspecialchars($user_name);
	
    //ecmoban模板堂 --zhuo start  商家ID
    $sql = "select user_id from " .$GLOBALS['ecs']->table('goods'). " where goods_id = '" .$cmt->id. "'";
    $ru_id = $GLOBALS['db']->getOne($sql);
    //ecmoban模板堂 --zhuo end

    /* 保存评论内容 */
    $sql = "INSERT INTO " .$GLOBALS['ecs']->table('comment') .
           "(comment_type, id_value, email, user_name, content, comment_rank, comment_server, comment_delivery, add_time, ip_address, status, parent_id, user_id, ru_id) VALUES " .
           "('" .$cmt->type. "', '" .$cmt->id. "', '$email', '$user_name', '" .$cmt->content."', '".$cmt->rank."', '".$cmt->server."', '".$cmt->delivery."', ".gmtime().", '".real_ip()."', '$status', '0', '$user_id', '$ru_id')";

    $result = $GLOBALS['db']->query($sql);
    clear_cache_files('comments_list.lbi');
    /*if ($status > 0)
    {
        add_feed($GLOBALS['db']->insert_id(), COMMENT_GOODS);
    }*/
    return $result;
}

?>