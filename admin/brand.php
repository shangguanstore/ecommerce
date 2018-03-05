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

$exc = new exchange($ecs->table("brand"), $db, 'brand_id', 'brand_name');

$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '06_goods_brand_list'));

/*------------------------------------------------------ */
//-- 品牌列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',      $_LANG['06_goods_brand_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['07_brand_add'], 'href' => 'brand.php?act=add'));
    $smarty->assign('full_page',    1);

    $brand_list = get_brandlist();

    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    assign_query_info();
    $smarty->display('brand_list.dwt');
}

/*------------------------------------------------------ */
//-- 添加品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('brand_manage');

    $smarty->assign('ur_here',     $_LANG['07_brand_add']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'brand.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->assign('brand', array('sort_order'=>50, 'is_show'=>1));
    $smarty->display('brand_info.dwt');
}
elseif ($_REQUEST['act'] == 'insert')
{
    /*检查品牌名是否重复*/
    admin_priv('brand_manage');

    $is_show = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 0;

    $is_only = $exc->is_only('brand_name', $_POST['brand_name']);

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
    $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));
    
    get_oss_add_file(array(DATA_DIR . '/brandlogo/' . $img_name));

     /*处理URL*/
    $site_url = sanitize_url( $_POST['site_url'] );

    /*插入数据*/

    $sql = "INSERT INTO ".$ecs->table('brand')."(brand_name, brand_letter, site_url, brand_desc, brand_logo, is_show, sort_order) ".
           "VALUES ('$_POST[brand_name]', '$_POST[brand_letter]', '$site_url', '$_POST[brand_desc]', '$img_name', '$is_show', '$_POST[sort_order]')";
    $db->query($sql);
	if($brand_id=$db->insert_id())
	{
		$is_recommend=!empty($_POST['is_recommend'])?intval($_POST['is_recommend']):0;
		$extend_sql="INSERT INTO ".$ecs->table('brand_extend')." (brand_id,is_recommend) values ('$brand_id','$is_recommend')";
		$db->query($extend_sql);
	}

    admin_log($_POST['brand_name'],'add','brand');

    /* 清除缓存 */
    clear_cache_files();

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'brand.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'brand.php?act=list';

    sys_msg($_LANG['brandadd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 编辑品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $sql = "SELECT b.brand_id, b.brand_name, b.brand_letter, b.site_url, b.brand_logo, b.brand_desc, b.brand_logo, b.is_show, b.sort_order,be.is_recommend ".
            "FROM " .$ecs->table('brand'). " as b left join ".$ecs->table('brand_extend')." as be on b.brand_id=be.brand_id WHERE b.brand_id='$_REQUEST[id]'";
    $brand = $db->GetRow($sql);
	
	$brand['brand_logo'] = empty($brand['brand_logo']) ? '' : "../" . DATA_DIR . '/brandlogo/'.$brand['brand_logo']; //by wu

    $smarty->assign('ur_here',     $_LANG['brand_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['06_goods_brand_list'], 'href' => 'brand.php?act=list&' . list_link_postfix()));
    $smarty->assign('brand',       $brand);
    $smarty->assign('form_action', 'updata');

    assign_query_info();
    $smarty->display('brand_info.dwt');
}
elseif ($_REQUEST['act'] == 'updata')
{
    admin_priv('brand_manage');
    if ($_POST['brand_name'] != $_POST['old_brandname'])
    {
        /*检查品牌名是否相同*/
        $is_only = $exc->is_only('brand_name', $_POST['brand_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['brandname_exist'], stripslashes($_POST['brand_name'])), 1);
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

    /* 处理图片 */
    $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));
    $param = "brand_name = '$_POST[brand_name]', brand_letter = '$_POST[brand_letter]',  site_url='$site_url', brand_desc='$_POST[brand_desc]', is_show='$is_show', sort_order='$_POST[sort_order]' ";
    if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,brand_logo = '$img_name' ";
    }
    
    get_oss_add_file(array(DATA_DIR . '/brandlogo/' . $img_name));

    if ($exc->edit($param,  $_POST['id']))
    {
        $brand_id=!empty($_POST['id'])?intval($_POST['id']):0;
        if($brand_id>0)
        {
                $is_recommend=!empty($_POST['is_recommend'])?intval($_POST['is_recommend']):0;
                if($db->query("select count(id) from ".$ecs->table('brand_extend')." where brand_id='$brand_id'"))
                {
                        $extend_sql="update ".$ecs->table('brand_extend')." set is_recommend='$is_recommend' where brand_id='$brand_id'";	
                }
                else
                {
                        $extend_sql="INSERT INTO ".$ecs->table('brand_extend')." (brand_id,is_recommend) values ('$brand_id','$is_recommend')";	
                }

                $db->query($extend_sql);
        }
		
        /* 清除缓存 */
        clear_cache_files();

        admin_log($_POST['brand_name'], 'edit', 'brand');

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'brand.php?act=list&' . list_link_postfix();
        $note = vsprintf($_LANG['brandedit_succed'], $_POST['brand_name']);
        sys_msg($note, 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 生成地区首字母
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'create_brand_letter')
{
    admin_priv('brand_manage');
    
    $smarty->assign('ur_here',      $_LANG['06_goods_brand_list']);
    
    $record_count = get_brand_list(0, 2);
    
    $smarty->assign('record_count', $record_count);
    $smarty->assign('page', 1);
    
    assign_query_info();
    $smarty->display('brand_first_letter.dwt');
}

/*------------------------------------------------------ */
//-- 生成地区首字母
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'create_brand_initial')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 1;
    
    if($page == 1){
        @unlink(ROOT_PATH . DATA_DIR . '/sc_file/pin_brands.php');
    }
    
    $brand_list = get_brand_list(0, 1);
    
    $brand_list = $ecs->page_array($page_size, $page, $brand_list);
    $result['list'] = $brand_list['list'][0];
        
    if ($result['list']) {
        $arr = array();
        
        $str_first = strtolower(substr($result['list']['brand_name'], 0, 1));
        if ($ecs->preg_is_letter($str_first)) {
            $arr['brand_id'] = $result['list']['brand_id'];
            $arr['brand_name'] = $result['list']['brand_name'];
            $arr['letter'] = strtoupper($str_first);
        } else {
            $pin = new pin();
            $letters = range('A', 'Z');
            foreach ($letters as $key => $val) {
                if (strtolower($val) == substr($pin->Pinyin($result['list']['brand_name'], EC_CHARSET), 0, 1)) {
                    $arr['brand_id'] = $result['list']['brand_id'];
                    $arr['brand_name'] = $result['list']['brand_name'];
                    $arr['letter'] = $val;
                }
            }
        }

        $result['list'] = $arr;
        
        $pin_brands = read_static_cache('pin_brands', '/data/sc_file/');
        
        if ($pin_brands === false) {
            write_static_cache('pin_brands', array($result['list']), '/data/sc_file/');
        }else{
            array_push($pin_brands, $result['list']);
            write_static_cache('pin_brands', $pin_brands, '/data/sc_file/');
        }
    }

    $result['page'] = $brand_list['filter']['page'] + 1;
    $result['page_size'] = $brand_list['filter']['page_size'];
    $result['record_count'] = $brand_list['filter']['record_count'];
    $result['page_count'] = $brand_list['filter']['page_count'];
        
    $result['is_stop'] = 1;
    if ($page > $brand_list['filter']['page_count']) {
        $result['is_stop'] = 0;
    }else{
        $result['filter_page'] = $brand_list['filter']['page'];
    }

    die($json->encode($result));
}    

/*------------------------------------------------------ */
//-- 编辑品牌中文名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_brand_name')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $name   = json_str_iconv(trim($_POST['val']));

    /* 检查名称是否重复 */
    if ($exc->num("brand_name",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brand_name = '$name'", $id))
        {
            admin_log($name,'edit','brand');
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
    if ($exc->num("brand_letter",$name, $id) != 0)
    {
        make_json_error(sprintf($_LANG['brandname_exist'], $name));
    }
    else
    {
        if ($exc->edit("brand_letter = '$name'", $id))
        {
            admin_log($name,'edit','brand');
            make_json_result(stripslashes($name));
        }
        else
        {
            make_json_result(sprintf($_LANG['brandedit_fail'], $name));
        }
    }
}

elseif($_REQUEST['act'] == 'add_brand')
{
    $brand = empty($_REQUEST['brand']) ? '' : json_str_iconv(trim($_REQUEST['brand']));

    if(brand_exists($brand))
    {
        make_json_error($_LANG['brand_name_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('brand') . "(brand_name)" .
               "VALUES ( '$brand')";

        $db->query($sql);
        $brand_id = $db->insert_id();

        $arr = array("id"=>$brand_id, "brand"=>$brand);

        make_json_result($arr);
    }
}
/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $order  = intval($_POST['val']);
    $name   = $exc->get_name($id);

    if ($exc->edit("sort_order = '$order'", $id))
    {
        admin_log(addslashes($name),'edit','brand');

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
    check_authz_json('brand_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_show='$val'", $id);

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_recommend')
{
    check_authz_json('brand_manage');

    $brand_id     = intval($_POST['id']);
    $is_recommend    = intval($_POST['val']);

    if($db->getOne("select count(id) from ".$ecs->table('brand_extend')." where brand_id='$brand_id'"))
	{
		$extend_sql="update ".$ecs->table('brand_extend')." set is_recommend='$is_recommend' where brand_id='$brand_id'";	
	}
	else
	{
		$extend_sql="INSERT INTO ".$ecs->table('brand_extend')." (brand_id,is_recommend) values ('$brand_id','$is_recommend')";	
	}

	$db->query($extend_sql);

    make_json_result($is_recommend);
}

/*------------------------------------------------------ */
//-- 删除品牌
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('brand_manage');

    $id = intval($_GET['id']);
    
    get_del_batch('', $id, array('brand_logo'), 'brand_id', 'brand', 0, DATA_DIR . '/brandlogo/'); //删除图片
    
    $exc->drop($id);

    /* 更新商品的品牌编号 */
    $sql = "UPDATE " .$ecs->table('goods'). " SET brand_id=0 WHERE brand_id='$id'";
    $db->query($sql);

    $url = 'brand.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除品牌图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_logo')
{
    /* 权限判断 */
    admin_priv('brand_manage');
    $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    get_del_batch('', $brand_id, array('brand_logo'), 'brand_id', 'brand', 0, DATA_DIR . '/brandlogo/'); //删除图片
    $sql = "UPDATE " .$ecs->table('brand'). " SET brand_logo = '' WHERE brand_id = '$brand_id'";
    $db->query($sql);
    
    $link= array(array('text' => $_LANG['brand_edit_lnk'], 'href' => 'brand.php?act=edit&id=' . $brand_id), array('text' => $_LANG['brand_list_lnk'], 'href' => 'brand.php?act=list'));
    sys_msg($_LANG['drop_brand_logo_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $brand_list = get_brandlist();
    $smarty->assign('brand_list',   $brand_list['brand']);
    $smarty->assign('filter',       $brand_list['filter']);
    $smarty->assign('record_count', $brand_list['record_count']);
    $smarty->assign('page_count',   $brand_list['page_count']);

    make_json_result($smarty->fetch('brand_list.dwt'), '',
        array('filter' => $brand_list['filter'], 'page_count' => $brand_list['page_count']));
}

/**
 * 获取品牌列表
 *
 * @access  public
 * @return  array
 */
function get_brandlist()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();
        
        $filter['sort_by']          = empty($_REQUEST['sort_by']) ? 'brand_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order']       = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        /* 记录总数以及页数 */
        if (isset($_POST['brand_name']))
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('brand') .' WHERE brand_name = \''.$_POST['brand_name'].'\'';
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('brand');
        }

        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);
		
		$leftjoin=" left join ".$GLOBALS['ecs']->table('brand_extend')." as be on b.brand_id=be.brand_id ";

        /* 查询记录 */
        if (isset($_POST['brand_name']))
        {
            if(strtoupper(EC_CHARSET) == 'GBK')
            {
                $keyword = iconv("UTF-8", "gb2312", $_POST['brand_name']);
            }
            else
            {
                $keyword = $_POST['brand_name'];
            }
            $sql = "SELECT b.*,be.is_recommend FROM ".$GLOBALS['ecs']->table('brand')." as b ".$leftjoin." WHERE brand_name like '%{$keyword}%' ORDER BY $filter[sort_by] $filter[sort_order]";
        }
        else
        {
            $sql = "SELECT b.*,be.is_recommend FROM ".$GLOBALS['ecs']->table('brand')." as b ".$leftjoin." where 1 ORDER BY $filter[sort_by] $filter[sort_order]";
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
        /*$brand_logo = empty($rows['brand_logo']) ? '' :
            '<a href="../' . DATA_DIR . '/brandlogo/'.$rows['brand_logo'].'"><img src="images/picflag.gif" width="16" height="16" border="0" alt='.$GLOBALS['_LANG']['brand_logo'].' /></a>';*/
        $brand_logo = empty($rows['brand_logo']) ? '' : "../" . DATA_DIR . '/brandlogo/'.$rows['brand_logo'];
		$site_url   = empty($rows['site_url']) ? 'N/A' : '<a href="'.$rows['site_url'].'" target="_brank">'.$rows['site_url'].'</a>';

        $rows['brand_logo'] = $brand_logo;
        $rows['site_url']   = $site_url;

        $arr[] = $rows;
    }

    return array('brand' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

?>
