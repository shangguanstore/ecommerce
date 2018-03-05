<?php

/**
 * ECSHOP 管理中心品牌管理
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
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"goods");
$exc = new exchange($ecs->table("merchants_shop_brand"), $db, 'bid', 'brandName');

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}

$smarty->assign('ru_id',   $adminru['ru_id']);

$smarty->assign('current', basename(PHP_SELF,'.php'));
//ecmoban模板堂 --zhuo end

$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '07_merchants_brand'));
/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['07_merchants_brand']);
	
	
    $smarty->assign('full_page',    1);
    if($adminru['ru_id'] > 0){
        $smarty->assign('action_link',  array('text' => $_LANG['07_brand_add'], 'href' => 'merchants_brand.php?act=add'));
    }

    $brand_list = get_brandlist($adminru['ru_id']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
	
	//分页
	$page_count_arr = seller_page($brand_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);		
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);

    assign_query_info();
    $smarty->display('merchants_brand_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	
    $brand_list = get_brandlist($adminru['ru_id']);
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);
    
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);
	
	//分页
	$page_count_arr = seller_page($brand_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);		

    make_json_result($smarty->fetch('merchants_brand_list.dwt'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('merchants_brand');

    $smarty->assign('ur_here',     $_LANG['07_brand_add']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'merchants_brand.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('merchants_brand_info.dwt');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('merchants_brand');

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    $major_business = isset($_POST['major_business']) ? intval($_POST['major_business']) : 0;
    $linkBrand = isset($_POST['link_brand']) ? intval($_POST['link_brand']) : 0;

    $is_only = $exc->is_only('brandName', $_POST['brand_name'], 0, "user_id = '" .$adminru['ru_id']. "'");

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['brandname_exist'], stripslashes($_POST['brand_name'])), 1);
    }

    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }
    
     /*处理图片*/
    if(!empty($_FILES['brand_logo']['name'])){
        $img_name = "data/septs_Image/" . basename($image->upload_image($_FILES['brand_logo'],'septs_Image'));
    }else{
        $img_name = '';
    }
    
    get_oss_add_file(array($img_name));
    
     /*处理URL*/
    $site_url = sanitize_url( $_POST['site_url'] );

    /*插入数据*/
    $sql = "INSERT INTO ".$ecs->table('merchants_shop_brand')."(user_id, brandName, bank_name_letter, site_url, brand_desc, brandLogo, is_show, sort_order, major_business) ".
           "VALUES (" .$adminru['ru_id']. ", '$_POST[brand_name]', '$_POST[brank_letter]', '$site_url', '$_POST[brand_desc]', '$img_name', '$is_show', '$_POST[sort_order]', 0)";
    $db->query($sql);
    
    if($linkBrand > 0){
        $link_brand = array(
            'bid' => $db->insert_id(),
            'brand_id' => $linkBrand
        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('link_brand'), $link_brand, 'INSERT'); //更新关联品牌
    }
    
    if($major_business > 0){
        $parent['major_brand'] = intval($_POST['id']);    
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('admin_user'), $parent, 'UPDTAE', 'ru_id=' . $adminru['ru_id']);
    }

    admin_log($_POST['brand_name'],'add','merchants_shop_brand');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'merchants_brand.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'merchants_brand.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('merchants_brand');
	
    $sql = "SELECT bid as brand_id, brandName as brand_name, bank_name_letter, site_url, brandLogo as brand_logo, brand_desc, is_show, sort_order, audit_status, major_business ".
           "FROM " .$ecs->table('merchants_shop_brand'). " WHERE bid = '$_REQUEST[id]'";
 
    $brand = $db->GetRow($sql);
    
    $platform_brand_list = get_merchants_search_brand(1 , 3);
    $smarty->assign('brand_list', $platform_brand_list);
    
    //关联品牌
    $link_brand = get_link_brand_list($brand['brand_id'], 3);
    $smarty->assign('link_brand', $link_brand);
   
    $smarty->assign('ubrand',     $_REQUEST['ubrand']);
    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['07_merchants_brand'], 'href' => 'merchants_brand.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');
    
    $date = array('major_brand');
    $where = " ru_id = '" .$adminru['ru_id']. "'";
    $major_brand = get_table_date('admin_user', $where, $date, 2);
    $smarty->assign('major_brand', $major_brand);
   
    assign_query_info();
    $smarty->display('merchants_brand_info.dwt');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('merchants_brand');
    
    $major_business = isset($_POST['major_business']) ? intval($_POST['major_business']) : '';
    $linkBrand = isset($_POST['link_brand']) ? intval($_POST['link_brand']) : 0;
    $bid = (isset($_POST["id"]) ? intval($_POST["id"]) : 0);

    if ($_POST['brand_name'] != $_POST['old_brandname'])
    {
        /*检查品牌名是否相同*/	
	$is_only = $exc->is_only('brandName', $_POST['brand_name'], $bid, '', $GLOBALS['ecs']->table('merchants_shop_brand'), 'bid');

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['brandname_exist'], stripslashes($_POST['brand_name'])), 1);
        }else{
            if($adminru['ru_id'] > 0){
                $audit_status = ", audit_status= 0";
            }
        }
    }
    
    if($_FILES['brand_logo']['name'] != ''){
        if($adminru['ru_id'] > 0){
            $audit_status = ", audit_status= 0";
        }
    }
    
    /*对描述处理*/
    if (!empty($_POST['brand_desc']))
    {
        $_POST['brand_desc'] = $_POST['brand_desc'];
    }

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;
    
     /*处理URL*/
    $site_url = sanitize_url( $_POST['site_url'] );

    /*处理图片*/
    if(!empty($_FILES['brand_logo']['name'])){
        $img_name = "data/septs_Image/" . basename($image->upload_image($_FILES['brand_logo'],'septs_Image'));
    }else{
        $img_name = '';
    }
    
    get_oss_add_file(array($img_name));
    
    if(isset($_POST['link_brand']) && $adminru['ru_id'] > 0){
        $parent['major_brand'] = $bid;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('admin_user'), $parent, 'UPDTAE', 'ru_id =' . $adminru['ru_id']);
    }
    
    if(isset($_POST['major_business']))
    {
        $major_business = ",  major_business='$major_business'";
    }

        $audit_status = ", audit_status= '$_POST[audit_status]'";

            $brand_name = trim($_POST['brand_name']);
            $brand_letter = trim($_POST['brank_letter']);
            $sql = "SELECT brand_id FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_name = '$brand_name'";
            $brand_id = $GLOBALS['db']->getOne($sql);
            if(!$brand_id)
            {
                $sql = 'INSERT INTO '. $ecs->table('brand') . " (`brand_name`, `brand_letter`) VALUES ('$brand_name', '$brand_letter')";
                $GLOBALS['db']->query($sql);
                $linkBrand = $GLOBALS['db']->insert_id();
            }
            
            if($linkBrand > 0)
            {
                $sql = "SELECT id FROM " .$GLOBALS['ecs']->table('link_brand'). " WHERE bid= '$bid'";
                $lid = $GLOBALS['db']->getOne($sql);

                $link_brand = array(
                    'bid' =>$bid,
                    'brand_id' => $linkBrand
                );

                if($lid)
                {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('link_brand'), $link_brand, 'UPDTAE', 'bid=' . $_POST['id']); //更新关联品牌
                }else
                {
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('link_brand'), $link_brand, 'INSERT'); //更新关联品牌
                }
            }

    $param = "brandName = '$_POST[brand_name]', bank_name_letter = '$_POST[brank_letter]',  site_url='$site_url', brand_desc='$_POST[brand_desc]' $audit_status, is_show='$is_show', sort_order='$_POST[sort_order]' $major_business";
    if (!empty($img_name))
    {
            //有图片上传
            $param .= " ,brandLogo = '$img_name' ";
    }

    if ($exc->edit($param, $_POST['id'], 'merchants_shop_brand', 'bid'))
    {
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'merchants_shop_brand');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'merchants_brand.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['brand_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌中文名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_brand_name')
{
    check_authz_json('merchants_brand');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("brandName",$name, $id, "user_id = '" .$adminru['ru_id']. "'") != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brandName = '$name'", $id))
        {
            admin_log($name,'edit','merchants_shop_brand');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑品牌英文名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_brand_letter')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("bank_name_letter",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("bank_name_letter = '$name'", $id))
        {
            admin_log($name,'edit','merchants_shop_brand');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $name));
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('merchants_brand');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','merchants_shop_brand');

        make_json_result($order);
    }
    else
    {
        make_json_error(sprintf($_LANG['brandedit_fail'], $name));
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('merchants_brand');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 删除品牌 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('merchants_brand');

    $id = intval($_GET['id']);
    $ru_id = $adminru['ru_id'];
    
    get_del_batch('', $id, array('brandLogo'), 'bid', 'merchants_shop_brand', 1); //删除图片

    $exc->drop($id, 'merchants_shop_brand', 'bid');

    $sql = 'DELETE FROM ' . $GLOBALS['ecs']->table('merchants_shop_brandfile') . " WHERE bid = '$id'";
    $GLOBALS['db']->query($sql);

    /* 更新商品的品牌编号 */
    $sql = "UPDATE " .$ecs->table('goods'). " SET brand_id=0 WHERE brand_id='$id' and user_id = '$ru_id'";
    $db->query($sql);

    $url = 'merchants_brand.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除品牌图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_logo')
{
    /* 权限判断 */
    admin_priv('merchants_brand');
    $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    get_del_batch('', $brand_id, array('brandLogo'), 'bid', 'merchants_shop_brand', 1); //删除图片
    
    $sql = "UPDATE " .$ecs->table('merchants_shop_brand'). " SET brandLogo = '' WHERE bid = '$brand_id'";
    $db->query($sql);
        
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'merchants_brand.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'merchants_brand.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
}

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist($ru_id)
{
    $where = '';
    if($ru_id > 0){
        $where .= " and user_id = '$ru_id'";
    }
	
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();
	
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'msb.bid' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $brand_name = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';
        
        //管理员查询的权限 -- 店铺查询 start
        $filter['store_search'] = empty($_REQUEST['store_search']) ? 0 : intval($_REQUEST['store_search']);
        $filter['merchant_id'] = isset($_REQUEST['merchant_id']) ? intval($_REQUEST['merchant_id']) : 0;
        $filter['store_keyword'] = isset($_REQUEST['store_keyword']) ? trim($_REQUEST['store_keyword']) : '';
        
        $store_where = '';
        $store_search_where = '';
        if($filter['store_search'] !=0){
           if($ru_id == 0){ 
               
               if($_REQUEST['store_type']){
                    $store_search_where = "AND msi.shopNameSuffix = '" .$_REQUEST['store_type']. "'";
                }
               
                if($filter['store_search'] == 1){
                    $where .= " AND msb.user_id = '" .$filter['merchant_id']. "' ";
                }elseif($filter['store_search'] == 2){
                    $store_where .= " AND msi.rz_shopName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%'";
                }elseif($filter['store_search'] == 3){
                    $store_where .= " AND msi.shoprz_brandName LIKE '%" . mysqli_like_quote($filter['store_keyword']) . "%' " . $store_search_where;
                }
                
                if($filter['store_search'] > 1){
                    $where .= " AND (SELECT msi.user_id FROM " . $GLOBALS['ecs']->table('merchants_shop_information') .' as msi ' .  
                              " WHERE msi.user_id = msb.user_id $store_where) > 0 ";
                }
           }
        }
        //管理员查询的权限 -- 店铺查询 end
        
        /* 记录总数以及页数 */
        if (!empty($brand_name))
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb "." WHERE msb.brandName LIKE '%" . mysqli_like_quote($brand_name) . "%'" . $where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('merchants_shop_brand')  ." AS msb ". " where 1 " . $where;
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);
		
        /* 查询记录 */
        if (!empty($brand_name))
        {
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $brand_name);
            }
            else
            {
                $keyword = $brand_name;
            }
			
            $sql = "SELECT msb.* FROM ".$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb "." WHERE msb.brandName like '%{$keyword}%' " .$where. " ORDER BY $filter[sort_by] $filter[sort_order]";
        }
        else
        {
            $sql = "SELECT msb.* FROM ".$GLOBALS['ecs']->table('merchants_shop_brand') ." AS msb "." where 1 " .$where. " ORDER BY $filter[sort_by] $filter[sort_order]";
        }

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    $arr = array();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $site_url   = empty($rows['site_url']) ? 'N/A' : '<a href="'.$rows['site_url'].'" target="_brank">'.$rows['site_url'].'</a>';
        $rows['site_url']   = $site_url;
        
        $brand_logo = empty($rows['brandLogo']) ? '' :
            '<a href="../' . $rows['brandLogo'].'" target="_brank"><img src="images/picflag.gif" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['brand_logo'].' /></a>';
        
        $site_url   = empty($rows['site_url']) ? 'N/A' : '<a href="'.$rows['site_url'].'" target="_brank">'.$rows['site_url'].'</a>';	
        $rows['brand_logo'] = $brand_logo;
        $rows['brand_id'] = $rows['bid'];
        $rows['brand_name'] = $rows['brandName'];
        $rows['brand_letter'] = $rows['bank_name_letter'];
        $rows['user_name'] = get_shop_name($rows['user_id'], 1);
        $rows['link_brand'] = get_link_brand_list($rows['bid'], 3);

        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>
