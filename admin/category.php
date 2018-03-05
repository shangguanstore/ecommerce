<?php

/**
 * ECSHOP 商品分类管理程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table("category"), $db, 'cat_id', 'cat_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

$adminru = get_admin_ru_id();
$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '03_category_list'));
/*------------------------------------------------------ */
//-- 商品分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    //$adminru = get_admin_ru_id();
	
    $parent_id = !isset($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    
    //返回上一页 start
    if(isset($_REQUEST['back_level']) && $_REQUEST['back_level'] > 0){
        $level = $_REQUEST['back_level'] - 1;
        $parent_id = $db->getOne("SELECT parent_id FROM " .$ecs->table('category'). " WHERE cat_id = '$parent_id'", true);
    }else{
        $level = isset($_REQUEST['level']) ? $_REQUEST['level'] + 1 : 0;
    }
    //返回上一页 end
    
    $smarty->assign('level', $level);
    $smarty->assign('parent_id', $parent_id);
    
    /* 获取分类列表 */
    $cat_list = get_cat_level($parent_id, $level);
    
    $smarty->assign('cat_info',     $cat_list);
    $smarty->assign('ru_id',     $adminru['ru_id']);

    if($adminru['ru_id'] == 0){
            $smarty->assign('action_link',  array('href' => 'category.php?act=add', 'text' => $_LANG['04_category_add']));
    }
    
    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['03_category_list']);
    $smarty->assign('full_page',    1);
    
    $cat_level = array('一', '二', '三', '四', '五', '六', '气', '八', '九', '十');
    $smarty->assign('cat_level', $cat_level[$level]);
    
    /* 列表页面 */
    assign_query_info();
    $smarty->display('category_list.dwt');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    //$adminru = get_admin_ru_id();
    
    /* 获取分类列表 */
    $cat_list = get_cat_level();
    $smarty->assign('cat_info',     $cat_list);
    $smarty->assign('ru_id',     $adminru['ru_id']);

    make_json_result($smarty->fetch('category_list.dwt'));
}

/*------------------------------------------------------ */
//-- 添加商品分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add') {
    /* 权限检查 */
    admin_priv('cat_manage');

    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    if (!empty($parent_id)) {
        set_default_filter(0, $parent_id); //设置默认筛选
        $smarty->assign('parent_category', get_every_category($parent_id)); //上级分类
        $smarty->assign('parent_id', $parent_id); //上级分类
    } else {
        set_default_filter(); //设置默认筛选
    }

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['04_category_add']);
    $smarty->assign('action_link', array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    $smarty->assign('goods_type_list', goods_type_list(0)); // 取得商品类型
    $smarty->assign('attr_list', get_attr_list()); // 取得商品属性
    $smarty->assign('form_act', 'insert');
    $smarty->assign('cat_info', array('is_show' => 1));

    //ecmoban模板堂 --zhuo start
    //$adminru = get_admin_ru_id();
    $smarty->assign('ru_id', $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end

    /* 显示页面 */
    assign_query_info();
    $smarty->display('category_info.dwt');
}

/*------------------------------------------------------ */
//-- 删除分类菜单图片 by wu
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'delete_icon') {
    /* 权限检查 */
    admin_priv('cat_manage');

    $result = array('error' => 0, 'msg' => '');
    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    $cat_info = get_cat_info($cat_id);
    if (!empty($cat_info)) {
        $sql = " update " . $GLOBALS['ecs']->table('category') . " set cat_icon='' where cat_id= " . $cat_id;
        if ($GLOBALS['db']->query($sql)) {
            @unlink(ROOT_PATH . $cat_info['cat_icon']);
            $result = array('error' => 1, 'msg' => '成功删除');
        }
    }
    die(json_encode($result));
}

/*------------------------------------------------------ */
//-- 商品分类添加时的处理
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
    /* 权限检查 */
    admin_priv('cat_manage');

    /* 初始化变量 */
    $cat['cat_id'] = !empty($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $cat['parent_id'] = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $cat['level'] = count(get_select_category($cat['parent_id'], 1, true)) - 2;

    if ($cat['level'] > 1 && $adminru['ru_id'] == 0) {
        $link[0]['text'] = $_LANG['go_back'];

        if ($cat['cat_id'] > 0) {
            $link[0]['href'] = 'category.php?act=edit&cat_id=' . $cat['cat_id'];
        } else {
            $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];
        }

        sys_msg('平台最多只能设置三级分类', 0, $link);
        exit;
    }

    //$adminru = get_admin_ru_id();
    //ecmoban模板堂 --zhuo start 
    if ($cat['level'] < 2 && $adminru['ru_id'] > 0) {

        $link[0]['text'] = $_LANG['go_back'];

        if ($cat['cat_id'] > 0) {
            $link[0]['href'] = 'category.php?act=edit&cat_id=' . $cat['cat_id'];
        } else {
            $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];
        }

        sys_msg('您目前的权限只能添加四级分类', 0, $link);
        exit;
    }
    //ecmoban模板堂 --zhuo end
    //上传分类菜单图标 by wu start
    if (!empty($_FILES['cat_icon']['name'])) {
        if ($_FILES["cat_icon"]["size"] > 200000) {
            sys_msg('上传图片不得大于200kb', 0, $link);
        }
        $type = end(explode('.', $_FILES['cat_icon']['name']));
        if ($type != 'jpg' && $type != 'png' && $type != 'gif') {
            sys_msg('请上传jpg,gif,png格式图片', 0, $link);
        }
        $imgNamePrefix = time() . mt_rand(1001, 9999);
        //文件目录
        $imgDir = ROOT_PATH . "images/cat_icon";
        if (!file_exists($imgDir)) {
            mkdir($imgDir);
        }
        //保存文件
        $imgName = $imgDir . "/" . $imgNamePrefix . '.' . $type;
        $saveDir = "images/cat_icon" . "/" . $imgNamePrefix . '.' . $type;
        move_uploaded_file($_FILES["cat_icon"]["tmp_name"], $imgName);
        $cat['cat_icon'] = $saveDir;
    }
    //上传分类菜单图标 by wu end

    $cat['sort_order'] = !empty($_POST['sort_order']) ? intval($_POST['sort_order']) : 0;
    $cat['keywords'] = !empty($_POST['keywords']) ? trim($_POST['keywords']) : '';
    $cat['cat_desc'] = !empty($_POST['cat_desc']) ? $_POST['cat_desc'] : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name'] = !empty($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
    $cat['cat_alias_name'] = !empty($_POST['cat_alias_name']) ? trim($_POST['cat_alias_name']) : '';

    //by guan start
    $pin = new pin();
    $pinyin = $pin->Pinyin($cat['cat_name'], 'UTF8');
    $cat['pinyin_keyword'] = $pinyin;
    //by guan end

    $cat['show_in_nav'] = !empty($_POST['show_in_nav']) ? intval($_POST['show_in_nav']) : 0;
    $cat['style'] = !empty($_POST['style']) ? trim($_POST['style']) : '';
    $cat['is_show'] = !empty($_POST['is_show']) ? intval($_POST['is_show']) : 0;

    /* by zhou */
    $cat['is_top_show'] = !empty($_POST['is_top_show']) ? intval($_POST['is_top_show']) : 0;
    $cat['is_top_style'] = !empty($_POST['is_top_style']) ? intval($_POST['is_top_style']) : 0;
    $cat['top_style_tpl'] = !empty($_POST['top_style_tpl']) ? $_POST['top_style_tpl'] : 0; //顶级分类页模板 by wu
    /* by zhou */
    $cat['grade'] = !empty($_POST['grade']) ? intval($_POST['grade']) : 0;
    $cat['filter_attr'] = !empty($_POST['filter_attr']) ? implode(',', array_unique(array_diff($_POST['filter_attr'], array(0)))) : 0;

    $cat['cat_recommend'] = !empty($_POST['cat_recommend']) ? $_POST['cat_recommend'] : array();

    if (cat_exists($cat['cat_name'], $cat['parent_id'])) {
        /* 同级别下不能有重复的分类名称 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['catname_exist'], 0, $link);
    }

    if ($cat['grade'] > 10 || $cat['grade'] < 0) {
        /* 价格区间数超过范围 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
        sys_msg($_LANG['grade_error'], 0, $link);
    }

    /* 入库的操作 */
    $cat_name = explode(',', $cat['cat_name']);

    if (count($cat_name) > 1) {

        $cat['is_show_merchants'] = !empty($_POST['is_show_merchants']) ? intval($_POST['is_show_merchants']) : 0;

        get_bacth_category($cat_name, $cat, $adminru['ru_id']);

        clear_cache_files();    // 清除缓存

        /* 添加链接 */
        $link[0]['text'] = $_LANG['continue_add'];
        $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];

        $link[1]['text'] = $_LANG['back_list'];
        $link[1]['href'] = 'category.php?act=list&parent_id=' . $cat['parent_id'] . '&level=' . $cat['level'];

        sys_msg($_LANG['catadd_succed'], 0, $link);
    } else {
        if ($db->autoExecute($ecs->table('category'), $cat) !== false) {
            $cat_id = $db->insert_id();
            if ($cat['show_in_nav'] == 1) {
                $vieworder = $db->getOne("SELECT max(vieworder) FROM " . $ecs->table('nav') . " WHERE type = 'middle'");
                $vieworder += 2;
                //显示在自定义导航栏中
                $sql = "INSERT INTO " . $ecs->table('nav') .
                        " (name,ctype,cid,ifshow,vieworder,opennew,url,type)" .
                        " VALUES('" . $cat['cat_name'] . "', 'c', '" . $db->insert_id() . "','1','$vieworder','0', '" . build_uri('category', array('cid' => $cat_id), $cat['cat_name']) . "','middle')";
                $db->query($sql);
            }
            insert_cat_recommend($cat['cat_recommend'], $cat_id);

            admin_log($_POST['cat_name'], 'add', 'category');   // 记录管理员操作
            
            //ecmoban模板堂 --zhuo start
            $dt_list = isset($_POST['document_title']) ? $_POST['document_title'] : array();
            $dt_id = isset($_POST['dt_id']) ? $_POST['dt_id'] : array();
            get_documentTitle_insert_update($dt_list, $cat_id, $dt_id);
            //ecmoban模板堂 --zhuo end

            clear_cache_files();    // 清除缓存

            /* 添加链接 */
            $link[0]['text'] = $_LANG['continue_add'];
            $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];

            $link[1]['text'] = $_LANG['back_list'];
            $link[1]['href'] = 'category.php?act=list&parent_id=' . $cat['parent_id'] . '&level=' . $cat['level'];

            sys_msg($_LANG['catadd_succed'], 0, $link);
        }
    }
}

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit') {
    admin_priv('cat_manage');   // 权限检查
    $cat_id = intval($_REQUEST['cat_id']);
    $cat_info = get_cat_info($cat_id);  // 查询分类信息数据
    $attr_list = get_attr_list();
    $filter_attr_list = array();

    //获取下拉列表 by wu start
    $smarty->assign('parent_id', $cat_info['parent_id']); //上级分类
    $smarty->assign('parent_category', get_every_category($cat_info['parent_id'])); //上级分类导航
    set_default_filter(0, $cat_info['parent_id']); //设置默认筛选
    //获取下拉列表 by wu end	

    if ($cat_info['filter_attr']) {
        $filter_attr = explode(",", $cat_info['filter_attr']);  //把多个筛选属性放到数组中

        foreach ($filter_attr AS $k => $v) {
            $attr_cat_id = $db->getOne("SELECT cat_id FROM " . $ecs->table('attribute') . " WHERE attr_id = '" . intval($v) . "'");
            $filter_attr_list[$k]['goods_type_list'] = goods_type_list($attr_cat_id);  //取得每个属性的商品类型
            $filter_attr_list[$k]['goods_type'] = $attr_cat_id;  //by wu
            $filter_attr_list[$k]['filter_attr'] = $v;
            $attr_option = array();

            if (isset($attr_list[$attr_cat_id]) && $attr_list[$attr_cat_id]) {
                foreach ($attr_list[$attr_cat_id] as $val) {
                    $attr_option[key($val)] = current($val);
                }
            }

            $filter_attr_list[$k]['option'] = $attr_option;
        }

        $smarty->assign('filter_attr_list', $filter_attr_list);
    } else {
        $attr_cat_id = 0;
    }

    /* 模板赋值 */

    //by guan start
    if ($cat_info['parent_id'] == 0) {
        $cat_name_arr = explode('、', $cat_info['cat_name']);
        $smarty->assign('cat_name_arr', $cat_name_arr); // 取得商品属性
    }
    //by guan end

    $smarty->assign('attr_list', $attr_list); // 取得商品属性
    $smarty->assign('attr_cat_id', $attr_cat_id);
    $smarty->assign('ur_here', $_LANG['category_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_category_list'], 'href' => 'category.php?act=list'));

    //分类是否存在首页推荐
    $res = $db->getAll("SELECT recommend_type FROM " . $ecs->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
    if (!empty($res)) {
        $cat_recommend = array();
        foreach ($res as $data) {
            $cat_recommend[$data['recommend_type']] = 1;
        }
        $smarty->assign('cat_recommend', $cat_recommend);
    }

    //ecmoban模板堂 --zhuo start
    $sql = "select dt_id, dt_title from " . $ecs->table('merchants_documenttitle') . " where cat_id = '$cat_id'";
    $title_list = $db->getAll($sql);
    $smarty->assign('title_list', $title_list);
    $smarty->assign('cat_id', $cat_id);

    //$adminru = get_admin_ru_id();
    $smarty->assign('ru_id', $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end

    $smarty->assign('cat_info', $cat_info);
    $smarty->assign('form_act', 'update');
    $smarty->assign('goods_type_list', goods_type_list(0)); // 取得商品类型

    /* 显示页面 */
    assign_query_info();
    $smarty->display('category_info.dwt');
}
//ecmoban模板堂 --zhuo start
elseif($_REQUEST['act'] == 'titleFileView'){
	
	$cat_id = intval($_REQUEST['cat_id']);
	
	$sql = "select dt_id, dt_title from " .$ecs->table('merchants_documenttitle'). " where cat_id = '$cat_id'";
	$title_list = $db->getAll($sql);
	$smarty->assign('title_list',    $title_list);
	$smarty->assign('cat_id',    $cat_id);
	$smarty->assign('form_act',    'title_update');
	
	$sql = "select cat_name from " .$ecs->table('category'). " where cat_id = '$cat_id'";
	$cat_name = $db->getOne($sql);
	$smarty->assign('cat_name',    $cat_name);
	
	$smarty->assign('action_link',  array('href' => 'category.php?act=edit&cat_id='.$cat_id, 'text' => $_LANG['go_back']));
	
	/* 显示页面 */
    assign_query_info();
    $smarty->display('category_titleFileView.dwt');
}
elseif($_REQUEST['act'] == 'title_update'){
	
	$cat_id = intval($_REQUEST['cat_id']);
	
	$dt_list        	 = isset($_POST['document_title'])? $_POST['document_title']     : array();
	$dt_id        	 = isset($_POST['dt_id'])? $_POST['dt_id']     : array();
	get_documentTitle_insert_update($dt_list, $cat_id, $dt_id);
	
	clear_cache_files(); // 清除缓存
	
	/* 提示信息 */
	$link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=titleFileView&cat_id=' . $cat_id);
	sys_msg($_LANG['title_catedit_succed'], 0, $link);
	
}
//ecmoban模板堂 --zhuo end
elseif($_REQUEST['act'] == 'add_category')
{
    $parent_id = empty($_REQUEST['parent_id']) ? 0 : intval($_REQUEST['parent_id']);
    $category = empty($_REQUEST['cat']) ? '' : json_str_iconv(trim($_REQUEST['cat']));

    if(cat_exists($category, $parent_id))
    {
        make_json_error($_LANG['catname_exist']);
    }
    else
    {
        $sql = "INSERT INTO " . $ecs->table('category') . "(cat_name, parent_id, is_show)" .
               "VALUES ( '$category', '$parent_id', 1)";

        $db->query($sql);
        $category_id = $db->insert_id();

        $arr = array("parent_id"=>$parent_id, "id"=>$category_id, "cat"=>$category);

        clear_cache_files();    // 清除缓存
        
        die(json_encode($arr));
    }
}

/*------------------------------------------------------ */
//-- 编辑商品分类信息
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'update')
{
    /* 权限检查 */
    admin_priv('cat_manage');

    /* 初始化变量 */
    $cat_id = $cat['cat_id']       = !empty($_POST['cat_id'])       ? intval($_POST['cat_id'])     : 0;
    $cat['parent_id']    = isset($_POST['parent_id'])     ? intval($_POST['parent_id'])  : 0;
	$cat['level']        = count(get_select_category($cat['parent_id'],1,true)) - 2;
	$old_cat_name = isset($_REQUEST['old_cat_name'])  ?  $_REQUEST['old_cat_name'] : '';
	if($cat['level'] > 1 && $adminru['ru_id'] == 0)
	{
        $link[0]['text'] = $_LANG['go_back'];
        
        if($cat['cat_id'] > 0){
            $link[0]['href'] = 'category.php?act=edit&cat_id=' . $cat['cat_id'];
        }else{
            $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];
        }

        sys_msg('平台最多只能设置三级分类', 0, $link);
        exit;		
	}
    
    if($cat['level'] < 2 && $adminru['ru_id'] > 0){
        
        $link[0]['text'] = $_LANG['go_back'];
        
        if($cat['cat_id'] > 0){
            $link[0]['href'] = 'category.php?act=edit&cat_id=' . $cat['cat_id'];
        }else{
            $link[0]['href'] = 'category.php?act=add&parent_id=' . $cat['parent_id'];
        }

        sys_msg('您目前的权限只能添加四级分类', 0, $link);
        exit;
    }
    //ecmoban模板堂 --zhuo end

	//上传分类菜单图标 by wu start
	if(!empty($_FILES['cat_icon']['name']))
	{
		if($_FILES["cat_icon"]["size"] > 200000)
		{
			sys_msg('上传图片不得大于200kb', 0, $link);	
		}
		$type=end(explode('.',$_FILES['cat_icon']['name']));
		if($type!='jpg' && $type!='png' && $type!='gif')
		{
			sys_msg('请上传jpg,gif,png格式图片', 0, $link);	
		}
		$imgNamePrefix=time().mt_rand(1001,9999);
		//文件目录
		$imgDir=ROOT_PATH . "images/cat_icon";
		if(!file_exists($imgDir))
		{
			mkdir($imgDir);
		}		
		//保存文件
		$imgName=$imgDir. "/" . $imgNamePrefix.'.'.$type;
		$saveDir="images/cat_icon" . "/" . $imgNamePrefix.'.'.$type;
		move_uploaded_file($_FILES["cat_icon"]["tmp_name"],$imgName);
		$cat['cat_icon']=$saveDir;
		//删除文件
		if(!empty($cat_id))
		{
			$cat_info = get_cat_info($cat_id);
			@unlink(ROOT_PATH . $cat_info['cat_icon']);
		}				
	}
	//上传分类菜单图标 by wu end
    
    $cat['sort_order']   = !empty($_POST['sort_order'])   ? intval($_POST['sort_order']) : 0;
    $cat['keywords']     = !empty($_POST['keywords'])     ? trim($_POST['keywords'])     : '';
    $cat['cat_desc']     = !empty($_POST['cat_desc'])     ? $_POST['cat_desc']           : '';
    $cat['measure_unit'] = !empty($_POST['measure_unit']) ? trim($_POST['measure_unit']) : '';
    $cat['cat_name']     = !empty($_POST['cat_name'])     ? trim($_POST['cat_name'])     : '';
    $cat['cat_alias_name']     = !empty($_POST['cat_alias_name'])     ? trim($_POST['cat_alias_name'])     : '';
    $cat['category_links']     = !empty($_POST['category_links'])     ? $_POST['category_links']           : '';
    $cat['category_topic']     = !empty($_POST['category_topic'])     ? $_POST['category_topic']           : '';
	
    //by guan start
    $pin = new pin();
    $pinyin = $pin->Pinyin($cat['cat_name'],'UTF8');
	
    $cat['pinyin_keyword']     = $pinyin;
    //by guan end
	
    $cat['is_show']      = !empty($_POST['is_show'])      ? intval($_POST['is_show'])    : 0;
    /*by zhou*/
    $cat['is_top_show']      = !empty($_POST['is_top_show'])      ? intval($_POST['is_top_show'])    : 0;	
    $cat['is_top_style']  = !empty($_POST['is_top_style'])  ? intval($_POST['is_top_style']): 0;
	$cat['top_style_tpl']  = !empty($_POST['top_style_tpl'])  ? $_POST['top_style_tpl']: 0; //顶级分类页模板 by wu
    /*by zhou*/
    $cat['show_in_nav']  = !empty($_POST['show_in_nav'])  ? intval($_POST['show_in_nav']): 0;
    $cat['style']        = !empty($_POST['style'])        ? trim($_POST['style'])        : '';
    $cat['grade']        = !empty($_POST['grade'])        ? intval($_POST['grade'])      : 0;
    $cat['filter_attr']  = !empty($_POST['filter_attr'])  ? implode(',', array_unique(array_diff($_POST['filter_attr'],array(0)))) : 0;
    $cat['cat_recommend']  = !empty($_POST['cat_recommend'])  ? $_POST['cat_recommend'] : array();

    /* 判断分类名是否重复 */
    if ($cat['cat_name'] != $old_cat_name)
    {
        if (cat_exists($cat['cat_name'],$cat['parent_id'], $cat_id))
        {
           $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
           sys_msg($_LANG['catname_exist'], 0, $link);
        }
    }
    
    /* 判断上级目录是否合法 */
    $children = get_array_keys_cat($cat_id);     // 获得当前分类的所有下级分类
    if (in_array($cat['parent_id'], $children))
    {
        /* 选定的父类是当前分类或当前分类的下级分类 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG["is_leaf_error"], 0, $link);
    }

    if($cat['grade'] > 10 || $cat['grade'] < 0)
    {
        /* 价格区间数超过范围 */
       $link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
       sys_msg($_LANG['grade_error'], 0, $link);
    }

    $dat = $db->getRow("SELECT cat_name, show_in_nav FROM ". $ecs->table('category') . " WHERE cat_id = '$cat_id'");

    if ($db->autoExecute($ecs->table('category'), $cat, 'UPDATE', "cat_id='$cat_id'"))
    {
        if($cat['cat_name'] != $dat['cat_name'])
        {
            //如果分类名称发生了改变
            $sql = "UPDATE " . $ecs->table('nav') . " SET name = '" . $cat['cat_name'] . "' WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'";
            $db->query($sql);
        }
        if($cat['show_in_nav'] != $dat['show_in_nav'])
        {
            //是否显示于导航栏发生了变化
            if($cat['show_in_nav'] == 1)
            {
                //显示
                $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
                if(empty($nid))
                {
                    //不存在
                    $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
                    $vieworder += 2;
                    $uri = build_uri('category', array('cid'=> $cat_id), $cat['cat_name']);

                    $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $cat['cat_name'] . "', 'c', '$cat_id','1','$vieworder','0', '" . $uri . "','middle')";
                }
                else
                {
                    $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'";
                }
                $db->query($sql);
            }
            else
            {
                //去除
                $db->query("UPDATE " . $ecs->table('nav') . " SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
            }
        }

        //更新首页推荐
        insert_cat_recommend($cat['cat_recommend'], $cat_id);
        /* 更新分类信息成功 */
		
        //ecmoban模板堂 --zhuo start
        $dt_list        	 = isset($_POST['document_title'])? $_POST['document_title']     : array();
        $dt_id        	 = isset($_POST['dt_id'])? $_POST['dt_id']     : array();
        get_documentTitle_insert_update($dt_list, $cat_id, $dt_id);
        //ecmoban模板堂 --zhuo end

        clear_cache_files(); // 清除缓存
        admin_log($_POST['cat_name'], 'edit', 'category'); // 记录管理员操作

        /* 提示信息 */
        $link[] = array('text' => $_LANG['back_list'], 'href' => 'category.php?act=list&parent_id=' . $cat['parent_id'] . '&level=' . $cat['level']);
        sys_msg($_LANG['catedit_succed'], 0, $link);
    }
}

/* ------------------------------------------------------ */
//-- 批量转移商品分类页面
/* ------------------------------------------------------ */
if ($_REQUEST['act'] == 'move') {
    check_authz_json('cat_drop');

    $cat_id = !empty($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;

    //获取下拉列表 by wu start
    $smarty->assign('parent_id', $cat_id); //上级分类
    $smarty->assign('parent_category', get_every_category($cat_id)); //上级分类导航
    set_default_filter(0, $cat_id); //设置默认筛选
    //获取下拉列表 by wu end	

    /* 模板赋值 */
    $smarty->assign('ur_here', $_LANG['move_goods']);
    $smarty->assign('action_link', array('href' => 'category.php?act=list', 'text' => $_LANG['03_category_list']));

    $smarty->assign('form_act', 'move_cat');

    $html = $smarty->fetch("library/move_category.lbi");

    clear_cache_files();
    make_json_result($html);
}

/*------------------------------------------------------ */
//-- 处理批量转移商品分类的处理程序
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'move_cat')
{
    /* 权限检查 */
    admin_priv('cat_drop');

    $cat_id        = !empty($_POST['cat_id'])        ? intval($_POST['cat_id'])        : 0;
    $target_cat_id = !empty($_POST['target_cat_id']) ? intval($_POST['target_cat_id']) : 0;

    /* 商品分类不允许为空 */
    if ($cat_id == 0 || $target_cat_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=move');
        sys_msg($_LANG['cat_move_empty'], 0, $link);
    }
	
    /* 更新商品分类 */
    $sql = "UPDATE " . $ecs->table('goods') . " SET cat_id = '$target_cat_id' ".
            "WHERE cat_id = '$cat_id' AND user_id = '" . $adminru['ru_id'] . "'";

    if ($db->query($sql))
    {
        /* 清除缓存 */
        clear_cache_files();

        /* 提示信息 */
        $link[] = array('text' => $_LANG['go_back'], 'href' => 'category.php?act=list');
        sys_msg($_LANG['move_cat_success'], 0, $link);
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('sort_order' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑数量单位
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_measure_unit')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = json_str_iconv($_POST['val']);

    if (cat_update($id, array('measure_unit' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit_grade')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if($val > 10 || $val < 0)
    {
        /* 价格区间数超过范围 */
        make_json_error($_LANG['grade_error']);
    }

    if (cat_update($id, array('grade' => $val)))
    {
        clear_cache_files(); // 清除缓存
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示在导航栏
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_show_in_nav')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('show_in_nav' => $val)) != false)
    {
        if($val == 1)
        {
            //显示
            $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
            $vieworder += 2;
            $catname = $db->getOne("SELECT cat_name FROM ". $ecs->table('category') . " WHERE cat_id = '$id'");
            //显示在自定义导航栏中
            $_CFG['rewrite'] = 0;
            $uri = build_uri('category', array('cid'=> $id), $catname);

            $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
            if(empty($nid))
            {
                //不存在
                $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $catname . "', 'c', '$id','1','$vieworder','0', '" . $uri . "','middle')";
            }
            else
            {
                $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'";
            }
            $db->query($sql);
        }
        else
        {
            //去除
            $db->query("UPDATE " . $ecs->table('nav') . "SET ifshow = 0 WHERE ctype = 'c' AND cid = '" . $id . "' AND type = 'middle'");
        }
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_is_show')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('is_show' => $val)) != false)
    {
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 删除商品分类
/*------------------------------------------------------ */
//if ($_REQUEST['act'] == 'remove') {
//    check_authz_json('cat_manage');
//
//    /* 初始化分类ID并取得分类名称 */
//   $cat_id = intval($_GET['id']);
//    $cat_name = $db->getOne('SELECT cat_name FROM ' . $ecs->table('category') . " WHERE cat_id='$cat_id'");
//
//    /* 当前分类下是否有子分类 */
//    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('category') . " WHERE parent_id='$cat_id'");
//
//   /* 当前分类下是否存在商品 */
//    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('goods') . " WHERE cat_id='$cat_id'");
//
//    /* 如果不存在下级子分类和商品，则删除之 */
//    if ($cat_count == 0 && $goods_count == 0) {
//        /* 删除分类 */
//        $sql = 'DELETE FROM ' . $ecs->table('category') . " WHERE cat_id = '$cat_id'";
//        if ($db->query($sql)) {
//            $db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
//            clear_cache_files();
//            admin_log($cat_name, 'remove', 'category');
//        }
//
//        //ecmoban模板堂 --zhuo start
//        $sql = "delete from " . $ecs->table('merchants_documenttitle') . " where cat_id = '$cat_id'";
//        $db->query($sql);
//        //ecmoban模板堂 --zhuo end
//    } else {
//        make_json_error($cat_name . ' ' . $_LANG['cat_isleaf']);
//    }
//
//    $url = 'category.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
//
//    ecs_header("Location: $url\n");
//    exit;
//}
//
/*------------------------------------------------------ */
//-- 删除类目证件标题 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
//if ($_REQUEST['act'] == 'title_remove')
//{
//    check_authz_json('cat_manage');
//	
//	$dt_id   = intval($_GET['dt_id']);
//	$cat_id   = intval($_GET['cat_id']);
//	
//	$sql = "delete from " .$ecs->table('merchants_documenttitle'). " where dt_id = '$dt_id'";
//	$db->query($sql);
//	
//	$url = 'category.php?act=titleFileView&cat_id=' . $cat_id ;
//
//  ecs_header("Location: $url\n");
//   exit;
//}
//
/*------------------------------------------------------ */
//-- 删除分类 ajax实现删除分类后页面不刷新 //ecmoban模板堂 --kong
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'remove') 
{
    check_authz_json('cat_manage');
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => 0, 'massege' => '', 'level' => '');
    /* 初始化分类ID并取得分类名称 */
    $result['level'] = $_REQUEST['level'];
    $cat_id = intval($_GET['cat_id']);
    $result['cat_id'] = $cat_id;
    $cat_name = $db->getOne('SELECT cat_name FROM ' . $ecs->table('category') . " WHERE cat_id='$cat_id'");

    /* 当前分类下是否有子分类 */
    $cat_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('category') . " WHERE parent_id='$cat_id'");

    /* 当前分类下是否存在商品 */
    $goods_count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('goods') . " WHERE cat_id='$cat_id'");

    /* 如果不存在下级子分类和商品，则删除之 */
    if ($cat_count == 0 && $goods_count == 0) {
        /* 删除分类 */
        $sql = 'DELETE FROM ' . $ecs->table('category') . " WHERE cat_id = '$cat_id'";
        if ($db->query($sql)) {
            $db->query("DELETE FROM " . $ecs->table('nav') . "WHERE ctype = 'c' AND cid = '" . $cat_id . "' AND type = 'middle'");
            clear_cache_files();
            admin_log($cat_name, 'remove', 'category');
            $result['error'] = 1;
        }

        $sql = "delete from " . $ecs->table('merchants_documenttitle') . " where cat_id = '$cat_id'";
        $db->query($sql);
    } else {
        $result['error'] = 2;
        $result['massege'] = $cat_name . ' ' . $_LANG['cat_isleaf'];
    }
    die($json->encode($result));
}
/*------------------------------------------------------ */
//-- 删除类目证件标题 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'title_remove')
{
    check_authz_json('cat_manage');
    
    $dt_id   = intval($_GET['dt_id']);
    $cat_id   = intval($_GET['cat_id']);
    
    $sql = "delete from " .$ecs->table('merchants_documenttitle'). " where dt_id = '$dt_id'";
    $db->query($sql);
    
    $url = 'category_store.php?act=titleFileView&cat_id=' . $cat_id ;

    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */

/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('category'), $args, 'update', "cat_id='$cat_id'");
}


/**
 * 获取属性列表
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_attr_list()
{
    $sql = "SELECT a.attr_id, a.cat_id, a.attr_name ".
           " FROM " . $GLOBALS['ecs']->table('attribute'). " AS a,  ".
           $GLOBALS['ecs']->table('goods_type') . " AS c ".
           " WHERE  a.cat_id = c.cat_id AND c.enabled = 1 ".
           " ORDER BY a.cat_id , a.sort_order";

    $arr = $GLOBALS['db']->getAll($sql);

    $list = array();

    foreach ($arr as $val)
    {
        $list[$val['cat_id']][] = array($val['attr_id']=>$val['attr_name']);
    }

    return $list;
}

/**
 * 插入首页推荐扩展分类
 *
 * @access  public
 * @param   array   $recommend_type 推荐类型
 * @param   integer $cat_id     分类ID
 *
 * @return void
 */
function insert_cat_recommend($recommend_type, $cat_id)
{
    //检查分类是否为首页推荐
    if (!empty($recommend_type))
    {
        //取得之前的分类
        $recommend_res = $GLOBALS['db']->getAll("SELECT recommend_type FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
        if (empty($recommend_res))
        {
            foreach($recommend_type as $data)
            {
                $data = intval($data);
                $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") . "(cat_id, recommend_type) VALUES ('$cat_id', '$data')");
            }
        }
        else
        {
            $old_data = array();
            foreach($recommend_res as $data)
            {
                $old_data[] = $data['recommend_type'];
            }
            $delete_array = array_diff($old_data, $recommend_type);
            if (!empty($delete_array))
            {
                $GLOBALS['db']->query("DELETE FROM " . $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=$cat_id AND recommend_type " . db_create_in($delete_array));
            }
            $insert_array = array_diff($recommend_type, $old_data);
            if (!empty($insert_array))
            {
                foreach($insert_array as $data)
                {
                    $data = intval($data);
                    $GLOBALS['db']->query("INSERT INTO " . $GLOBALS['ecs']->table("cat_recommend") . "(cat_id, recommend_type) VALUES ('$cat_id', '$data')");
                }
            }
        }
    }
    else
    {
        $GLOBALS['db']->query("DELETE FROM ". $GLOBALS['ecs']->table("cat_recommend") . " WHERE cat_id=" . $cat_id);
    }
}

//ajax分类列表
function get_cat_level($parent_id = 0, $level = 0){
     
    $sql = "SELECT c.cat_id, c.cat_name, c.measure_unit, c.parent_id, c.is_show, c.show_in_nav, c.grade, c.sort_order " .
            " FROM " .$GLOBALS['ecs']->table('category'). " AS c WHERE c.parent_id = '$parent_id' " . 
            " order by c.sort_order, c.cat_id";
    $res = $GLOBALS['db']->getAll($sql);
    
    foreach ($res as $k => $row) {
        //ecmoban模板堂 --zhuo 查询服分类下子分类下的商品数量 start
        $cat_id_str = get_class_nav($res[$k]['cat_id']);
        $res[$k]['cat_child'] = substr($cat_id_str['catId'], 0, -1);
        if (empty($cat_id_str['catId'])) {
            $res[$k]['cat_child'] = substr($res[$k]['cat_id'], 0, -1);
        }

        $res[$k]['cat_child'] = isset($res[$k]['cat_child']) && !empty($res[$k]['cat_child']) ? get_del_str_comma($res[$k]['cat_child']) : '';

        if ($res[$k]['cat_child']) {
            $cat_in = " AND g.cat_id in(" . $res[$k]['cat_child'] . ")";
        }
        else {
            $cat_in = "";
        }

        $goodsNums = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('goods') . " AS g  WHERE g.is_delete = 0 " . $cat_in . $ruCat);

        $goods_ids = array();
        foreach ($goodsNums as $num_key => $num_val) {
            $goods_ids[] = $num_val['goods_id'];
        }

        $goodsCat = get_goodsCat_num($res[$k]['cat_child'], $goods_ids, $ruCat);

        $res[$k]['goods_num'] = count($goodsNums) + $goodsCat;

        $res[$k]['goodsCat'] = $goodsCat; //扩展商品数量
        $res[$k]['goodsNum'] = $goodsNum; //本身以及子分类的商品数量
        //ecmoban模板堂 --zhuo 查询服分类下子分类下的商品数量 end
        $res[$k]['level'] = $level;
    }

    return $res;
}
?>