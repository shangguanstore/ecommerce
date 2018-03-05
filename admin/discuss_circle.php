<?php

/**
 * ECSHOP 用户评论管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: comment_manage.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_goods.php');

include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
    $smarty->assign('priv_ru',   1);
}else{
    $smarty->assign('priv_ru',   0);
} 	
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 获取没有回复的评论列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('discuss_circle');

    $smarty->assign('ur_here',      $_LANG['discuss_circle']);
    $smarty->assign('full_page',    1);

    $list = get_discuss_list($adminru['ru_id']);

    $smarty->assign('discuss_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    $smarty->assign('action_link',  array('text' => $_LANG['discuss_add'],
    		'href' => 'discuss_circle.php?act=add'));

    assign_query_info();
    $smarty->display('discuss_list.dwt');
}



/*------------------------------------------------------ */
//-- 主题添加页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
	admin_priv('discuss_circle');

	$smarty->assign('lang',         $_LANG);
	$smarty->assign('ur_here',      $_LANG['discuss_add']);
	$smarty->assign('action_link',  array('href' => 'discuss_circle.php?act=list', 'text' => $_LANG['discuss_circle']));
	$smarty->assign('action',       'add');

	$smarty->assign('act',     'insert');
	$smarty->assign('cfg_lang',     $_CFG['lang']);
	
	assign_query_info();
	$smarty->display('discuss_info.dwt');
}

/*------------------------------------------------------ */
//-- 主题添加的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
	$goods_id   = !empty($_POST['goods_id']) ? trim($_POST['goods_id']) : 0;
	$dis_title     = !empty($_POST['dis_title'])    ? trim($_POST['dis_title'])    : '';
	$dis_text  = !empty($_POST['content']) ? trim($_POST['content']) : '';
	$user_name  = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
	$discuss_type  = !empty($_POST['discuss_type']) ? intval($_POST['discuss_type']) : 0;
	
	$sql = "SELECT user_id, user_name FROM " . $ecs->table('users') . " WHERE user_name='$user_name'";
	$user = $db->getRow($sql);
	if ($user['user_id'] <= 0)
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG['type_name_exist'], 0, $link);
	}

	$add_time = gmtime();
	
	if($_FILES['img_url'])
	{
		foreach ($_FILES['img_url']['error'] AS $key => $value)
		{
			if ($value == 0)
			{
				if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
				{
					$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
					sys_msg($_LANG['invalid_img_url'], 0, $link);
				}
			}
			elseif ($value == 1)
			{
				$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
				sys_msg($_LANG['img_url_too_big'], 0, $link);
			}
			elseif ($_FILES['img_url']['error'] == 2)
			{
				$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
				sys_msg($_LANG['img_url_too_big'], 0, $link);
			}
		}

		
		// 相册图片
		foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
		{
			if ($value != 'none')
			{
				if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
				{
					$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
					sys_msg($_LANG['invalid_img_url'], 0, $link);
				}
			}
		}		
	}

	/* 插入数据库。 */
	$sql = "INSERT INTO ".$ecs->table('discuss_circle')." (goods_id, user_id, order_id, dis_type, dis_title, dis_text, add_time, user_name)
	VALUES ('$goods_id',
	'$user[user_id]',
	'0',
	'$discuss_type',
	'$dis_title',
	'$dis_text',
	'$add_time',
	'$user[user_name]')";

	$db->query($sql);
	
	$dis_id = $db->insert_id();
	
	/* 处理相册图片 */
	if($_FILES['img_url'])
	{
		if(!empty($dis_id))
		{
			handle_gallery_image(0, $_FILES['img_url'], $_POST['img_desc'], $_POST['img_file'], 0, $dis_id, 'true');
		}
		else
		{
			$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
			sys_msg($_LANG['dis_error'], 0, $link);
		}		
	}
	
	/* 记录管理员操作 */
	admin_log($dis_title, 'add', 'discussinsert');

	/* 清除缓存 */
	clear_cache_files();

	/* 提示信息 */
	$link[0]['text'] = $_LANG['discuss_add'];
	$link[0]['href'] = 'discuss_circle.php?act=add';

	$link[1]['text'] = $_LANG['back_list'];
	$link[1]['href'] = 'discuss_circle.php?act=list';

	sys_msg($_LANG['add'] . "&nbsp;" . $dis_title . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}


/*------------------------------------------------------ */
//-- 主题修改的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
	$dis_id   = !empty($_POST['dis_id']) ? trim($_POST['dis_id']) : 0;
	
	if(empty($dis_id))
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg($_LANG['discuss_exits'], 0, $link);
	}
	
	$dis_title     = !empty($_POST['dis_title'])    ? trim($_POST['dis_title'])    : '';
	$dis_text  = !empty($_POST['content']) ? trim($_POST['content']) : '';
	$old_img_desc  = !empty($_POST['old_img_desc']) ? $_POST['old_img_desc'] : '';
	$front_cover  = !empty($_POST['front_cover']) ? $_POST['front_cover'] : 0;
	$discuss_type  = !empty($_POST['discuss_type']) ? $_POST['discuss_type'] : 1;
	
	
// 	$user_name  = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
// 	$discuss_type  = !empty($_POST['discuss_type']) ? intval($_POST['discuss_type']) : 0;

// 	$sql = "SELECT user_id, user_name FROM " . $ecs->table('users') . " WHERE user_name='$user_name'";
// 	$user = $db->getRow($sql);
// 	if ($user['user_id'] <= 0)
// 	{
// 		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
// 		sys_msg($_LANG['type_name_exist'], 0, $link);
// 	}

	$add_time = gmtime();
	

	/* 插入数据库。 */
	$sql = "UPDATE " . $ecs->table('discuss_circle') . " SET 
			dis_title='$dis_title', 
			dis_text='$dis_text',
			add_time='$add_time',
			dis_type='$discuss_type'
			 WHERE dis_id='$dis_id'";
	$db->query($sql);
// 	if($db->query($sql))
// 	{
// 		//插入相册修改
// 		foreach($old_img_desc as $key => $val)
// 		{
// 			if($key == $front_cover)
// 			{
// 				$set = ", front_cover='1' ";
// 			}
// 			if(!empty($key))
// 			{
// 				$sql = "UPDATE " . $ecs->table('goods_gallery') . " SET img_desc = '$val' $set WHERE img_id = '$key'";
// 				$db->query($sql);
// 			}
// 		}
// 	}

	
	/* 记录管理员操作 */
	admin_log($dis_title, 'add', 'discussinsert');

	/* 清除缓存 */
	clear_cache_files();

	/* 提示信息 */
	$link[0]['text'] = $_LANG['discuss_edit'];
	$link[0]['href'] = "discuss_circle.php?act=reply&id=$dis_id";

	$link[1]['text'] = $_LANG['back_list'];
	$link[1]['href'] = 'discuss_circle.php?act=list';

	sys_msg($_LANG['edit'] . "&nbsp;" . $dis_title . "&nbsp;" . $_LANG['attradd_succed'],0, $link);

}


/*------------------------------------------------------ */
//-- 翻页、搜索、排序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
    $list = get_discuss_list($adminru['ru_id']);

    $smarty->assign('discuss_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('discuss_list.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 回复用户评论(同时查看评论详情)
/*------------------------------------------------------ */
if ($_REQUEST['act']=='reply')
{
    /* 检查权限 */
    admin_priv('discuss_circle');

    $discuss_info = array();
//     $reply_info   = array();
    $id_value     = array();

    /* 获取评论详细信息并进行字符处理 */
    $sql = "SELECT * FROM " .$ecs->table('discuss_circle'). " WHERE dis_id = '$_REQUEST[id]'";
    $discuss_info = $db->getRow($sql);
    $discuss_info['dis_title']  = str_replace('\r\n', '<br />', htmlspecialchars($discuss_info['dis_title']));
    $discuss_info['dis_title']  = nl2br(str_replace('\n', '<br />', $discuss_info['dis_title']));
    $discuss_info['dis_text']  = str_replace('\r\n', '<br />', htmlspecialchars($discuss_info['dis_text']));
    $discuss_info['dis_text']  = nl2br(str_replace('\n', '<br />', $discuss_info['dis_text']));
    $discuss_info['add_time'] = local_date($_CFG['time_format'], $discuss_info['add_time']);
    
    //取得商品名称
    $sql = "SELECT goods_name, original_img FROM " . $ecs->table('goods') . " WHERE goods_id='$discuss_info[goods_id]'";
    $goods = $db->getRow($sql);
	$discuss_info['original_img'] = $goods['original_img'];

    $discuss_info['goods_name'] = $goods['goods_name'];
    
    //取得图片地址
    $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE dis_id = '$discuss_info[dis_id]'";
    $imgs = $db->getAll($sql);
    
    /* 获取管理员的用户名和Email地址 */
    $sql = "SELECT user_name, email FROM ". $ecs->table('admin_user').
           " WHERE user_id = '$_SESSION[admin_id]'";
    $admin_info = $db->getRow($sql);

    /* 取得评论的对象(文章或者商品) */
        $sql = "SELECT goods_name FROM ".$ecs->table('goods').
               " WHERE goods_id = '$discuss_info[goods_id]'";
        $id_value = $db->getOne($sql);

    /* 模板赋值 */

    $smarty->assign('imgs', $imgs);
    $smarty->assign('msg',          $discuss_info); //评论信息
    $smarty->assign('admin_info',   $admin_info);   //管理员信息
    $smarty->assign('act',     'update');  //评论的对象
    
    $smarty->assign('ur_here',      $_LANG['discuss_info']);
    $smarty->assign('action_link',  array('text' => $_LANG['discuss_circle'],
    'href' => 'discuss_circle.php?act=list'));

    /* 页面显示 */
    assign_query_info();
    $smarty->display('discuss_info.dwt');
}
/*------------------------------------------------------ */
//-- 处理 回复用户评论
/*------------------------------------------------------ */
if ($_REQUEST['act']=='action')
{
    admin_priv('discuss_circle');

    /* 获取IP地址 */
    $ip     = real_ip();

    /* 获得评论是否有回复 */
    $sql = "SELECT comment_id, content, parent_id FROM ".$ecs->table('comment').
           " WHERE parent_id = '$_REQUEST[comment_id]'";
    $reply_info = $db->getRow($sql);

    if (!empty($reply_info['content']))
    {
        /* 更新回复的内容 */
        $sql = "UPDATE ".$ecs->table('comment')." SET ".
               "email     = '$_POST[email]', ".
               "user_name = '$_POST[user_name]', ".
               "content   = '$_POST[content]', ".
               "add_time  =  '" . gmtime() . "', ".
               "ip_address= '$ip', ".
               "status    = 0".
               " WHERE comment_id = '".$reply_info['comment_id']."'";
    }
    else
    {
        /* 插入回复的评论内容 */
        $sql = "INSERT INTO ".$ecs->table('comment')." (comment_type, id_value, email, user_name , ".
                    "content, add_time, ip_address, status, parent_id) ".
               "VALUES('$_POST[comment_type]', '$_POST[id_value]','$_POST[email]', " .
                    "'$_SESSION[admin_name]','$_POST[content]','" . gmtime() . "', '$ip', '0', '$_POST[comment_id]')";
    }
    $db->query($sql);

    /* 更新当前的评论状态为已回复并且可以显示此条评论 */
    $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_POST[comment_id]'";
    $db->query($sql);

    /* 邮件通知处理流程 */
    if (!empty($_POST['send_email_notice']) or isset($_POST['remail']))
    {
        //获取邮件中的必要内容
        $sql = 'SELECT user_name, email, content ' .
               'FROM ' .$ecs->table('comment') .
               " WHERE comment_id ='$_REQUEST[comment_id]'";
        $comment_info = $db->getRow($sql);

        /* 设置留言回复模板所需要的内容信息 */
        $template    = get_mail_template('recomment');

        $smarty->assign('user_name',   $comment_info['user_name']);
        $smarty->assign('recomment', $_POST['content']);
        $smarty->assign('comment', $comment_info['content']);
        $smarty->assign('shop_name',   "<a href='".$ecs->url()."'>" . $_CFG['shop_name'] . '</a>');
        $smarty->assign('send_date',   local_date($GLOBALS['_CFG']['time_format'], gmtime()));

        $content = $smarty->fetch('str:' . $template['template_content']);

        /* 发送邮件 */
        if (send_mail($comment_info['user_name'], $comment_info['email'], $template['template_subject'], $content, $template['is_html']))
        {
            $send_ok = 0;
        }
        else
        {
            $send_ok = 1;
        }
    }

    /* 清除缓存 */
    clear_cache_files();

    /* 记录管理员操作 */
    admin_log(addslashes($_LANG['reply']), 'edit', 'users_comment');

    ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[comment_id]&send_ok=$send_ok\n");
    exit;
}
/*------------------------------------------------------ */
//-- 更新评论的状态为显示或者禁止
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'check')
{
    if ($_REQUEST['check'] == 'allow')
    {
        /* 允许评论显示 */
        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 1 WHERE comment_id = '$_REQUEST[id]'";
        $db->query($sql);

        //add_feed($_REQUEST['id'], COMMENT_GOODS);

        /* 清除缓存 */
        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
        exit;
    }
    else
    {
        /* 禁止评论显示 */
        $sql = "UPDATE " .$ecs->table('comment'). " SET status = 0 WHERE comment_id = '$_REQUEST[id]'";
        $db->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        ecs_header("Location: comment_manage.php?act=reply&id=$_REQUEST[id]\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 删除某一条评论
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('discuss_circle');

    $id = intval($_GET['id']);
    $dis_id = intval($_GET['dis_id']);

    $sql = "DELETE FROM " .$ecs->table('discuss_circle'). " WHERE dis_id = '$id'";
    $db->query($sql);

    admin_log('', 'remove', 'ads');
    
    if($dis_id){
        $query = "discuss_reply_query";
    }else{
        $query = "query";
    }
    $url = 'discuss_circle.php?act=' .$query. '&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 批量删除
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'batch_drop')
{
    admin_priv('discuss_circle');
    $dis_id = isset($_POST['dis_id']) ? trim($_POST['dis_id']) : 0;
    $action = isset($_POST['sel_action']) ? trim($_POST['sel_action']) : 'remove';
    
    if (isset($_POST['checkboxes']))
    {
        switch ($action)
        {
            case 'remove':
                $db->query("DELETE FROM " . $ecs->table('discuss_circle') . " WHERE " . db_create_in($_POST['checkboxes'], 'dis_id'));
                break;

           default :
               break;
        }

        clear_cache_files();
        $action = ($action == 'remove') ? 'remove' : 'edit';
        admin_log('', $action, 'adminlog');
        
        if($dis_id > 0){
            $href = "discuss_circle.php?act=user_reply&id=" . $dis_id;
            $back_list = $_LANG['discuss_user_reply'];
        }else{
            $href = "discuss_circle.php?act=list";
            $back_list = $_LANG['back_list'];
        }
        
        $link[] = array('text' => $back_list, 'href' => $href);
        sys_msg(sprintf($_LANG['batch_drop_success'], count($_POST['checkboxes'])), 0, $link);
    }
    else
    {
        /* 提示信息 */
        $link[] = array('text' => $back_list, 'href' => $href);
        sys_msg($_LANG['no_select_discuss'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 获取没有回复的评论列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'user_reply')
{
    /* 检查权限 */
    admin_priv('discuss_circle');
    
    $smarty->assign('ur_here',      $_LANG['discuss_user_reply']);
    $smarty->assign('full_page',    1);

    $list = get_discuss_user_reply_list();

    $smarty->assign('reply_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('dis_id',   $list['dis_id']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('discuss_user_reply.dwt');
}

/*------------------------------------------------------ */
//-- 翻页、搜索、排序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'discuss_reply_query')
{
    $list = get_discuss_user_reply_list();

    $smarty->assign('reply_list', $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('dis_id',   $list['dis_id']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('discuss_user_reply.dwt'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/**
 * 获取讨论列表
 * @access  public
 * @return  array
 */
function get_discuss_list($ru_id)
{
    /* 查询条件 */
    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'dc.add_time' : trim($_REQUEST['sort_by']);
    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

    $where = " WHERE 1";
    $where .= (!empty($filter['keywords'])) ? " AND dc.dis_title LIKE '%" . mysqli_like_quote($filter['keywords']) . "%' " : '';
    
    if($ru_id > 0){
        $where .= " AND g.user_id = '$ru_id'";
    }

    $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('discuss_circle') ." as dc, " .$GLOBALS['ecs']->table('goods') ." g ". " $where" . " AND dc.goods_id = g.goods_id";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);

    /* 获取评论数据 */
    $arr = array();
    $sql  = "SELECT dc.*, g.goods_name, g.user_id as ru_id FROM " .$GLOBALS['ecs']->table('discuss_circle') ." as dc, " .$GLOBALS['ecs']->table('goods') ." g ". " $where "  . " AND dc.goods_id = g.goods_id" .
            " ORDER BY $filter[sort_by] $filter[sort_order] ".
            " LIMIT ". $filter['start'] .", $filter[page_size]";
    $res  = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        $row['shop_name'] = get_shop_name($row['ru_id'], 1);
        $arr[] = $row;
    }
    
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 获取讨论列表
 * @access  public
 * @return  array
 */
function get_discuss_user_reply_list()
{
    $result = get_filter();
    if ($result === false) {
        /* 查询条件 */
        $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        $dis_id = empty($_REQUEST['dis_id']) ? 0 : trim($_REQUEST['dis_id']);
        $id = empty($_REQUEST['id']) ? 0 : trim($_REQUEST['id']);
         $filter['dis_id'] = $dis_id > 0  ? $dis_id : $id;

        $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'dc.add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = " WHERE 1";
        $where .= (!empty($filter['keywords'])) ? " AND dc.dis_title LIKE '%" . mysqli_like_quote($filter['keywords']) . "%' " : '';

        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('discuss_circle') ." as dc " . " $where" . " AND dc.parent_id = '" .$filter['dis_id']. "'";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 获取评论数据 */
        $arr = array();
        $sql  = "SELECT dc.* FROM " .$GLOBALS['ecs']->table('discuss_circle') ." as dc " . " $where "  . " AND dc.parent_id = '" .$filter['dis_id']. "'" .
                " ORDER BY $filter[sort_by] $filter[sort_order] ".
                " LIMIT ". $filter['start'] .", $filter[page_size]";
        set_filter($filter, $sql);
    }else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    
    $res  = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $row['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
        
        $sql = "SELECT u.user_name FROM " .$GLOBALS['ecs']->table('users') ." AS u, " .$GLOBALS['ecs']->table('discuss_circle') ." AS dc ". " WHERE u.user_id = dc.user_id AND dc.dis_id = '" .$row['quote_id']. "'";
        $users  = $GLOBALS['db']->getRow($sql);
        $row['quote_name'] = $users['user_name'];
        
        $arr[] = $row;
    }
    
    $filter['keywords'] = stripslashes($filter['keywords']);
    $arr = array('item' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'dis_id' => $filter['dis_id']);
    
    return $arr;
}
?>