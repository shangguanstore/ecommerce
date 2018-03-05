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
$exc = new exchange($ecs->table('merchants_shop_information'), $db, 'shop_id', 'shoprz_brandName');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 申请流程列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')
{
    /* 检查权限 */
    admin_priv('users_merchants');

    $smarty->assign('ur_here',      $_LANG['02_merchants_users_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['01_merchants_user_add'], 'href'=>'merchants_users_list.php?act=add_shop'));

    $users_list = steps_users_list();

    $smarty->assign('users_list',    $users_list['users_list']);
    $smarty->assign('filter',       $users_list['filter']);
    $smarty->assign('record_count', $users_list['record_count']);
    $smarty->assign('page_count',   $users_list['page_count']);
    $smarty->assign('full_page',    1);
    $smarty->assign('sort_user_id', '<img src="images/sort_desc.gif">');
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    assign_query_info();
    $smarty->display('merchants_users_list.dwt');
}

/*------------------------------------------------------ */
//-- ajax判断商家名称是否重复 by wu
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'check_shop_name')
{
	//已设置的shop_name,店铺名如何处理的
	$shop_name=empty($_REQUEST['shop_name'])? '':$_REQUEST['shop_name'];
	$adminru=empty($_REQUEST['user_id'])? '':$_REQUEST['user_id'];
    $sql=" select * from ".$GLOBALS['ecs']->table('merchants_shop_information')." where rz_shopName='".$shop_name."' limit 1";
	$shop_info=$GLOBALS['db']->getRow($sql);
	$data=array();
	if(!empty($shop_info)&&$shop_info['user_id']!=$adminru)
	{
		$data['error']=1;
	}
	else
	{
		$data['error']=2;
	}
	die(json_encode($data));
}

/*------------------------------------------------------ */
//-- ajax返回申请流程列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $users_list = steps_users_list();

    $smarty->assign('users_list',    $users_list['users_list']);
    $smarty->assign('filter',       $users_list['filter']);
    $smarty->assign('record_count', $users_list['record_count']);
    $smarty->assign('page_count',   $users_list['page_count']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    $sort_flag  = sort_flag($users_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('merchants_users_list.dwt'), '', array('filter' => $users_list['filter'], 'page_count' => $users_list['page_count']));
}

/*------------------------------------------------------ */
//-- 店铺详细信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add_shop' || $_REQUEST['act'] == 'edit_shop')
{
    /* 检查权限 */
    admin_priv('users_merchants');
	 /*删除未绑定品牌 by kong*/
    $db->query("DELETE FROM".$ecs->table("merchants_shop_brand")." WHERE (user_id = 0 or user_id = '')");
    $user_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $shopInfo_list = get_steps_user_shopInfo_list($user_id);
    $smarty->assign('shopInfo_list', $shopInfo_list);
    $smarty->assign('action_link',  array('text' => $_LANG['02_merchants_users_list'], 'href'=>'merchants_users_list.php?act=list'));
    /*获取商家等级  by kong  start*/
    $grade_list=$db->getAll(" SELECT * FROM".$ecs->table("seller_grade"));
    $smarty->assign("grade_list",$grade_list);
    
     /*获取当前商家等级 by kong*/
    $grade=$db->getRow("SELECT a.grade_id ,a.year_num, b.grade_name FROM".$ecs->table('merchants_grade')." as a LEFT JOIN ".$ecs->table('seller_grade')." as b ON a.grade_id = b.id  WHERE ru_id = '$user_id'");
    $smarty->assign("grade",$grade);
    
    $category_info = get_fine_category_info(0, $user_id); // 详细类目
    $smarty->assign('category_info',        $category_info);

    $permanent_list = get_category_permanent_list($user_id);// 一级类目证件
    $smarty->assign('permanent_list',        $permanent_list);	

	$country_list 	= get_regions_steps();
    $province_list 	= get_regions_steps(1,1);
    $city_list     	= get_regions_steps(2,$consignee['province']);
    $district_list 	= get_regions_steps(3,$consignee['city']);
	$sql = " SELECT region_id, region_name FROM ".$ecs->table('region');
	$region = $db->getAll($sql);
	foreach($region as $v){
		$regions[$v['region_id']] = $v['region_name'];
	}

	$smarty->assign('regions',$regions);

    $sql = "select steps_audit, merchants_audit, merchants_message, review_goods from " .$ecs->table('merchants_shop_information'). " where user_id = '$user_id'";
    $merchants = $db->getRow($sql);
    $smarty->assign('merchants',    $merchants);

    $sn = 0;
    $smarty->assign('country_list',    $country_list);
    $smarty->assign('province_list',    $province_list);
    $smarty->assign('city_list',        $city_list);
    $smarty->assign('district_list',    $district_list);
    $smarty->assign('consignee',    $consignee);
    $smarty->assign('sn',    $sn);
    $smarty->assign('user_id',    $user_id);
    
    if($_REQUEST['act'] == 'edit_shop'){
        $seller_shopinfo = get_shop_name($user_id, 2);
        $smarty->assign('seller_shopinfo',    $seller_shopinfo);
        $smarty->assign('form_action',    'update_shop');
    }else{
        
        $sql = "select user_id, user_name from" .$ecs->table('users'). " where 1";
        $user_list = $db->getAll($sql);
        
        $smarty->assign('user_list',    $user_list);      
        $smarty->assign('form_action',    'insert_shop');
    }
    

    assign_query_info();
    $smarty->display('merchants_users_shopInfo.dwt');
}

/*------------------------------------------------------ */
//-- 修改是否显示店铺街
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_street')
{
    check_authz_json('goods_manage');

    $shop_id       = intval($_POST['id']);
    $is_street        = intval($_POST['val']);

    if ($exc->edit("is_street = '$is_street'", $shop_id))
    {
        clear_cache_files();
        make_json_result($is_street);
    }
}

/*------------------------------------------------------ */
//-- 修改是否显示"在线客服" bylu
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_IM')
{
    check_authz_json('goods_manage');

    $shop_id       = intval($_POST['id']);
    $is_IM        = intval($_POST['val']);

    if ($exc->edit("is_IM = '$is_IM'", $shop_id))
    {
        clear_cache_files();
        make_json_result($is_IM);
    }
}

/*------------------------------------------------------ */
//-- 更新申请商家信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_shop' || $_REQUEST['act'] == 'update_shop')
{
    /* 检查权限 */
    admin_priv('users_merchants');

    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0; 
    $merchants_audit = isset($_REQUEST['merchants_audit']) ? intval($_REQUEST['merchants_audit']) : 0; 
    $merchants_allow = isset($_REQUEST['merchants_allow']) ? intval($_REQUEST['merchants_allow']) : 0; 
    $merchants_message = isset($_REQUEST['merchants_message']) ? trim($_REQUEST['merchants_message']) : ''; 
    $review_goods = isset($_REQUEST['review_goods']) ? intval($_REQUEST['review_goods']) : 0; 
    $shopname_audit = isset($_REQUEST['shopname_audit']) ? intval($_REQUEST['shopname_audit']) : 1; //审核使用店铺名称类型
    $old_merchants_audit = isset($_REQUEST['old_merchants_audit']) ? intval($_REQUEST['old_merchants_audit']) : 0; // by kong grade
    
    $default_grade = $db->getOne("SELECT id FROM".$ecs->table('seller_grade')." WHERE is_default = 1");//获取默认等级
    $grade_id = isset($_REQUEST['grade_id'])  ? intval($_REQUEST['grade_id']) : $grade_id;
    $year_num = isset($_REQUEST['year_num'])  ? intval($_REQUEST['year_num']) : 1;
    
    $form = get_admin_steps_title_insert_form($user_id);
    $parent = get_setps_form_insert_date($form['formName']);
    
    /*判断审核状态是否改变 by kong grade*/
    if($old_merchants_audit != $merchants_audit){
        $sql="SELECT COUNT(id) FROM ".$ecs->table('merchants_grade')." WHERE ru_id = $user_id";//判断原来是否存在等级
        $grade = $db->getOne($sql);
        
        if($merchants_audit == 1 ){
            if($grade > 0){
                $db->query("UPDATE".$ecs->table('merchants_grade')." SET grade_id = '$grade_id' ,year_num='$year_num'  WHERE ru_id = '$user_id' ");
            }else{
                $add_time = gmtime();
                $db->query("INSERT INTO ".$ecs->table('merchants_grade')." (`ru_id`,`grade_id`,`add_time`,`year_num`) VALUES ('$user_id','$grade_id','$add_time','$year_num')");
            }
            /*跟新商家权限*/
            $sql = " SELECT action_list FROM ".$ecs->table('merchants_privilege')." WHERE grade_id = '$grade_id' ";
            $action = array(
                    'action_list'=>$db->getOne($sql)
            );
            $db->autoExecute($ecs->table('admin_user'), $action, 'UPDATE', "ru_id = '$user_id'");
        }else{
            if($grade > 0){
                 $db->query("DELETE FROM ".$ecs->table('merchants_grade')." WHERE ru_id = '$user_id'");//审核未通过是删除该商家等级
            }
        }
    }
    
    if($_REQUEST['act'] == 'update_shop'){ //更新数据
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_steps_fields'), $parent, 'UPDATE', "user_id = '$user_id'");
    }else{ //插入数据
        $parent['user_id'] = $user_id;
        $parent['agreement'] = 1;
        
        $sql ="select fid from " .$ecs->table('merchants_steps_fields'). " where user_id = '$user_id'";
        $fid = $db->getOne($sql);
        
        if($fid > 0){
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'merchants_users_list.php?act=add_shop');
            $centent = $_LANG['insert_fail'];
            sys_msg($centent, 0, $link);
            exit;
        }else{
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_steps_fields'), $parent, 'INSERT');	
        }
    } 

    $sql = "select allow_number from " .$GLOBALS['ecs']->table('merchants_shop_information'). " where user_id = '$user_id'";
    $allow_number = $GLOBALS['db']->getOne($sql);

    $info['merchants_audit'] = $merchants_audit;
    $info['review_goods'] = $review_goods;

    if($merchants_allow == 1){
            $info['steps_audit'] = 0;
            $info['allow_number'] = $allow_number + 1;
    }else{
        
            $ec_hopeLoginName = isset($_REQUEST['ec_hopeLoginName']) ? trim($_REQUEST['ec_hopeLoginName']) : '';
            $sql = "select user_id from " .$ecs->table('admin_user'). " where user_name = '$ec_hopeLoginName' AND ru_id <> '$user_id'";
            $adminId = $db->getOne($sql);
            
            if($adminId > 0){
                if($_REQUEST['act'] == 'update_shop'){
                    $link[] = array('text' => $_LANG['go_back'], 'href'=>'merchants_users_list.php?act=edit_shop&id='.$user_id);
                }else{
                    $link[] = array('text' => $_LANG['go_back'], 'href'=>'merchants_users_list.php?act=add_shop');
                }
                
                sys_msg($_LANG['adminId_have'] , 0, $link);
                exit;
            }
        
            $info['steps_audit'] = 1;
    }

    $info['merchants_message'] = $merchants_message;

    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $info, 'UPDATE', "user_id = '$user_id'");	
    
    $seller_shopinfo = array(
        'shopname_audit' => $shopname_audit
    );
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('seller_shopinfo'), $seller_shopinfo, 'UPDATE', "ru_id = '$user_id'");	

    if($merchants_audit == 1){
            $href = 'merchants_users_list.php?act=allot&user_id=' . $user_id;
    }else{
            $href = 'merchants_users_list.php?act=list';
    }

    if($review_goods == 0){
            $goods_date['review_status'] = 3;
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods'), $goods_date, 'UPDATE', "user_id = '$user_id'");	
    }
    
    if($_REQUEST['act'] == 'update_shop'){
        $centent = $_LANG['update_success'];
    }else{
        $centent = $_LANG['insert_success'];
    }
	
	/* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>$href);
    sys_msg($centent, 0, $link);
}
/*------------------------------------------------------ */
//-- 商家分配权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'allot'){
    /* 检查权限 */
    admin_priv('users_merchants');

    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;

    if ($id == 0) {
        $user_id = $user_id;
    } else {
        $user_id = $id;
    }

    /* 恢复商家默认权限 by wu start */
    $smarty->assign('ur_here', $_LANG['allot_priv']);
    $smarty->assign('action_link', array('text' => $_LANG['restore_default_priv'], 'href' => 'merchants_users_list.php?act=restore_default_priv&user_id=' . $user_id));
    /* 恢复商家默认权限 by wu end */

    $sql = "select steps_audit, merchants_audit, hopeLoginName from " . $ecs->table('merchants_shop_information') . " where user_id = '$user_id'";
    $merchants = $db->getRow($sql);

    $sql = "select user_name, email, user_id from " . $ecs->table('users') . " where user_id = '$user_id'";
    $user = $db->getRow($sql);

    if (empty($merchants['hopeLoginName'])) {
        $user_name = $user['user_name'];
    } else {
        $user_name = $merchants['hopeLoginName'];
    }

    //添加管理员 --start
    $ec_salt=rand(1,9999);
    $tmp_pwd=rand(10000000,99999000);
    $pwd = 'myshop' . $user_id . $tmp_pwd ;
    $password = md5(md5($pwd).$ec_salt);

    /* 获取商家等级 by kong grade */
    $sql = "SELECT grade_id FROM" . $ecs->table('merchants_grade') . " WHERE ru_id = '$user_id' LIMIT 1 ";
    $merchants_grade = $db->getRow($sql);
    $grade_id = $merchants_grade['grade_id'] > 0 ? $merchants_grade['grade_id'] : 0;

    //入驻默认初始权限
    $action_list = $db->getOne("SELECT action_list FROM " . $ecs->table('merchants_privilege') . " WHERE  grade_id = '$grade_id'");

    $sql = "SELECT nav_list FROM " . $ecs->table('admin_user') . " WHERE action_list = 'all'";
    $row = $db->getRow($sql);

    $sql = "SELECT action_list FROM " . $ecs->table('admin_user') . " WHERE ru_id = '$user_id'";
    $rows = $db->getRow($sql);
    if(isset($rows['action_list'])){
            $action_list = $rows['action_list'];
    }

    $sql = "select user_id from " .$ecs->table('admin_user'). " where ru_id = '$user_id'";
    $adminId = $db->getOne($sql);

    if($adminId > 0){
            $sql = "update " .$ecs->table('admin_user'). " set user_name = '$user_name', email = '$email', " . 
                    "nav_list = '$row[nav_list]', action_list = '$action_list' where ru_id = '$user_id' AND parent_id =0";//by kong 
    }else{
            $sql = "INSERT INTO ".$ecs->table('admin_user')." (user_name, email, password, ec_salt, nav_list, action_list, ru_id) ".
                    "VALUES ('$user_name', '" .$email. "', '$password', '$ec_salt', '$row[nav_list]', '$action_list', '$user_id')";
    }

    $db->query($sql);
    //添加管理员 --end

    $user_priv = $db->getRow("SELECT user_id, user_name, action_list FROM " .$ecs->table('admin_user'). " WHERE user_name = '$user_name'");
    $admin_id = $user_priv['user_id'];
    $priv_str = $user_priv['action_list'];
    
    if ($id == 0) {
        if ($adminId < 1) {
            /* 取得当前管理员用户名 */
            $current_admin_name = $db->getOne("SELECT user_name FROM " . $ecs->table('admin_user') . " WHERE user_id = '" . $_SESSION['admin_id'] . "'");

            //商家名称
            $shop_name = get_shop_name($user_id, 1);

            $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'contactPhone');
            if ($field['bool']) {

                $sql = " SELECT contactPhone AS mobile FROM " . $ecs->table('merchants_steps_fields') . " WHERE user_id = '$user_id' LIMIT 1";
                $shopinfo = $db->getRow($sql);

                /* 如果需要，发短信 */
                if ($adminru['ru_id'] == 0 && $GLOBALS['_CFG']['sms_seller_signin'] == '1' && $shopinfo['mobile'] != '') { //添加条件 by wu
                    if ($GLOBALS['_CFG']['sms_type'] == 0) {
                        $templates_sms = "名称：" . htmlspecialchars($user_name) . "，密码：" . htmlspecialchars($pwd);

                        if ($shopinfo['mobile'] != '') {
                            include_once(ROOT_PATH . 'includes/cls_sms.php');
                            $sms = new sms();
                            $send_result = $sms->send($shopinfo['mobile'], sprintf($GLOBALS['_LANG']['seller_signin_sms'], $_CFG['shop_name'], $shop_name, $templates_sms));
                        }
                    } elseif ($GLOBALS['_CFG']['sms_type'] == 1) {
                        //阿里大鱼短信接口参数
                        $smsParams = array(
                            'seller_name'   => $shop_name,
                            'login_name'    => $user_name ? htmlspecialchars($user_name) : '',
                            'password'      => $pwd ? htmlspecialchars($pwd) : '',
                            'admin_name' => $current_admin_name ? $current_admin_name : '',
                            'edit_time' => local_date('H:i:s', gmtime()),
                            'mobile_phone' => $shopinfo['mobile'] ? $shopinfo['mobile'] : ''
                        );
                        
                        $result = sms_ali($smsParams, 'sms_seller_signin'); //阿里大鱼短信变量传值，发送时机传值

                        if ($result) {
                            $resp = $GLOBALS['ecs']->ali_yu($result);
                        } else {
                            sys_msg('阿里大鱼短信配置异常', 1);
                        }
                    }
                }
            }

            $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'contactEmail');
            if ($field['bool']) {

                $sql = " SELECT contactEmail AS seller_email FROM " . $ecs->table('merchants_steps_fields') . " WHERE user_id = '$user_id' LIMIT 1";
                $shopinfo = $db->getRow($sql);

                /* 发送邮件 */
                $template = get_mail_template('seller_signin');
                if ($adminru['ru_id'] == 0 && $template['template_content'] != '') {
                    if ($shopinfo['seller_email']) {
                        $smarty->assign('shop_name', $shop_name);
                        $smarty->assign('seller_name', $user_name);
                        $smarty->assign('seller_psw', $pwd);
                        $smarty->assign('site_name', $_CFG['shop_name']);
                        $smarty->assign('send_date', local_date($GLOBALS['_CFG']['time_format'], gmtime()));
                        $content = $smarty->fetch('str:' . $template['template_content']);

                        send_mail($user_name, $shopinfo['seller_email'], $template['template_subject'], $content, $template['is_html']);
                    }
                }
            }
        }
    }

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
                 " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }

    if($priv_arr){
        /* 按权限组查询底级的权限名称 */
        $sql = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
               " WHERE parent_id " .db_create_in(array_keys($priv_arr));
        $result = $db->query($sql);
        while ($priv = $db->FetchRow($result))
        {
            $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
        }

        // 将同一组的权限使用 "," 连接起来，供JS全选
        foreach ($priv_arr AS $action_id => $action_group) {
            if (isset($action_group['priv']) && $action_group['priv']) {
                $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));

                foreach ($action_group['priv'] AS $key => $val) {
                    $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;

                    if ((strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') == 0) {
                        unset($priv_arr[$action_id]['priv'][$key]);
                    }
                }
            }
        }
    }
	
    $smarty->assign('priv_arr', $priv_arr);
    $smarty->assign('form_action', 'update_allot');
    $smarty->assign('admin_id', $admin_id);
    $smarty->assign('user_id', $user_id);

    if (!empty($user_priv['user_name'])) {
        $user_name = $user_priv['user_name'];
    }
    $smarty->assign('user_name', $user_name);

    assign_query_info();
    $smarty->display('merchants_user_allot.dwt');
}

/*------------------------------------------------------ */
//-- 恢复商家默认权限 by wu
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'restore_default_priv'){
	/* 检查权限 */
    admin_priv('users_merchants');
	
	$user_id = !empty($_REQUEST['user_id'])? intval($_REQUEST['user_id']):0;

    if($user_id >0)
	{
		//获取管理员id
		$sql = "select user_id from " .$ecs->table('admin_user'). " where ru_id = '$user_id' AND parent_id =0 ";
		$adminId = $db->getOne($sql);
	
		//获取商家等级
		$sql="SELECT grade_id FROM".$ecs->table('merchants_grade')." WHERE ru_id = '$user_id' LIMIT 1 ";
		$merchants_grade = $db->getRow($sql);
		$grade_id =  $merchants_grade['grade_id'] > 0 ?  $merchants_grade['grade_id'] : 0;	
		
		//入驻默认初始权限
		$action_list = $db->getOne("SELECT action_list FROM " .$ecs->table('merchants_privilege'). " WHERE grade_id = '$grade_id'");

		//更新权限
        $sql = " update " .$ecs->table('admin_user'). " set action_list = '$action_list' where user_id = '$adminId' ";
		$db->query($sql);
		$update_success = $_LANG['update_success'];
    }
	else
	{
		$update_success = $_LANG['update_fail'];
	}
    $href = "merchants_users_list.php?act=list";
    $link[] = array('text' => $_LANG['go_back'], 'href' => $href);
    sys_msg($update_success, 0, $link);
}

/*------------------------------------------------------ */
//-- 修改商家密码和权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_allot')
{
    /* 检查权限 */
    admin_priv('users_merchants');

    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $login_name = isset($_REQUEST['login_name']) ? trim($_REQUEST['login_name']) : '';
    $ec_salt = rand(1, 9999);

    $login_password = !empty($_REQUEST['login_password']) ? trim($_REQUEST['login_password']) : ''; //默认密码

    if (!empty($login_password)) {
        $seller_psw = $login_password;
        $login_password = ", password = '" . md5(md5($login_password) . $ec_salt) . "'";
        $ec_salt = ", ec_salt = '$ec_salt'";
    } else {
        $login_password = "";
        $ec_salt = "";
    }
    
    if (!empty($login_name)) {
        $sql = "SELECT COUNT(*) FROM ". $ecs->table('admin_user') ." WHERE user_name = '$login_name' AND ru_id <> '$user_id'";
        if($db->getOne($sql) == 0)
        {
            $sql = "update " . $ecs->table('merchants_shop_information') . " set hopeLoginName = '$login_name' where user_id = '$user_id'";
            $db->query($sql);
            
            $seller_name = $login_name;
            $login_name = " ,user_name = '$login_name'";
        }else{
            exit;
            sys_msg("登录名称已存在！");
        }
    } else {
        sys_msg("登录名称不为空！");
    }
    
    /* 更新管理员的权限 */
    $act_list = @join(",", $_POST['action_code']);
    $sql = "UPDATE " . $ecs->table('admin_user') . " SET action_list = '$act_list', role_id = '' " . $login_name . $login_password . $ec_salt .
            " WHERE ru_id = '$user_id' AND parent_id = 0";

    $db->query($sql);
    
    /* 取得当前管理员用户名 */
    $current_admin_name = $db->getOne("SELECT user_name FROM " . $ecs->table('admin_user') . " WHERE user_id = '" .$_SESSION['admin_id']. "'");

    //商家名称
    $shop_name = get_shop_name($user_id, 1);
    
    $sql = " SELECT mobile, seller_email FROM ". $ecs->table('seller_shopinfo') ." WHERE ru_id = '$user_id' LIMIT 1";
    $shopinfo = $db->getRow($sql);
    
    $err_code = true;
    
    if(empty($shopinfo['mobile'])){
        $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'contactPhone');

        if ($field['bool']) {
            $sql = " SELECT contactPhone AS mobile FROM " . $ecs->table('merchants_steps_fields') . " WHERE user_id = '$user_id' LIMIT 1";
            $shopinfo = $db->getRow($sql);
        }
    }
	
    /* 如果需要，发短信 */
    if($adminru['ru_id'] == 0 && $GLOBALS['_CFG']['sms_seller_signin'] == '1' && $shopinfo['mobile'] != '')
    {
        if($GLOBALS['_CFG']['sms_type'] == 0)
        {
            $templates_sms = '';
            if($seller_name && $seller_psw)
            {
                $templates_sms = "名称：". htmlspecialchars($seller_name) ."，密码：". htmlspecialchars($seller_psw);
            }
            else
            {
                $templates_sms = "名称：". htmlspecialchars($seller_name);
            }

            if ($shopinfo['mobile'] != '' && $shop_name != '')
            {
                include_once(ROOT_PATH.'includes/cls_sms.php');
                $sms = new sms();
                $send_result = $sms->send($shopinfo['mobile'], sprintf($GLOBALS['_LANG']['seller_signin_sms'], $shop_name, $_CFG['shop_name'], $templates_sms));
            }
            
            if (isset($send_result) && $send_result) 
            {
                $err_code = true;
            }else{
                $err_code = false;
            }
        }
        elseif($GLOBALS['_CFG']['sms_type'] == 1)
        {
            if($seller_name && $seller_psw)
            {
                //阿里大鱼短信接口参数
                $smsParams = array(
                    'seller_name'   => $shop_name,
                    'login_name'    => $seller_name ? htmlspecialchars($seller_name) : '',
                    'password'      => $seller_psw ? htmlspecialchars($seller_psw) : '',
                    'admin_name' => $current_admin_name ? $current_admin_name : '',
                    'edit_time' => local_date('H:i:s', gmtime()),
                    'mobile_phone' => $shopinfo['mobile'] ? $shopinfo['mobile'] : ''
                );

                $result = sms_ali($smsParams, 'sms_seller_signin'); //阿里大鱼短信变量传值，发送时机传值
                
                if ($result) {
                    $resp = $GLOBALS['ecs']->ali_yu($result);
                    
                    if ($resp->code == 0) {
                        $err_code = true;
                    } else {
                        $err_code = false;
                    }
                } else {
                    sys_msg('阿里大鱼短信配置异常', 1);
                }
            }
        }    
    }
    
    if($seller_name && $seller_psw)
    {
        if($err_code)
        {
            $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'contactEmail');

            if ($field['bool']) {
                if (empty($shopinfo['seller_email'])) {
                    $sql = " SELECT contactEmail AS seller_email FROM " . $ecs->table('merchants_steps_fields') . " WHERE user_id = '$user_id' LIMIT 1";
                    $shopinfo = $db->getRow($sql);
                }
            }
            /* 记录管理员操作 */
            admin_log(addslashes($current_admin_name), 'edit', 'merchants_users_list');

            /* 发送邮件 */
            $template = get_mail_template('seller_signin');
            if($adminru['ru_id'] == 0 && $template['template_content'] != '')
            {
                if(empty($shopinfo['seller_email'])){
                    $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'contactEmail');
                    if($field['bool']){
                        $sql = " SELECT contactEmail AS seller_email FROM ". $ecs->table('merchants_steps_fields') ." WHERE user_id = '$user_id'";
                        $seller_email = $db->getOne($sql);
                        $shopinfo['seller_email'] = $seller_email;
                    }
                }

                if ($shopinfo['seller_email'] && ($seller_name != '' || $seller_psw != ''))
                {
                    $smarty->assign('shop_name', $shop_name);
                    $smarty->assign('seller_name', $seller_name);
                    $smarty->assign('seller_psw', $seller_psw);
                    $smarty->assign('site_name', $_CFG['shop_name']);
                    $smarty->assign('send_date', local_date($GLOBALS['_CFG']['time_format'], gmtime()));
                    $content = $smarty->fetch('str:' . $template['template_content']);

                    send_mail($seller_name, $shopinfo['seller_email'], $template['template_subject'], $content, $template['is_html']);
                }
            }

            $update_success = $_LANG['update_success'];
        }
        else
        {
            $update_success = $_LANG['update_fail']; //by wu
        }
    } 
    else
    {
        $update_success = $_LANG['update_success'];
    }
    
    $href = "merchants_users_list.php?act=list";
    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href' => $href);
    sys_msg($update_success, 0, $link);
}

/*------------------------------------------------------ */
//-- 删除申请商家
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    admin_priv('users_merchants_drop');

    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	
    $sql = "delete from " .$ecs->table('merchants_shop_information'). " where user_id = '$id'";
    $db->query($sql);

    $sql = "delete from " .$ecs->table('merchants_steps_fields'). " where user_id = '$id'";
    $db->query($sql);
    
    if($GLOBALS['_CFG']['delete_seller'] && $id){ 
        get_delete_seller_info('seller_shopbg', "ru_id = '$id'"); //删除店铺背景
        get_delete_seller_info('seller_shopwindow', "ru_id = '$id'"); //删除店铺橱窗
        get_delete_seller_info('seller_shopheader', "ru_id = '$id'"); //删除店铺头部
        get_delete_seller_info('seller_shopslide', "ru_id = '$id'"); //删除店铺轮播图
        get_delete_seller_info('seller_shopinfo', "ru_id = '$id'"); //删除店铺基本信息
        get_delete_seller_info('seller_domain', "ru_id = '$id'"); //删除店铺二级域名
        get_delete_seller_info('admin_user', "ru_id = '$id'"); //删除商家管理员身份
        
        get_seller_delete_order_list($id); //删除商家订单
        get_seller_delete_goods_list($id); //删除商家商品
        get_delete_seller_info('merchants_category', "user_id = '$id'"); //删除商家分类信息
    }

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'merchants_users_list.php?act=list');
    sys_msg('删除成功', 0, $link);
}

/*------------------------------------------------------ */
//-- 查找二级类目
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'addChildCate'){ 

	check_authz_json('users_merchants');

	include_once(ROOT_PATH . 'includes/cls_json.php');
	$json   = new JSON;
    $filter = $json->decode($_GET['JSON']);

	if($type == 1){ //取消二级类目
		$catarr = $filter->cat_id;
	}
	
	$cate_list = get_first_cate_list($filter->cat_id, $filter->type, $catarr, $filter->cat_id);
	$smarty->assign('cate_list',        $cate_list);	
	$smarty->assign('cat_id',        $filter->cat_id);	
	
	make_json_result($smarty->fetch('merchants_cate_list.dwt'));
}

/*------------------------------------------------------ */
//-- 添加二级类目
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'addChildCate_checked'){ 

	check_authz_json('users_merchants');

	include_once(ROOT_PATH . 'includes/cls_json.php');

	$result = array('error' => 0, 'message' => '', 'content' => '', 'cat_id' => '');
	$json  = new JSON;
	
	$_POST['cat_id']=strip_tags(urldecode($_POST['cat_id']));
	$_POST['cat_id'] = json_str_iconv($_POST['cat_id']);
	$cat = $json->decode($_POST['cat_id']);
	$child_category = get_child_category($cat->cat_id);
	$category_info = get_fine_category_info($child_category['cat_id'], $cat->user_id);
	$smarty->assign('category_info',        $category_info);
	make_json_result($smarty->fetch("merchants_cate_checked_list.dwt"));	

	$permanent_list = get_category_permanent_list($cat->user_id);
	$smarty->assign('permanent_list',        $permanent_list);	
	make_json_result($smarty->fetch("merchants_steps_catePermanent.dwt"));
	
}

/*------------------------------------------------------ */
//-- 删除二级类目
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'deleteChildCate_checked'){

	check_authz_json('users_merchants');

	include_once(ROOT_PATH . 'includes/cls_json.php');
	
	$ct_id = isset($_REQUEST['ct_id'])  ? intval($_REQUEST['ct_id']) : '';
	$user_id = isset($_REQUEST['user_id'])  ? intval($_REQUEST['user_id']) : 0;
	
	$result = array('error' => 0, 'message' => '', 'content' => '', 'cat_id' => '');
	$json  = new JSON;
	

	$catParent = get_temporarydate_ctId_catParent($ct_id);
	if($catParent['num'] == 1){
		$sql = "delete from " .$ecs->table('merchants_dt_file'). " where cat_id = '" .$catParent['parent_id']. "'";
		$db->query($sql);
	}
	
	$sql = "delete from " .$ecs->table('merchants_category_temporarydate'). " where ct_id = '$ct_id'";
	$db->query($sql);
	
	$category_info = get_fine_category_info(0, $user_id);
	$smarty->assign('category_info',        $category_info);	
	make_json_result($smarty->fetch("merchants_cate_checked_list.dwt"));
	
	$permanent_list = get_category_permanent_list($user_id);
	$smarty->assign('permanent_list',        $permanent_list);	
	make_json_result($smarty->fetch("merchants_steps_catePermanent.dwt"));

}

/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'deleteBrand')
{
    check_authz_json('users_merchants');

    include_once(ROOT_PATH . 'includes/cls_json.php');

    $json   = new JSON;
    $filter = $json->decode($_GET['JSON']);
	
	$sql = "delete from " .$ecs->table('merchants_shop_brand'). " where bid = '" .$filter->ct_id. "'";
	$db->query($sql);
	
	$brand_list = get_septs_shop_brand_list($filter->user_id); //品牌列表
	$smarty->assign('brand_list', $brand_list);
	
    make_json_result($smarty->fetch('merchants_steps_brank_list.htm'));
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'brand_edit'){
	
    $b_fid = isset($_REQUEST['del_bFid']) ? intval($_REQUEST['del_bFid']) : 0; 
    if($b_fid > 0){
            $sql = "delete from " .$ecs->table('merchants_shop_brandfile'). " where b_fid = '$b_fid'";
            $db->query($sql);
    }

    $ec_shop_bid = isset($_REQUEST['ec_shop_bid']) ? intval($_REQUEST['ec_shop_bid']) : 0;
    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $brandView = isset($_REQUEST['brandView']) ? $_REQUEST['brandView'] : '';

    $shopInfo_list = get_steps_user_shopInfo_list($user_id,$ec_shop_bid);	
    $smarty->assign('shopInfo_list', $shopInfo_list);

    $category_info = get_fine_category_info(0, $user_id); // 详细类目
    $smarty->assign('category_info',        $category_info);

    $permanent_list = get_category_permanent_list($user_id);// 一级类目证件
    $smarty->assign('permanent_list',        $permanent_list);	

    $country_list 	= get_regions_steps();
    $province_list 	= get_regions_steps(1,$consignee['country']);
    $city_list     	= get_regions_steps(2,$consignee['province']);
    $district_list 	= get_regions_steps(3,$consignee['city']);

    $sn = 0;
    $smarty->assign('country_list',    $country_list);
    $smarty->assign('province_list',    $province_list);
    $smarty->assign('city_list',        $city_list);
    $smarty->assign('district_list',    $district_list);
    $smarty->assign('consignee',    $consignee);
    $smarty->assign('sn',    $sn);
    $smarty->assign('user_id',    $user_id);
    $smarty->assign('brandView',    $brandView);
    $smarty->assign('ec_shop_bid',    $ec_shop_bid);
    $smarty->assign('form_action',    'update_shop');

    assign_query_info();
    $smarty->display('merchants_users_shopInfo.htm');
}
elseif($_REQUEST['act'] == 'addBrand'){
	include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '');
    $b_fid = isset($_REQUEST['del_bFid']) ? intval($_REQUEST['del_bFid']) : 0; 
    if($b_fid > 0){
            $sql = "delete from " .$ecs->table('merchants_shop_brandfile'). " where b_fid = '$b_fid'";
            $db->query($sql);
    }
    
    $ec_shop_bid = isset($_REQUEST['ec_shop_bid']) ? intval($_REQUEST['ec_shop_bid']) : 0;
    $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $brandView = isset($_REQUEST['brandView']) ? $_REQUEST['brandView'] : '';
    
    $shopInfo_list = get_steps_user_shopInfo_list($user_id,$ec_shop_bid);

    foreach($shopInfo_list as $k=>$v){
		
        foreach($v['steps_title'] as $key=>$val){
			
            if($val['steps_style'] == 3 && $val['fields_titles'] == '新品牌信息'){
                $title=$val;
            }
        }
    }

    $smarty->assign("title",$title);
    $smarty->assign('shopInfo_list', $shopInfo_list);
    $category_info = get_fine_category_info(0, $user_id); // 详细类目
    $smarty->assign('category_info',        $category_info);
    
    $permanent_list = get_category_permanent_list($user_id);// 一级类目证件
    $smarty->assign('permanent_list',        $permanent_list);
    
    $country_list 	= get_regions_steps();
    $province_list 	= get_regions_steps(1,$consignee['country']);
    $city_list     	= get_regions_steps(2,$consignee['province']);
    $district_list 	= get_regions_steps(3,$consignee['city']);

    $sn = 0;
    $smarty->assign('country_list',    $country_list);
    $smarty->assign('province_list',    $province_list);
    $smarty->assign('city_list',        $city_list);
    $smarty->assign('district_list',    $district_list);
    $smarty->assign('consignee',    $consignee);
    $smarty->assign('sn',    $sn);
    $smarty->assign('user_id',    $user_id);
    $smarty->assign('brandView',    $brandView);
    $smarty->assign('ec_shop_bid',    $ec_shop_bid);
    $smarty->assign('form_action',    'update_shop');
    $result['content'] = $GLOBALS['smarty']->fetch('merchants_bank_dialog.dwt');
    die($json->encode($result));
}
/*------------------------------------------------------ */
//-- 查询会员名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_user_name')
{
    check_authz_json('goods_manage');

    $user_name = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);

    /* 获取会员列表信息 */
    $sql = "select user_id, user_name FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_name like '%" .$user_name. "%'";
    $user_list = $GLOBALS['db']->getAll($sql);

    $res = get_search_user_list($user_list);
    
    clear_cache_files();
    make_json_result($res);
}
//添加品牌  by kong
elseif($_REQUEST['act'] == 'addImg'){
	include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '','error'=>0, 'massege' => '');
    $user_id=!empty($_REQUEST['user_id']) ? $_REQUEST['user_id']:0;
    $steps_title=get_admin_merchants_steps_title($user_id,'addImg');
    if(!empty($steps_title)){
         $result['error']='2';
         if($user_id > 0){
             $title['brand_list']=$db->getAll(" SELECT * FROM".$ecs->table('merchants_shop_brand')." WHERE  user_id = '".$_REQUEST['user_id']."'");
         }else{
             $title['brand_list']=$db->getAll(" SELECT * FROM".$ecs->table('merchants_shop_brand')." WHERE  user_id = 0 ");
         }
         if(!empty($title['brand_list'])){
             foreach($title['brand_list'] as $k=>$v){
                $brand_id.=$v['bid'].",";	
            }
         }
         $brand_id= substr($brand_id,0,strlen($brand_id)-1);
         $smarty->assign("brand_id",$brand_id);
         $smarty->assign("title",$title);
         $result['content'] = $GLOBALS['smarty']->fetch('merchants_steps_brankType.htm');
    }else{
         $result['error']='1';
         $result['massege']='添加失败';
    }
    die($json->encode($result));
    
}

/*------------------------------------------------------ */
//-- 修改商品排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('users_merchants');

    $shop_id       = intval($_POST['id']);
    $sort_order     = intval($_POST['val']);

    if ($exc->edit("sort_order = '$sort_order'", $shop_id))
    {
        clear_cache_files();
        make_json_result($sort_order);
    }
}

/*------------------------------------------------------ */
//-- 商家评分
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'create_seller_grade')
{
	/*权限检查*/
	admin_priv('users_merchants');
	$smarty->assign('ur_here', $_LANG['create_seller_grade']);
	$seller_grade_list = seller_grade_list();
	$record_count = count($seller_grade_list);
	$smarty->assign('record_count', $record_count);
	$smarty->assign('page', 1);

	/*显示模版*/
	assign_query_info();
	$smarty->display('merchants_grade.dwt');
}
elseif ($_REQUEST['act'] == 'ajax_seller_grade')
{
	include_once (ROOT_PATH . 'includes/cls_json.php');
	$json = new JSON;
	$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 1;
	$seller_grade_list = seller_grade_list();
	$grade_list = $ecs->page_array($page_size, $page, $seller_grade_list);
	$arr = array();

	foreach ($grade_list['list'] as $key => $row )
	{
		@unlink(ROOT_PATH . DATA_DIR . '/sc_file/seller_comment_' . $row['user_id'] . '.php');
		$seller_list[$key]['user_id'] = $row['user_id'];
		$seller_list[$key]['shop_name'] = get_shop_name($row['user_id'], 1);
		$seller_list[$key]['seller_comment'] = get_merchants_goods_comment($row['user_id']);
		$mc_all = isset($seller_list[$key]['seller_comment']['commentRank']['mc_all']) ? $seller_list[$key]['seller_comment']['commentRank']['mc_all'] : 0;
		$desc = isset($seller_list[$key]['seller_comment']['cmt']['commentRank']['zconments']['score']) ? $seller_list[$key]['seller_comment']['cmt']['commentRank']['zconments']['score'] : 0;
		$service = isset($seller_list[$key]['seller_comment']['cmt']['commentServer']['zconments']['score']) ? $seller_list[$key]['seller_comment']['cmt']['commentServer']['zconments']['score'] : 0;
		$delivery = isset($seller_list[$key]['seller_comment']['cmt']['commentDelivery']['zconments']['score']) ? $seller_list[$key]['seller_comment']['cmt']['commentDelivery']['zconments']['score'] : 0;
		write_static_cache('seller_comment_' . $row['user_id'], $seller_list[$key], '/data/sc_file/');
		$arr = array('user_id' => $row['user_id'], 
					'shop_name' => $seller_list[$key]['shop_name'], 
					'desc' => $desc, 
					'service' => $service, 
					'delivery' => $delivery, 
					'mc_all' => $mc_all
				);
	}

	$result['list'] = $arr;
	$result['page'] = $grade_list['filter']['page'] + 1;
	$result['page_size'] = $grade_list['filter']['page_size'];
	$result['record_count'] = $grade_list['filter']['record_count'];
	$result['page_count'] = $grade_list['filter']['page_count'];
	$result['is_stop'] = 1;

	if ($grade_list['filter']['page_count'] < $page)
	{
		$result['is_stop'] = 0;
	}
	else
	{
		$result['filter_page'] = $grade_list['filter']['page'];
	}

	die($json->encode($result));
}

/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

/**
 *  返回申请流程列表数据
 *
 * @access  public
 * @param
 *
 * @return void
 */
function steps_users_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = !isset($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }

        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'mis.shop_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);

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
                    $store_search_where = "AND mis.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }

                if($filter['store_search'] == 1){
                    $ex_where .= " AND mis.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND mis.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND mis.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }

                if($filter['store_search'] > 1){
                    $ex_where .= " AND mis.user_id > 0 $store_where ";
                }
           }
        }
        //管理员查询的权限 -- 店铺查询 end
        
        $ex_where .= (!empty($filter['user_name'])) ? " AND (u.user_name LIKE '%" . mysqli_like_quote($filter['user_name']) . "%')" : '';

        $filter['record_count'] = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .  " as mis " . 
                                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " as u on mis.user_id = u.user_id " .
                                $ex_where);

        /* 分页大小 */
        $filter = page_and_size($filter);
        $sql = "SELECT mis.* ".
                " FROM " . $GLOBALS['ecs']->table('merchants_shop_information')  ." AS mis ". 
                " LEFT JOIN " .$GLOBALS['ecs']->table('users'). " as u on mis.user_id = u.user_id " .
                $ex_where .
                " ORDER BY " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
                " LIMIT " . $filter['start'] . ',' . $filter['page_size'];		

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $users_list = $GLOBALS['db']->getAll($sql);

    $count = count($users_list);
    for ($i=0; $i<$count; $i++)
    {
        $users_list[$i]['shop_id'] = $users_list[$i]['shop_id'];
        $users_list[$i]['user_name'] = $GLOBALS['db']->getOne("select user_name from " .$GLOBALS['ecs']->table('users'). " where user_id = '" .$users_list[$i]['user_id']. "'");
        $users_list[$i]['cat_name'] = $GLOBALS['db']->getOne("select cat_name from " .$GLOBALS['ecs']->table('category'). " where cat_id = '" .$users_list[$i]['shop_categoryMain']. "'");
        $users_list[$i]['rz_shopName'] = get_shop_name($users_list[$i]['user_id'], 1); 
        $grade = $GLOBALS['db']->getRow("select a.grade_img,a.grade_name from " .$GLOBALS['ecs']->table('seller_grade'). " AS a LEFT JOIN ".$GLOBALS['ecs']->table('merchants_grade')." AS b ON a.id = b.grade_id  where b.ru_id = '" .$users_list[$i]['user_id']. "'");//by kong  获取商家等级
        $users_list[$i]['grade_img'] = $grade['grade_img'];
        $users_list[$i]['grade_name'] = $grade['grade_name'];
    }

    $arr = array('users_list' => $users_list, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

//获取会员申请入驻信息 start -- zhuo
function get_steps_user_shopInfo_list($user_id = 0, $ec_shop_bid = 0){
	$sql = "select * from " .$GLOBALS['ecs']->table('merchants_steps_process'). " where 1 and process_steps <> 1 AND is_show = 1 AND id <> 10 order by process_steps asc";
	$res = $GLOBALS['db']->getAll($sql);
	
	$arr = array();
	foreach($res as $key=>$row){
		$arr[$key]['sp_id'] = $row['id']; 
		$arr[$key]['process_title'] = $row['process_title']; 
		$arr[$key]['steps_title'] = get_user_steps_title($row['id'],$user_id, $ec_shop_bid);
	}
	
	return $arr;
}

function get_user_steps_title($id = 0, $user_id, $ec_shop_bid) {

    include_once(ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);

    $sql = "select tid, fields_titles, steps_style, titles_annotation from " . $GLOBALS['ecs']->table('merchants_steps_title') . " where fields_steps = '$id'";
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res as $key => $row) {
        $arr[$key]['tid'] = $row['tid'];
        $arr[$key]['fields_titles'] = $row['fields_titles'];
        $arr[$key]['steps_style'] = $row['steps_style'];
        $arr[$key]['titles_annotation'] = $row['titles_annotation'];

        $sql = "select * from " . $GLOBALS['ecs']->table('merchants_steps_fields_centent') . " where tid = '" . $row['tid'] . "'";
        $centent = $GLOBALS['db']->getRow($sql);
        $cententFields = get_fields_centent_info($centent['id'], $centent['textFields'], $centent['fieldsDateType'], $centent['fieldsLength'], $centent['fieldsNotnull'], $centent['fieldsFormName'], $centent['fieldsCoding'], $centent['fieldsForm'], $centent['fields_sort'], $centent['will_choose'], 'root', $user_id);
        $arr[$key]['cententFields'] = get_array_sort($cententFields, 'fields_sort');

        $shop_info = get_merchants_septs_custom_userInfo('merchants_shop_information', $user_id); //店铺类型、 可经营类目---信息表
        $brand_info = get_merchants_septs_custom_userInfo('merchants_shop_brand', $user_id, 'pingpai', $ec_shop_bid); //品牌表

        if ($row['steps_style'] == 1) {
            $parent = array(//店铺类型数据插入
                'shoprz_type' => $shop_info['shoprz_type'],
                'subShoprz_type' => $shop_info['subShoprz_type'],
                'shop_expireDateStart' => local_date("Y-m-d H:i", $shop_info['shop_expireDateStart']),
                'shop_expireDateEnd' => local_date("Y-m-d H:i", $shop_info['shop_expireDateEnd']),
                'shop_permanent' => $shop_info['shop_permanent'],
                'authorizeFile' => $shop_info['authorizeFile'],
                'shop_hypermarketFile' => $shop_info['shop_hypermarketFile'],
                'shop_categoryMain' => $shop_info['shop_categoryMain']
            );
        } elseif ($row['steps_style'] == 2) { //一级类目列表
            $arr[$key]['first_cate'] = get_first_cate_list('', '', '', $user_id);

            $parent = array(
                'shop_categoryMain' => $shop_info['shop_categoryMain']
            );
        } elseif ($row['steps_style'] == 3) { //品牌列表
            $arr[$key]['brand_list'] = get_septs_shop_brand_list($user_id); //品牌列表

            $brandfile_list = get_shop_brandfile_list($ec_shop_bid);
            $arr[$key]['brandfile_list'] = $brandfile_list;

            if (!empty($brand_info['brandEndTime'])) {
                $brand_info['brandEndTime'] = local_date("Y-m-d H:i", $brand_info['brandEndTime']);
            } else {
                $brand_info['brandEndTime'] = '';
            }

            $parent = array(
                'bank_name_letter' => $brand_info['bank_name_letter'],
                'brandName' => $brand_info['brandName'],
                'brandFirstChar' => $brand_info['brandFirstChar'],
                'brandLogo' => $brand_info['brandLogo'],
                'brandType' => $brand_info['brandType'],
                'brand_operateType' => $brand_info['brand_operateType'],
                'brandEndTime' => $brand_info['brandEndTime'],
                'brandEndTime_permanent' => $brand_info['brandEndTime_permanent']
            );
        } elseif ($row['steps_style'] == 4) {
            $sql = "select bid, brandName from " . $GLOBALS['ecs']->table('merchants_shop_brand') . " where user_id = '" . $user_id . "'";
            $brand_list = $GLOBALS['db']->getAll($sql);
            $arr[$key]['brand_list'] = $brand_list;

            $parent = array(
                'shoprz_brandName' => $shop_info['shoprz_brandName'],
                'shop_class_keyWords' => $shop_info['shop_class_keyWords'],
                'shopNameSuffix' => $shop_info['shopNameSuffix'],
                'rz_shopName' => $shop_info['rz_shopName'],
                'hopeLoginName' => $shop_info['hopeLoginName']
            );
			
			switch ($shop_info['shoprz_type']){
				case 1:$shop_info['shoprz_type'] = '旗舰店';
				  break;  
				case 2:$shop_info['shoprz_type'] = '专卖店';
				  break;
				case 3:$shop_info['shoprz_type'] = '专营店';
				  break;  
				case 4:$shop_info['shoprz_type'] = '管';
				  break;
				default:$shop_info['shoprz_type'];
				}

            $parent['shoprz_type'] = $shop_info['shoprz_type'];
        }

        $arr[$key]['parentType'] = $parent; //自定义显示
    }

    //get_print_r($arr);
    return $arr;
}

function get_merchants_septs_custom_userInfo($table = '', $user_id = 0, $type = '', $id = ''){
	
	if($type == 'pingpai'){
		$id = " and bid = '$id'";
	}
	
	$sql = "select * from " .$GLOBALS['ecs']->table($table). " where user_id = '" .$user_id. "'" . $id;
	return $GLOBALS['db']->getRow($sql);		
}
//获取会员申请入驻信息 end -- zhuo

//更新申请商家信息 start
function get_admin_merchants_steps_title($user_id = 0, $addImg = '') {

    include_once(ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']);

    $sql = "SELECT mst.tid, mst.fields_titles, mst.titles_annotation, mst.steps_style, mst.fields_special, mst.special_type FROM " . $GLOBALS['ecs']->table('merchants_steps_process') . " AS msp, " .
            $GLOBALS['ecs']->table('merchants_steps_title') . " AS mst WHERE 1 AND msp.is_show = 1 GROUP BY mst.tid ";
    $res = $GLOBALS['db']->getAll($sql);

    $ec_shop_bid = isset($_REQUEST['ec_shop_bid']) ? trim($_REQUEST['ec_shop_bid']) : 0;
    $ec_shoprz_type = isset($_POST['ec_shoprz_type']) ? intval($_POST['ec_shoprz_type']) : 0;
    $ec_subShoprz_type = isset($_POST['ec_subShoprz_type']) ? intval($_POST['ec_subShoprz_type']) : 0;
    $ec_shop_expireDateStart = isset($_POST['ec_shop_expireDateStart']) ? trim($_POST['ec_shop_expireDateStart']) : '';
    $ec_shop_expireDateEnd = isset($_POST['ec_shop_expireDateEnd']) ? trim($_POST['ec_shop_expireDateEnd']) : '';
    $ec_shop_permanent = isset($_POST['ec_shop_permanent']) ? intval($_POST['ec_shop_permanent']) : 0;
    $ec_shop_categoryMain = isset($_POST['ec_shop_categoryMain']) ? intval($_POST['ec_shop_categoryMain']) : 0;

    //品牌基本信息
    $bank_name_letter = isset($_POST['ec_bank_name_letter']) ? trim($_POST['ec_bank_name_letter']) : '';
    $brandName = isset($_POST['ec_brandName']) ? trim($_POST['ec_brandName']) : '';
    $brandFirstChar = isset($_POST['ec_brandFirstChar']) ? trim($_POST['ec_brandFirstChar']) : '';
    $brandLogo = isset($_FILES['ec_brandLogo']) ? $_FILES['ec_brandLogo'] : '';
    $brandLogo = $image->upload_image($brandLogo, 'septs_Image');  //图片存放地址 -- data/septs_Image
    $brandType = isset($_POST['ec_brandType']) ? intval($_POST['ec_brandType']) : 0;
    $brand_operateType = isset($_POST['ec_brand_operateType']) ? intval($_POST['ec_brand_operateType']) : 0;
    $brandEndTime = isset($_POST['ec_brandEndTime']) ? intval($_POST['ec_brandEndTime']) : '';
    $brandEndTime_permanent = isset($_POST['ec_brandEndTime_permanent']) ? intval($_POST['ec_brandEndTime_permanent']) : 0;

    //品牌资质证件
    $qualificationNameInput = isset($_POST['ec_qualificationNameInput']) ? $_POST['ec_qualificationNameInput'] : array();
    $qualificationImg = isset($_FILES['ec_qualificationImg']) ? $_FILES['ec_qualificationImg'] : array();
    $expiredDateInput = isset($_POST['ec_expiredDateInput']) ? $_POST['ec_expiredDateInput'] : array();
    $b_fid = isset($_POST['b_fid']) ? $_POST['b_fid'] : array();

    //店铺命名信息
    $ec_shoprz_brandName = isset($_POST['ec_shoprz_brandName']) ? $_POST['ec_shoprz_brandName'] : '';
    $ec_shop_class_keyWords = isset($_POST['ec_shop_class_keyWords']) ? $_POST['ec_shop_class_keyWords'] : '';
    $ec_shopNameSuffix = isset($_POST['ec_shopNameSuffix']) ? $_POST['ec_shopNameSuffix'] : '';
    $ec_rz_shopName = isset($_POST['ec_rz_shopName']) ? $_POST['ec_rz_shopName'] : '';
    $ec_hopeLoginName = isset($_POST['ec_hopeLoginName']) ? $_POST['ec_hopeLoginName'] : '';

    $arr = array();
    foreach ($res as $key => $row) {

        $sql = "select shop_id from " . $GLOBALS['ecs']->table('merchants_shop_information') . " where user_id = '" . $user_id . "'";
        $shop_id = $GLOBALS['db']->getOne($sql);

        $arr[$key]['tid'] = $row['tid'];

        //自定义表单数据插入 start

        $sql = "select * from " . $GLOBALS['ecs']->table('merchants_steps_fields_centent') . " where tid = '" . $row['tid'] . "'";
        $centent = $GLOBALS['db']->getRow($sql);
        $cententFields = get_fields_centent_info($centent['id'], $centent['textFields'], $centent['fieldsDateType'], $centent['fieldsLength'], $centent['fieldsNotnull'], $centent['fieldsFormName'], $centent['fieldsCoding'], $centent['fieldsForm'], $centent['fields_sort'], $centent['will_choose'], 'root', $user_id);
        $arr[$key]['cententFields'] = get_array_sort($cententFields, 'fields_sort');

        $shop_info = get_merchants_septs_custom_userInfo('merchants_shop_information'); //店铺类型、 可经营类目---信息表
        $brand_info = get_merchants_septs_custom_userInfo('merchants_shop_brand', $user_id, 'pingpai', $ec_shop_bid); //品牌表

        if ($row['steps_style'] == 1) {

            $ec_authorizeFile = $image->upload_image($_FILES['ec_authorizeFile'], 'septs_Image');  //图片存放地址 -- data/septs_Image
            $ec_authorizeFile = empty($ec_authorizeFile) ? $shop_info['authorizeFile'] : $ec_authorizeFile;
            $ec_shop_hypermarketFile = $image->upload_image($_FILES['ec_shop_hypermarketFile'], 'septs_Image');  //图片存放地址 -- data/septs_Image
            $ec_shop_hypermarketFile = empty($ec_shop_hypermarketFile) ? $shop_info['shop_hypermarketFile'] : $ec_shop_hypermarketFile;

            if ($ec_shop_permanent != 1) {
                $ec_shop_expireDateStart = empty($ec_shop_expireDateStart) ? local_date("Y-m-d H:i", $shop_info['shop_expireDateStart']) : $ec_shop_expireDateStart;
                $ec_shop_expireDateEnd = empty($ec_shop_expireDateEnd) ? local_date("Y-m-d H:i", $shop_info['shop_expireDateEnd']) : $ec_shop_expireDateEnd;

                if (!empty($ec_shop_expireDateStart) || !empty($ec_shop_expireDateEnd)) {
                    $ec_shop_expireDateStart = local_strtotime($ec_shop_expireDateStart);
                    $ec_shop_expireDateEnd = local_strtotime($ec_shop_expireDateEnd);
                }
            } else {
                $ec_shop_expireDateStart = '';
                $ec_shop_expireDateEnd = '';
            }

            //判断数据是否存在，如果存在则引用 start
            if ($ec_shoprz_type == 0) {
                $ec_shoprz_type = $shop_info['shoprz_type'];
            }
            if ($ec_subShoprz_type == 0) {
                $ec_subShoprz_type = $shop_info['subShoprz_type'];
            }
            if ($ec_shop_categoryMain == 0) {
                $ec_shop_categoryMain = $shop_info['shop_categoryMain'];
            }
            //判断数据是否存在，如果存在则引用 end

            $parent = array(//店铺类型数据插入
                'user_id' => $user_id,
                'shoprz_type' => $ec_shoprz_type,
                'subShoprz_type' => $ec_subShoprz_type,
                'shop_expireDateStart' => $ec_shop_expireDateStart,
                'shop_expireDateEnd' => $ec_shop_expireDateEnd,
                'shop_permanent' => $ec_shop_permanent,
                'authorizeFile' => $ec_authorizeFile,
                'shop_hypermarketFile' => $ec_shop_hypermarketFile,
                'shop_categoryMain' => $ec_shop_categoryMain
            );

            if ($user_id > 0) {
                if ($shop_id > 0) {

                    if ($parent['shop_expireDateStart'] == '' || $parent['shop_expireDateEnd'] == '') {
                        if ($ec_shop_permanent != 1) {
                            if ($shop_info['shop_permanent'] == 1) {
                                $parent['shop_permanent'] = $shop_info['shop_permanent'];
                            }
                        }
                    }

                    if (empty($parent['authorizeFile'])) {
                        $parent['shop_permanent'] = 0;
                    } else {
                        if ($parent['shop_expireDateStart'] == '' || $parent['shop_expireDateEnd'] == '') {
                            $parent['shop_permanent'] = 1;
                            $parent['shop_expireDateStart'] = '';
                            $parent['shop_expireDateEnd'] = '';
                        }
                    }

                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'UPDATE', "user_id = '" . $user_id . "'");
                } else {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'INSERT');
                }
            }

            if ($ec_shop_permanent == 0) {
                if ($parent['shop_expireDateStart'] != '') {
                    $parent['shop_expireDateStart'] = local_date("Y-m-d H:i", $shop_info['shop_expireDateStart']);
                }
                if ($parent['shop_expireDateEnd'] != '') {
                    $parent['shop_expireDateEnd'] = local_date("Y-m-d H:i", $shop_info['shop_expireDateEnd']);
                }
            }
        } elseif ($row['steps_style'] == 2) { //一级类目列表
            //2014-11-19 start
            if ($user_id > 0) {
                if ($shop_id < 1) {
                    $parent['user_id'] = $user_id;
                    $parent['shop_categoryMain'] = $ec_shop_categoryMain;
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'INSERT');
                }
            }
            //2014-11-19 end

            $arr[$key]['first_cate'] = get_first_cate_list('', '', '', $user_id);
            $catId_array = get_catId_array($user_id);

            $parent['user_shopMain_category'] = implode('-', $catId_array);

            //2014-11-19 start
            if ($ec_shop_categoryMain == 0) {
                $ec_shop_categoryMain = $shop_info['shop_categoryMain'];
                $parent['shop_categoryMain'] = $ec_shop_categoryMain;
            }
            $parent['shop_categoryMain'] = $ec_shop_categoryMain;
            //2014-11-19 end

            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'UPDATE', "user_id = '$user_id'");
            
            if (!empty($parent['user_shopMain_category'])) {
                get_update_temporarydate_isAdd($catId_array, $user_id);
            }
            
            get_update_temporarydate_isAdd($catId_array, $user_id, 1);

        } elseif ($row['steps_style'] == 3) { //品牌列表
            $arr[$key]['brand_list'] = get_septs_shop_brand_list($user_id); //品牌列表

            if ($ec_shop_bid > 0) { //更新品牌数据
                $bank_name_letter = empty($bank_name_letter) ? $brand_info['bank_name_letter'] : $bank_name_letter;
                $brandName = empty($brandName) ? $brand_info['brandName'] : $brandName;
                $brandFirstChar = empty($brandFirstChar) ? $brand_info['brandFirstChar'] : $brandFirstChar;
                $brandLogo = empty($brandLogo) ? $brand_info['brandLogo'] : $brandLogo;
                $brandType = empty($brandType) ? $brand_info['brandType'] : $brandType;
                $brand_operateType = empty($brand_operateType) ? $brand_info['brand_operateType'] : $brand_operateType;
                $brandEndTime = empty($brandEndTime) ? $brand_info['brandEndTime'] : local_strtotime($brandEndTime);
                $brandEndTime_permanent = empty($brandEndTime_permanent) ? $brand_info['brandEndTime_permanent'] : $brandEndTime_permanent;

                $brandfile_list = get_shop_brandfile_list($ec_shop_bid);
                $arr[$key]['brandfile_list'] = $brandfile_list;

                $parent = array(
                    'user_id' => $user_id,
                    'bank_name_letter' => $bank_name_letter,
                    'brandName' => $brandName,
                    'brandFirstChar' => $brandFirstChar,
                    'brandLogo' => $brandLogo,
                    'brandType' => $brandType,
                    'brand_operateType' => $brand_operateType,
                    'brandEndTime' => $brandEndTime,
                    'brandEndTime_permanent' => $brandEndTime_permanent
                );

                if (!empty($parent['brandEndTime'])) {
                    $arr[$key]['parentType']['brandEndTime'] = local_date("Y-m-d H:i", $parent['brandEndTime']); //输出
                }

                if ($user_id > 0 || $addImg == 'addImg') {

                    if ($parent['brandEndTime_permanent'] == 1) {
                        $parent['brandEndTime'] = '';
                    }

                    $sql = "select bid from " . $GLOBALS['ecs']->table('merchants_shop_brand') . " where brandName = '$brandName' and bid <> '$ec_shop_bid' and user_id = '" . $user_id . "'";
                    $bRes = $GLOBALS['db']->getOne($sql);

                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_brand'), $parent, 'UPDATE', "user_id = '" . $user_id . "' and bid = '$ec_shop_bid'");

                    get_shop_brand_file($qualificationNameInput, $qualificationImg, $expiredDateInput, $b_fid, $ec_shop_bid); //品牌资质文件上传
                }
            } else { //插入品牌数据
                if ($user_id > 0 || $addImg == 'addImg') {//by kong 改
                    if (!empty($bank_name_letter)) {
                        $sql = "select bid from " . $GLOBALS['ecs']->table('merchants_shop_brand') . " where brandName = '$brandName' and user_id = '" . $user_id . "'";
                        $bRes = $GLOBALS['db']->getOne($sql);

                        if (!$bRes) {
                            $parent = array(
                                'user_id' => $user_id,
                                'bank_name_letter' => $bank_name_letter,
                                'brandName' => $brandName,
                                'brandFirstChar' => $brandFirstChar,
                                'brandLogo' => $brandLogo,
                                'brandType' => $brandType,
                                'brand_operateType' => $brand_operateType,
                                'brandEndTime' => $brandEndTime,
                                'brandEndTime_permanent' => $brandEndTime_permanent
                            );

                            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_brand'), $parent, 'INSERT');
                            $bid = $GLOBALS['db']->insert_id();

                            get_shop_brand_file($qualificationNameInput, $qualificationImg, $expiredDateInput, $b_fid, $bid); //品牌资质文件上传
                        }
                    }
                }
            }
        } elseif ($row['steps_style'] == 4) {

            $sql = "select bid, brandName from " . $GLOBALS['ecs']->table('merchants_shop_brand') . " where user_id = '" . $user_id . "'";
            $brand_list = $GLOBALS['db']->getAll($sql);
            $arr[$key]['brand_list'] = $brand_list;

            $ec_shoprz_brandName = empty($ec_shoprz_brandName) ? $shop_info['shoprz_brandName'] : $ec_shoprz_brandName;
            $ec_shop_class_keyWords = empty($ec_shop_class_keyWords) ? $shop_info['shop_class_keyWords'] : $ec_shop_class_keyWords;
            $ec_shopNameSuffix = empty($ec_shopNameSuffix) ? $shop_info['shopNameSuffix'] : $ec_shopNameSuffix;
            $ec_rz_shopName = empty($ec_rz_shopName) ? $shop_info['rz_shopName'] : $ec_rz_shopName;
            $ec_hopeLoginName = empty($ec_hopeLoginName) ? $shop_info['hopeLoginName'] : $ec_hopeLoginName;

            if (!empty($ec_rz_shopName)) {
                $parent = array(
                    'shoprz_brandName' => $ec_shoprz_brandName,
                    'shop_class_keyWords' => $ec_shop_class_keyWords,
                    'shopNameSuffix' => $ec_shopNameSuffix,
                    'rz_shopName' => $ec_rz_shopName,
                    'hopeLoginName' => $ec_hopeLoginName,
                );

                if ($user_id > 0) {
                    if ($shop_id > 0) {
                        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'UPDATE', "user_id = '" . $user_id . "'");
                    } else {
                        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('merchants_shop_information'), $parent, 'INSERT');
                    }
                }
            }
        }
    }

    return $arr;
}

function get_admin_steps_title_insert_form($user_id){

	$steps_title = get_admin_merchants_steps_title($user_id);
	
	for($i=0; $i<count($steps_title); $i++){

		if(is_array($steps_title[$i]['cententFields'])){
			$cententFields = $steps_title[$i]['cententFields'];
			for($j=1; $j<=count($cententFields); $j++){
				$arr['formName'] .= $cententFields[$j]['textFields'] . ',';
			}
		}
	}
	
	$arr['formName'] = substr($arr['formName'], 0, -1);

	return $arr;
}
//更新申请商家信息 end

//获取会员信息列表
function get_search_user_list($user_list){
    
    $html = '';
    if($user_list){
		
        $html .= "<ul>";

        foreach($user_list as $key=>$user){
            $html .= "<li data-name='".$user['user_name']."' data-id='".$user['user_id']."'>".$user['user_name']."</li>";
        }

        $html .= '</ul>';
    }else{
        $html = '<span class="red">查无该会员</span><input name="user_id" value="0" type="hidden" />';
    }
    
    return $html;
}
?>