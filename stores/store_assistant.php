<?php

/**
 * ECSHOP 商品管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods.php 17217 2011-01-19 06:29:08Z liubo $
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_image.php');
$exc = new exchange($ecs->table("store_user"), $db, 'id', 'stores_user', 'store_id', 'email');
$sto = new exchange($ecs->table("offline_store"), $db, 'id', 'stores_user', 'stores_name', 'is_confirm', 'stores_tel', 'stores_opening_hours');
$store_id = $_SESSION['stores_id'];

$ru_id = $GLOBALS['db']->getOne(" SELECT ru_id FROM " . $GLOBALS['ecs']->table('offline_store') . " WHERE id = '$store_id' ");
$smarty->assign("app", "assistant");
$allow_file_types = '|GIF|JPG|PNG|';

if ($_REQUEST['act'] == 'list') {
    store_priv('user_manage'); //检查权限
    $smarty->assign('action_link', array('href' => 'store_assistant.php?act=add', 'text' => $_LANG['store_assistant_add']));
    $smarty->assign('action_link2', array('href' => 'store_assistant.php?act=message_edit', 'text' => $_LANG['store_message_edit']));
    $list = get_store_user($ru_id, $store_id);

    $page_count_arr = seller_page($list, $_REQUEST['page']);
    $smarty->assign('page_count_arr', $page_count_arr);
    $smarty->assign('list', $list['pzd_list']);
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);
    $smarty->assign('full_page', 1);

    $smarty->assign('page_title', $_LANG['store_user']);
    $smarty->display('store_assistant.dwt');
} elseif ($_REQUEST['act'] == 'query') {

    $list = get_store_user($ru_id, $store_id);
    $page_count_arr = seller_page($list, $_REQUEST['page']);
    $smarty->assign('page_count_arr', $page_count_arr);
    $smarty->assign('list', $list['pzd_list']);
    $smarty->assign('filter', $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count', $list['page_count']);

//跳转页面  
    make_json_result($smarty->fetch('store_assistant.dwt'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
} elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') {
    store_priv('user_manage'); //检查权限
    $smarty->assign('action_link', array('href' => 'store_assistant.php?act=list', 'text' => $_LANG['store_assistant_list']));

    $act = ($_REQUEST['act'] == 'add') ? 'insert' : 'update';
    $smarty->assign("act", $act);

    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $smarty->assign("store_action", get_store_action($id));

    if ($_REQUEST['act'] == 'edit') {
        $sql = "SELECT * FROM" . $ecs->table('store_user') . " WHERE id = '$id'";
        $store_user = $db->getRow($sql);
        $smarty->assign('store_user', $store_user);
        $smarty->assign('aa', 1);
    }

    $smarty->assign('page_title', ($_REQUEST['act'] == 'add') ? $_LANG['add_assistant'] : $_LANG['edit_assistant']);
    $smarty->display('store_assistant_info.dwt');
} elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') {
    store_priv('user_manage'); //检查权限
    $store_user = !empty($_REQUEST['store_user']) ? $_REQUEST['store_user'] : '';
    $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $newpassword = !empty($_REQUEST['newpassword']) ? $_REQUEST['newpassword'] : '';
    $confirm_pwd = !empty($_REQUEST['confirm_pwd']) ? $_REQUEST['confirm_pwd'] : '';
    $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';
    $tel = !empty($_REQUEST['tel']) ? $_REQUEST['tel'] : '';
    $store_action = !empty($_REQUEST['store_action']) ? implode(',', $_REQUEST['store_action']) : '';
    if ($_REQUEST['act'] == 'insert') {
        $is_only_user = $exc->is_only('stores_user', $store_user, 0);
        if (!$is_only_user) {
            make_json_response('', 0, sprintf($_LANG['user_exist'], stripslashes($store_user)));
        }
        /* 判断两次密码是否一样 */
        if (strlen($password) !== strlen($confirm_pwd)) {
            make_json_response('', 0, $_LANG['is_different']);
        }
        $ec_salt = rand(1, 9999);
        $time = gmtime();
        $parent_id = $db->getOne("SELECT id FROM" . $ecs->table('store_user') . " WHERE store_id = '$store_id' AND ru_id = '$ru_id' AND parent_id = 0");
        $sql = "INSERT INTO" . $ecs->table('store_user') . "(`ru_id`,`store_id`,`parent_id`,`stores_user`,`stores_pwd`,`ec_salt`,`add_time`,`tel`,`email`,`store_action`) "
                . "VALUES ('$ru_id','$store_id','$parent_id','$store_user','" . md5(md5($password) . $ec_salt) . "','$ec_salt','$time','$tel','$email','$store_action')";
        if ($db->query($sql) == true) {
            $link[0]['text'] = $_LANG['GO_add'];
            $link[0]['href'] = 'store_assistant.php?act=add';

            $link[1]['text'] = $_LANG['bank_list'];
            $link[1]['href'] = 'store_assistant.php?act=list';

            make_json_response('', 1, $_LANG['add_succeed'], array('url' => 'store_assistant.php?act=list'));
        }
    } else {
        $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        /* 检查是否重复 */

        $is_only_user = $exc->is_only('stores_user', $store_user, 0, "id != $id");
        if (!$is_only_user) {
            make_json_response('', 0, sprintf($_LANG['user_exist'], stripslashes($store_user)));
        }

        /* 判断两次密码是否一样 */
        if (strlen($newpassword) !== strlen($confirm_pwd)) {
            make_json_response('', 0, $_LANG['is_different']);
        }
        $sql = "SELECT ec_salt FROM" . $ecs->table('store_user') . " WHERE id = '$id'";
        $ec_salt = $db->getOne($sql);
        $where = '';
        if ($newpassword != '') {
            $where = "stores_pwd = '" . md5(md5($newpassword) . $ec_salt) . "',";
        }

        /* 权限 */
        $user_action = $GLOBALS['db']->getOne("SELECT store_action FROM" . $ecs->table('store_user') . " WHERE id = '$id'");
        if ($user_action != 'all') {
            $set_action = " , store_action = '$store_action' ";
        }

        $sql = "UPDATE" . $ecs->table('store_user') . " SET $where stores_user = '$store_user',tel = '$tel',email = '$email'" . $set_action . " WHERE id = '$id'";
        if ($db->query($sql) == true) {
            $link[0]['text'] = $_LANG['bank_list'];
            $link[0]['href'] = 'store_assistant.php?act=list';
            make_json_response('', 1, $_LANG['edit_succeed'], array('url' => 'store_assistant.php?act=list'));
        }
    }
} elseif ($_REQUEST['act'] == 'remove') {
    $id = intval($_GET['id']);
    $exc->drop($id);
    $url = 'store_assistant.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
} elseif ($_REQUEST['act'] == 'message_edit') {
    store_priv('user_manage'); //检查权限
    $smarty->assign('action_link', array('href' => 'store_assistant.php?act=list', 'text' => $_LANG['store_assistant_list']));
    $smarty->assign('page_title', $_LANG['store_message_edit']);
    $sql = "SELECT stores_name,country,province,city,district,stores_address,stores_tel,stores_opening_hours,stores_traffic_line,stores_img FROM" . $ecs->table('offline_store') . " WHERE id='$store_id'";
    $store_info = $db->getRow($sql);
    $smarty->assign('offline_store', $store_info);
    $smarty->assign('countries', get_regions());
    $smarty->assign('provinces', get_regions(1, 1));
    $smarty->assign('cities', get_regions(2, $store_info['province']));
    $smarty->assign('districts', get_regions(3, $store_info['city']));
    $smarty->display('store_message_info.dwt');
} elseif ($_REQUEST['act'] == 'message_update') {

    store_priv('user_manage'); //检查权限
    $stores_name = isset($_REQUEST['stores_name']) ? $_REQUEST['stores_name'] : '';
    $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
    $province = isset($_REQUEST['province']) ? $_REQUEST['province'] : '';
    $city = isset($_REQUEST['city']) ? $_REQUEST['city'] : '';
    $district = isset($_REQUEST['district']) ? $_REQUEST['district'] : '';
    $stores_address = isset($_REQUEST['stores_address']) ? $_REQUEST['stores_address'] : '';
    $stores_tel = isset($_REQUEST['stores_tel']) ? $_REQUEST['stores_tel'] : '';
    $stores_opening_hours = isset($_REQUEST['stores_opening_hours']) ? $_REQUEST['stores_opening_hours'] : '';
    $stores_traffic_line = isset($_REQUEST['stores_traffic_line']) ? $_REQUEST['stores_traffic_line'] : '';
    /* 检查是否重复 */
    $is_only = $sto->is_only('stores_name', $stores_name, 0, "id != $store_id");
    if (!$is_only) {
        make_json_response('', 0, $_LANG['title_exist'], array('url' => 'store_assistant.php?act=message_edit'));
    }
    /* 取得文件地址 */
    $file_url = '';
    if ((isset($_FILES['stores_img']['error']) && $_FILES['stores_img']['error'] == 0) || (!isset($_FILES['stores_img']['error']) && isset($_FILES['stores_img']['tmp_name']) && $_FILES['stores_img']['tmp_name'] != 'none')) {
        // 检查文件格式
        if (!check_file_type($_FILES['stores_img']['tmp_name'], $_FILES['stores_img']['name'], $allow_file_types)) {
            sys_msg($_LANG['invalid_file']);
        }

        // 复制文件
        $res = upload_article_file($_FILES['stores_img']);
        if ($res != false) {
            $file_url = $res;
        }
    }

    if ($file_url == '') {
        $file_url = $_POST['file_url'];
    }
    /* 如果 file_url 跟以前不一样，且原来的文件是本地文件，删除原来的文件 */
    $sql = "SELECT stores_img FROM " . $ecs->table('offline_store') . " WHERE id = '$store_id'";
    $old_url = $db->getOne($sql);
    if ($old_url != '' && $old_url != $file_url && strpos($old_url, 'http: ') === false && strpos($old_url, 'https: ') === false) {
        @unlink(ROOT_PATH . $old_url);
    }

    $sql = "UPDATE" . $ecs->table('offline_store') . " SET stores_name='$stores_name',country='$country'"
            . ",province='$province',city='$city',district='$district',stores_address='$stores_address',stores_tel='$stores_tel',"
            . "stores_opening_hours='$stores_opening_hours',stores_traffic_line='$stores_traffic_line',stores_img='$file_url' WHERE id = '$store_id'";
    if ($db->query($sql)) {
        $link[0]['text'] = $_LANG['bank_list'];
        $link[0]['href'] = 'store_assistant.php?act=list';
        make_json_response('', 1, $_LANG['edit_succeed'], array('url' => 'store_assistant.php?act=message_edit'));
    }
}

function get_store_user($ru_id = 0, $store_id = 0) {
    $result = get_filter();
    if ($result === false) {
        $where = "WHERE 1";
        /* 商家 */
        $where .= " and ru_id = '$ru_id' ";
        $where .= " and store_id = '$store_id' ";

        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('store_user') . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获活动数据 */
        $sql = "SELECT id,stores_user,tel,email,add_time FROM " . $GLOBALS['ecs']->table('store_user') . " $where ORDER BY id ASC LIMIT " . $filter['start'] . "," . $filter['page_size'];
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);
    foreach ($row as $k => $v) {
        $row[$k]['add_time'] = local_date('Y-m-d H:i:s', $v['add_time']);
    }
    $arr = array('pzd_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

function get_store_action($store_user_id = 0) {
    //获取权限列表
    $sql = " SELECT * FROM " . $GLOBALS['ecs']->table('store_action');
    $store_action = $GLOBALS['db']->getAll($sql);

    //获取店员权限
    $sql = " SELECT store_action FROM " . $GLOBALS['ecs']->table('store_user') . " WHERE id = '$store_user_id' ";
    $user_action = $GLOBALS['db']->getOne($sql);

    foreach ($store_action as $key => $val) {
        if (in_array($val['action_code'], explode(',', $user_action)) || $user_action == 'all') {
            $store_action[$key]['is_check'] = 1;
        } else {
            $store_action[$key]['is_check'] = 0;
        }
    }

    return $store_action;
}

/* 上传文件 */

function upload_article_file($upload) {
    if (!make_dir("../" . DATA_DIR . "/offline_store")) {
        /* 创建目录失败 */
        return false;
    }
    $filename = cls_image::random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
    $path = ROOT_PATH . DATA_DIR . "/offline_store/" . $filename;

    if (move_upload_file($upload['tmp_name'], $path)) {
        return DATA_DIR . "/offline_store/" . $filename;
    } else {
        return false;
    }
}
