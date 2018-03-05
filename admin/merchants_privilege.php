<?php

/**
 * ECSHOP 管理员信息以及权限管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: merchants_privilege.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'allot';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}
/*------------------------------------------------------ */
//-- 为管理员分配权限
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'allot')
{
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/priv_action.php');

    admin_priv('users_merchants_priv');
    
    /*获取默认等级ID*/
    $sql = "SELECT id FROM".$ecs->table("seller_grade")."WHERE is_default = 1";
    $default_grade_id = $db->getOne($sql);
    
    $grade_id=!empty($_REQUEST['grade_id'])   ? $_REQUEST['grade_id'] : $default_grade_id;
    $smarty->assign("grade_id",$grade_id);
    
    /*获取全部等级*/
    $sql = "SELECT grade_name , id FROM".$ecs->table("seller_grade");
    $seller_grade = $db->getAll($sql);
    $smarty->assign("seller_grade",$seller_grade);
    
    $priv_str = $db->getOne("SELECT action_list FROM " .$ecs->table('merchants_privilege'). " WHERE grade_id='$grade_id'");

    /* 获取权限的分组数据 */
    $sql_query = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
                 " WHERE parent_id = 0";
    $res = $db->query($sql_query);
    while ($rows = $db->FetchRow($res))
    {
        $priv_arr[$rows['action_id']] = $rows;
    }
	
	if($priv_arr){
		$db_create_in = array_keys($priv_arr);
	}else{
		$db_create_in = '';
	}

    /* 按权限组查询底级的权限名称 */
    $sql = "SELECT action_id, parent_id, action_code,relevance FROM " .$ecs->table('admin_action').
           " WHERE parent_id " .db_create_in($db_create_in);
    $result = $db->query($sql);
    while ($priv = $db->FetchRow($result))
    {
        $priv_arr[$priv["parent_id"]]["priv"][$priv["action_code"]] = $priv;
    }
	
	if($priv_arr){
		// 将同一组的权限使用 "," 连接起来，供JS全选 ecmoban模板堂 --zhuo
		foreach ($priv_arr AS $action_id => $action_group)
		{
			if($action_group['priv']){
				$priv = @array_keys($action_group['priv']);
				$priv_arr[$action_id]['priv_list'] = join(',', $priv);
                                if(!empty($action_group['priv'])){
                                    foreach ($action_group['priv'] AS $key => $val)
                                    {
                                            $priv_arr[$action_id]['priv'][$key]['cando'] = (strpos($priv_str, $val['action_code']) !== false || $priv_str == 'all') ? 1 : 0;
                                    }
                                }
				
			}
		}
	}else{
		$priv_arr = array();
	}

    /* 赋值 */
    $smarty->assign('lang',        $_LANG);
    $smarty->assign('ur_here',     $_LANG['allot_priv']);
    $smarty->assign('priv_arr',    $priv_arr);
    $smarty->assign('form_act',    'update_allot');

    /* 显示页面 */
    assign_query_info();
    $smarty->display('merchants_privilege_allot.dwt');
}

/*------------------------------------------------------ */
//-- 更新管理员的权限
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update_allot')
{
    admin_priv('users_merchants_priv');
    
    $initialize_allot = isset($_POST['initialize_allot']) ? intval($_POST['initialize_allot']) : 0;
   $grade_id=!empty($_REQUEST['grade_id'])   ? $_REQUEST['grade_id'] : 0;
   
    if($_POST['action_code']){
        $act_list = implode(",", $_POST['action_code']);
    
        $sql = "SELECT action_list FROM " .$GLOBALS['ecs']->table('merchants_privilege'). " WHERE grade_id = '$grade_id'";
        $action_list = $db->getOne($sql);

        if($action_list){
            $sql = "UPDATE " .$ecs->table('merchants_privilege'). " SET action_list = '$act_list' WHERE grade_id = '$grade_id'";
            $db->query($sql);
        }else{
            $sql = "INSERT INTO " .$ecs->table('merchants_privilege'). " (`action_list`,`grade_id`) VALUES ('$act_list','$grade_id')";
            $db->query($sql);
        }
        
        //初始化所有商家管理权限
        if($initialize_allot == 1){
            $grade_ru=arr_foreach($db->getAll(" SELECT ru_id FROM ".$ecs->table('merchants_grade')." WHERE grade_id = '$grade_id'"));
            //$sql = "UPDATE " .$ecs->table('admin_user'). " SET action_list = '$act_list' WHERE 1 AND ru_id > 0";
            $sql = "UPDATE " .$ecs->table('admin_user'). " SET action_list = '$act_list' WHERE 1 AND ru_id > 0 AND ru_id ".db_create_in($grade_ru);
            $db->query($sql);
        }
    }

    /* 提示信息 */
    $link[] = array('text' => $_LANG['go_back'], 'href'=>'merchants_privilege.php?act=allot&grade_id='.$grade_id);
    sys_msg($_LANG['action_succeed'], 0, $link);

}

?>
