<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_image.php');
/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("seller_grade"), $db, 'id', 'grade_name');
/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';

/*------------------------------------------------------ */
//-- 等级列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list') 
{
    admin_priv('seller_grade');
    
    $smarty->assign('ur_here',      $_LANG['seller_garde_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['add_seller_garde'], 'href' => 'seller_grade.php?act=add'));
    $smarty->assign('action_link2',  array('text' => $_LANG['entry_criteria'], 'href' => 'entry_criteria.php?act=list'));
    
    $seller_garde = get_pzd_list();
    
    $smarty->assign('garde_list', $seller_garde['pzd_list']);
    $smarty->assign('filter', $seller_garde['filter']);
    $smarty->assign('record_count', $seller_garde['record_count']);
    $smarty->assign('page_count', $seller_garde['page_count']);
    $smarty->assign('full_page', 1);
	
    $smarty->display("seller_grade_list.dwt");
} 

/*------------------------------------------------------ */
//-- 等级查询列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query') 
{
    admin_priv('seller_grade');
    $seller_garde = get_pzd_list();
    
    $smarty->assign('garde_list', $seller_garde['pzd_list']);
    $smarty->assign('filter', $seller_garde['filter']);
    $smarty->assign('record_count', $seller_garde['record_count']);
    $smarty->assign('page_count', $seller_garde['page_count']);
    
//跳转页面  
    make_json_result($smarty->fetch('seller_grade_list.dwt'), '', array('filter' => $seller_garde['filter'], 'page_count' => $seller_garde['page_count']));
} 

/*------------------------------------------------------ */
//-- 等级添加/编辑
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') 
{
    admin_priv('seller_grade');
    
    $smarty->assign('ur_here',      $_LANG['add_seller_garde']);
    $smarty->assign('action_link',  array('text' => $_LANG['seller_garde_list'], 'href' => 'seller_grade.php?act=list'));
    
    $criteria=$db->getAll(" SELECT id,criteria_name FROM ".$ecs->table('entry_criteria')." WHERE  parent_id = 0");//获取所有第一级标准
   
    
    $id=!empty($_REQUEST['id'])   ? $_REQUEST['id']:0;
    if($id > 0){
        $seller_grade=$db->getRow(" SELECT * FROM ".$ecs->table('seller_grade')." WHERE  id = '$id' ");
        $entry_criteria =  unserialize($seller_grade['entry_criteria']);
        /*判断是否选中*/
        foreach($criteria as $k=>$v){
            foreach($entry_criteria as $val){
                if($val == $v['id']){
                    $criteria[$k]['in_check'] = 1;
                }
            }
        }
        $smarty->assign("seller_grade",$seller_grade);
    }
     $smarty->assign('criteria',$criteria);
    $act =  ($_REQUEST['act'] == 'add')  ?  'insert' : 'update';
    $smarty->assign('act',$act);
   
    $smarty->display("seller_grade_info.dwt");
}

/*------------------------------------------------------ */
//-- 等级插入/更新数据
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update') 
{
    admin_priv('seller_grade');

    $grade_name = !empty($_REQUEST['grade_name']) ? $_REQUEST['grade_name'] : '';
    $goods_sun = !empty($_REQUEST['goods_sun']) ? $_REQUEST['goods_sun'] : '';
    $seller_temp = !empty($_REQUEST['seller_temp']) ? $_REQUEST['seller_temp'] : '';
    $grade_introduce = !empty($_REQUEST['grade_introduce']) ? $_REQUEST['grade_introduce'] : '';
    $top_amount = !empty($_REQUEST['top_amount']) ? $_REQUEST['top_amount'] : '';
    $top_deal_num = !empty($_REQUEST['top_deal_num']) ? $_REQUEST['top_deal_num'] : '';
    $entry_criteria = serialize(!empty($_REQUEST['entry_criteria']) ? $_REQUEST['entry_criteria'] : array());
    $is_open = !empty($_REQUEST['is_open']) ? intval($_REQUEST['is_open']) : 0;
    $is_default = !empty($_REQUEST['is_default']) ? intval($_REQUEST['is_default']) : 0;
    $favorable_rate = !empty($_REQUEST['favorable_rate']) ? intval($_REQUEST['favorable_rate']) : 0;
    $give_integral = !empty($_REQUEST['give_integral']) ? intval($_REQUEST['give_integral']) : 0;
    $rank_integral = !empty($_REQUEST['rank_integral']) ? intval($_REQUEST['rank_integral']) : 0;
    $pay_integral = !empty($_REQUEST['pay_integral']) ? intval($_REQUEST['pay_integral']) : 0;
    $white_bar = !empty($_REQUEST['white_bar']) ? intval($_REQUEST['white_bar']) : 0;

    /* 如果默认则取消其他默认 */
    if ($is_default == 1) {
        $sql = "UPDATE" . $ecs->table('seller_grade') . " SET is_default = 0 WHERE is_default=1";
        $db->query($sql);
    }
    
    if( $_REQUEST['act'] == 'update'){
        /*检查是否重复*/
        $is_only = $exc->is_only('grade_name', $grade_name,0,"id != $_POST[id]");
        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['title_exist'], stripslashes($grade_name)), 1);
        }
          /* 取得文件地址 */
         $file_url = '';
         if ((isset($_FILES['file']['error']) && $_FILES['file']['error'] == 0) || (!isset($_FILES['file']['error']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != 'none'))
         {
               //检查文件格式
             if (!check_file_type($_FILES['file']['tmp_name'], $_FILES['file']['name'], $allow_file_types))
             {
                 sys_msg($_LANG['invalid_file']);
             }
 
               //复制文件
             $res = upload_article_file($_FILES['file']);
             if ($res != false)
             {
                 $file_url = $res;
             }
         }
         
         if ($file_url == '')
         {
            if ($_POST['file_url']) {
                $return_content = http_get_data($_POST['file_url']);
                $filename = basename($_POST['file_url']);
                $path = DATA_DIR . '/seller_grade/' . $filename;
                $fp = @fopen('../' . $path, 'a');
                @fwrite($fp, $return_content);
                $file_url = $path;
            }
         }
        /* 如果 file_url 跟以前不一样，且原来的文件是本地文件，删除原来的文件 */
        $sql = "SELECT grade_img FROM " . $ecs->table('seller_grade') . " WHERE id = '$_POST[id]'";
        $old_url = $db->getOne($sql);
        if ($old_url != '' && $old_url != $file_url && strpos($old_url, 'http: ') === false && strpos($old_url, 'https: ') === false) {
            @unlink(ROOT_PATH . $old_url);
        }
        
        get_oss_del_file(array($file_url));

        if ($exc->edit("favorable_rate = '$favorable_rate', white_bar = '$white_bar', " .
                        "give_integral = '$give_integral', rank_integral = '$rank_integral', pay_integral = '$pay_integral', " .
                        "grade_name='$grade_name', is_default='$is_default', goods_sun='$goods_sun', seller_temp='$seller_temp', " .
                        "grade_introduce='$grade_introduce', entry_criteria='$entry_criteria', grade_img='$file_url', is_open='$is_open'", 
            $_POST['id'])) {

            $link[0]['text'] = $_LANG['bank_list'];
            $link[0]['href'] = 'seller_grade.php?act=list&' . list_link_postfix();
            
            $note = sprintf($_LANG['batch_handle_ok'], stripslashes($_POST['title']));

            clear_cache_files();
            sys_msg($_LANG['edit_succeed'], 0, $link);
        } else {
            die($db->error());
        }
    } elseif ($_REQUEST['act'] == 'insert') {
        /* 检查是否重复 */
        $is_only = $exc->is_only('grade_name', $grade_name, 0);
        if (!$is_only) {
            sys_msg(sprintf($_LANG['title_exist'], stripslashes($grade_name)), 1);
        }
        /* 取得文件地址 */
        $file_url = '';
        if ((isset($_FILES['file']['error']) && $_FILES['file']['error'] == 0) || (!isset($_FILES['file']['error']) && isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] != 'none')) {
            // 检查文件格式
            if (!check_file_type($_FILES['file']['tmp_name'], $_FILES['file']['name'], $allow_file_types)) {
                sys_msg($_LANG['invalid_file']);
            }

            // 复制文件
            $res = upload_article_file($_FILES['file']);
            if ($res != false) {
                $file_url = $res;
            }
        }

        if ($file_url == '') {
            if ($_POST['file_url']) {
                $return_content = http_get_data($_POST['file_url']);
                $filename = basename($_POST['file_url']);
                $path = DATA_DIR . '/seller_grade/' . $filename;
                $fp = @fopen($path, 'w');
                @fwrite($fp, $return_content);
                $file_url = $path;
            }
        }
        
        get_oss_del_file(array($file_url));
        
        $sql = "INSERT INTO " . $ecs->table('seller_grade') . "(grade_name, goods_sun, seller_temp, grade_introduce, " .
                "entry_criteria, grade_img, is_open, is_default, favorable_rate, give_integral, rank_integral, pay_integral, white_bar) " .
                "VALUES ('$grade_name', '$goods_sun', '$seller_temp', " .
                "'$grade_introduce', '$entry_criteria','$file_url','$is_open','$is_default','$favorable_rate'," .
                "'$give_integral','$rank_integral','$pay_integral', '$white_bar'" .
                ")";
        if ($db->query($sql) == true) {
            $link[0]['text'] = $_LANG['GO_add'];
            $link[0]['href'] = 'seller_grade.php?act=add';

            $link[1]['text'] = $_LANG['bank_list'];
            $link[1]['href'] = 'seller_grade.php?act=list';

            clear_cache_files(); // 清除相关的缓存文件
            
            sys_msg($_LANG['add_succeed'],0, $link);
         }
    }
}

/*------------------------------------------------------ */
//-- 等级介绍
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_grade_introduce') {


    $id = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("grade_introduce = '$order'", $id)) {
        clear_cache_files();
        make_json_result(stripslashes($order));
    } else {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 等级优惠比例
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_favorable_rate') {
    check_authz_json('seller_grade');
    $id = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("favorable_rate = '$order'", $id)) {
        clear_cache_files();
        make_json_result(stripslashes($order));
    } else {
        make_json_error($db->error());
    }
} 

/*------------------------------------------------------ */
//-- 等级是否开启
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show') 
{
    check_authz_json('seller_grade');
    $id = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));
    if ($exc->edit("is_open = '$order'", $id)) {
        clear_cache_files();
        make_json_result(stripslashes($order));
    } else {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 等级发布商品数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_sun') {
    check_authz_json('seller_grade');
    $id = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("goods_sun = '$order'", $id)) {
        clear_cache_files();
        make_json_result(stripslashes($order));
    } else {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 等级店铺模板数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_seller_temp') {
    check_authz_json('seller_grade');
    $id = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("seller_temp = '$order'", $id)) {
        clear_cache_files();
        make_json_result(stripslashes($order));
    } else {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove') {
    check_authz_json('seller_grade');
    $id = intval($_GET['id']);
    /* 删除原来的文件 */
    $sql = "SELECT grade_img FROM " . $ecs->table('seller_grade') . " WHERE id = '$id'";
    $old_url = $db->getOne($sql);
    if ($old_url != '' && strpos($old_url, 'http://') === false && strpos($old_url, 'https://') === false) {
        @unlink(ROOT_PATH . $old_url);
    }
    $exc->drop($id);
    admin_log(addslashes($name), 'remove', 'business');
    $url = 'seller_grade.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}
/* 上传文件 */

function upload_article_file($upload) {
    if (!make_dir("../" . DATA_DIR . "/seller_grade")) {
        /* 创建目录失败 */
        return false;
    }
    $filename = cls_image::random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
    $path = ROOT_PATH . DATA_DIR . "/seller_grade/" . $filename;

    if (move_upload_file($upload['tmp_name'], $path)) {
        return DATA_DIR . "/seller_grade/" . $filename;
    } else {
        return false;
    }
}

/* 分页 */

function get_pzd_list() {
    $result = get_filter();
    if ($result === false) {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('seller_grade');
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        $filter = page_and_size($filter);
        /* 获活动数据 */
        $sql = "SELECT * FROM" . $GLOBALS['ecs']->table('seller_grade') . "  ORDER BY id ASC LIMIT " . $filter['start'] . "," . $filter['page_size'];
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    } else {
        $sql = $result['sql'];
        $filter = $result['filter'];
    }
    $row = $GLOBALS['db']->getAll($sql);
    foreach ($row as $k => $v) {

        if ($v['entry_criteria']) {
            $entry_criteria = unserialize($v['entry_criteria']);
            $criteria = '';
            foreach ($entry_criteria as $key => $val) {
                $sql = "SELECT criteria_name FROM" . $GLOBALS['ecs']->table('entry_criteria') . " WHERE id = '" . $val . "'";
                $criteria_name = $GLOBALS['db']->getOne($sql);
                if ($criteria_name) {
                    $entry_criteria[$key] = $criteria_name;
                }
            }
            $row[$k]['entry_criteria'] = implode(" , ", $entry_criteria);
        }
    }
    $arr = array('pzd_list' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}
function http_get_data($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();
    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return $return_content;
}