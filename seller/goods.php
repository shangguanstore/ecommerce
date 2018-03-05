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
require_once(ROOT_PATH . '/' . SELLER_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
$exc = new exchange($ecs->table('goods'), $db, 'goods_id', 'goods_name');
$exc_extend = new exchange($ecs->table('goods_extend'), $db, 'goods_id', 'extend_id');
$smarty->assign('menus',$_SESSION['menus']);
$smarty->assign('action_type',"goods");

//ecmoban模板堂 --zhuo start
$adminru = get_admin_ru_id();
if($adminru['ru_id'] == 0){
        $smarty->assign('priv_ru',   1);
}else{
        $smarty->assign('priv_ru',   0);
}

$smarty->assign('review_goods',   $GLOBALS['_CFG']['review_goods']);
//ecmoban模板堂 --zhuo end

/*------------------------------------------------------ */
//-- 商品列表，商品回收站
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list' || $_REQUEST['act'] == 'trash')
{
    admin_priv('goods_manage');
	
	if($_REQUEST['act'] == 'list')
	{
		$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
		//页面分菜单 by wu start
		$tab_menu = array();
		$tab_menu[] = array('curr' => 1, 'text'=> $_LANG['01_goods_list'], 'href' => 'goods.php?act=list');
		$tab_menu[] = array('curr' => 0, 'text'=> $_LANG['50_virtual_card_list'], 'href' => 'goods.php?act=list&extension_code=virtual_card');
		//$tab_menu[] = array('curr' => 0, 'text'=> $_LANG['goods_search_pl'], 'href' => 'goods.php?act=search_goods');
		$smarty->assign('tab_menu', $tab_menu);
		//页面分菜单 by wu end			
	}	

    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $code   = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? (empty($_REQUEST['suppliers_id']) ? '' : trim($_REQUEST['suppliers_id'])) : '';
    $is_on_sale = isset($_REQUEST['is_on_sale']) ? ((empty($_REQUEST['is_on_sale']) && $_REQUEST['is_on_sale'] === 0) ? '' : trim($_REQUEST['is_on_sale'])) : '';

    $handler_list = array();
        $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'icon'=>'icon-credit-card');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'icon'=>'icon-plus-sign');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'icon'=>'icon-plus-sign');

    if ($_REQUEST['act'] == 'list' && isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
		
		$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '50_virtual_card_list'));
		//页面分菜单 by wu start
		$tab_menu = array();
		$tab_menu[] = array('curr' => 0, 'text'=> $_LANG['01_goods_list'], 'href' => 'goods.php?act=list');
		$tab_menu[] = array('curr' => 1, 'text'=> $_LANG['50_virtual_card_list'], 'href' => 'goods.php?act=list&extension_code=virtual_card');
		$smarty->assign('tab_menu', $tab_menu);
		//页面分菜单 by wu end		
    }

    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('is_on_sale', $is_on_sale);
    $smarty->assign('suppliers_id', $suppliers_id);
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);

    /* 模板赋值 */
    $goods_ur = array('' => $_LANG['01_goods_list'], 'virtual_card'=>$_LANG['50_virtual_card_list']);
    $ur_here = ($_REQUEST['act'] == 'list') ? $goods_ur[$code] : $_LANG['11_goods_trash'];
    $smarty->assign('ur_here', $ur_here);

    $action_link = ($_REQUEST['act'] == 'list') ? add_link($code) : array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']);
    $smarty->assign('action_link',  $action_link);
    
    //ecmoban模板堂 --zhuo start
    $action_link2 = ($_REQUEST['act'] == 'list') ? array('href' => 'goods.php?act=add_desc', 'text' => "商品统一详情") :  '';
    $smarty->assign('action_link2',  $action_link2);
    //ecmoban模板堂 --zhuo start

    $smarty->assign('code',     $code);
    //$smarty->assign('cat_list',     cat_list(0, $cat_id));
    //$smarty->assign('cat_list', cat_list_one());

    $smarty->assign('brand_list',   get_brand_list());
	//$smarty->assign('store_brand',   get_store_brand_list()); //商家品牌
    $smarty->assign('intro_list',   get_intro_list());
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('list_type',    $_REQUEST['act'] == 'list' ? 'goods' : 'trash');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

    $suppliers_list = suppliers_list_info(' is_check = 1 ');
    $suppliers_list_count = count($suppliers_list);
    $smarty->assign('suppliers_list', ($suppliers_list_count == 0 ? 0 : $suppliers_list)); // 取供货商列表

    $goods_list = goods_list($_REQUEST['act'] == 'list' ? 0 : 1, ($_REQUEST['act'] == 'list') ? (($code == '') ? 1 : 0) : -1);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('full_page',    1);
	
	//分页
	$page_count_arr = seller_page($goods_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	
    
    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);
    
    //$store_list = get_common_store_list();
    //$smarty->assign('store_list',        $store_list);
	
	$smarty->assign('nowTime', gmtime());
	
	//分类下拉列表 by wu
	//$smarty->assign('select_category_html',insert_select_category(0,0,0,'cat_id',1));	
    
    $smarty->assign('user_id', $adminru['ru_id']);
    set_default_filter(0, 0, $adminru['ru_id']);

    /* 显示商品列表页面 */
    assign_query_info();
    $htm_file = ($_REQUEST['act'] == 'list' ?
        'goods_list.dwt' : ($_REQUEST['act'] == 'trash' ? 'goods_trash.dwt' : 'group_list.dwt'));
    $smarty->display($htm_file);
}

/*------------------------------------------------------ */
//-- 添加新商品 编辑商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'copy')
{
    if ($adminru['ru_id'])
    {
        $seller_shop_cat = seller_shop_cat($adminru['ru_id']);
    }
    else
    {
        $seller_shop_cat = array();
    }

    if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'copy')
    {
        $smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '02_goods_add'));
    }
    elseif($_REQUEST['act'] == 'edit')
    {
        $smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
    }

    //微分销
    if (file_exists(MOBILE_DRP))
    {        
        if ($adminru['ru_id'] > 0)
        {
            $dis = is_distribution($adminru['ru_id']);
            $smarty->assign('is_dis', $dis);
        }

        if ($adminru['ru_id'] == 0)
        {
            $smarty->assign('is_dis', 1);
        }
    }
    //微分销

    include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
        
	
    $is_add = $_REQUEST['act'] == 'add'; // 添加还是编辑的标识
    $is_copy = $_REQUEST['act'] == 'copy'; //是否复制
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $code =='virtual_card' ? 'virtual_card': '';
	
	$properties = empty($_REQUEST['properties']) ? 0 : intval($_REQUEST['properties']);
	$smarty -> assign('properties',$properties);
	
	/*删除未绑定图片*/
        $res=$db->getAll(" SELECT img_url,thumb_url,img_original FROM".$ecs->table('goods_gallery')." WHERE (goods_id = 0 or goods_id = '') AND img_id".  db_create_in($_SESSION['thumb_img_id'.$_SESSION['seller_id']]));
        //删除图片
        if(!empty($res)){
            foreach($res as $k){
                if($k['img_url']){
                    @unlink('../' . $k['img_url']);
                }
                if($k['thumb_url']){
                    @unlink('../' . $k['thumb_url']);
                }
                if($k['img_original']){
                    @unlink('../' . $k['img_original']);
                }
            }
        }
	$db->query("DELETE FROM".$ecs->table("goods_gallery")." WHERE (goods_id = 0 or goods_id = '') AND img_id".  db_create_in($_SESSION['thumb_img_id'.$_SESSION['seller_id']]));
        unset($_SESSION['thumb_img_id'.$_SESSION['seller_id']]);//清楚临时$_COOKIE
	/*删除未绑定仓库 by kong*/
	$db->query("DELETE FROM".$ecs->table("warehouse_goods")." WHERE (goods_id = 0 or goods_id = '')");
        /*删除未绑定地区 by kong*/
	$db->query("DELETE FROM".$ecs->table("warehouse_area_goods")." WHERE (goods_id = 0 or goods_id = '')");
    if ($code == 'virtual_card')
    {
        admin_priv('virualcard'); // 检查权限
		$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '51_virtual_card_add'));
    }
    else
    {
        admin_priv('goods_manage'); // 检查权限
    }

    /* 供货商名 */
    $suppliers_list_name = suppliers_list_name();
    $suppliers_exists = 1;
    if (empty($suppliers_list_name))
    {
        $suppliers_exists = 0;
    }
    $smarty->assign('suppliers_exists', $suppliers_exists);
    $smarty->assign('suppliers_list_name', $suppliers_list_name);
    unset($suppliers_list_name, $suppliers_exists);

    /* 如果是安全模式，检查目录是否存在 */
    if (ini_get('safe_mode') == 1 && (!file_exists('../' . IMAGE_DIR . '/'.date('Ym')) || !is_dir('../' . IMAGE_DIR . '/'.date('Ym'))))
    {
        if (@!mkdir('../' . IMAGE_DIR . '/'.date('Ym'), 0777))
        {
            $warning = sprintf($_LANG['safe_mode_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
            $smarty->assign('warning', $warning);
        }
    }

    /* 如果目录存在但不可写，提示用户 */
    elseif (file_exists('../' . IMAGE_DIR . '/'.date('Ym')) && file_mode_info('../' . IMAGE_DIR . '/'.date('Ym')) < 2)
    {
        $warning = sprintf($_LANG['not_writable_warning'], '../' . IMAGE_DIR . '/'.date('Ym'));
        $smarty->assign('warning', $warning);
    }
	
    $adminru = get_admin_ru_id();
    
    $grade_rank = get_seller_grade_rank($adminru['ru_id']);    
    $smarty->assign('grade_rank',$grade_rank);
    $smarty->assign('integral_scale',$_CFG['integral_scale']);
    
    /* 取得商品信息 */

    $goods_id = isset($_REQUEST['goods_id']) && !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

    if ($is_add)
    {
        /*判断商家等级发布商品数量是否达到该等级上限 by kong grade*/
        if($adminru['ru_id'] > 0){
            /*获取商家等级封顶商品数*/
            if($grade_rank['goods_sun'] != -1){
                /*获取商家商品总数*/
                $sql = " SELECT COUNT(*) FROM".$ecs->table("goods")." WHERE user_id = '" .$adminru['ru_id']. "'";
                $goods_numer = $db->getOne($sql);
                
                if($goods_numer > $grade_rank['goods_sun']){
                    sys_msg($_LANG['on_goods_num']);
                    exit;
                }
            }
        }
        /* 默认值 */
        $last_choose = array(0, 0);
        if (!empty($_COOKIE['ECSCP']['last_choose']))
        {
            $last_choose = explode('|', $_COOKIE['ECSCP']['last_choose']);
        }
        $goods = array(
            'goods_id'      => 0,
            'goods_desc'    => '',
			'goods_shipai'    => '',
            //'cat_id'        => $last_choose[0],
			'cat_id'        => '0',
            'brand_id'      => $last_choose[1],
            'is_on_sale'    => '1',
            'is_alone_sale' => '1',
            'is_shipping' => '0',
            'other_cat'     => array(), // 扩展分类
            'goods_type'    => 0,       // 商品类型
            'shop_price'    => 0,
            'promote_price' => 0,
            'market_price'  => 0,
            'integral'      => 0,
            'goods_number'  => $_CFG['default_storage'],
            'warn_number'   => 1,
            'promote_start_date' => local_date($GLOBALS['_CFG']['time_format']),
            'promote_end_date'   => local_date($GLOBALS['_CFG']['time_format'], local_strtotime('+1 month')),
            'goods_weight'  => 0,
            'give_integral' => 0,
            'rank_integral' => 0,
            'user_cat'      => 0,
			'goods_extend'=>array('is_reality'=>0,'is_return'=>0,'is_fast'=>0)//by wang
        );

        if ($code != '')
        {
            $goods['goods_number'] = 0;
        }

        /* 关联商品 */
        $link_goods_list = array();
        $sql = "DELETE FROM " . $ecs->table('link_goods') .
                " WHERE (goods_id = 0 OR link_goods_id = 0)" .
                " AND admin_id = '$_SESSION[seller_id]'";
        $db->query($sql);

        /* 组合商品 */
        $group_goods_list = array();
        $sql = "DELETE FROM " . $ecs->table('group_goods') .
                " WHERE parent_id = 0 AND admin_id = '$_SESSION[seller_id]'";
        $db->query($sql);

        /* 关联文章 */
        $goods_article_list = array();
        $sql = "DELETE FROM " . $ecs->table('goods_article') .
                " WHERE goods_id = 0 AND admin_id = '$_SESSION[seller_id]'";
        $db->query($sql);

        /* 属性 */
        $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = 0";
        $db->query($sql);

        /* 图片列表 */
        $img_list = array();
    }
    else
    {
        /* 商品信息 */
        $goods = get_goods_info($goods_id);

        /* 虚拟卡商品复制时, 将其库存置为0*/
        if ($is_copy && $code != '')
        {
            $goods['goods_number'] = 0;
        }

        if (empty($goods) === true)
        {
            /* 默认值 */
            $goods = array(
                'goods_id'      => 0,
                'goods_desc'    => '',
				'goods_shipai'    => '',
                'cat_id'        => 0,
				
                'is_on_sale'    => '1',
                'is_alone_sale' => '1',
                'is_shipping' => '0',
                'other_cat'     => array(), // 扩展分类
                'goods_type'    => 0,       // 商品类型
                'shop_price'    => 0,
                'promote_price' => 0,
                'market_price'  => 0,
                'integral'      => 0,
                'goods_number'  => 1,
                'warn_number'   => 1,
                'promote_start_date' => local_date($GLOBALS['_CFG']['time_format']),
                'promote_end_date'   => local_date($GLOBALS['_CFG']['time_format'], local_strtotime('+1 month')),
                'goods_weight'  => 0,
                'give_integral' => 0,
                'rank_integral' => 0,
                'user_cat'      => 0,
				'goods_extend'=>array('is_reality'=>0,'is_return'=>0,'is_fast'=>0)
            );
        }
        
        $goods['goods_extend'] = get_goods_extend($goods['goods_id']);

        /* 获取商品类型存在规格的类型 */
        $specifications = get_goods_type_specifications();
        $goods['specifications_id'] = $specifications[$goods['goods_type']];
        $_attribute = get_goods_specifications_list($goods['goods_id']);
        $goods['_attribute'] = empty($_attribute) ? '' : 1;

        /* 根据商品重量的单位重新计算 */
        if ($goods['goods_weight'] > 0)
        {
            $goods['goods_weight_by_unit'] = ($goods['goods_weight'] >= 1) ? $goods['goods_weight'] : ($goods['goods_weight'] / 0.001);
        }

        if (!empty($goods['goods_brief']))
        {
            //$goods['goods_brief'] = trim_right($goods['goods_brief']);
            $goods['goods_brief'] = $goods['goods_brief'];
        }
        if (!empty($goods['keywords']))
        {
            //$goods['keywords']    = trim_right($goods['keywords']);
            $goods['keywords']    = $goods['keywords'];
        }
		
		//ecmoban模板堂 --zhuo start 限购
		/* 如果不是限购，处理限购日期 */
		if (isset($goods['is_xiangou']) && $goods['is_xiangou'] == '0'){
            unset($goods['xiangou_start_date']);
            unset($goods['xiangou_end_date']);
        }
        else{
            $goods['xiangou_start_date'] = local_date('Y-m-d H:i:s', $goods['xiangou_start_date']);
            $goods['xiangou_end_date'] = local_date('Y-m-d H:i:s', $goods['xiangou_end_date']);
        }
		//ecmoban模板堂 --zhuo end 限购
		
		 //@author guan 晒单评论 start
        if (!empty($goods['goods_product_tag']))
        {
        	$goods['goods_product_tag'] = $goods['goods_product_tag'];
        }
        //@author guan  晒单评论 end

        /* 如果不是促销，处理促销日期 */
        if (isset($goods['is_promote']) && $goods['is_promote'] == '0')
        {
            unset($goods['promote_start_date']);
            unset($goods['promote_end_date']);
        }
        else
        {
            $goods['promote_start_date'] = local_date($GLOBALS['_CFG']['time_format'], $goods['promote_start_date']);
            $goods['promote_end_date'] = local_date($GLOBALS['_CFG']['time_format'], $goods['promote_end_date']);
        }

        // 扩展分类
        $other_cat_list1 = array();
        $sql = "SELECT ga.cat_id FROM " . $ecs->table('goods_cat') . " as ga  WHERE ga.goods_id = '" . intval($goods_id) . "'";
        $other_cat1 = $db->getCol($sql);
        $other_catids = '';

        foreach ($other_cat1 as $key => $val )
        {
            $other_catids .= $val . ',';
        }

        $other_catids = substr($other_catids, 0, -1);
        $smarty->assign('other_catids', $other_catids);

        /* 如果是复制商品，处理 */
        if ($_REQUEST['act'] == 'copy')
        {
            // 商品信息
            $goods['goods_id'] = 0;
            $goods['goods_sn'] = '';
            $goods['goods_name'] = '';
            $goods['goods_img'] = '';
            $goods['goods_thumb'] = '';
            $goods['original_img'] = '';

            // 扩展分类不变

            // 关联商品
            $sql = "DELETE FROM " . $ecs->table('link_goods') .
                    " WHERE (goods_id = 0 OR link_goods_id = 0)" .
                    " AND admin_id = '$_SESSION[seller_id]'";
            $db->query($sql);

            $sql = "SELECT '0' AS goods_id, link_goods_id, is_double, '$_SESSION[seller_id]' AS admin_id" .
                    " FROM " . $ecs->table('link_goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('link_goods'), $row, 'INSERT');
            }

            $sql = "SELECT goods_id, '0' AS link_goods_id, is_double, '$_SESSION[seller_id]' AS admin_id" .
                    " FROM " . $ecs->table('link_goods') .
                    " WHERE link_goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('link_goods'), $row, 'INSERT');
            }

            // 配件
            $sql = "DELETE FROM " . $ecs->table('group_goods') .
                    " WHERE parent_id = 0 AND admin_id = '$_SESSION[seller_id]'";
            $db->query($sql);

            $sql = "SELECT 0 AS parent_id, goods_id, goods_price, '$_SESSION[seller_id]' AS admin_id " .
                    "FROM " . $ecs->table('group_goods') .
                    " WHERE parent_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('group_goods'), $row, 'INSERT');
            }

            // 关联文章
            $sql = "DELETE FROM " . $ecs->table('goods_article') .
                    " WHERE goods_id = 0 AND admin_id = '$_SESSION[seller_id]'";
            $db->query($sql);

            $sql = "SELECT 0 AS goods_id, article_id, '$_SESSION[seller_id]' AS admin_id " .
                    "FROM " . $ecs->table('goods_article') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('goods_article'), $row, 'INSERT');
            }

            // 图片不变

            // 商品属性
            $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = 0";
            $db->query($sql);

            $sql = "SELECT 0 AS goods_id, attr_id, attr_value, attr_price " .
                    "FROM " . $ecs->table('goods_attr') .
                    " WHERE goods_id = '$_REQUEST[goods_id]' ";
            $res = $db->query($sql);
            while ($row = $db->fetchRow($res))
            {
                $db->autoExecute($ecs->table('goods_attr'), addslashes_deep($row), 'INSERT');
            }
        }

        // 扩展分类			
        $other_cat_list1 = array();
        $sql = "SELECT ga.cat_id FROM " . $ecs->table('goods_cat') . " as ga " .
				" WHERE ga.goods_id = '$_REQUEST[goods_id]'";
        $goods['other_cat1'] = $db->getCol($sql);

        foreach ($goods['other_cat1'] AS $cat_id)
        {
            $other_cat_list1[$cat_id] = cat_list($cat_id);
        }

        $smarty->assign('other_cat_list1', $other_cat_list1);		
        $smarty->assign('other_cat_list2', $other_cat_list2);

        $link_goods_list    = get_linked_goods($goods['goods_id']); // 关联商品
        $group_goods_list   = get_group_goods($goods['goods_id']); // 配件
        $goods_article_list = get_goods_articles($goods['goods_id']);   // 关联文章
        if (is_array($group_goods_list))
        { 
            //by mike add
            foreach($group_goods_list as $k=>$val)
            {
                $group_goods_list[$k]['goods_name'] = '['.$val['group_name'].']'.$val['goods_name'];	
            }
        }

        /* 商品图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 10) && !empty($goods['original_img']))
        {
            $goods['goods_img'] = get_image_path($goods_id, $goods['goods_img']);
            $goods['goods_thumb'] = get_image_path($goods_id, $goods['goods_thumb'], true);
        }

        /* 图片列表 */
        $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";

        $img_list = $db->getAll($sql);

        /* 格式化相册图片路径 */
        if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0))
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
            }
        }
        else
        {
            foreach ($img_list as $key => $gallery_img)
            {
                $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
        }
          $img_desc=array();
			foreach($img_list as $k=>$v){
				$img_desc[]=$v['img_desc'];
			}
			@$img_default = min($img_desc);
			$min_img_id=$db->getOne(" SELECT img_id   FROM ".$ecs->table('goods_gallery')." WHERE goods_id = '$goods_id' AND img_desc = '$img_default' ORDER BY img_desc   LIMIT 1");
			$smarty->assign('min_img_id',$min_img_id);
		
    }
    //ecmoban模板堂 --zhuo start
    if (empty($goods['user_id']))
    {
        $goods['user_id'] = $adminru['ru_id'];
    }

    $warehouse_list = get_warehouse_list();
    $smarty->assign('warehouse_list', $warehouse_list);
    $smarty->assign('count_warehouse', count($warehouse_list)); 

    $warehouse_goods_list = get_warehouse_goods_list($goods_id);
    $smarty->assign('warehouse_goods_list', $warehouse_goods_list);   

    $warehouse_area_goods_list = get_warehouse_area_goods_list($goods_id);
    $smarty->assign('warehouse_area_goods_list', $warehouse_area_goods_list);  

    $area_count = get_all_warehouse_area_count();
    $smarty->assign('area_count', $area_count); 

    $areaRegion_list = get_areaRegion_list(); 
    $smarty->assign('areaRegion_list', $areaRegion_list); 
    $smarty->assign('area_goods_list', get_area_goods($goods_id)); 
    
    $consumption = get_goods_con_list($goods_id, 'goods_consumption'); //满减订单金额
    $smarty->assign('consumption', $consumption); 
    $conshipping = get_goods_con_list($goods_id, 'goods_conshipping', 1); //满减运费
    $smarty->assign('conshipping', $conshipping); 
	
    $group_goods = get_cfg_group_goods();
    $smarty->assign('group_list', $group_goods);
    
    $smarty->assign('ru_id',     $adminru['ru_id']);
    //ecmoban模板堂 --zhuo end


    /* 拆分商品名称样式 */
    $goods_name_style = explode('+', empty($goods['goods_name_style']) ? '+' : $goods['goods_name_style']);

    /* 创建 html editor */
    create_html_editor('goods_desc', $goods['goods_desc']);
    create_html_editor2('goods_shipai', 'goods_shipai',$goods['goods_shipai']);


    /*  @author-bylu 处理分期数据 start  */
        if(!empty($goods['stages'])){
            $stages=unserialize($goods['stages']);
        }
    /*  @author-bylu 处理分期数据 end  */
    
    if($goods['is_promote']){ //促销
        $goods['use_give_integral'] = $grade_rank['give_integral'] * $goods['promote_price'];
        $goods['use_rank_integral'] = $grade_rank['rank_integral'] * $goods['promote_price'];
        $goods['use_pay_integral'] = $goods['promote_price'] / 100 * $_CFG['integral_scale'] * $grade_rank['pay_integral'];        
    }else{ //本店价
        $goods['use_give_integral'] = $grade_rank['give_integral'] * $goods['shop_price'];
        $goods['use_rank_integral'] = $grade_rank['rank_integral'] * $goods['shop_price'];
        $goods['use_pay_integral'] = $goods['shop_price'] / 100 * $_CFG['integral_scale'] * $grade_rank['pay_integral']; 
    }

    /* 模板赋值 */
    $smarty->assign('code',    $code);
    $smarty->assign('ur_here', $is_add ? (empty($code) ? $_LANG['02_goods_add'] : $_LANG['51_virtual_card_add']) : ($_REQUEST['act'] == 'edit' ? $_LANG['edit_goods'] : $_LANG['copy_goods']));
    $smarty->assign('action_link', list_link($is_add, $code));
    $smarty->assign('goods', $goods);
    $smarty->assign('stages', $stages);//分期期数数据 bylu;
    $smarty->assign('goods_name_color', $goods_name_style[0]);
    $smarty->assign('goods_name_style', $goods_name_style[1]);

    if ($is_add)
    {
        $smarty->assign('cat_list', cat_list_one(0, 0, $seller_shop_cat));    
    }
    else
    {
        $smarty->assign('cat_list', cat_list_one($goods['cat_id'], 0, $seller_shop_cat)); //ecmoban模板堂 --zhuo    
    }

    $smarty->assign('cat_list_new', cat_list($goods['cat_id']));
    $smarty->assign('brand_list', get_brand_list($goods_id));
    $smarty->assign('unit_list', get_unit_list());
    $smarty->assign('user_rank_list', get_user_rank_list());
    $smarty->assign('weight_unit', $is_add ? '1' : ($goods['goods_weight'] >= 1 ? '1' : '0.001'));
    $smarty->assign('cfg', $_CFG);
    $smarty->assign('form_act', $is_add ? 'insert' : ($_REQUEST['act'] == 'edit' ? 'update' : 'insert'));
    if ($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit') 
    {
        $smarty->assign('is_add', true);
    }
    if(!$is_add)
    {
        $smarty->assign('member_price_list', get_member_price_list($goods_id));
    }
    $smarty->assign('link_goods_list', $link_goods_list);
    $smarty->assign('group_goods_list', $group_goods_list);
    $smarty->assign('goods_article_list', $goods_article_list);
    $smarty->assign('img_list', $img_list);
    $smarty->assign('goods_type_list', goods_type_list($goods['goods_type'], $goods['goods_id']));
    $smarty->assign('gd', gd_version());
    $smarty->assign('thumb_width', $_CFG['thumb_width']);
    $smarty->assign('thumb_height', $_CFG['thumb_height']);
    $smarty->assign('goods_attr_html', build_attr_html($goods['goods_type'], $goods['goods_id']));
    $volume_price_list = '';
    if(isset($goods_id))
    {
        $volume_price_list = get_volume_price_list($goods_id);
    }
    if (empty($volume_price_list))
    {
        $volume_price_list = array('0'=>array('number'=>'','price'=>''));
    }
    $smarty->assign('volume_price_list', $volume_price_list);
	

    $cat_info = get_seller_cat_info($goods['user_cat']);

    get_add_edit_goods_cat_list($goods_id, $goods['cat_id'], 'category', '', $goods['user_id'], $seller_shop_cat);
    get_add_edit_goods_cat_list($goods_id, $goods['user_cat'], 'merchants_category', 'seller_', $goods['user_id']);

    if (file_exists(MOBILE_DRP))
    {
        $smarty->assign('is_dir', 1);
    }
    else
    {
        $smarty->assign('is_dir', 0);
    }

    /* 显示商品信息页面 */
    assign_query_info();
    $smarty->display('goods_info.dwt');
}

elseif ($_REQUEST['act'] == 'get_select_category_pro')
{
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $cat_level = empty($_REQUEST['cat_level']) ? 0 : intval($_REQUEST['cat_level']);

    $result = array('error' => 0, 'message' => '', 'content' => '');
    $goods = get_admin_goods_info($goods_id, array('user_id'));
    $seller_shop_cat = seller_shop_cat($goods['user_id']);

    $smarty->assign('cat_id', $cat_id);
    $smarty->assign('cat_level', $cat_level + 1);
    $smarty->assign('category_list', get_category_list($cat_id, 2, $seller_shop_cat, $goods['user_id'], $cat_level + 1));
    $result['content'] = $smarty->fetch('templates/library/get_select_category.lbi');
    die(json_encode($result));
}
elseif ($_REQUEST['act'] == 'set_common_category_pro')
{
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $result = array('error' => 0, 'message' => '', 'content' => '');
    $level_limit = 3;
    $category_level = array();
    $parent_cat_list = get_select_category($cat_id, 1, true);

    for ($i = 1; $i <= $level_limit; $i++)
    {
        $category_list = array();

        if (isset($parent_cat_list[$i]))
        {
            $category_list = get_category_list($parent_cat_list[$i]);
        }
        elseif ($i == 1)
        {
            $category_list = get_category_list();
        }

        $smarty->assign('cat_level', $i);
        $smarty->assign('category_list', $category_list);
        $category_level[$i] = $smarty->fetch('templates/library/get_select_category.lbi');
    }

    $smarty->assign('cat_id', $cat_id);
    $result['content'] = $category_level;
    die(json_encode($result));
}
elseif ($_REQUEST['act'] == 'deal_extension_category')
{
    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
    $type = empty($_REQUEST['type']) ? '' : trim($_REQUEST['type']);
    $other_catids = empty($_REQUEST['other_catids']) ? '' : trim($_REQUEST['other_catids']);

    $result = array('error' => 0, 'message' => '', 'content' => '');

    if ($type == 'add')
    {
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_cat') . " (goods_id, cat_id) VALUES ('$goods_id', '$cat_id')";
        $GLOBALS['db']->query($sql);

        if ($other_catids == '')
        {
            $other_catids = $cat_id;
        }
        else
        {
            $other_catids = $other_catids . ',' . $cat_id;
        }
    }
    elseif ($type == 'delete')
    {
        $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_cat') . " WHERE goods_id = '$goods_id' AND cat_id = '$cat_id' ";
        $GLOBALS['db']->query($sql);
        $other_catids = str_replace(',' . $cat_id, '', $other_catids);
    }

    $result['content'] = $other_catids;
    die(json_encode($result));
}
elseif ($_REQUEST['act'] == 'search_brand') 
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $search_keyword   = trim($_GET['search_keyword']);
    $ru_id   = intval($_GET['ru_id']);
	$goods_id = intval($_GET['goods_id']);
    if($ru_id > 0)
    {
        $sql = 'SELECT bid, brandName FROM ' . $GLOBALS['ecs']->table('merchants_shop_brand') . " where brandName LIKE '%$search_keyword%' AND user_id = '$ru_id' AND audit_status = 1 ORDER BY bid ASC";
        $res = $GLOBALS['db']->getAll($sql);

        $brand_list = array();
        foreach ($res AS $row)
        {
            $link_brand = get_link_brand_list($row['bid'], 3);
            $brand_list[$row['bid']] = addslashes($row['brandName']);
        }
    }
    else
    {
        if(!is_mer($goods_id)){
			$sql = 'SELECT brand_id, brand_name FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_name LIKE '%$search_keyword%' ORDER BY sort_order";
			$res = $GLOBALS['db']->getAll($sql);
			
			$brand_list = array();
			foreach ($res AS $row)
			{
					$brand_list[$row['brand_id']] = addslashes($row['brand_name']);
			}
		}else{
			$user_id = is_mer($goods_id);
			$sql = 'SELECT bid, brandName FROM ' . $GLOBALS['ecs']->table('merchants_shop_brand') . " where brandName LIKE '%$search_keyword%' AND user_id = '$user_id' AND audit_status = 1 ORDER BY bid ASC";
			$res = $GLOBALS['db']->getAll($sql);

			$brand_list = array();
			foreach ($res AS $row)
			{
				$link_brand = get_link_brand_list($row['bid'], 3);
				$brand_list[$row['bid']] = addslashes($row['brandName']);
			}			
		}
    }
    
    $option = '<option value="0">请选择...</option>';
    foreach ($brand_list as $key => $value)
    {
        $option .= "<option value='$key'>$value</option>";
    }
    exit($json->encode($option));
}

/*------------------------------------------------------ */
//-- 插入关联商品描述，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_desc')
{
    
    admin_priv('goods_manage');
	
	$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
	$smarty->assign('ur_here',$_LANG['same_goods_desc']);
    
    $action_link = array('href' => 'goods.php?act=list', 'text' => "商品列表");
    $smarty->assign('action_link',  $action_link);
    
    $sql = "delete from " .$ecs->table('link_desc_temporary'). " where 1";
    $db->query($sql);
    
    //创建编辑器
    create_html_editor2('goods_desc', 'goods_desc', '');
    
    $sql = "select id, goods_id, desc_name, goods_desc from " .$GLOBALS['ecs']->table('link_goods_desc'). " where 1";
    $desc_list = $db->getAll($sql);
    $seller_shop_cat = seller_shop_cat($adminru['ru_id']);
    $cat_list = cat_list_one(0, 0, $seller_shop_cat);
    
    // $smarty->assign('cat_list',     cat_list(0, $cat_id));
    $smarty->assign('cat_list', $cat_list);
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('form_act',   'insert_link_desc');
    $smarty->assign('desc_list',   $desc_list);
    
    /* 显示商品信息页面 */
    assign_query_info();
    $smarty->display('goods_desc.dwt');
}

/*------------------------------------------------------ */
//-- 插入关联商品描述数据，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_link_desc')
{
    admin_priv('goods_manage');
    
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
    
    $sql = "delete from " .$ecs->table('link_desc_temporary'). " where 1";
    $db->query($sql);
    
    $action_link = array('href' => 'goods.php?act=add_desc', 'text' => $_LANG['go_back']);
    $smarty->assign('action_link',  $action_link);
    $action_link2 = array('href' => 'goods.php?act=list', 'text' => "商品列表");
    $smarty->assign('action_link2',  $action_link2);
 
    $other = array( 'id', 'desc_name', 'goods_desc');
    $goods_desc = get_table_date('link_goods_desc', "id='$id'", $other);
    
    $link_goods_list = get_linked_goods_desc($id);
    //创建编辑器
    create_html_editor2('goods_desc', 'goods_desc', $goods_desc['goods_desc']);
    
    $smarty->assign('goods',   $goods_desc);
    $smarty->assign('link_goods_list',   $link_goods_list);
    $seller_shop_cat = seller_shop_cat($adminru['ru_id']);
    $cat_list = cat_list_one(0, 0, $seller_shop_cat);
    $smarty->assign('cat_list',     $cat_list);
    $smarty->assign('brand_list',   get_brand_list());
    $smarty->assign('form_act',   'update_link_desc');
    
    /* 显示商品信息页面 */
    assign_query_info();
    $smarty->display('goods_desc.dwt');
}

/*------------------------------------------------------ */
//-- 插入关联商品描述，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_desc')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $linked_array   = $json->decode($_GET['add_ids']);
    $linked_goods   = $json->decode($_GET['JSON']);
    $id = $linked_goods[0];
    
    get_add_edit_link_desc($linked_array, 0, $id);
    $linked_goods   = get_linked_goods_desc();
    
    $options        = array();
    foreach ($linked_goods AS $val)
    {
        $options[] = array('value'  => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 删除关联商品描述，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_link_desc')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    check_authz_json('goods_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $linked_goods   = $json->decode($_GET['JSON']);
    $id       = $linked_goods[0];
    
    get_add_edit_link_desc($drop_goods, 1, $id);
    $linked_goods   = get_linked_goods_desc();
    
    $options      = array();
    foreach ($linked_goods AS $val)
    {
        $options[] = array(
                        'value' => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }
    
    if(empty($linked_goods)){
        $sql = "delete from " .$ecs->table('link_desc_temporary'). " where 1";
        $db->query($sql); 
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 插入关联商品描述数据，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_link_desc' || $_REQUEST['act'] == 'update_link_desc')
{
    $desc_name = !empty($_REQUEST['desc_name']) ? trim($_REQUEST['desc_name']) : '';
    $goods_desc = !empty($_REQUEST['goods_desc']) ? $_REQUEST['goods_desc'] : '';
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    
    $sql = "select goods_id from " .$GLOBALS['ecs']->table('link_desc_temporary'). " where 1";
    $goods_id = $GLOBALS['db']->getOne($sql);
    
    $other = array(
        'goods_id' => $goods_id,
        'desc_name' => $desc_name,
        'goods_desc' => $goods_desc
    );
    
    if(!empty($desc_name)){
        
        $sql = "delete from " .$GLOBALS['ecs']->table('link_desc_goodsid'). " where d_id = '$id'";
        $GLOBALS['db']->query($sql);
        
        if($id > 0){
           $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('link_goods_desc'), $other, "UPDATE", "id = '$id'");
           $link_cnt = "编辑成功"; 
        }else{
           $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('link_goods_desc'), $other, "INSERT");
           $id = $GLOBALS['db']->insert_id();
           $link_cnt = "添加成功"; 
        }
        
    }else{
        $link_cnt = "描述名称不能为空";
    }
   
    if(!empty($goods_id)){
        get_add_desc_goodsId($goods_id, $id);
    }

    if($id > 0){
        $link[0] = array('text' => $_LANG['go_back'], 'href' => "goods.php?act=edit_link_desc&id=".$id);
    }
    
    $link[1] = array('text' => "添加关联商品描述", 'href' => "goods.php?act=add_desc");
    $link[2] = array('text' => $_LANG['01_goods_list'], 'href' => 'goods.php?act=list');
    sys_msg($link_cnt, 0, $link);
}

/*------------------------------------------------------ */
//-- 插入关联商品描述数据，多商品共同描述内容 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'del_link_desc')
{
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    
    $sql = "delete from " .$ecs->table('link_goods_desc'). " where id = '$id'";
    $db->query($sql);
    
    $link[0] = array('text' => "添加关联商品描述", 'href' => "goods.php?act=add_desc");
    $link[1] = array('text' => $_LANG['01_goods_list'], 'href' => 'goods.php?act=list');
    sys_msg("删除成功", 0, $link);
}

/*------------------------------------------------------ */
//-- 插入商品 更新商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    unset($_SESSION['thumb_img_id'.$_SESSION['seller_id']]);//清楚临时$_COOKIE
    /* 是否处理缩略图 */
    $proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
    if ($code == 'virtual_card')
    {
        admin_priv('virualcard'); // 检查权限
    }
    else
    {
        admin_priv('goods_manage'); // 检查权限
    }

    /* 检查货号是否重复 */
    if ($_POST['goods_sn'])
    {
        $sql = "SELECT COUNT(*) FROM " . $ecs->table('goods') .
                " WHERE goods_sn = '$_POST[goods_sn]' AND is_delete = 0 AND goods_id <> '$_POST[goods_id]'";
        if ($db->getOne($sql) > 0)
        {
            sys_msg($_LANG['goods_sn_exists'], 1, array(), false);
        }
    }

    /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
    if (isset($_FILES['goods_img']['error'])) // php 4.2 版本才支持 error
    {
        // 最大上传文件大小
        $php_maxsize = ini_get('upload_max_filesize');
        $htm_maxsize = '2M';

        // 商品图片
        if ($_FILES['goods_img']['error'] == 0)
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {
                sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
            }
        }
        elseif ($_FILES['goods_img']['error'] == 1)
        {
            sys_msg(sprintf($_LANG['goods_img_too_big'], $php_maxsize), 1, array(), false);
        }
        elseif ($_FILES['goods_img']['error'] == 2)
        {
            sys_msg(sprintf($_LANG['goods_img_too_big'], $htm_maxsize), 1, array(), false);
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['error'] == 0)
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
                }
            }
            elseif ($_FILES['goods_thumb']['error'] == 1)
            {
                sys_msg(sprintf($_LANG['goods_thumb_too_big'], $php_maxsize), 1, array(), false);
            }
            elseif ($_FILES['goods_thumb']['error'] == 2)
            {
                sys_msg(sprintf($_LANG['goods_thumb_too_big'], $htm_maxsize), 1, array(), false);
            }
        }

        // 相册图片
//        foreach ($_FILES['img_url']['error'] AS $key => $value)
//        {
//            if ($value == 0)
//            {
//                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
//                {
//                    sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
//                }
//            }
//            elseif ($value == 1)
//            {
//                sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize), 1, array(), false);
//            }
//            elseif ($_FILES['img_url']['error'] == 2)
//            {
//                sys_msg(sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize), 1, array(), false);
//            }
//        }
    }
    /* 4.1版本 */
    else
    {
        // 商品图片
        if ($_FILES['goods_img']['tmp_name'] != 'none')
        {
            if (!$image->check_img_type($_FILES['goods_img']['type']))
            {

                sys_msg($_LANG['invalid_goods_img'], 1, array(), false);
            }
        }

        // 商品缩略图
        if (isset($_FILES['goods_thumb']))
        {
            if ($_FILES['goods_thumb']['tmp_name'] != 'none')
            {
                if (!$image->check_img_type($_FILES['goods_thumb']['type']))
                {
                    sys_msg($_LANG['invalid_goods_thumb'], 1, array(), false);
                }
            }
        }

        // 相册图片
//        foreach ($_FILES['img_url']['tmp_name'] AS $key => $value)
//        {
//            if ($value != 'none')
//            {
//                if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
//                {
//                    sys_msg(sprintf($_LANG['invalid_img_url'], $key + 1), 1, array(), false);
//                }
//            }
//        }
    }

    /* 插入还是更新的标识 */
    $is_insert = $_REQUEST['act'] == 'insert';

    /* 处理商品图片 */
    $goods_img        = '';  // 初始化商品图片
    $goods_thumb      = '';  // 初始化商品缩略图
    $original_img     = '';  // 初始化原始图片
    $old_original_img = '';  // 初始化原始图片旧图

    // 如果上传了商品图片，相应处理
    if (($_FILES['goods_img']['tmp_name'] != '' && $_FILES['goods_img']['tmp_name'] != 'none') or (($_POST['goods_img_url'] != $_LANG['lab_picture_url'] && $_POST['goods_img_url'] != 'http://') && $is_url_goods_img = 1))
    {
        if ($_REQUEST['goods_id'] > 0)
        {
            /* 删除原来的图片文件 */
            $sql = "SELECT goods_thumb, goods_img, original_img " .
                    " FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
            $row = $db->getRow($sql);
            if ($row['goods_thumb'] != '' && is_file('../' . $row['goods_thumb']))
            {
                @unlink('../' . $row['goods_thumb']);
            }
            if ($row['goods_img'] != '' && is_file('../' . $row['goods_img']))
            {
                @unlink('../' . $row['goods_img']);
            }
            if ($row['original_img'] != '' && is_file('../' . $row['original_img']))
            {
                /* 先不处理，以防止程序中途出错停止 */
                //$old_original_img = $row['original_img']; //记录旧图路径
            }
            /* 清除原来商品图片 */
            if ($proc_thumb === false)
            {
                get_image_path($_REQUEST[goods_id], $row['goods_img'], false, 'goods', true);
                get_image_path($_REQUEST[goods_id], $row['goods_thumb'], true, 'goods', true);
            }
        }

        if (empty($is_url_goods_img))
        {
            $original_img   = $image->upload_image($_FILES['goods_img']); // 原始图片
        }
        elseif (copy(trim($_POST['goods_img_url']), ROOT_PATH . 'temp/' . basename($_POST['goods_img_url'])))
        {
            $original_img = 'temp/' . basename($_POST['goods_img_url']);
        }

        if ($original_img === false)
        {
            sys_msg($image->error_msg(), 1, array(), false);
        }
        $goods_img      = $original_img;   // 商品图片

        /* 复制一份相册图片 */
        /* 添加判断是否自动生成相册图片 */
        if ($_CFG['auto_generate_gallery'])
        {
            $img        = $original_img;   // 相册图片
            $pos        = strpos(basename($img), '.');
            $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
            if (!copy('../' . $img, '../' . $newname))
            {
                sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
            }
            $img        = $newname;

            $gallery_img    = $img;
            $gallery_thumb  = $img;
        }

        // 如果系统支持GD，缩放商品图片，且给商品图片和相册图片加水印
        if ($proc_thumb && $image->gd_version() > 0 && $image->check_img_function($_FILES['goods_img']['type']) || $is_url_goods_img)
        {

            if (empty($is_url_goods_img))
            {
                // 如果设置大小不为0，缩放图片
                if ($_CFG['image_width'] != 0 || $_CFG['image_height'] != 0)
                {
                    $goods_img = $image->make_thumb('../'. $goods_img , $GLOBALS['_CFG']['image_width'],  $GLOBALS['_CFG']['image_height']);
                    if ($goods_img === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                }

                /* 添加判断是否自动生成相册图片 */
                if ($_CFG['auto_generate_gallery'])
                {
                    $newname    = dirname($img) . '/' . $image->random_filename() . substr(basename($img), $pos);
                    if (!copy('../' . $img, '../' . $newname))
                    {
                        sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
                    }
                    $gallery_img        = $newname;
                }

                // 加水印
                if (intval($_CFG['watermark_place']) > 0 && !empty($GLOBALS['_CFG']['watermark']))
                {
                    if ($image->add_watermark('../'.$goods_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                    /* 添加判断是否自动生成相册图片 */
                    if ($_CFG['auto_generate_gallery'])
                    {
                        if ($image->add_watermark('../'. $gallery_img,'',$GLOBALS['_CFG']['watermark'], $GLOBALS['_CFG']['watermark_place'], $GLOBALS['_CFG']['watermark_alpha']) === false)
                        {
                            sys_msg($image->error_msg(), 1, array(), false);
                        }
                    }
                }
            }

            // 相册缩略图
            /* 添加判断是否自动生成相册图片 */
            if ($_CFG['auto_generate_gallery'])
            {
                if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
                {
                    $gallery_thumb = $image->make_thumb('../' . $img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
                    if ($gallery_thumb === false)
                    {
                        sys_msg($image->error_msg(), 1, array(), false);
                    }
                }
            }
        }
        /* 取消该原图复制流程 */
        // else
        // {
        //     /* 复制一份原图 */
        //     $pos        = strpos(basename($img), '.');
        //     $gallery_img = dirname($img) . '/' . $image->random_filename() . // substr(basename($img), $pos);
        //     if (!copy('../' . $img, '../' . $gallery_img))
        //     {
        //         sys_msg('fail to copy file: ' . realpath('../' . $img), 1, array(), false);
        //     }
        //     $gallery_thumb = '';
        // }
    }


    // 是否上传商品缩略图
    if (isset($_FILES['goods_thumb']) && $_FILES['goods_thumb']['tmp_name'] != '' &&
        isset($_FILES['goods_thumb']['tmp_name']) &&$_FILES['goods_thumb']['tmp_name'] != 'none')
    {
        // 上传了，直接使用，原始大小
        $goods_thumb = $image->upload_image($_FILES['goods_thumb']);
        if ($goods_thumb === false)
        {
            sys_msg($image->error_msg(), 1, array(), false);
        }
    }
    else
    {
        // 未上传，如果自动选择生成，且上传了商品图片，生成所略图
        if ($proc_thumb && isset($_POST['auto_thumb']) && !empty($original_img))
        {
            // 如果设置缩略图大小不为0，生成缩略图
            if ($_CFG['thumb_width'] != 0 || $_CFG['thumb_height'] != 0)
            {
                $goods_thumb = $image->make_thumb('../' . $original_img, $GLOBALS['_CFG']['thumb_width'],  $GLOBALS['_CFG']['thumb_height']);
                if ($goods_thumb === false)
                {
                    sys_msg($image->error_msg(), 1, array(), false);
                }
            }
            else
            {
                $goods_thumb = $original_img;
            }
        }
    }


    /* 删除下载的外链原图 */
    if (!empty($is_url_goods_img))
    {
        unlink(ROOT_PATH . $original_img);
        empty($newname) || unlink(ROOT_PATH . $newname);
        $url_goods_img = $goods_img = $original_img = htmlspecialchars(trim($_POST['goods_img_url']));
    }


    /* 如果没有输入商品货号则自动生成一个商品货号 */
    if (empty($_POST['goods_sn']))
    {
        $max_id     = $is_insert ? $db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods')) : $_REQUEST['goods_id'];
        $goods_sn   = generate_goods_sn($max_id);
    }
    else
    {
        $goods_sn   = $_POST['goods_sn'];
    }

    /* 处理商品数据 */
    $shop_price = !empty($_POST['shop_price']) ? trim($_POST['shop_price']) : 0;
    $shop_price = floatval($shop_price);
    $market_price = !empty($_POST['market_price']) ? trim($_POST['market_price']) : 0;
    $market_price = floatval($market_price);
    $promote_price = !empty($_POST['promote_price']) ? trim($_POST['promote_price']) : 0;
    $promote_price = floatval($promote_price);
    //$is_promote = empty($promote_price) ? 0 : 1;
	
	//ecmoban模板堂 --zhuo satrt
	if(!isset($_POST['is_promote'])){
		$is_promote = 0;
	}else{
		$is_promote = $_POST['is_promote'];
	}
	//ecmoban模板堂 --zhuo end
	
    $promote_start_date = ($is_promote && !empty($_POST['promote_start_date'])) ? local_strtotime($_POST['promote_start_date']) : 0;
    $promote_end_date = ($is_promote && !empty($_POST['promote_end_date'])) ? local_strtotime($_POST['promote_end_date']) : 0;
    $goods_weight = !empty($_POST['goods_weight']) ? $_POST['goods_weight'] * $_POST['weight_unit'] : 0;
    $is_best = isset($_POST['is_best']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    $is_alone_sale = isset($_POST['is_alone_sale']) ? 1 : 0;
    $is_shipping = isset($_POST['is_shipping']) ? 1 : 0;
    $goods_number = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
    $warn_number = isset($_POST['warn_number']) ? $_POST['warn_number'] : 0;
    $goods_type = isset($_POST['goods_type']) ? $_POST['goods_type'] : 0;
    $give_integral = isset($_POST['give_integral']) ? intval($_POST['give_integral']) : '-1';
    $rank_integral = isset($_POST['rank_integral']) ? intval($_POST['rank_integral']) : '-1';
    $suppliers_id = isset($_POST['suppliers_id']) ? intval($_POST['suppliers_id']) : '0';
    $warehouse_id = isset($_POST['warehouse_id']) ? explode(',', $_POST['warehouse_id']) : array(); //by kong
    $warehouse_area_id = isset($_POST['warehouse_area_id']) ? explode(',', $_POST['warehouse_area_id']) : array(); //by kong

    //微分销
    $is_distribution = (isset($_POST['is_distribution']) ? intval($_POST['is_distribution']) : 0);
    if ($is_distribution == 1)
    {
        $dis_commission = (($_POST['dis_commission'] > 0) && ($_POST['dis_commission'] <= 100) ? intval($_POST['dis_commission']) : 0);
    }
    //微分销
    
    $bar_code   = !empty($_POST['bar_code']) ? trim($_POST['bar_code']) : '';
    $goods_name_style = $_POST['goods_name_color'] . '+' . $_POST['goods_name_style'];

    //$catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
    //$catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
	$cat_id_arr = explode('_', $_POST['cat_id']);
	$catgory_id = $cat_id_arr[0];
    $user_cat_arr = explode('_', $_POST['user_cat']);
    $user_cat = $user_cat_arr[0];
		
    $brand_id = empty($_POST['brand_id']) ? '' : intval($_POST['brand_id']);
	
	//ecmoban模板堂 --zhuo
	$store_category = !empty($_POST['store_category'])       ? intval($_POST['store_category'])     : 0;
	if($store_category > 0){
		$catgory_id = $store_category;
	}

    $goods_thumb = (empty($goods_thumb) && !empty($_POST['goods_thumb_url']) && goods_parse_url($_POST['goods_thumb_url'])) ? htmlspecialchars(trim($_POST['goods_thumb_url'])) : $goods_thumb;
    $goods_thumb = (empty($goods_thumb) && isset($_POST['auto_thumb']))? $goods_img : $goods_thumb;
	
	/* ecmoban模板堂  序列化分期送期数数据   start bylu */
    if($_POST['is_stages']){
        $stages=serialize($_POST['stages_num']);//分期期数;
        $stages_rate=$_POST['stages_rate'];//分期费率;
    }
    /* ecmoban模板堂  end bylu */

    $adminru = get_admin_ru_id();
    //ecmoban模板堂 --zhuo start 仓库
    //$warehouse_name = isset($_POST['warehouse_name']) ? $_POST['warehouse_name'] : array();
    //$warehouse_number = isset($_POST['warehouse_number']) ? $_POST['warehouse_number'] : array();
    //$warehouse_price = isset($_POST['warehouse_price']) ? $_POST['warehouse_price'] : array();
    //$warehouse_promote_price = isset($_POST['warehouse_promote_price']) ? $_POST['warehouse_promote_price'] : array();
    //ecmoban模板堂 --zhuo end 仓库

    //ecmoban模板堂 --zhuo start 仓库地区
    //$area_name = isset($_POST['area_name']) ? $_POST['area_name'] : array();
    //$region_number = isset($_POST['region_number']) ? $_POST['region_number'] : array();
    //$region_price = isset($_POST['region_price']) ? $_POST['region_price'] : array();
    //$region_promote_price = isset($_POST['region_promote_price']) ? $_POST['region_promote_price'] : array();
    //ecmoban模板堂 --zhuo end 仓库地区

    $model_price = isset($_POST['model_price']) ? intval($_POST['model_price']) : 0;
    $model_inventory = isset($_POST['model_inventory']) ? intval($_POST['model_inventory']) : 0;
    $model_attr = isset($_POST['model_attr']) ? intval($_POST['model_attr']) : 0;

    $review_status = 1;
    if($GLOBALS['_CFG']['review_goods'] == 0){
        $review_status = 5;
    }else{
        if($adminru['ru_id'] > 0){
            $sql = "select review_goods from " .$ecs->table('merchants_shop_information'). " where user_id = '" .$adminru['ru_id']. "'";
            $review_goods = $db->getOne($sql); //判断

            if($review_goods == 0){
                $review_status = 5;
            }
        }else{
            $review_status = 5;
        }
    }    

    //ecmoban模板堂 --zhuo start 限购
    $xiangou_num = !empty($_POST['xiangou_num']) ? intval($_POST['xiangou_num'])  : 0;
    $is_xiangou = empty($xiangou_num) ? 0 : 1;
    $xiangou_start_date = ($is_xiangou && !empty($_POST['xiangou_start_date'])) ? local_strtotime($_POST['xiangou_start_date']) : 0;
    $xiangou_end_date = ($is_xiangou && !empty($_POST['xiangou_end_date'])) ? local_strtotime($_POST['xiangou_end_date']) : 0;
    //ecmoban模板堂 --zhuo end 限购
    
    //ecmoban模板堂 --zhuo start 促销满减 
    $cfull      = isset($_POST['cfull']) ? $_POST['cfull'] : array();
    $creduce    = isset($_POST['creduce']) ? $_POST['creduce'] : array();
    $c_id    = isset($_POST['c_id']) ? $_POST['c_id'] : array();
    
    $sfull      = isset($_POST['sfull']) ? $_POST['sfull'] : array();
    $sreduce    = isset($_POST['sreduce']) ? $_POST['sreduce'] : array();
    $s_id    = isset($_POST['s_id']) ? $_POST['s_id'] : array();
    
    $goods_img_id=!empty($_REQUEST['img_id'])  ? $_REQUEST['img_id']:'';//相册ID
    $largest_amount = !empty($_POST['largest_amount']) ? trim($_POST['largest_amount'])  : 0;
    $largest_amount = floatval($largest_amount);
    //ecmoban模板堂 --zhuo end 促销满减
    
    $group_number = !empty($_POST['group_number']) ? intval($_POST['group_number'])  : 0;
    
    $store_new = isset($_POST['store_new']) ? 1 : 0;
    $store_hot = isset($_POST['store_hot']) ? 1 : 0;
	$store_best = isset($_POST['store_best']) ? 1 : 0;
	
	$goods_name = trim($_POST['goods_name']);
	//by guan start
    $pin = new pin();
    $pinyin = $pin->Pinyin($goods_name,'UTF8');
    //by guan end
    //微分销
    $where_drp_sql = "";
    $where_drp_val = "";

    if (file_exists(MOBILE_DRP))
    {
        $where_drp_sql = ", is_distribution, dis_commission";
        $where_drp_val = ", '$is_distribution', '$dis_commission'";
    }
    //微分销

    /* 入库 */
    if ($is_insert)
    {
        if ($code == '')
        {
            $sql = "INSERT INTO " . $ecs->table('goods') . " (goods_name, goods_name_style, goods_sn, bar_code, " .
                    "cat_id, user_cat, brand_id, shop_price, market_price, is_promote, promote_price, " .
                    "promote_start_date, promote_end_date, goods_img, goods_thumb, original_img, keywords, goods_brief, " .
                    "seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, " .
                    "is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, rank_integral, suppliers_id , goods_shipai" . 
                    ", user_id, model_price, model_inventory, model_attr, review_status" . 
                    ", group_number, store_new, store_hot, store_best" .
					", goods_product_tag" . $where_drp_sql .
                    ", is_xiangou, xiangou_num, xiangou_start_date, xiangou_end_date, largest_amount, pinyin_keyword,stages,stages_rate" .//@author bylu 白条分期;
                    ")" .
                "VALUES ('$goods_name', '$goods_name_style', '$goods_sn', '$bar_code', '$catgory_id', '$user_cat', " .
                    "'$brand_id', '$shop_price', '$market_price', '$is_promote','$promote_price', ".
                    "'$promote_start_date', '$promote_end_date', '$goods_img', '$goods_thumb', '$original_img', ".
                    "'$_POST[keywords]', '$_POST[goods_brief]', '$_POST[seller_note]', '$goods_weight', '$goods_number',".
                    " '$warn_number', '$_POST[integral]', '$give_integral', '$is_best', '$is_new', '$is_hot', '$is_on_sale', '$is_alone_sale', $is_shipping, ".
                    " '$_POST[goods_desc]', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$rank_integral', '$suppliers_id' , '$_POST[goods_shipai]'" . 
                    ", '$adminru[ru_id]', '$model_price', '$model_inventory', '$model_attr', '$review_status'" .
                    ", '$group_number', '$store_new', '$store_hot', '$store_best'" .
					", '$_POST[goods_product_tag]'" .  $where_drp_val .
                    ", '$is_xiangou', '$xiangou_num', '$xiangou_start_date', '$xiangou_end_date', '$largest_amount', '$pinyin','$stages','$stages_rate'" .//@author bylu 白条分期;
                    ")";
        }
        else
        {
            $sql = "INSERT INTO " . $ecs->table('goods') . " (goods_name, goods_name_style, goods_sn, bar_code, " .
                    "cat_id, user_cat, brand_id, shop_price, market_price, is_promote, promote_price, " .
                    "promote_start_date, promote_end_date, goods_img, goods_thumb, original_img, keywords, goods_brief, " .
                    "seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, is_real, " .
                    "is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, extension_code, rank_integral ,  goods_shipai" .
                    ", user_id, model_price, model_inventory, model_attr, review_status" . 
                     ", group_number, store_new, store_hot, store_best" .
					 ", goods_product_tag" . $where_drp_sql .
                    ", is_xiangou, xiangou_num, xiangou_start_date, xiangou_end_date, largest_amount, pinyin_keyword,stages,stages_rate" .//@author bylu 白条分期;
                    ")" .
                "VALUES ('$goods_name', '$goods_name_style', '$goods_sn', '$bar_code', '$catgory_id', '$user_cat', " .
                    "'$brand_id', '$shop_price', '$market_price', '$is_promote','$promote_price', ".
                    "'$promote_start_date', '$promote_end_date', '$goods_img', '$goods_thumb', '$original_img', ".
                    "'$_POST[keywords]', '$_POST[goods_brief]', '$_POST[seller_note]', '$goods_weight', '$goods_number',".
                    " '$warn_number', '$_POST[integral]', '$give_integral', '$is_best', '$is_new', '$is_hot', 0, '$is_on_sale', '$is_alone_sale', $is_shipping, ".
                    " '$_POST[goods_desc]', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$code', '$rank_integral' , '$_POST[goods_shipai]'" . 
                    ", '$adminru[ru_id]', '$model_price', '$model_inventory', '$model_attr', '$review_status'" .
                    ", '$group_number', '$store_new', '$store_hot', '$store_best'" .
					", '$_POST[goods_product_tag]'" .  $where_drp_val .
                    ", '$is_xiangou', '$xiangou_num', '$xiangou_start_date', '$xiangou_end_date', '$largest_amount', '$pinyin','$stages','$stages_rate'" .//@author bylu 白条分期;
                    ")";
        }
        $not_number = !empty($goods_number) ? 1 : 0;
        $number = "+ " . $goods_number;
        $use_storage = 7;
    }
    else
    {
        /* 如果有上传图片，删除原来的商品图 */
        $sql = "SELECT goods_thumb, goods_img, original_img " .
                    " FROM " . $ecs->table('goods') .
                    " WHERE goods_id = '$_REQUEST[goods_id]'";
        $row = $db->getRow($sql);
        if ($proc_thumb && $goods_img && $row['goods_img'] && !goods_parse_url($row['goods_img']))
        {
            @unlink(ROOT_PATH . $row['goods_img']);
            @unlink(ROOT_PATH . $row['original_img']);
        }

        if ($proc_thumb && $goods_thumb && $row['goods_thumb'] && !goods_parse_url($row['goods_thumb']))
        {
            @unlink(ROOT_PATH . $row['goods_thumb']);
        }
		
		get_goods_file_content($_REQUEST['goods_id'], $GLOBALS['_CFG']['goods_file'], $adminru['ru_id'], $review_goods, $model_attr); //编辑商品需审核通过

        //微分销
        $where_drp_up = "";

        if (file_exists(MOBILE_DRP))
        {
            $where_drp_up = "dis_commission = '$dis_commission', is_distribution = '$is_distribution', ";
        }
        //微分销

        $sql = "UPDATE " . $ecs->table('goods') . " SET " .
                "goods_name = '$goods_name', " .
                "goods_name_style = '$goods_name_style', " .
                "goods_sn = '$goods_sn', " .
                "bar_code = '$bar_code', " .
                "cat_id = '$catgory_id', " .
                "brand_id = '$brand_id', " .
                "shop_price = '$shop_price', " .
                "market_price = '$market_price', " .
                "is_promote = '$is_promote', " .
               
                //ecmoban模板堂 --zhuo start
                "model_price = '$model_price', " .
                "model_inventory = '$model_inventory', " .
                "model_attr = '$model_attr', " .
                "largest_amount = '$largest_amount', " .
                "group_number = '$group_number'," .
                "store_new = '$store_new'," .
                "store_hot = '$store_hot'," .
				"store_best = '$store_best'," .
                //ecmoban模板堂 --zhuo end

                //ecmoban模板堂 --zhuo start 限购
                "is_xiangou='$is_xiangou',".
                "xiangou_num = '$xiangou_num',".
                "xiangou_start_date = '$xiangou_start_date',".
                "xiangou_end_date = '$xiangou_end_date'," .
                //ecmoban模板堂 --zhuo end 限购
				
                //@author guan 晒单评价 start
                "goods_product_tag = '$_POST[goods_product_tag]', " .
                //@author guan 晒单评价 end

                "pinyin_keyword = '$pinyin', " . //模糊搜索 buy guan

                /* ecmoban模板堂  修改分期送期数,分期费率  start bylu */
                "stages = '$stages', ".
                "stages_rate = '$stages_rate', ".
                " user_cat = '$user_cat', " .
                $where_drp_up . 
                /* ecmoban模板堂     end bylu */

                "promote_price = '$promote_price', " .
                "promote_start_date = '$promote_start_date', " .
                "suppliers_id = '$suppliers_id', " .
                "promote_end_date = '$promote_end_date', ";

        /* 如果有上传图片，需要更新数据库 */
        if ($goods_img)
        {
            $sql .= "goods_img = '$goods_img', original_img = '$original_img', ";
        }
        if ($goods_thumb)
        {
            $sql .= "goods_thumb = '$goods_thumb', ";
        }
        if ($code != '')
        {
            $sql .= "is_real=0, extension_code='$code', ";
        }
        $sql .= "keywords = '$_POST[keywords]', " .
                "goods_brief = '$_POST[goods_brief]', " .
                "seller_note = '$_POST[seller_note]', " .
                "goods_weight = '$goods_weight'," .
                "goods_number = '$goods_number', " .
                "warn_number = '$warn_number', " .
                "integral = '$_POST[integral]', " .
                "give_integral = '$give_integral', " .
                "rank_integral = '$rank_integral', " .
                "is_best = '$is_best', " .
                "is_new = '$is_new', " .
                "is_hot = '$is_hot', " .
                "is_on_sale = '$is_on_sale', " .
                "is_alone_sale = '$is_alone_sale', " .
                "is_shipping = '$is_shipping', " .
                "goods_desc = '$_POST[goods_desc]', " .
				"goods_shipai = '$_POST[goods_shipai]', " .
                "last_update = '". gmtime() ."', ".
                "goods_type = '$goods_type' " .
                "WHERE goods_id = '$_REQUEST[goods_id]' LIMIT 1";
        $goodsInfo = get_goods_info($_REQUEST['goods_id'], array('goods_number'));

        if ($goods_number > $goodsInfo['goods_number'])
        {
            $not_number = $goods_number - $goodsInfo['goods_number'];
            $not_number = !empty($not_number) ? 1 : 0;
            $number = $goods_number - $goodsInfo['goods_number'];
            $number = '+ ' . $number;
            $use_storage = 13;
        }
        else
        {
            $not_number = $goodsInfo['goods_number'] - $goods_number;
            $not_number = !empty($not_number) ? 1 : 0;
            $number = $goodsInfo['goods_number'] - $goods_number;
            $number = '- ' . $number;
            $use_storage = 8;
        }
    }
    $db->query($sql);

    /* 商品编号 */
    $goods_id = $is_insert ? $db->insert_id() : $_REQUEST['goods_id'];
    //by wang start	
    if($goods_id>0)
    {
            //商品扩展信息
            $is_reality=!empty($_POST['is_reality'])?intval($_POST['is_reality']):0;
            $is_return=!empty($_POST['is_return'])?intval($_POST['is_return']):0;
            $is_fast=!empty($_POST['is_fast'])?intval($_POST['is_fast']):0;
            $extend=$db->getOne("select count(goods_id) from ".$ecs->table('goods_extend')." where goods_id='$goods_id'");
            if($extend>0)
            {
                    //跟新商品扩展信息
                    $extend_sql="update ".$ecs->table('goods_extend')." SET `is_reality`='$is_reality',`is_return`='$is_return',`is_fast`='$is_fast' WHERE goods_id='$goods_id'";
            }
            else
            {
                    //插入商品扩展信息
                    $extend_sql="INSERT INTO " . $ecs->table('goods_extend') ."(`goods_id`, `is_reality`, `is_return`, `is_fast`) VALUES ('$goods_id','$is_reality','$is_return','$is_fast')";
            }
            $db->query($extend_sql);
    }
    if ($not_number)
    {
        $logs_other = array(
                        'goods_id' => $goods_id, 
                        'order_id' => 0, 
                        'use_storage' => $use_storage, 
                        'admin_id' => $_SESSION['seller_id'], 
                        'number' => $number, 
                        'model_inventory' => $model_inventory, 
                        'model_attr' => $model_attr, 
                        'product_id' => 0, 
                        'warehouse_id' => 0, 
                        'area_id' => 0, 
                        'add_time' => gmtime()
                        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
    }
    //by wang end
	
    //消费满N金额减N减额
    get_goods_payfull($cfull, $creduce, $c_id, $goods_id, 'goods_consumption'); 
    //消费满N金额减N减运费
    //get_goods_payfull($sfull, $sreduce, $s_id, $goods_id, 'goods_conshipping', 1);

    /* 记录日志 */
    if ($is_insert) {
		if ($model_price == 1) {
			$warehouse_id = isset($_POST['warehouse_id']) && !empty($_POST['warehouse_id']) ? $_POST['warehouse_id'] : '';

			if ($warehouse_id) {
				$db->query(" UPDATE " . $ecs->table('warehouse_goods') . " SET goods_id = '$goods_id' WHERE w_id " . db_create_in($warehouse_id));
			}
		}
		else if ($model_price == 2) {
			$warehouse_area_id = isset($_POST['warehouse_area_id']) && !empty($_POST['warehouse_id']) ? $_POST['warehouse_area_id'] : '';

			if ($warehouse_id) {
				$db->query(" UPDATE " . $ecs->table('warehouse_area_goods') . " SET goods_id = '$goods_id' WHERE a_id " . db_create_in($warehouse_area_id));
			}
		}
        admin_log($_POST['goods_name'], 'add', 'goods');
    } else {
        admin_log($_POST['goods_name'], 'edit', 'goods');

        //by li start
        $shop_price_format = price_format($shop_price);
        //降价通知
        $sql = "SELECT * FROM " . $ecs->table('sale_notice') . " WHERE goods_id='$_REQUEST[goods_id]' AND STATUS!=1";
        $notice_list = $db->getAll($sql);

        foreach ($notice_list as $key => $val) {
            //查询会员名称 by wu
            $sql = " select user_name from " . $GLOBALS['ecs']->table('users') . " where user_id='" . $val['user_id'] . "' ";
            $user_info = $GLOBALS['db']->getRow($sql);
            $user_name = $user_info['user_name'];

            //短信发送
            $send_ok = 0;

            if ($shop_price <= $val['hopeDiscount'] && $val['cellphone'] && $_CFG['sms_price_notice'] == '1') {

                if ($goods['user_id']) {
                    $shop_name = get_shop_name($goods['user_id'], 1);
                } else {
                    $shop_name = $GLOBALS['_CFG']['shop_name'];
                }

                if ($GLOBALS['_CFG']['sms_type'] == 0)
                {
                    $goods_name = sub_str($_POST['goods_name'], 15);
                    $msg = sprintf($GLOBALS['_LANG']['sale_notice_sms'], $goods_name, $shop_price_format);
                    include_once('../includes/cls_sms.php');
                    $sms = new sms();
                    $res = $sms->send($val['cellphone'], $msg, '', '60', 1);
                }
                elseif ($GLOBALS['_CFG']['sms_type'] == 1)
                { 
                    $user_info = get_admin_user_info($val['user_id']);
                    //阿里大鱼短信接口参数
                    $smsParams = array(
                        'user_name' => $user_info['user_name'], 
                        'goods_sn' => $goods_sn, 
                        'mobile_phone' => $val['cellphone']
                    );

                    $result = sms_ali($smsParams, 'sms_price_notic'); //阿里大鱼短信变量传值，发送时机传值

                    if ($result) {
                        $resp = $GLOBALS['ecs']->ali_yu($result);
                    } else {
                        sys_msg('阿里大鱼短信配置异常', 1);
                    }
                }

                //记录日志
                $send_type = 2;
                if ($res) {
                    $sql = "UPDATE " . $ecs->table('sale_notice') . " SET status = 1, send_type=2 WHERE goods_id = '$_REQUEST[goods_id]' AND user_id='$val[user_id]'";
                    $db->query($sql);
                    $send_ok = 1;
                    notice_log($goods_id, $val['cellphone'], $send_ok, $send_type);
                } else {
                    $sql = "UPDATE " . $ecs->table('sale_notice') . " SET status = 3, send_type=2 WHERE goods_id = '$_REQUEST[goods_id]' AND user_id='$val[user_id]'";
                    $db->query($sql);
                    $send_ok = 0;
                    notice_log($goods_id, $val['cellphone'], $send_ok, $send_type);
                }
            }

            //当短信发送失败，邮件发送
            if ($send_ok == 0 && $shop_price <= $val['hopeDiscount'] && $val['email']) {
                /* 设置留言回复模板所需要的内容信息 */
                $template = get_mail_template('sale_notice');

                $smarty->assign('user_name', $user_name);
                $smarty->assign('goods_name', $_POST['goods_name']);
                $smarty->assign('goods_link', $ecs->url() . "goods.php?id=" . $_REQUEST['goods_id']);
                $smarty->assign('send_date', local_date($GLOBALS['_CFG']['time_format'], gmtime()));

                $content = $smarty->fetch('str:' . $template['template_content']);

                $send_type = 1;
                /* 发送邮件 */
                if (send_mail($user_name, $val['email'], $template['template_subject'], $content, $template['is_html'])) {
                    $sql = "UPDATE " . $ecs->table('sale_notice') . " SET status = 1, send_type=1 WHERE goods_id = '$_REQUEST[goods_id]' AND user_id='$val[user_id]'";
                    $db->query($sql);
                    $send_ok = 1;
                    notice_log($goods_id, $val['email'], $send_ok, $send_type);
                } else {
                    $sql = "UPDATE " . $ecs->table('sale_notice') . " SET status = 3, send_type=1 WHERE goods_id = '$_REQUEST[goods_id]' AND user_id='$val[user_id]'";
                    $db->query($sql);
                    $send_ok = 0;
                    notice_log($goods_id, $val['email'], $send_ok, $send_type);
                }
            }
        }
        //by li end
    }

    //ecmoban模板堂 --zhuo start 仓库
    //$sql = "select user_id from " . $ecs->table('goods') . " where goods_id = '$goods_id'";
    //$user_id = $db->getOne($sql);
    //$db->query(" UPDATE " . $ecs->table("warehouse_goods") . " SET goods_id = '$goods_id' WHERE w_id " . db_create_in($warehouse_id));
    // by kong
    //$db->query(" UPDATE " . $ecs->table("warehouse_area_goods") . " SET goods_id = '$goods_id' WHERE a_id " . db_create_in($warehouse_area_id));
    // by kong
    //get_insert_warehouse_goods($goods_id, $warehouse_name, $warehouse_number, $warehouse_price, $warehouse_promote_price, $user_id);

    //get_insert_warehouse_area_goods($goods_id, $area_name, $region_number, $region_price, $region_promote_price, $user_id);
    //ecmoban模板堂 --zhuo end 仓库

    /* 处理属性 */
    if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (empty($_POST['attr_id_list']) && empty($_POST['attr_value_list'])))
    {
        // 取得原有的属性值
        $goods_attr_list = array();

        $keywords_arr = explode(" ", $_POST['keywords']);

        $keywords_arr = array_flip($keywords_arr);
        if (isset($keywords_arr['']))
        {
            unset($keywords_arr['']);
        }

        $sql = "SELECT attr_id, attr_index FROM " . $ecs->table('attribute') . " WHERE cat_id = '$goods_type'";

        $attr_res = $db->query($sql);

        $attr_list = array();

        while ($row = $db->fetchRow($attr_res))
        {
            $attr_list[$row['attr_id']] = $row['attr_index'];
        }

        $sql = "SELECT g.*, a.attr_type
                FROM " . $ecs->table('goods_attr') . " AS g
                    LEFT JOIN " . $ecs->table('attribute') . " AS a
                        ON a.attr_id = g.attr_id
                WHERE g.goods_id = '$goods_id'";

        $res = $db->query($sql);

        while ($row = $db->fetchRow($res))
        {
            $goods_attr_list[$row['attr_id']][$row['attr_value']] = array('sign' => 'delete', 'goods_attr_id' => $row['goods_attr_id']);
        }
        // 循环现有的，根据原有的做相应处理
        if(isset($_POST['attr_id_list']))
        {
            foreach ($_POST['attr_id_list'] AS $key => $attr_id)
            {
                $attr_value = $_POST['attr_value_list'][$key];
                $attr_price = $_POST['attr_price_list'][$key];
				$attr_sort = $_POST['attr_sort_list'][$key]; //ecmoban模板堂 --zhuo
                if (!empty($attr_value))
                {
                    if (isset($goods_attr_list[$attr_id][$attr_value]))
                    {
                        // 如果原来有，标记为更新
                        $goods_attr_list[$attr_id][$attr_value]['sign'] = 'update';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
						$goods_attr_list[$attr_id][$attr_value]['attr_sort'] = $attr_sort; //ecmoban模板堂 --zhuo
                    }
                    else
                    {
                        // 如果原来没有，标记为新增
                        $goods_attr_list[$attr_id][$attr_value]['sign'] = 'insert';
                        $goods_attr_list[$attr_id][$attr_value]['attr_price'] = $attr_price;
						$goods_attr_list[$attr_id][$attr_value]['attr_sort'] = $attr_sort; //ecmoban模板堂 --zhuo
                    }
                    $val_arr = explode(' ', $attr_value);
                    foreach ($val_arr AS $k => $v)
                    {
                        if (!isset($keywords_arr[$v]) && $attr_list[$attr_id] == "1")
                        {
                            $keywords_arr[$v] = $v;
                        }
                    }
                }
            }
        }
        $keywords = join(' ', array_flip($keywords_arr));
		
        $sql = "UPDATE " .$ecs->table('goods'). " SET keywords = '$keywords' WHERE goods_id = '$goods_id' LIMIT 1";
        $db->query($sql);
		
        /* 插入、更新、删除数据 */
        foreach ($goods_attr_list as $attr_id => $attr_value_list)
        {
            foreach ($attr_value_list as $attr_value => $info)
            {
                if ($info['sign'] == 'insert') //ecmoban模板堂 --zhuo attr_sort
                {
                    $sql = "INSERT INTO " .$ecs->table('goods_attr'). " (attr_id, goods_id, attr_value, attr_price, attr_sort)".
                            "VALUES ('$attr_id', '$goods_id', '$attr_value', '$info[attr_price]', '$info[attr_sort]')";
                }
                elseif ($info['sign'] == 'update') //ecmoban模板堂 --zhuo attr_sort
                {
                    $sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_price = '$info[attr_price]', attr_sort = '$info[attr_sort]' WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
                }
                else
                {
                    $sql = "DELETE FROM " .$ecs->table('goods_attr'). " WHERE goods_attr_id = '$info[goods_attr_id]' LIMIT 1";
                }
                $db->query($sql);
            }
        }
    }

    /* 处理会员价格 */
    if (isset($_POST['user_rank']) && isset($_POST['user_price']))
    {
        handle_member_price($goods_id, $_POST['user_rank'], $_POST['user_price']);
    }

    /* 处理优惠价格 */
    if (isset($_POST['volume_number']) && isset($_POST['volume_price']))
    {
        $temp_num = array_count_values($_POST['volume_number']);
        foreach($temp_num as $v)
        {
            if ($v > 1)
            {
                sys_msg($_LANG['volume_number_continuous'], 1, array(), false);
                break;
            }
        }
        handle_volume_price($goods_id, $_POST['volume_number'], $_POST['volume_price']);
    }

    /* 处理扩展分类 */
    if (isset($_POST['other_cat']))
    {
        handle_other_cat($goods_id, array_unique($_POST['other_cat']));
    }

    if ($is_insert)
    {
        /* 处理关联商品 */
        handle_link_goods($goods_id);

        /* 处理组合商品 */
        handle_group_goods($goods_id);

        /* 处理关联文章 */
        handle_goods_article($goods_id);
        
        /* 处理关联地区 add by qin */
        handle_goods_area($goods_id);
    }

    /* 重新格式化图片名称 */
    $original_img = reformat_image_name('goods', $goods_id, $original_img, 'source');
    $goods_img = reformat_image_name('goods', $goods_id, $goods_img, 'goods');
    $goods_thumb = reformat_image_name('goods_thumb', $goods_id, $goods_thumb, 'thumb');
    
    if ($goods_img !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET goods_img = '$goods_img' WHERE goods_id='$goods_id'");
    }

    if ($original_img !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET original_img = '$original_img' WHERE goods_id='$goods_id'");
    }

    if ($goods_thumb !== false)
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET goods_thumb = '$goods_thumb' WHERE goods_id='$goods_id'");
    }

    /* 如果有图片，把商品图片加入图片相册 */
    if (isset($img))
    {
        /* 重新格式化图片名称 */
        if (empty($is_url_goods_img))
        {
            $img = reformat_image_name('gallery', $goods_id, $img, 'source');
            $gallery_img = reformat_image_name('gallery', $goods_id, $gallery_img, 'goods');
        }
        else
        {
            $img = $url_goods_img;
            $gallery_img = $url_goods_img;
        }

        $gallery_thumb = reformat_image_name('gallery_thumb', $goods_id, $gallery_thumb, 'thumb');
        
        $sql = "INSERT INTO " . $ecs->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) " .
                "VALUES ('$goods_id', '$gallery_img', '', '$gallery_thumb', '$img')";
        $db->query($sql);
        
        get_oss_add_file(array($goods_img, $goods_thumb, $original_img, $gallery_img, $gallery_thumb, $img));
    }
    /*处理相册图片 by kong*/
    if(!empty($goods_img_id)){
        $old_img_id=array();
        $sql=" UPDATE ".$ecs->table('goods_gallery')." SET goods_id = '".$goods_id."' WHERE img_id ".db_create_in($goods_img_id);
        $db->query($sql);
    }
    
    /*by kong end*/
    /* 处理相册图片 */
   // handle_gallery_image($goods_id, $_FILES['img_url'], $_POST['img_desc'], $_POST['img_file']);
    
    /* 编辑时处理相册图片描述 */
    if (!$is_insert && isset($_POST['old_img_desc']))
    {
        foreach ($_POST['old_img_desc'] AS $img_id => $img_desc)
        {
            $sql = "UPDATE " . $ecs->table('goods_gallery') . " SET img_desc = '$img_desc' WHERE img_id = '$img_id' LIMIT 1";
            $db->query($sql);
        }
    }
    
    /*删除空白相册*/
    $db->query("DELETE FROM ".$ecs->table('goods_gallery')." WHERE goods_id = ''");
    /* 不保留商品原图的时候删除原图 */
    if ($proc_thumb && !$_CFG['retain_original_img'] && !empty($original_img))
    {
        $db->query("UPDATE " . $ecs->table('goods') . " SET original_img='' WHERE `goods_id`='{$goods_id}'");
        $db->query("UPDATE " . $ecs->table('goods_gallery') . " SET img_original='' WHERE `goods_id`='{$goods_id}'");
        @unlink('../' . $original_img);
        @unlink('../' . $img);
    }

    /* 记录上一次选择的分类和品牌 */
    setcookie('ECSCP[last_choose]', $catgory_id . '|' . $brand_id, gmtime() + 86400);
    /* 清空缓存 */
    clear_cache_files();

    /* 提示页面 */
    $link = array();
    if (check_goods_specifications_exist($goods_id))
    {
		$date = array('model_attr');
		$where = "goods_id = '$goods_id'";
		$model_attr = get_table_date('goods', $where, $date, 2);
	
		if($model_attr == 1){
			$link[0] = array('href' => 'goods_warehouse_attr.php?act=warehouse_list&goods_id=' . $goods_id, 'text' => $_LANG['product']);
		}elseif($model_attr == 2){
			$link[0] = array('href' => 'goods_area_attr.php?act=warehouse_list&goods_id=' . $goods_id, 'text' => $_LANG['product']);
		}else{
			$link[0] = array('href' => 'goods.php?act=product_list&goods_id=' . $goods_id, 'text' => $_LANG['product']);
		}
    }
    if ($code == 'virtual_card')
    {
        $link[1] = array('href' => 'virtual_card.php?act=replenish&goods_id=' . $goods_id, 'text' => $_LANG['add_replenish']);
    }
    if ($is_insert)
    {
        $link[2] = add_link($code);
    }
    $link[3] = list_link($is_insert, $code);


    //$key_array = array_keys($link);
    for($i=0;$i<count($link);$i++)
    {
       $key_array[]=$i;
    }
    krsort($link);
    $link = array_combine($key_array, $link);


    sys_msg($is_insert ? $_LANG['add_goods_ok'] : $_LANG['edit_goods_ok'], 0, $link);
}

/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    $code = empty($_REQUEST['extension_code'])? '' : trim($_REQUEST['extension_code']);

    /* 取得要操作的商品编号 */
    $goods_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;

    if (isset($_POST['type']))
    {
        /* 放入回收站 */
        if ($_POST['type'] == 'trash')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'is_delete', '1');

            /* 记录日志 */
            admin_log('', 'batch_trash', 'goods');
        }
        /* 上架 */
        elseif ($_POST['type'] == 'on_sale')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_on_sale', '1');
        }

        /* 下架 */
        elseif ($_POST['type'] == 'not_on_sale')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_on_sale', '0');
        }

        /* 设为精品 */
        elseif ($_POST['type'] == 'best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '1');
        }

        /* 取消精品 */
        elseif ($_POST['type'] == 'not_best')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_best', '0');
        }

        /* 设为新品 */
        elseif ($_POST['type'] == 'new')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_new', '1');
        }

        /* 取消新品 */
        elseif ($_POST['type'] == 'not_new')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_new', '0');
        }

        /* 设为热销 */
        elseif ($_POST['type'] == 'hot')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_hot', '1');
        }

        /* 取消热销 */
        elseif ($_POST['type'] == 'not_hot')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'is_hot', '0');
        }

        /* 转移到分类 */
        elseif ($_POST['type'] == 'move_to')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'cat_id', $_POST['target_cat']);
        }

        /* 转移到供货商 */
        elseif ($_POST['type'] == 'suppliers_move_to')
        {
            /* 检查权限 */
            admin_priv('goods_manage');
            update_goods($goods_id, 'suppliers_id', $_POST['suppliers_id']);
        }

        /* 还原 */
        elseif ($_POST['type'] == 'restore')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'is_delete', '0');

            /* 记录日志 */
            admin_log('', 'batch_restore', 'goods');
        }
        /* 删除 */
        elseif ($_POST['type'] == 'drop')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            delete_goods($goods_id);

            /* 记录日志 */
            admin_log('', 'batch_remove', 'goods');
        }
		
		/* 审核商品 ecmoban模板堂 --zhuo */
        elseif ($_POST['type'] == 'review_to')
        {
            /* 检查权限 */
            admin_priv('remove_back');

            update_goods($goods_id, 'review_status', $_POST['review_status'], $_POST['review_content']);

            /* 记录日志 */
            admin_log('', 'review_to', 'goods');
        }
    }

    /* 清除缓存 */
    clear_cache_files();

    if ($_POST['type'] == 'drop' || $_POST['type'] == 'restore')
    {
        $link[] = array('href' => 'goods.php?act=trash', 'text' => $_LANG['11_goods_trash']);
    }
    else
    {
        $link[] = list_link(true, $code);
    }
    sys_msg($_LANG['batch_handle_ok'], 0, $link);
}

/*------------------------------------------------------ */
//-- 显示图片
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'show_image')
{

    if (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)
    {
        $img_url = $_GET['img_url'];
    }
    else
    {
        if (strpos($_GET['img_url'], 'http://') === 0)
        {
            $img_url = $_GET['img_url'];
        }
        else
        {
            $img_url = '../' . $_GET['img_url'];
        }
    }
    $smarty->assign('img_url', $img_url);
    $smarty->display('goods_show_image.dwt');
}

/*------------------------------------------------------ */
//-- 修改商品名称
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_name')
{
    check_authz_json('goods_manage');

    $goods_id   = intval($_POST['id']);
    $goods_name = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("goods_name = '$goods_name', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result(stripslashes($goods_name));
    }
}

/*------------------------------------------------------ */
//-- 修改商品货号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_POST['id']);
    $goods_sn = json_str_iconv(trim($_POST['val']));

    /* 检查是否重复 */
    if (!$exc->is_only('goods_sn', $goods_sn, $goods_id, "user_id = '" .$adminru['ru_id']. "'"))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    
    $where = " AND (SELECT g.user_id FROM " . $GLOBALS['ecs']->table('goods') . " AS g WHERE g.goods_id = p.goods_id LIMIT 1) = '" .$adminru['ru_id']. "'";
    $sql="SELECT p.goods_id FROM ". $ecs->table('products')." AS p WHERE p.product_sn='$goods_sn'" . $where;
    if($db->getOne($sql))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    if ($exc->edit("goods_sn = '$goods_sn', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result(stripslashes($goods_sn));
    }
}

/*------------------------------------------------------ */
//-- 修改商品条形码
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_bar_code')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_POST['id']);
    $bar_code = json_str_iconv(trim($_POST['val']));

    /* 检查是否重复 */
    if (!$exc->is_only('bar_code', $bar_code, $goods_id, "user_id = '" .$adminru['ru_id']. "'"))
    {
        make_json_error($_LANG['goods_bar_code_exists']);
    }
    
    $where = " AND (SELECT g.user_id FROM " . $GLOBALS['ecs']->table('goods') . " AS g WHERE g.goods_id = p.goods_id LIMIT 1) = '" .$adminru['ru_id']. "'";
    $sql="SELECT p.goods_id FROM ". $ecs->table('products')." AS p WHERE p.bar_code = '$bar_code'" . $where;
    if($db->getOne($sql))
    {
        make_json_error($_LANG['goods_bar_code_exists']);
    }
    if ($exc->edit("bar_code = '$bar_code'", $goods_id))
    {
        clear_cache_files();
        make_json_result(stripslashes($bar_code));
    }
}

elseif ($_REQUEST['act'] == 'check_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_sn = htmlspecialchars(json_str_iconv(trim($_REQUEST['goods_sn'])));

    /* 检查是否重复 */
    if (!$exc->is_only('goods_sn', $goods_sn, $goods_id))
    {
        make_json_error($_LANG['goods_sn_exists']);
    }
    if(!empty($goods_sn))
    {
        $sql="SELECT goods_id FROM ". $ecs->table('products')."WHERE product_sn='$goods_sn'";
        if($db->getOne($sql))
        {
            make_json_error($_LANG['goods_sn_exists']);
        }
    }
    make_json_result('');
}
elseif ($_REQUEST['act'] == 'check_products_goods_sn')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_sn = json_str_iconv(trim($_REQUEST['goods_sn']));
    $products_sn=explode('||',$goods_sn);
    if(!is_array($products_sn))
    {
        make_json_result('');
    }
    else
    {
        foreach ($products_sn as $val)
        {
            if(empty($val))
            {
                 continue;
            }
            if(is_array($int_arry))
            {
                if(in_array($val,$int_arry))
                {
                     make_json_error($val.$_LANG['goods_sn_exists']);
                }
            }
            $int_arry[]=$val;
            if (!$exc->is_only('goods_sn', $val, '0'))
            {
                make_json_error($val.$_LANG['goods_sn_exists']);
            }
            $sql="SELECT goods_id FROM ". $ecs->table('products')."WHERE product_sn='$val'";
            if($db->getOne($sql))
            {
                make_json_error($val.$_LANG['goods_sn_exists']);
            }
        }
    }
    /* 检查是否重复 */
    make_json_result('');
}

/*------------------------------------------------------ */
//-- 修改商品价格
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_price')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $goods_price    = floatval($_POST['val']);
    $price_rate     = floatval($_CFG['market_price_rate'] * $goods_price);

    if ($goods_price < 0 || $goods_price == 0 && $_POST['val'] != "$goods_price")
    {
        make_json_error($_LANG['shop_price_invalid']);
    }
    else
    {
        if ($exc->edit("shop_price = '$goods_price', market_price = '$price_rate', last_update=" .gmtime(), $goods_id))
        {
            clear_cache_files();
            make_json_result(number_format($goods_price, 2, '.', ''));
        }
    }
}

/*------------------------------------------------------ */
//-- 修改商品库存数量
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_goods_number')
{
    check_authz_json('goods_manage');

    $goods_id   = intval($_POST['id']);
    $goods_num  = intval($_POST['val']);

    if($goods_num < 0 || $goods_num == 0 && $_POST['val'] != "$goods_num")
    {
        make_json_error($_LANG['goods_number_error']);
    }

    if(check_goods_product_exist($goods_id) == 1)
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_goods_number']);
    }

    $goodsInfo = get_goods_info($goods_id, array('goods_number', 'model_inventory', 'model_attr'));
    if ($goods_num != $goodsInfo['goods_number'])
    {
        if ($goods_num > $goodsInfo['goods_number']) {
            $number = $goods_num - $goodsInfo['goods_number'];
            $number = '+ ' . $number;
            $use_storage = 13;
        }
        else
        {
            $number = $goodsInfo['goods_number'] - $goods_num;
            $number = '- ' . $number;
            $use_storage = 8;
        }
        $logs_other = array(
                        'goods_id' => $goods_id, 
                        'order_id' => 0, 
                        'use_storage' => $use_storage, 
                        'admin_id' => $_SESSION['seller_id'], 
                        'number' => $number, 
                        'model_inventory' => $goodsInfo['model_inventory'], 
                        'model_attr' => $goodsInfo['model_attr'], 
                        'product_id' => 0, 
                        'warehouse_id' => 0, 
                        'area_id' => 0, 
                        'add_time' => gmtime()
                        );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
    }

    if ($exc->edit("goods_number = '$goods_num', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($goods_num);
    }
}

/*------------------------------------------------------ */
//-- 修改上架状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_on_sale')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $on_sale        = intval($_POST['val']);

    if ($exc->edit("is_on_sale = '$on_sale', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($on_sale);
    }
}

/*------------------------------------------------------ */
//-- 修改精品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_best')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_best        = intval($_POST['val']);

    if ($exc->edit("is_best = '$is_best', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_best);
    }
}
elseif ($_REQUEST['act'] == 'main_dsc') {
    $data = read_static_cache('seller_goods_str');
    if ($data === false){
        $shop_url = urlencode($ecs->url());
        $shop_info = get_shop_info_content(0);
        if($shop_info){
            $shop_country   = $shop_info['country'];
            $shop_province  = $shop_info['province'];
            $shop_city      = $shop_info['city'];
            $shop_address   = $shop_info['shop_address'];
        }else{
            $shop_country   = $_CFG['shop_country'];
            $shop_province  = $_CFG['shop_province'];
            $shop_city      = $_CFG['shop_city'];
            $shop_address   = $_CFG['shop_address'];
        }
        
        $qq = !empty($_CFG['qq']) ? $_CFG['qq'] : $shop_info['kf_qq'];
        $ww = !empty($_CFG['ww']) ? $_CFG['ww'] : $shop_info['kf_ww'];
        $service_email = !empty($_CFG['service_email']) ? $_CFG['service_email'] : $shop_info['seller_email'];
        $service_phone = !empty($_CFG['service_phone']) ? $_CFG['service_phone'] : $shop_info['kf_tel'];

        $shop_country   = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id='$shop_country'");
        $shop_province  = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id='$shop_province'");
        $shop_city      = $db->getOne("SELECT region_name FROM ".$ecs->table('region')." WHERE region_id='$shop_city'");

        $httpData = array(
                    'domain'            =>  $ecs->get_domain(), //当前域名
                    'url'               =>  urldecode($shop_url), //当前url
                    'shop_name'         =>  $_CFG['shop_name'],
                    'shop_title'        =>  $_CFG['shop_title'],
                    'shop_desc'         =>  $_CFG['shop_desc'],
                    'shop_keywords'     =>  $_CFG['shop_keywords'],
                    'country'           =>  $shop_country,
                    'province'          =>  $shop_province,
                    'city'              =>  $shop_city,
                    'address'           =>  $shop_address,
                    'qq'                =>  $qq,
                    'ww'                =>  $ww,
                    'ym'                =>  $service_phone, //客服电话
                    'msn'               =>  $_CFG['msn'],
                    'email'             =>  $service_email,
                    'phone'             =>  $_CFG['sms_shop_mobile'], //手机号
                    'icp'               =>  $_CFG['icp_number'],
                    'version'           =>  VERSION,
                    'release'           =>  RELEASE,
                    'language'          =>  $_CFG['lang'],
                    'php_ver'           =>  PHP_VERSION,
                    'mysql_ver'         =>  $db->version(),
                    'charset'           =>  EC_CHARSET
            );

        $Http = new Http();
        $Http->doPost($_CFG['certi'], $httpData); 
        
        write_static_cache('seller_goods_str', $httpData);
    }  
}
/*------------------------------------------------------ */
//-- 修改新品推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_new')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_new         = intval($_POST['val']);

    if ($exc->edit("is_new = '$is_new', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_new);
    }
}

/*------------------------------------------------------ */
//-- 修改热销推荐状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_hot')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_hot         = intval($_POST['val']);

    if ($exc->edit("is_hot = '$is_hot', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_hot);
    }
}

/*------------------------------------------------------ */
//-- 修改店铺精品推荐状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_store_best')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $store_best         = intval($_POST['val']);
    
    if ($exc->edit("store_best = '$store_best', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($store_best);
    }
}

/*------------------------------------------------------ */
//-- 修改店铺新品推荐状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_store_new')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $store_new         = intval($_POST['val']);

    if ($exc->edit("store_new = '$store_new', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($store_new);
    }
}

/*------------------------------------------------------ */
//-- 修改店铺热销推荐状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_store_hot')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $store_hot         = intval($_POST['val']);

    if ($exc->edit("store_hot = '$store_hot', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($store_hot);
    }
}

/*------------------------------------------------------ */
//-- 修改正品保证状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_reality')
{
    check_authz_json('goods_manage');

    $id      = intval($_POST['id']);
    $val     = intval($_POST['val']);

    if ($exc_extend->edit("is_reality = '$val'", $id))
    {
        clear_cache_files();
        make_json_result($val);
    }
}

/*------------------------------------------------------ */
//-- 修改包退服务状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_return')
{
    check_authz_json('goods_manage');

    $id      = intval($_POST['id']);
    $val     = intval($_POST['val']);

    if ($exc_extend->edit("is_return = '$val'", $id))
    {
        clear_cache_files();
        make_json_result($val);
    }
}

/*------------------------------------------------------ */
//-- 修改闪速配送状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_fast')
{
    check_authz_json('goods_manage');

    $id      = intval($_POST['id']);
    $val     = intval($_POST['val']);

    if ($exc_extend->edit("is_fast = '$val'", $id))
    {
        clear_cache_files();
        make_json_result($val);
    }
}

/*------------------------------------------------------ */
//-- 修改是否为免运费商品状态 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_is_shipping')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $is_shipping         = intval($_POST['val']);

    if ($exc->edit("is_shipping = '$is_shipping', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($is_shipping);
    }
}

/*------------------------------------------------------ */
//-- 修改商品排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('goods_manage');

    $goods_id       = intval($_POST['id']);
    $sort_order     = intval($_POST['val']);

    if ($exc->edit("sort_order = '$sort_order', last_update=" .gmtime(), $goods_id))
    {
        clear_cache_files();
        make_json_result($sort_order);
    }
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $is_delete = empty($_REQUEST['is_delete']) ? 0 : intval($_REQUEST['is_delete']);
    $code = empty($_REQUEST['extension_code']) ? '' : trim($_REQUEST['extension_code']);
    $goods_list = goods_list($is_delete, ($code=='') ? 1 : 0);

    $handler_list = array();
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=card', 'title'=>$_LANG['card'], 'img'=>'icon_send_bonus.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=replenish', 'title'=>$_LANG['replenish'], 'img'=>'icon_add.gif');
    $handler_list['virtual_card'][] = array('url'=>'virtual_card.php?act=batch_card_add', 'title'=>$_LANG['batch_card_add'], 'img'=>'icon_output.gif');
    //$smarty->assign('cat_list', cat_list_one(0));
    if (isset($handler_list[$code]))
    {
        $smarty->assign('add_handler',      $handler_list[$code]);
    }
    $smarty->assign('code',         $code);
    $smarty->assign('goods_list',   $goods_list['goods']);
    $smarty->assign('filter',       $goods_list['filter']);
    $smarty->assign('record_count', $goods_list['record_count']);
    $smarty->assign('page_count',   $goods_list['page_count']);
    $smarty->assign('list_type',    $is_delete ? 'trash' : 'goods');
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);

	//分页
	$page_count_arr = seller_page($goods_list,$_REQUEST['page']);
    $smarty->assign('page_count_arr',$page_count_arr);	

    /* 排序标记 */
    $sort_flag  = sort_flag($goods_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    /* 获取商品类型存在规格的类型 */
    $specifications = get_goods_type_specifications();
    $smarty->assign('specifications', $specifications);

    $tpl = $is_delete ? 'goods_trash.dwt' : 'goods_list.dwt';
        
    $store_list = get_common_store_list();
    $smarty->assign('store_list',        $store_list);
    $smarty->assign('nowTime', gmtime());

    make_json_result($smarty->fetch($tpl), '',
        array('filter' => $goods_list['filter'], 'page_count' => $goods_list['page_count']));
}

/*------------------------------------------------------ */
//-- 放入回收站
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $goods_id = intval($_REQUEST['id']);

    /* 检查权限 */
    check_authz_json('remove_back');
	
	$sql = "SELECT goods_id, user_id " . "FROM " . $ecs->table('goods') .
            " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);

	$adminru = get_admin_ru_id();
	if($adminru['ru_id'] > 0 && $adminru['ru_id'] != $goods['user_id']){
		make_json_error("非法操作,信息已被记录");
	}

    if ($exc->edit("is_delete = 1", $goods_id))
    {
        clear_cache_files();
        $goods_name = $exc->get_name($goods_id);

        admin_log(addslashes($goods_name), 'trash', 'goods'); // 记录日志

        $url = 'goods.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
}

/*------------------------------------------------------ */
//-- 还原回收站中的商品
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'restore_goods')
{
    $goods_id = intval($_REQUEST['id']);

    check_authz_json('remove_back'); // 检查权限

    $exc->edit("is_delete = 0, add_time = '" . gmtime() . "'", $goods_id);
    clear_cache_files();

    $goods_name = $exc->get_name($goods_id);

    admin_log(addslashes($goods_name), 'restore', 'goods'); // 记录日志

    $url = 'goods.php?act=query&' . str_replace('act=restore_goods', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/*------------------------------------------------------ */
//-- 彻底删除商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods')
{
    // 检查权限
    check_authz_json('remove_back');

    // 取得参数
    $goods_id = intval($_REQUEST['id']);
    if ($goods_id <= 0)
    {
        make_json_error('invalid params');
    }
    
    //OSS文件存储ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['open_oss'] == 1){
        $bucket_info = get_bucket_info();
        $urlip = get_ip_url($GLOBALS['ecs']->url());
        $url = $urlip . "oss.php?act=del_file";
        $Http = new Http();
    }
    //OSS文件存储ecmoban模板堂 --zhuo end

    /* 取得商品信息 */
    $sql = "SELECT goods_id, goods_name, is_delete, is_real, goods_thumb, user_id, " .
                "goods_img, original_img " .
            "FROM " . $ecs->table('goods') .
            " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        make_json_error($_LANG['goods_not_exist']);
    }
    
    $adminru = get_admin_ru_id();
    if($adminru['ru_id'] > 0 && $adminru['ru_id'] != $goods['user_id']){
            make_json_error("非法操作，信息已被记录");
    }

    if ($goods['is_delete'] != 1)
    {
        make_json_error($_LANG['goods_not_in_recycle_bin']);
    }
    
    //ecmoban模板堂 --zhuo start
    if($goods['goods_desc']){
        $desc_preg = get_goods_desc_images_preg('', $goods['goods_desc']); 
        get_desc_images_del($desc_preg['images_list']);
    }
    //ecmoban模板堂 --zhuo end

    /* 删除商品图片和轮播图片 */
    if (!empty($goods['goods_thumb']))
    {
        @unlink('../' . $goods['goods_thumb']);
    }
    if (!empty($goods['goods_img']))
    {
        @unlink('../' . $goods['goods_img']);
    }
    if (!empty($goods['original_img']))
    {
        @unlink('../' . $goods['original_img']);
    }
    
    //OSS文件存储ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['open_oss'] == 1){
        $post_data = array(
            'bucket'        => $bucket_info['bucket'],
            'keyid'         => $bucket_info['keyid'],
            'keysecret'     => $bucket_info['keysecret'],
            'is_cname'      => $bucket_info['is_cname'],
            'endpoint'      => $bucket_info['outside_site'],
            'object' => array(
                $goods['goods_thumb'],
                $goods['goods_img'],
                $goods['original_img']
                ) 
        );

        $Http->doPost($url, $post_data);
    }
    //OSS文件存储ecmoban模板堂 --zhuo end
    
    /* 删除商品 */
    $exc->drop($goods_id);
	
	//删除商品扩展信息by wang
	$sql="delete from ".$ecs->table('goods_extend')." where goods_id='$goods_id'";
	$db->query($sql);
    /* 删除商品的货品记录 */
    $sql = "DELETE FROM " . $ecs->table('products') .
            " WHERE goods_id = '$goods_id'";
    $db->query($sql);

    /* 记录日志 */
    admin_log(addslashes($goods['goods_name']), 'remove', 'goods');

    /* 删除商品相册 */
    $sql = "SELECT img_url, thumb_url, img_original " .
            "FROM " . $ecs->table('goods_gallery') .
            " WHERE goods_id = '$goods_id'";
    $res = $db->query($sql);
    while ($row = $db->fetchRow($res))
    {
        if (!empty($row['img_url']))
        {
            @unlink('../' . $row['img_url']);
        }
        if (!empty($row['thumb_url']))
        {
            @unlink('../' . $row['thumb_url']);
        }
        if (!empty($row['img_original']))
        {
            @unlink('../' . $row['img_original']);
        }
        
        //OSS文件存储ecmoban模板堂 --zhuo start
        if($GLOBALS['_CFG']['open_oss'] == 1){
            $post_data = array(
                'bucket'        => $bucket_info['bucket'],
                'keyid'         => $bucket_info['keyid'],
                'keysecret'     => $bucket_info['keysecret'],
                'is_cname'      => $bucket_info['is_cname'],
                'endpoint'      => $bucket_info['outside_site'],
                'object' => array(
                    $row['img_url'],
                    $row['thumb_url'],
                    $row['img_original']
                    ) 
            );

            $Http->doPost($url, $post_data);
        }
        //OSS文件存储ecmoban模板堂 --zhuo end
    }
    
    $sql = "DELETE FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);

    /* 删除相关表记录 */
    $sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_attr') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_cat') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('member_price') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE parent_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('group_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('link_goods') . " WHERE link_goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('tag') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('comment') . " WHERE comment_type = 0 AND id_value = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('collect_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('booking_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('goods_activity') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('cart') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('warehouse_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('warehouse_attr') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('warehouse_area_goods') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);
    $sql = "DELETE FROM " . $ecs->table('warehouse_area_attr') . " WHERE goods_id = '$goods_id'";
    $db->query($sql);

    /* 如果不是实体商品，删除相应虚拟商品记录 */
    if ($goods['is_real'] != 1)
    {
        $sql = "DELETE FROM " . $ecs->table('virtual_card') . " WHERE goods_id = '$goods_id'";
        if (!$db->query($sql, 'SILENT') && $db->errno() != 1146)
        {
            die($db->error());
        }
    }

    clear_cache_files();
    $url = 'goods.php?act=query&' . str_replace('act=drop_goods', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");

    exit;
}

/*------------------------------------------------------ */
//-- 切换商品类型
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_attr')
{
    check_authz_json('goods_manage');

    $goods_id   = empty($_GET['goods_id']) ? 0 : intval($_GET['goods_id']);
    $goods_type = empty($_GET['goods_type']) ? 0 : intval($_GET['goods_type']);
	
	//判断商品模式
	$modelAttr = empty($_GET['modelAttr']) ? 0 : intval($_GET['modelAttr']);
	
    $content    = build_attr_html($goods_type, $goods_id, $modelAttr);

    make_json_result($content);
}

/*------------------------------------------------------ */
//-- 删除图片
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_image')
{
    check_authz_json('goods_manage');

    $img_id = empty($_REQUEST['img_id']) ? 0 : intval($_REQUEST['img_id']);

    /* 删除图片文件 */
    $sql = "SELECT img_url, thumb_url, img_original " .
            " FROM " . $GLOBALS['ecs']->table('goods_gallery') .
            " WHERE img_id = '$img_id'";
    $row = $GLOBALS['db']->getRow($sql);
    
    $img_url = ROOT_PATH . $row['img_url'];
    $thumb_url = ROOT_PATH . $row['thumb_url'];
    $img_original = ROOT_PATH . $row['img_original'];

    if ($row['img_url'] != '' && is_file($img_url))
    {
        @unlink($img_url);
    }
    if ($row['thumb_url'] != '' && is_file($thumb_url))
    {
        @unlink($thumb_url);
    }
    if ($row['img_original'] != '' && is_file($img_original))
    {
        @unlink($img_original);
    }
    
    get_oss_del_file(array($row['img_url'], $row['thumb_url'], $row['img_original']));

    /* 删除数据 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_gallery') . " WHERE img_id = '$img_id' LIMIT 1";
    $GLOBALS['db']->query($sql);

    clear_cache_files();
    make_json_result($img_id);
}

/*------------------------------------------------------ */
//-- 删除仓库库存 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_warehouse')
{
    check_authz_json('goods_manage');

    $w_id = empty($_REQUEST['w_id']) ? 0 : intval($_REQUEST['w_id']);

    /* 删除数据 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('warehouse_goods') . " WHERE w_id = '$w_id' LIMIT 1";
    $GLOBALS['db']->query($sql);

    clear_cache_files();
    make_json_result($w_id);
}

/*------------------------------------------------------ */
//-- 修改商品仓库库存 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_number')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $region_number   = intval($_POST['val']);

    $sql = "SELECT goods_id, region_number, region_id FROM " . $ecs->table('warehouse_goods') . " WHERE w_id = '$w_id' LIMIT 1";
    $warehouse_goods = $db->getRow($sql);
    $goodsInfo = get_goods_info($warehouse_goods['goods_id'], array('model_inventory', 'model_attr'));

    if ($region_number != $warehouse_goods['region_number'])
    {
        if ($region_number > $warehouse_goods['region_number'])
        {
            $number = $region_number - $warehouse_goods['region_number'];
            $number = '+ ' . $number;
            $use_storage = 13;
        }
        else
        {
            $number = $warehouse_goods['region_number'] - $region_number;
            $number = '- ' . $number;
            $use_storage = 8;
        }
        $logs_other = array('goods_id' => $warehouse_goods['goods_id'], 
                            'order_id' => 0, 
                            'use_storage' => $use_storage, 
                            'admin_id' => $_SESSION['seller_id'], 
                            'number' => $number, 
                            'model_inventory' => $goodsInfo['model_inventory'], 
                            'model_attr' => $goodsInfo['model_attr'], 
                            'product_id' => 0, 
                            'warehouse_id' => $warehouse_goods['region_id'], 
                            'area_id' => 0, 
                            'add_time' => gmtime()
                            );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
    }

    $sql = "update " .$ecs->table('warehouse_goods'). " set region_number = '$region_number' where w_id = '$w_id' ";
    $res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($region_number);
    }
}

/*------------------------------------------------------ */
//-- 修改库存商品货号 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_sn')
{
    check_authz_json('goods_manage');

    $w_id = intval($_POST['id']);
    $region_sn = addslashes(trim($_POST['val']));

    /*更新数据*/
    $sql = "update " . $ecs->table('warehouse_goods') . " set region_sn = '$region_sn' where w_id = '$w_id' ";
    $res = $db->query($sql);

    if ($res)
    {
        clear_cache_files();
        make_json_result($region_sn);
    }
}
/*------------------------------------------------------ */
//-- 修改商品仓库价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_price')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $warehouse_price   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_goods'). " set warehouse_price = '$warehouse_price' where w_id = '$w_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($warehouse_price);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库促销价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_promote_price')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $warehouse_promote_price   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_goods'). " set warehouse_promote_price = '$warehouse_promote_price' where w_id = '$w_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($warehouse_promote_price);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库赠送消费积分数 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_give_integral')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $give_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_goods'). " set give_integral = '$give_integral' where w_id = '$w_id' ";
    $res = $db->query($sql);
    
    $other = array( 'w_id', 'user_id', 'warehouse_price', 'warehouse_promote_price');
    $goods = get_table_date('warehouse_goods', "w_id='$w_id'", $other);
    
    if($goods['warehouse_promote_price']){
        if($goods['warehouse_promote_price'] < $goods['warehouse_price']){
            $shop_price = $goods['warehouse_promote_price'];
        }else{
            $shop_price = $goods['warehouse_price'];
        }
    }else{
        $shop_price = $goods['warehouse_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $give = floor($shop_price * $grade_rank['give_integral']);
    
    if($give_integral > $give){
        make_json_error(sprintf($_LANG['goods_give_integral'], $give));
    }
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($give_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库赠送等级积分数 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_rank_integral')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $rank_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_goods'). " set rank_integral = '$rank_integral' where w_id = '$w_id' ";
    $res = $db->query($sql);
    
    $other = array( 'w_id', 'user_id', 'warehouse_price', 'warehouse_promote_price');
    $goods = get_table_date('warehouse_goods', "w_id='$w_id'", $other);
    
    if($goods['warehouse_promote_price']){
        if($goods['warehouse_promote_price'] < $goods['warehouse_price']){
            $shop_price = $goods['warehouse_promote_price'];
        }else{
            $shop_price = $goods['warehouse_price'];
        }
    }else{
        $shop_price = $goods['warehouse_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $rank = floor($shop_price * $grade_rank['rank_integral']);
    
    if($rank_integral > $rank){
        make_json_error(sprintf($_LANG['goods_rank_integral'], $rank));
    }
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($rank_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库积分购买金额 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_pay_integral')
{
    check_authz_json('goods_manage');

    $w_id       = intval($_POST['id']);
    $pay_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_goods'). " set pay_integral = '$pay_integral' where w_id = '$w_id' ";
    $res = $db->query($sql);
    
    $other = array( 'w_id', 'user_id', 'warehouse_price', 'warehouse_promote_price');
    $goods = get_table_date('warehouse_goods', "w_id='$w_id'", $other);
    
    if($goods['warehouse_promote_price']){
        if($goods['warehouse_promote_price'] < $goods['warehouse_price']){
            $shop_price = $goods['warehouse_promote_price'];
        }else{
            $shop_price = $goods['warehouse_price'];
        }
    }else{
        $shop_price = $goods['warehouse_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $pay = floor($shop_price / 100 * $_CFG['integral_scale'] * $grade_rank['pay_integral']);
    
    if($pay_integral > $rank){
        make_json_error(sprintf($_LANG['goods_pay_integral'], $pay));
    }

    if ($res)
    {
        clear_cache_files();
        make_json_result($pay_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改地区商品货号 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST["act"] == "edit_region_sn")
{
    check_authz_json("goods_manage");

    $a_id = intval($_POST["id"]);
    $region_sn = addslashes(trim($_POST["val"]));

    /*更新数据*/
    $sql = "update " . $ecs->table("warehouse_area_goods") . " set region_sn = '$region_sn' where a_id = '$a_id' ";
    $res = $db->query($sql);

    if ($res)
    {
        clear_cache_files();
        make_json_result($region_sn);
    }
}
/*------------------------------------------------------ */
//-- 删除仓库地区价格 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_warehouse_area')
{
    check_authz_json('goods_manage');

    $a_id = empty($_REQUEST['a_id']) ? 0 : intval($_REQUEST['a_id']);

    /* 删除数据 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('warehouse_area_goods') . " WHERE a_id = '$a_id' LIMIT 1";
    $GLOBALS['db']->query($sql);

    clear_cache_files();
    make_json_result($a_id);
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_price')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $region_price   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_area_goods'). " set region_price = '$region_price' where a_id = '$a_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($region_price);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区库存 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_number')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $region_number   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_area_goods'). " set region_number = '$region_number' where a_id = '$a_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($region_number);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区促销价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_promote_price')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $region_promote_price   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_area_goods'). " set region_promote_price = '$region_promote_price' where a_id = '$a_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($region_promote_price);
    }
}

/*------------------------------------------------------ */
//-- 查询该仓库的地区列表 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_warehouse_area_list')
{
    check_authz_json('goods_manage');

    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $key = isset($_REQUEST['key']) ? intval($_REQUEST['key']) : 0;
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $ru_id = isset($_REQUEST['ru_id']) ? intval($_REQUEST['ru_id']) : 0;
	
	if($id > 0){
		$area_list = get_warehouse_area_list($id, 1, $goods_id, $ru_id);
		$smarty->assign('area_list',    $area_list);
		
		$result['error'] = 0;
		$result['key'] = $key;
		$result['html'] = $smarty->fetch('warehouse_area_list.dwt');
	}else{
		$result['key'] = $key;
		$result['error'] = 1;
	}
	
	make_json_result($result);
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区赠送消费积分数 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_give_integral')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $give_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_area_goods'). " set give_integral = '$give_integral' where a_id = '$a_id' ";
    $res = $db->query($sql);
    
    $other = array( 'a_id', 'user_id', 'region_price', 'region_promote_price');
    $goods = get_table_date('warehouse_area_goods', "a_id='$a_id'", $other);
    
    if($goods['region_promote_price']){
        if($goods['region_promote_price'] < $goods['region_price']){
            $shop_price = $goods['region_promote_price'];
        }else{
            $shop_price = $goods['region_price'];
        }
    }else{
        $shop_price = $goods['region_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $give = floor($shop_price * $grade_rank['give_integral']);
    
    if($give_integral > $give){
        make_json_error(sprintf($_LANG['goods_give_integral'], $give));
    }
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($give_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区赠送等级积分数 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_rank_integral')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $rank_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_area_goods'). " set rank_integral = '$rank_integral' where a_id = '$a_id' ";
    $res = $db->query($sql);
    
    $other = array( 'a_id', 'user_id', 'region_price', 'region_promote_price');
    $goods = get_table_date('warehouse_area_goods', "a_id='$a_id'", $other);
    
    if($goods['region_promote_price']){
        if($goods['region_promote_price'] < $goods['region_price']){
            $shop_price = $goods['region_promote_price'];
        }else{
            $shop_price = $goods['region_price'];
        }
    }else{
        $shop_price = $goods['region_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $rank = floor($shop_price * $grade_rank['rank_integral']);
    
    if($rank_integral > $rank){
        make_json_error(sprintf($_LANG['goods_rank_integral'], $rank));
    }
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($rank_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区积分购买金额 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_pay_integral')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $pay_integral   = floatval($_POST['val']);

    $sql = "update " .$ecs->table('warehouse_area_goods'). " set pay_integral = '$pay_integral' where a_id = '$a_id' ";
    $res = $db->query($sql);
    
    $other = array( 'a_id', 'user_id', 'region_price', 'region_promote_price');
    $goods = get_table_date('warehouse_area_goods', "a_id='$a_id'", $other);
    
    if($goods['region_promote_price']){
        if($goods['region_promote_price'] < $goods['region_price']){
            $shop_price = $goods['region_promote_price'];
        }else{
            $shop_price = $goods['region_price'];
        }
    }else{
        $shop_price = $goods['region_price'];
    }

    $grade_rank = get_seller_grade_rank($goods['user_id']);    
    $pay = floor($shop_price / 100 * $_CFG['integral_scale'] * $grade_rank['pay_integral']);
    
    if($pay_integral > $rank){
        make_json_error(sprintf($_LANG['goods_pay_integral'], $pay));
    }
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($pay_integral);
    }
}

/*------------------------------------------------------ */
//-- 修改商品仓库地区排序 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_region_sort')
{
    check_authz_json('goods_manage');

    $a_id       = intval($_POST['id']);
    $region_sort   = floatval($_POST['val']);

	$sql = "update " .$ecs->table('warehouse_area_goods'). " set region_sort = '$region_sort' where a_id = '$a_id' ";
	$res = $db->query($sql);
	
    if ($res)
    {
        clear_cache_files();
        make_json_result($region_sort);
    }
}

/*------------------------------------------------------ */
//-- 添加地区属性价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_area_price')
{
	$smarty->assign('menu_select', array('action' => '02_cat_and_goods', 'current' => '02_goods_add'));
	$smarty->assign('ur_here', $_LANG['area_spec_price']);
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
	$goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';
	
	$action_link = array('href' => 'goods.php?act=edit&goods_id=' .$goods_id. '&extension_code=', 'text' => $_LANG['goods_info']);
	
	$goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID
	
	$goods_date = array('goods_name');
	$goods_info = get_table_date('goods', "goods_id = '$goods_id'", $goods_date);
	
	$attr_date = array('attr_name');
	$attr_info = get_table_date('attribute', "attr_id = '$attr_id'", $attr_date);
	
	$warehouse_area_list = get_fine_warehouse_area_all(0, $goods_id, $goods_attr_id);
	
	$smarty->assign('goods_info', $goods_info);
	$smarty->assign('attr_info', $attr_info);
	$smarty->assign('goods_attr_name', $goods_attr_name);
	$smarty->assign('warehouse_area_list', $warehouse_area_list);
	$smarty->assign('goods_id', $goods_id);
	$smarty->assign('attr_id', $attr_id);
	$smarty->assign('goods_attr_id', $goods_attr_id);
	$smarty->assign('form_action', 'insert_area_price');
	$smarty->assign('action_link', $action_link);
	
	/* 显示属性地区价格信息页面 */
    assign_query_info();
    $smarty->display('goods_area_price_info.dwt');
}

/*------------------------------------------------------ */
//-- 添加地区属性价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_area_price')
{
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
	$area_name = isset($_REQUEST['area_name']) ? $_REQUEST['area_name'] : array();
	$attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
	$goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';
	
	get_warehouse_area_attr_price_insert($area_name, $goods_id, $goods_attr_id, 'warehouse_area_attr');
	
	$link[] = array('href' => 'javascript:history.back(-1)', 'text' => $_LANG['go_back']);
    sys_msg($_LANG['attradd_succed'], 1, $link);
}

/*------------------------------------------------------ */
//-- 添加仓库属性价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_warehouse_price')
{
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
	$goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';
	
	$action_link = array('href' => 'goods.php?act=edit&goods_id=' .$goods_id. '&extension_code=', 'text' => $_LANG['goods_info']);
	
	$goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID
	
	$goods_date = array('goods_name');
	$goods_info = get_table_date('goods', "goods_id = '$goods_id'", $goods_date);
	
	$attr_date = array('attr_name');
	$attr_info = get_table_date('attribute', "attr_id = '$attr_id'", $attr_date);
	
	$warehouse_area_list = get_fine_warehouse_all(0, $goods_id, $goods_attr_id);
	
	$smarty->assign('goods_info', $goods_info);
	$smarty->assign('attr_info', $attr_info);
	$smarty->assign('goods_attr_name', $goods_attr_name);
	$smarty->assign('warehouse_area_list', $warehouse_area_list);
	$smarty->assign('goods_id', $goods_id);
	$smarty->assign('attr_id', $attr_id);
	$smarty->assign('goods_attr_id', $goods_attr_id);
	$smarty->assign('form_action', 'insert_warehouse_price');
	$smarty->assign('action_link', $action_link);
	
	/* 显示属性地区价格信息页面 */
    assign_query_info();
	make_json_result($smarty->fetch('goods_warehouse_price_info.dwt'));
}

/*------------------------------------------------------ */
//-- 添加仓库属性价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_warehouse_price')
{
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
	$warehouse_name = isset($_REQUEST['warehouse_name']) ? $_REQUEST['warehouse_name'] : array();
	$attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
	$goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';
	
	get_warehouse_area_attr_price_insert($warehouse_name, $goods_id, $goods_attr_id, 'warehouse_attr');
	
	//$link[] = array('href' => 'goods.php?act=add_warehouse_price&goods_id=' .$goods_id. '&attr_id=' .$attr_id. '&goods_attr_name=' . $goods_attr_name, 'text' => $_LANG['go_back']);
	$link[] = array('href' => 'javascript:history.back(-1)', 'text' => $_LANG['go_back']); //by wu
    sys_msg($_LANG['attradd_succed'], 1, $link);
}

/*------------------------------------------------------ */
//-- 添加属性图片 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_attr_img')
{
	check_authz_json('goods_manage');
	
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
	$goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';
	
	$action_link = array('href' => 'goods.php?act=edit&goods_id=' .$goods_id. '&extension_code=', 'text' => $_LANG['goods_info']);
	
	$goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID
	
	$goods_date = array('goods_name');
	$goods_info = get_table_date('goods', "goods_id = '$goods_id'", $goods_date);
	
	$goods_attr_date = array('attr_img_flie, attr_img_site, attr_checked, attr_gallery_flie');
	$goods_attr_info = get_table_date('goods_attr', "goods_id = '$goods_id' and attr_id = '$attr_id' and goods_attr_id = '$goods_attr_id'", $goods_attr_date);
	
	$attr_date = array('attr_name');
	$attr_info = get_table_date('attribute', "attr_id = '$attr_id'", $attr_date);
	
	$smarty->assign('goods_info', $goods_info);
	$smarty->assign('attr_info', $attr_info);
	$smarty->assign('goods_attr_info', $goods_attr_info);
	$smarty->assign('goods_attr_name', $goods_attr_name);
	$smarty->assign('goods_id', $goods_id);
	$smarty->assign('attr_id', $attr_id);
	$smarty->assign('goods_attr_id', $goods_attr_id);
	$smarty->assign('form_action', 'insert_attr_img');
	$smarty->assign('action_link', $action_link);

	make_json_result($smarty->fetch('goods_attr_img_info.dwt'));
}

/*------------------------------------------------------ */
//-- 添加属性图片插入数据 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_attr_img')
{
	admin_priv('goods_manage');
	
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';
    $img_url = !empty($_REQUEST['img_url']) ? $_REQUEST['img_url'] : '';

    include_once(ROOT_PATH . '/includes/cls_image.php');
    $image = new cls_image($_CFG['bgcolor']); 
    /* 允许上传的文件类型 */
    $allow_file_types = '|GIF|JPG|JEPG|PNG|';
 
    $other['attr_img_flie'] = get_upload_pic('attr_img_flie');
    
    get_oss_add_file(array($other['attr_img_flie']));

    $goods_attr_date = array('attr_img_flie, attr_img_site');
    $goods_attr_info = get_table_date('goods_attr', "goods_id = '$goods_id' and attr_id = '$attr_id' and goods_attr_id = '$goods_attr_id'", $goods_attr_date);

    if(empty($other['attr_img_flie'])){
            $other['attr_img_flie'] = $goods_attr_info['attr_img_flie'];
    }

    $other['attr_img_site'] = !empty($_REQUEST['attr_img_site']) ? $_REQUEST['attr_img_site'] : '';
    $other['attr_checked'] = !empty($_REQUEST['attr_checked']) ? intval($_REQUEST['attr_checked']) : 0;
    $other['attr_gallery_flie'] = $img_url;

    $db->autoExecute($ecs->table('goods_attr'), $other, 'UPDATE', 'goods_attr_id = ' . $goods_attr_id . ' and attr_id = ' . $attr_id . ' and goods_id = ' . $goods_id);

    $link[0] = array('text' => "返回商品详情页", 'href' => "goods.php?act=edit&goods_id=" .$goods_id. "&extension_code=&properties=1");
    sys_msg($_LANG['attradd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 删除属性图片 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_attr_img')
{
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_name = isset($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';
    
    $sql = "select attr_img_flie from " .$ecs->table('goods_attr'). " where goods_attr_id = '$goods_attr_id'";
    $attr_img_flie = $db->getOne($sql);
    
    //OSS文件存储ecmoban模板堂 --zhuo start
    if($GLOBALS['_CFG']['open_oss'] == 1){
        $bucket_info = get_bucket_info();
        $urlip = get_ip_url($GLOBALS['ecs']->url());
        $url = $urlip . "oss.php?act=del_file";
        $Http = new Http();
        $post_data = array(
            'bucket'        => $bucket_info['bucket'],
            'keyid'         => $bucket_info['keyid'],
            'keysecret'     => $bucket_info['keysecret'],
            'is_cname'      => $bucket_info['is_cname'],
            'endpoint'      => $bucket_info['outside_site'],
            'object' => array(
                $attr_img_flie
            ) 
        );

        $Http->doPost($url, $post_data);
    }
    //OSS文件存储ecmoban模板堂 --zhuo end
    
    @unlink(ROOT_PATH  . $attr_img_flie);
    $other['attr_img_flie'] = '';
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_attr'), $other, "UPDATE", "goods_attr_id = '$goods_attr_id'");
    
    //$link[0] = array('text' => "返回上一步", 'href' => "goods.php?act=add_attr_img&goods_id=" .$goods_id. "&attr_id=" .$attr_id. "&goods_attr_name=" . $goods_attr_name);
    $link[0] = array('text' => "返回商品详情页", 'href' => "goods.php?act=edit&goods_id=" .$goods_id. "&extension_code=");
    sys_msg($_LANG['drop_attr_img_success'], 0, $link);
}

/*------------------------------------------------------ */
//-- 选择属性图片 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'choose_attrImg')
{
    check_authz_json('goods_manage');

    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
	$goods_attr_id = empty($_REQUEST['goods_attr_id']) ? 0 : intval($_REQUEST['goods_attr_id']);
	$on_img_id = isset($_REQUEST['img_id']) ? intval($_REQUEST['img_id']) : 0;
	
	$sql = "SELECT attr_gallery_flie FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_attr_id = '$goods_attr_id' AND goods_id = '$goods_id'";
    $attr_gallery_flie = $GLOBALS['db']->getOne($sql);
  	
    /* 删除数据 */
    $sql = "SELECT img_id, thumb_url, img_url FROM " . $GLOBALS['ecs']->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
    $img_list = $GLOBALS['db']->getAll($sql);
	
	$result = "<ul>";
	foreach($img_list as $idx => $row)
	{
		if($attr_gallery_flie == $row['img_url'])
		{
			$result .= '<li id="gallery_'.$row['img_id'].'" onClick="gallery_on(this,'.$row['img_id'].','.$goods_id.','.$goods_attr_id.')" class="on"><img src="../'.$row['thumb_url'].'" width="120" /><i><img src="images/gallery_yes.png" width="30" height="30"></i></li>';
		}
		else
		{
			$result .= '<li id="gallery_'.$row['img_id'].'" onClick="gallery_on(this,'.$row['img_id'].','.$goods_id.','.$goods_attr_id.')"><img src="../'.$row['thumb_url'].'" width="120" /><i><img src="images/gallery_yes.png" width="30" height="30"></i></li>';	
		}
		
	}
	$result .= "</ul>";

    clear_cache_files();
    make_json_result($result);
}

/*------------------------------------------------------ */
//-- 选择属性图片 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_gallery_attr')
{
    check_authz_json('goods_manage');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_attr_id = intval($_REQUEST['goods_attr_id']);
    $gallery_id = intval($_REQUEST['gallery_id']);
  
    if(!empty($gallery_id))
    {
        $sql="SELECT img_id, img_url FROM ". $ecs->table('goods_gallery')."WHERE img_id='$gallery_id'";
        $img = $db->getRow($sql);
        $result = $img['img_id'];

        $sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_gallery_flie = '" .$img['img_url']. "' WHERE goods_attr_id = '$goods_attr_id' AND goods_id = '$goods_id'";
        $db->query($sql);
    }
    else
    {
            make_json_error("此相册图片不存在!");	
    }

    make_json_result($result, '', array('img_url' => $img['img_url']));
}

/*------------------------------------------------------ */
//-- 搜索商品，仅返回名称及ID
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);
    }

    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索区域地区，仅返回名称及ID  //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'get_area_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);
	
    $arr = get_areaRegion_info_list($filters->ra_id);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['region_id'],
                        'text' => $val['region_name'],
						'data' => 0);
    }
	
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 把商品加入关联
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $linked_array   = $json->decode($_GET['add_ids']);
    $linked_goods   = $json->decode($_GET['JSON']);
    $goods_id       = $linked_goods[0];
    $is_double      = $linked_goods[1] == true ? 0 : 1;

    foreach ($linked_array AS $val)
    {
        if ($is_double)
        {
            /* 双向关联 */
            $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                    "VALUES ('$val', '$goods_id', '$is_double', '$_SESSION[seller_id]')";
            $db->query($sql, 'SILENT');
        }

        $sql = "INSERT INTO " . $ecs->table('link_goods') . " (goods_id, link_goods_id, is_double, admin_id) " .
                "VALUES ('$goods_id', '$val', '$is_double', '$_SESSION[seller_id]')";
        $db->query($sql, 'SILENT');
    }

    $linked_goods   = get_linked_goods($goods_id);
    $options        = array();

    foreach ($linked_goods AS $val)
    {
        $options[] = array('value'  => $val['goods_id'],
                        'text'      => $val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 删除关联商品
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_link_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $drop_goods     = $json->decode($_GET['drop_ids']);
    $drop_goods_ids = db_create_in($drop_goods);
    $linked_goods   = $json->decode($_GET['JSON']);
    $goods_id       = $linked_goods[0];
    $is_signle      = $linked_goods[1];

    if (!$is_signle)
    {
        $sql = "DELETE FROM " .$ecs->table('link_goods') .
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    }
    else
    {
        $sql = "UPDATE " .$ecs->table('link_goods') . " SET is_double = 0 ".
                " WHERE link_goods_id = '$goods_id' AND goods_id " . $drop_goods_ids;
    }
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[seller_id]'";
    }
    $db->query($sql);

    $sql = "DELETE FROM " .$ecs->table('link_goods') .
            " WHERE goods_id = '$goods_id' AND link_goods_id " . $drop_goods_ids;
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[seller_id]'";
    }
    $db->query($sql);

    $linked_goods = get_linked_goods($goods_id);
    $options      = array();

    foreach ($linked_goods AS $val)
    {
        $options[] = array(
                        'value' => $val['goods_id'],
                        'text'  => $val['goods_name'],
                        'data'  => '');
    }

    clear_cache_files();
    make_json_result($options);
}

/*------------------------------------------------------ */
//-- 增加一个配件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_group_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $fittings   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $price      = $arguments[1];
    $group_id      = $arguments[2];//by mike add
    
    $sql = "select count(*) from " .$ecs->table('group_goods'). " where parent_id = '$goods_id' and group_id = '$group_id' and admin_id = '" .$_SESSION['seller_id']. "'";
    $groupCount = $db->getOne($sql);
    
    $message = "";
    if($groupCount < 1000){
        foreach ($fittings AS $val)
        {
            $sql = "SELECT id FROM " .$ecs->table('group_goods'). " WHERE parent_id = '$goods_id' AND goods_id = '$val' AND group_id = '$group_id'";
            if(!$db->getOne($sql))
            {
                $sql = "INSERT INTO " . $ecs->table('group_goods') . " (parent_id, goods_id, goods_price, admin_id, group_id) " .
                        "VALUES ('$goods_id', '$val', '$price', '$_SESSION[seller_id]', '$group_id')";//by mike add
                $db->query($sql, 'SILENT');
            }
        }
        
        $error = 0;
    }else{
        $error = 1;
        $message = "一组配件只能添加五个商品，如需添加则删除该组其它配件商品";
    }

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['goods_id'],
                        'text'      => '['.$val['group_name'].']'.$val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt, $message, array('error' => $error));
}

/*------------------------------------------------------ */
//-- 删除一个配件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'drop_group_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $fittings   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $price      = $arguments[1];

    $sql = "DELETE FROM " .$ecs->table('group_goods') .
            " WHERE parent_id='$goods_id' AND " .db_create_in($fittings, 'goods_id');
    if ($goods_id == 0)
    {
        $sql .= " AND admin_id = '$_SESSION[seller_id]'";
    }
    $db->query($sql);

    $arr = get_group_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['goods_id'],
                        'text'      => '['.$val['group_name'].']'.$val['goods_name'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 增加一个关联地区 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_area_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $fittings   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $region_id      = $arguments[1];

    $sql = "SELECT user_id FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id = '$goods_id'";
    $ru_id = $GLOBALS['db']->getOne($sql);

    foreach ($fittings AS $val)
    {
        $sql = "INSERT INTO " . $ecs->table('link_area_goods') . " (goods_id, region_id, ru_id) " .
                "VALUES ('$goods_id', '$val', '$ru_id')";
        $db->query($sql, 'SILENT');
    }

    $arr = get_area_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['region_id'],
                        'text'      => $val['region_name'],
                        'data'      => 0);
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除一个关联地区 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'drop_area_goods')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

	$drop_goods     = $json->decode($_GET['drop_ids']);
    $drop_goods_ids = db_create_in($drop_goods);

    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];
    $region_id      = $arguments[1];

    $sql = "DELETE FROM " .$ecs->table('link_area_goods') . " WHERE region_id" .$drop_goods_ids. " and goods_id = '$goods_id'";
    if ($goods_id == 0)
    {
		$adminru = get_admin_ru_id();
		$ru_id = $adminru['ru_id'];
		
        $sql .= " AND ru_id = '$ru_id'";
    }
    $db->query($sql);

    $arr = get_area_goods($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['region_id'],
                        'text'      => $val['region_name'],
                        'data'      => 0);
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 搜索文章
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'get_article_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters =(array) $json->decode(json_str_iconv($_GET['JSON']));

    $where = " WHERE cat_id > 0 ";
    if (!empty($filters['title']))
    {
        $keyword  = trim($filters['title']);
        $where   .=  " AND title LIKE '%" . mysqli_like_quote($keyword) . "%' ";
    }

    $sql        = 'SELECT article_id, title FROM ' .$ecs->table('article'). $where.
                  'ORDER BY article_id DESC LIMIT 50';
    $res        = $db->query($sql);
    $arr        = array();

    while ($row = $db->fetchRow($res))
    {
        $arr[]  = array('value' => $row['article_id'], 'text' => $row['title'], 'data'=>'');
    }

    make_json_result($arr);
}

/*------------------------------------------------------ */
//-- 添加关联文章
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'add_goods_article')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $articles   = $json->decode($_GET['add_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    foreach ($articles AS $val)
    {
        $sql = "INSERT INTO " . $ecs->table('goods_article') . " (goods_id, article_id, admin_id) " .
                "VALUES ('$goods_id', '$val', '$_SESSION[seller_id]')";
        $db->query($sql);
    }

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['article_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 删除关联文章
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_goods_article')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    check_authz_json('goods_manage');

    $articles   = $json->decode($_GET['drop_ids']);
    $arguments  = $json->decode($_GET['JSON']);
    $goods_id   = $arguments[0];

    $sql = "DELETE FROM " .$ecs->table('goods_article') . " WHERE " . db_create_in($articles, "article_id") . " AND goods_id = '$goods_id'";
    $db->query($sql);

    $arr = get_goods_articles($goods_id);
    $opt = array();

    foreach ($arr AS $val)
    {
        $opt[] = array('value'      => $val['article_id'],
                        'text'      => $val['title'],
                        'data'      => '');
    }

    clear_cache_files();
    make_json_result($opt);
}

/*------------------------------------------------------ */
//-- 货品列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_list')
{
    admin_priv('goods_manage');
	
	$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));

    /* 是否存在商品id */
    if (empty($_GET['goods_id']))
    {
        $link[] = array('href' => 'goods.php?act=list', 'text' => $_LANG['cannot_found_goods']);
        sys_msg($_LANG['cannot_found_goods'], 1, $link);
    }
    else
    {
        $goods_id = intval($_GET['goods_id']);
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price, model_attr FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        $link[] = array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']);
        sys_msg($_LANG['cannot_found_goods'], 1, $link);
    }
    $smarty->assign('sn', sprintf($_LANG['good_goods_sn'], $goods['goods_sn']));
    $smarty->assign('price', sprintf($_LANG['good_shop_price'], $goods['shop_price']));
    $smarty->assign('goods_name', sprintf($_LANG['products_title'], $goods['goods_name']));
    $smarty->assign('goods_sn', sprintf($_LANG['products_title_2'], $goods['goods_sn']));
	$smarty->assign('model_attr', $goods['model_attr']);


    /* 获取商品规格列表 */
    $attribute = get_goods_specifications_list($goods_id);
    if (empty($attribute))
    {
        $link[] = array('href' => 'goods.php?act=edit&goods_id=' . $goods_id, 'text' => $_LANG['edit_goods']);
        sys_msg($_LANG['not_exist_goods_attr'], 1, $link);
    }
    foreach ($attribute as $attribute_value)
    {
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
    $attribute_count = count($_attribute);

    $smarty->assign('attribute_count',          $attribute_count);
    $smarty->assign('attribute_count_5',        ($attribute_count + 5));
    $smarty->assign('attribute',                $_attribute);
    $smarty->assign('product_sn',               $goods['goods_sn'] . '_');
    $smarty->assign('product_number',           $_CFG['default_storage']);

    /* 取商品的货品 */
    $product = product_list($goods_id, '');

    $smarty->assign('ur_here',      $_LANG['18_product_list']);
    $smarty->assign('action_link',  array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']));
    $smarty->assign('product_list', $product['product']);
    $smarty->assign('product_null', empty($product['product']) ? 0 : 1);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    $smarty->assign('goods_id',     $goods_id);
    $smarty->assign('filter',       $product['filter']);
    $smarty->assign('full_page',    1);

	$smarty->assign('product_php', 'goods.php');

    /* 显示商品列表页面 */
    assign_query_info();

    $smarty->display('product_info.dwt');
}

/*------------------------------------------------------ */
//-- 货品排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_query')
{
    /* 是否存在商品id */
    if (empty($_REQUEST['goods_id']))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    else
    {
        $goods_id = intval($_REQUEST['goods_id']);
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price FROM " . $ecs->table('goods') . " WHERE goods_id = '$goods_id'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    $smarty->assign('sn', sprintf($_LANG['good_goods_sn'], $goods['goods_sn']));
    $smarty->assign('price', sprintf($_LANG['good_shop_price'], $goods['shop_price']));
    $smarty->assign('goods_name', sprintf($_LANG['products_title'], $goods['goods_name']));
    $smarty->assign('goods_sn', sprintf($_LANG['products_title_2'], $goods['goods_sn']));


    /* 获取商品规格列表 */
    $attribute = get_goods_specifications_list($goods_id);
    if (empty($attribute))
    {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods']);
    }
    foreach ($attribute as $attribute_value)
    {
        //转换成数组
        $_attribute[$attribute_value['attr_id']]['attr_values'][] = $attribute_value['attr_value'];
        $_attribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
        $_attribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
    }
    $attribute_count = count($_attribute);

    $smarty->assign('attribute_count',          $attribute_count);
    $smarty->assign('attribute',                $_attribute);
    $smarty->assign('attribute_count_3',        ($attribute_count + 10));
    $smarty->assign('product_sn',               $goods['goods_sn'] . '_');
    $smarty->assign('product_number',           $_CFG['default_storage']);

    /* 取商品的货品 */
    $product = product_list($goods_id, '');

    $smarty->assign('ur_here', $_LANG['18_product_list']);
    $smarty->assign('action_link', array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']));
    $smarty->assign('product_list',  $product['product']);
    $smarty->assign('use_storage',  empty($_CFG['use_storage']) ? 0 : 1);
    $smarty->assign('goods_id',    $goods_id);
    $smarty->assign('filter',       $product['filter']);
	
	$smarty->assign('product_php', 'goods.php');

    /* 排序标记 */
    $sort_flag  = sort_flag($product['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('product_info.dwt'), '',
        array('filter' => $product['filter'], 'page_count' => $product['page_count']));
}

/*------------------------------------------------------ */
//-- 货品删除
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_remove')
{
    /* 检查权限 */
    check_authz_json('remove_back');
	
	//ecmoban模板堂 --zhuo satrt
	$id_val = $_REQUEST['id'];
	$id_val = explode(',', $id_val);
	$product_id = intval($id_val[0]);
	$warehouse_id = intval($id_val[1]);
	//ecmoban模板堂 --zhuo end

    /* 是否存在商品id */
    if (empty($product_id))
    {
        make_json_error($_LANG['product_id_null']);
    }
    else
    {
        $product_id = intval($product_id);
    }

    /* 货品库存 */
    $product = get_product_info($product_id, 'product_number, goods_id');

    /* 删除货品 */
    $sql = "DELETE FROM " . $ecs->table('products') . " WHERE product_id = '$product_id'";
    $result = $db->query($sql);
	
    if ($result)
    {
        /* 修改商品库存 */ //ecmoban模板堂 --zhuo 注释
        /*if (update_goods_stock($product['goods_id'], $product_number - $product['product_number'], $warehouse_id))
        {
            //记录日志
            admin_log('', 'update', 'goods');
        }

        //记录日志
        admin_log('', 'trash', 'products');*/

        $url = 'goods.php?act=product_query&' . str_replace('act=product_remove', '', $_SERVER['QUERY_STRING']);

        ecs_header("Location: $url\n");
        exit;
    }
}

/* ------------------------------------------------------ */
//-- 修改货品号
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'edit_product_sn') {
    check_authz_json('goods_manage');

    $product_id = intval($_REQUEST['id']);

    $product_sn = json_str_iconv(trim($_POST['val']));
    $product_sn = ($_LANG['n_a'] == $product_sn) ? '' : $product_sn;

    if (check_product_sn_exist($product_sn, $product_id, $adminru['ru_id'])) {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['exist_same_product_sn']);
    }

    /* 修改 */
    $sql = "UPDATE " . $ecs->table('products') . " SET product_sn = '$product_sn' WHERE product_id = '$product_id'";
    $result = $db->query($sql);
    if ($result) {
        clear_cache_files();
        make_json_result($product_sn);
    }
}

/*------------------------------------------------------ */
//-- 修改条形码
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'edit_bar_code') {
    check_authz_json('goods_manage');

    $product_id = intval($_REQUEST['id']);
    $bar_code = json_str_iconv(trim($_POST['val']));

    if (check_product_sn_exist($bar_code, $product_id, $adminru['ru_id'], 1)) {
        make_json_error($_LANG['sys']['wrong'] . $_LANG['exist_same_bar_code']);
    }

    /* 修改 */
    $sql = "UPDATE " . $ecs->table('products') . " SET bar_code = '$bar_code' WHERE product_id = '$product_id'";
    $result = $db->query($sql);
    if ($result) {
        clear_cache_files();
        make_json_result($bar_code);
    }
}

/*------------------------------------------------------ */
//-- 修改货品库存
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_product_number')
{
    check_authz_json('goods_manage');

    $product_id       = intval($_POST['id']);
    $product_number       = intval($_POST['val']);

    /* 货品库存 */
    $product = get_product_info($product_id, 'product_number, goods_id');
    if ($product['product_number'] != $product_number)
    {
        if ($product['product_number'] > $product_number)
        {
            $number = $product['product_number'] - $product_number;
            $number = '- ' . $number;
            $log_use_storage = 10;
        }
        else
        {
            $number = $product_number - $product['product_number'];
            $number = '+ ' . $number;
            $log_use_storage = 11;
        }
        $goods = get_goods_info($product['goods_id'], array('goods_number', 'model_inventory', 'model_attr'));
        $logs_other = array('goods_id' => $product['goods_id'], 
                            'order_id' => 0, 
                            'use_storage' => $log_use_storage, 
                            'admin_id' => $_SESSION['seller_id'], 
                            'number' => $number, 
                            'model_inventory' => $goods['model_inventory'], 
                            'model_attr' => $goods['model_attr'], 
                            'product_id' => $product_id, 
                            'warehouse_id' => 0, 
                            'area_id' => 0, 
                            'add_time' => gmtime()
                            );
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
    }
	
    /* 修改货品库存 */
    $sql = "UPDATE " . $ecs->table('products') . " SET product_number = '$product_number' WHERE product_id = '$product_id'";
    $result = $db->query($sql);
	
    if ($result)
    {
        clear_cache_files();
        make_json_result($product_number);
    }
}
/*------------------------------------------------------ */
//-- 修改货品价格
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_product_price')
{
    check_authz_json('goods_manage');
    $product_id = intval($_POST['id']);
    $product_price = floatval($_POST['val']);
    $sql = "UPDATE " . $ecs->table('products') . " SET product_price = '$product_price' WHERE product_id = '$product_id'";
    $result = $db->query($sql);

    if ($result)
    {
        clear_cache_files();
        make_json_result($product_price);
    }
}
/*------------------------------------------------------ */
//-- 货品添加 执行
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'product_add_execute')
{
    admin_priv('goods_manage');

    $product['goods_id']        = intval($_POST['goods_id']);
    $product['attr']            = $_POST['attr'];
    $product['product_sn']      = $_POST['product_sn'];
    $product['bar_code']        = $_POST['bar_code'];
    $product['product_price']   = $_POST['product_price'];
    $product['product_number']  = $_POST['product_number'];

    /* 是否存在商品id */
    if (empty($product['goods_id']))
    {
        sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods'], 1, array(), false);
    }

    /* 判断是否为初次添加 */
    $insert = true;
    if (product_number_count($product['goods_id']) > 0)
    {
        $insert = false;
    }

    /* 取出商品信息 */
    $sql = "SELECT goods_sn, goods_name, goods_type, shop_price , model_inventory, model_attr FROM " . $ecs->table('goods') . " WHERE goods_id = '" . $product['goods_id'] . "'";
    $goods = $db->getRow($sql);
    if (empty($goods))
    {
        sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_found_goods'], 1, array(), false);
    }

    /*  */
    foreach($product['product_sn'] as $key => $value)
    {
        //过滤
        $product['product_number'][$key] = empty($product['product_number'][$key]) ? (empty($_CFG['use_storage']) ? 0 : $_CFG['default_storage']) : trim($product['product_number'][$key]); //库存

        //获取规格在商品属性表中的id
        foreach($product['attr'] as $attr_key => $attr_value)
        {
            /* 检测：如果当前所添加的货品规格存在空值或0 */
            if (empty($attr_value[$key]))
            {
                continue 2;
            }

            $is_spec_list[$attr_key] = 'true';

            $value_price_list[$attr_key] = $attr_value[$key] . chr(9) . ''; //$key，当前

            $id_list[$attr_key] = $attr_key;
        }
        $goods_attr_id = handle_goods_attr($product['goods_id'], $id_list, $is_spec_list, $value_price_list);

        /* 是否为重复规格的货品 */
        $goods_attr = sort_goods_attr_id_array($goods_attr_id);
        $goods_attr = implode('|', $goods_attr['sort']);
        if (check_goods_attr_exist($goods_attr, $product['goods_id']))
        {
            continue;
            //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_goods_attr'], 1, array(), false);
        }
        //货品号不为空
        if (!empty($value))
        {
            /* 检测：货品货号是否在商品表和货品表中重复 */
            if (check_goods_sn_exist($value))
            {
                continue;
                //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_goods_sn'], 1, array(), false);
            }
            if (check_product_sn_exist($value))
            {
                continue;
                //sys_msg($_LANG['sys']['wrong'] . $_LANG['exist_same_product_sn'], 1, array(), false);
            }
        }

        /* 插入货品表 */
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('products') . " (goods_id, goods_attr, product_sn, bar_code,, product_price, product_number)  VALUES ('" . $product['goods_id'] . "', '$goods_attr', '$value', '" . $product['bar_code'][$key] . "', '" . $product['product_price'][$key] . "', '" . $product['product_number'][$key] . "')";
        if (!$GLOBALS['db']->query($sql))
        {
            continue;
            //sys_msg($_LANG['sys']['wrong'] . $_LANG['cannot_add_products'], 1, array(), false);
        }

        $number = '+ ' . $product['product_number'][$key];

        if ($product['product_number'][$key])
        {
            $logs_other = array('goods_id' => $product['goods_id'], 
                                'order_id' => 0, 
                                'use_storage' => 9, 
                                'admin_id' => $_SESSION['seller_id'], 
                                'number' => $number, 
                                'model_inventory' => $goods['model_inventory'], 
                                'model_attr' => $goods['model_attr'], 
                                'product_id' => $GLOBALS['db']->insert_id(), 
                                'warehouse_id' => 0, 
                                'area_id' => 0, 
                                'add_time' => gmtime()
                                );
            $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
        }
        //货品号为空 自动补货品号
        if (empty($value))
        {
            $sql = "UPDATE " . $GLOBALS['ecs']->table('products') . "
                    SET product_sn = '" . $goods['goods_sn'] . "g_p" . $GLOBALS['db']->insert_id() . "'
                    WHERE product_id = '" . $GLOBALS['db']->insert_id() . "'";
            $GLOBALS['db']->query($sql);
        }

        /* 修改商品表库存 */
        $product_count = product_number_count($product['goods_id']);
    }

    clear_cache_files();

    /* 返回 */
    if ($insert)
    {
         $link[] = array('href' => 'goods.php?act=add', 'text' => $_LANG['02_goods_add']);
         $link[] = array('href' => 'goods.php?act=list', 'text' => $_LANG['01_goods_list']);
         $link[] = array('href' => 'goods.php?act=product_list&goods_id=' . $product['goods_id'], 'text' => $_LANG['18_product_list']);
    }
    else
    {
         $link[] = array('href' => 'goods.php?act=list&uselastfilter=1', 'text' => $_LANG['01_goods_list']);
         $link[] = array('href' => 'goods.php?act=edit&goods_id=' . $product['goods_id'], 'text' => $_LANG['edit_goods']);
         $link[] = array('href' => 'goods.php?act=product_list&goods_id=' . $product['goods_id'], 'text' => $_LANG['18_product_list']);
    }
    sys_msg($_LANG['save_products'], 0, $link);
}

/*------------------------------------------------------ */
//-- 货品批量操作
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'batch_product')
{
    /* 定义返回 */
    $link[] = array('href' => 'goods.php?act=product_list&goods_id=' . $_POST['goods_id'], 'text' => $_LANG['item_list']);

    /* 批量操作 - 批量删除 */
    if ($_POST['type'] == 'drop')
    {
        //检查权限
        admin_priv('remove_back');

        //取得要操作的商品编号
        $product_id = !empty($_POST['checkboxes']) ? join(',', $_POST['checkboxes']) : 0;
        $product_bound = db_create_in($product_id);

        //取出货品库存总数
        $sum = 0;
        $goods_id = 0;
        $sql = "SELECT product_id, goods_id, product_number FROM  " . $GLOBALS['ecs']->table('products') . " WHERE product_id $product_bound";
        $product_array = $GLOBALS['db']->getAll($sql);
        if (!empty($product_array))
        {
            foreach ($product_array as $value)
            {
                $sum += $value['product_number'];
            }
            $goods_id = $product_array[0]['goods_id'];

            /* 删除货品 */
            $sql = "DELETE FROM " . $ecs->table('products') . " WHERE product_id $product_bound";
            if ($db->query($sql))
            {
                //记录日志
                admin_log('', 'delete', 'products');
            }

            /* 修改商品库存 */
            if (update_goods_stock($goods_id, -$sum))
            {
                //记录日志
                admin_log('', 'update', 'goods');
            }

            /* 返回 */
            sys_msg($_LANG['product_batch_del_success'], 0, $link);
        }
        else
        {
            /* 错误 */
            sys_msg($_LANG['cannot_found_products'], 1, $link);
        }
    }

    /* 返回 */
    sys_msg($_LANG['no_operation'], 1, $link);
}
elseif($_REQUEST['act'] == 'search_cat')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $keyword    =   !empty($_REQUEST['seacrch_key']) ? trim($_REQUEST['seacrch_key']) : '';
    $parent_id  =   !empty($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
    $cat_level  =   !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    $res = array('error' => 0, 'message' => '');
    if(!empty($keyword))
    {
            if($adminru['ru_id'] == 0)
            {
                    $sql="SELECT `cat_id`,`cat_name` FROM ".$GLOBALS['ecs']->table('category')."WHERE `cat_name` like '%$keyword%' AND parent_id = '$parent_id'";
                    $options=$GLOBALS['db']->getAll($sql);
            }
            else
            {
                    $sql = "select user_shopMain_category from " .$GLOBALS['ecs']->table('merchants_shop_information'). " where user_id = '".$adminru['ru_id']."'";
                    $shopMain_category = $GLOBALS['db']->getOne($sql);
                    $cat_ids=explode(',',get_category_child_tree($shopMain_category));
                    $sql="SELECT `cat_id`,`cat_name` FROM ".$GLOBALS['ecs']->table('category')."WHERE `cat_name` like '%$keyword%' and cat_id ".db_create_in($cat_ids) . " AND parent_id = '$parent_id'";
                    $options=$GLOBALS['db']->getAll($sql);
            }
            
            if($options){
                foreach($options AS $key=>$row){
                    $options[0]['cat_id'] = 0;
                    $options[0]['cat_name'] = '所有分类';
                    $key += 1;
                    $options[$key] = $row;
                }
            }else{
                $res['error'] = 1;
                $res['message'] = '没有查询到分类!';
            }
    }
    
    $res['parent_id'] = $parent_id;
    $res['cat_level'] = $cat_level + 1;
    
    make_json_result($options, '', $res);
}
/*//商品搜索
elseif ($_REQUEST['act'] == 'search_goods')
{
	$smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
	//页面分菜单 by wu start
	$tab_menu = array();
	$tab_menu[] = array('curr' => 0, 'text'=> $_LANG['01_goods_list'], 'href' => 'goods.php?act=list');
	$tab_menu[] = array('curr' => 0, 'text'=> $_LANG['50_virtual_card_list'], 'href' => 'goods.php?act=list&extension_code=virtual_card');
	$tab_menu[] = array('curr' => 1, 'text'=> $_LANG['goods_search_pl'], 'href' => 'goods.php?act=search_goods');
	$smarty->assign('tab_menu', $tab_menu);
	
    $smarty->assign('ur_here', $_LANG['03_order_query']);
	$smarty->display('search_goods.dwt');
}*/

// 选择分类 -by qin
elseif ($_REQUEST['act'] == 'sel_cat')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one($cat_id, $cat_level);
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 添加或编辑商品 选择分类 -by qin
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'sel_cat1')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one1($cat_id, $cat_level);
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 关联或配件 选择分类 -by qin
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'sel_cat2')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one2($cat_id, $cat_level);
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 商品批量修改 选择分类 -by qin
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'sel_cat_edit')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one_new($cat_id, $cat_level, 'sel_cat_edit');
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 图片批量处理 选择分类 -by qin
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'sel_cat_picture')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one_new($cat_id, $cat_level, 'sel_cat_picture');
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 图片批量处理 选择分类 -by qin
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'sel_cat_goodslist')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;
    
    $res = array('error' => 0, 'message' => '', 'cat_level' => 0, 'content' => '');
    
    $cat_id = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $cat_level = !empty($_GET['cat_level']) ? intval($_GET['cat_level']) : 0;
    
    if ($cat_id > 0)
    {
        $arr = cat_list_one_new($cat_id, $cat_level, 'sel_cat_goodslist');
    }
    
    $res['content'] = $arr;
    $res['parent_id'] = $cat_id;
    $res['cat_level'] = $cat_level;
    echo $json->encode($res);die;
}

/*------------------------------------------------------ */
//-- 单个添加商品仓库 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'addWarehouse') {
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '', 'error' => 0, 'massege' => '');
    $ware_name = !empty($_POST['ware_name']) ? $_POST['ware_name'] : '';
    $ware_number = !empty($_POST['ware_number']) ? intval($_POST['ware_number']) : 0;
    $ware_price = !empty($_POST['ware_price']) ? $_POST['ware_price'] : 0;
    $ware_price = floatval($ware_price);
    $ware_promote_price = !empty($_POST['ware_promote_price']) ? $_POST['ware_promote_price'] : 0;
    $ware_promote_price = floatval($ware_promote_price);
    $give_integral = !empty($_POST['give_integral']) ? intval($_POST['give_integral']) : 0;
    $rank_integral = !empty($_POST['rank_integral']) ? intval($_POST['rank_integral']) : 0;
    $pay_integral = !empty($_POST['pay_integral']) ? intval($_POST['pay_integral']) : 0;
    $goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
    
    if (empty($ware_name)) {
        $result['error'] = '1';
        $result['massege'] = "请选择仓库";
    } else {
        $sql = "select w_id from " . $GLOBALS['ecs']->table('warehouse_goods') . " where goods_id = '$goods_id' and region_id = '" . $ware_name . "' AND user_id = '$user_id'";
        $w_id = $GLOBALS['db']->getOne($sql);
        $add_time = gmtime();
        if ($w_id > 0) {
            $result['error'] = '1';
            $result['massege'] = "该商品的仓库库存已存在";
        } else {
            if ($ware_number == 0) {
                $result['error'] = '1';
                $result['massege'] = "仓库库存不能为0";
            } elseif ($ware_price == 0) {
                $result['error'] = '1';
                $result['massege'] = "仓库价格不能为0";
            } else {
                $goodsInfo = get_goods_info($goods_id, array('user_id', 'model_inventory', 'model_attr'));
                $goodsInfo['user_id'] = !empty($goodsInfo['user_id']) ? $goodsInfo['user_id'] : $adminru['ru_id'];
                $number = '+ ' . $ware_number;
                $use_storage = 13;
                $logs_other = array('goods_id' => $goods_id, 
                                    'order_id' => 0, 
                                    'use_storage' => $use_storage, 
                                    'admin_id' => $_SESSION['seller_id'], 
                                    'number' => $number, 
                                    'model_inventory' => $goodsInfo['model_inventory'], 
                                    'model_attr' => $goodsInfo['model_attr'], 
                                    'product_id' => 0, 
                                    'warehouse_id' => $ware_name, 
                                    'area_id' => 0, 
                                    'add_time' => $add_time
                                    );
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');

                $sql = "insert into " . $GLOBALS['ecs']->table('warehouse_goods') .
                        "(goods_id, region_id, region_number, warehouse_price, warehouse_promote_price, give_integral, rank_integral, pay_integral, user_id, add_time)VALUES('" .
                        $goods_id . "','" . $ware_name . "','" . $ware_number . "','" . $ware_price . "','" . $ware_promote_price . "','" . $give_integral . "','" . $rank_integral . "','" . $pay_integral . "','" . $goodsInfo["user_id"] . "','$add_time')";
                if ($GLOBALS['db']->query($sql) == true) {
                    $result['error'] = '2';
                    $get_warehouse_goods_list = get_warehouse_goods_list($goods_id);
                    $warehouse_id = '';
                    if (!empty($get_warehouse_goods_list)) {
                        foreach ($get_warehouse_goods_list as $k => $v) {
                            $warehouse_id.=$v['w_id'] . ",";
                        }
                    }
                    $warehouse_id = substr($warehouse_id, 0, strlen($warehouse_id) - 1);
                    $smarty->assign("warehouse_id", $warehouse_id);
                    $smarty->assign("warehouse_goods_list", $get_warehouse_goods_list);
                    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_warehouse.lbi');
                }
            }
        }
    }
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 批量添加商品仓库 ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'addBatchWarehouse')
{
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '', 'error' => 0, 'massege' => '');

    $ware_name = !empty($_POST['ware_name']) ? explode(',', $_POST['ware_name']) : array();
    $ware_number = !empty($_POST['ware_number']) ? explode(',', $_POST['ware_number']) : array();
    $ware_price = !empty($_POST['ware_price']) ? explode(',', $_POST['ware_price']) : array();
    $ware_promote_price = !empty($_POST['ware_promote_price']) ? explode(',', $_POST['ware_promote_price']) : array();
    $give_integral = !empty($_POST['give_integral']) ? explode(',', $_POST['give_integral']) : array();
    $rank_integral = !empty($_POST['rank_integral']) ? explode(',', $_POST['rank_integral']) : array();
    $pay_integral = !empty($_POST['pay_integral']) ? explode(',', $_POST['pay_integral']) : array();
    $goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
    if (empty($ware_name))
    {
        $result['error'] = '1';
        $result['massege'] = "请选择仓库";
    }
    else
    {
        $add_time = gmtime();
        $goodsInfo = get_goods_info($goods_id, array('user_id', 'model_inventory', 'model_attr'));
        $goodsInfo['user_id'] = !empty($goodsInfo['user_id']) ? $goodsInfo['user_id'] : $adminru['ru_id'];

        for ($i = 0; $i < count($ware_name); $i++)
        {
            if (!empty($ware_name[$i]))
            {

                if ($ware_number[$i] == 0) {
                    $ware_number[$i] = 1;
                }

                $sql = "select w_id from " . $GLOBALS['ecs']->table('warehouse_goods') . " where goods_id = '$goods_id' and region_id = '" . $ware_name[$i] . "'";
                $w_id = $GLOBALS['db']->getOne($sql, true);

                if ($w_id > 0) {
                    $result['error'] = '1';
                    $result['massege'] = "该商品的仓库库存已存在";
                    break;
                }
                else
                {
                    $ware_number[$i] = intval($ware_number[$i]);
                    $ware_price[$i] = floatval($ware_price[$i]);
                    $ware_promote_price[$i] = floatval($ware_promote_price[$i]);
                    $number = '+ ' . $ware_number[$i];
                    $use_storage = 13;
                    $logs_other = array('goods_id' => $goods_id, 
                                        'order_id' => 0, 
                                        'use_storage' => $use_storage, 
                                        'admin_id' => $_SESSION['seller_id'], 
                                        'number' => $number, 
                                        'model_inventory' => $goodsInfo['model_inventory'], 
                                        'model_attr' => $goodsInfo['model_attr'], 
                                        'product_id' => 0, 
                                        'warehouse_id' => $ware_name[$i], 
                                        'area_id' => 0, 
                                        'add_time' => $add_time
                                        );
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
                    $sql = "insert into " . $GLOBALS['ecs']->table('warehouse_goods') . 
                            "(goods_id, region_id, region_number, warehouse_price, warehouse_promote_price, user_id, add_time)VALUES('" . 
                            $goods_id . "','" . $ware_name[$i] . "','" . $ware_number[$i] . "','" . $ware_price[$i] . "','" . $ware_promote_price[$i] . "','" . $goodsInfo["user_id"] . "','$add_time')";

                    $GLOBALS['db']->query($sql);

                    $get_warehouse_goods_list = get_warehouse_goods_list($goods_id);
                    $warehouse_id = '';
                    if (!empty($get_warehouse_goods_list))
                    {
                        foreach ($get_warehouse_goods_list as $k => $v)
                        {
                            $warehouse_id.=$v['w_id'] . ",";
                        }
                    }

                        $warehouse_id = substr($warehouse_id, 0, strlen($warehouse_id) - 1);
                        $smarty->assign("warehouse_id", $warehouse_id);
                        $smarty->assign("warehouse_goods_list", $get_warehouse_goods_list);
                }
            }
            else 
            {
                $result['error'] = '1';
                $result['massege'] = "请选择仓库";
            }
        }
    }
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_warehouse.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 仓库信息列表 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'goods_warehouse')
{
     require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '','error'=>0, 'massege' => '');
   
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    
    $warehouse_goods_list = get_warehouse_goods_list($goods_id);
    $GLOBALS['smarty']->assign('warehouse_goods_list', $warehouse_goods_list);
    $GLOBALS['smarty']->assign('is_list', 1);
    
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_warehouse.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 仓库信息列表 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'goods_region')
{
     require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '','error'=>0, 'massege' => '');
   
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    
    $warehouse_area_goods_list = get_warehouse_area_goods_list($goods_id);
    $GLOBALS['smarty']->assign('warehouse_area_goods_list', $warehouse_area_goods_list);  
    $GLOBALS['smarty']->assign('is_list', 1);
    
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_region.lbi');
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加商品地区 ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'addRegion') {
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '', 'error' => 0, 'massege' => '');
    $warehouse_area_name = !empty($_POST['warehouse_area_name']) ? $_POST['warehouse_area_name'] : '';
    $area_name = !empty($_POST['warehouse_area_list']) ? $_POST['warehouse_area_list'] : '';
    $region_number = !empty($_POST['region_number']) ? intval($_POST['region_number']) : 0;
    $region_price = !empty($_POST['region_price']) ? floatval($_POST['region_price']) : 0;
    $region_promote_price = !empty($_POST['region_promote_price']) ? floatval($_POST['region_promote_price']) : 0;
    $give_integral = !empty($_POST['give_integral']) ? intval($_POST['give_integral']) : 0;
    $rank_integral = !empty($_POST['rank_integral']) ? intval($_POST['rank_integral']) : 0;
    $pay_integral = !empty($_POST['pay_integral']) ? intval($_POST['pay_integral']) : 0;
    $goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
    if (empty($area_name)) {
        $result['error'] = '1';
        $result['massege'] = "请选择地区";
    } else {
        if ($region_number == 0) {
            $result['error'] = '1';
            $result['massege'] = "地区库存不能为0";
        } elseif ($region_price == 0) {
            $result['error'] = '1';
            $result['massege'] = "地区价格不能为0";
        } else {
            $add_time = gmtime();
            $sql = "select a_id from " . $GLOBALS['ecs']->table('warehouse_area_goods') . " where goods_id = '$goods_id' and region_id = '" . $area_name . "'";
            $a_id = $GLOBALS['db']->getOne($sql);

            if ($a_id > 0) {
                $result['error'] = '1';
                $result['massege'] = "该商品的地区价格已存在";
            } else {
                $goodsInfo = get_goods_info($goods_id, array('goods_id', 'user_id', 'model_inventory', 'model_attr'));
                $goodsInfo['user_id'] = !empty($goodsInfo['user_id']) ? $goodsInfo['user_id'] : $adminru['ru_id'];
                $number = '+ ' . $region_number;
                $use_storage = 13;
                $logs_other = array('goods_id' => $goods_id, 
                                    'order_id' => 0, 
                                    'use_storage' => $use_storage, 
                                    'admin_id' => $_SESSION['seller_id'], 
                                    'number' => $number, 
                                    'model_inventory' => $goodsInfo['model_inventory'], 
                                    'model_attr' => $goodsInfo['model_attr'], 
                                    'product_id' => 0, 
                                    'warehouse_id' => 0, 
                                    'area_id' => $area_name, 
                                    'add_time' => $add_time
                                    );
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');
                        
                $sql = "insert into " . $GLOBALS['ecs']->table('warehouse_area_goods') .
                        "(goods_id, region_id, region_number, region_price, region_promote_price, give_integral, rank_integral, pay_integral, user_id, add_time)VALUES('" .
                        $goods_id . "','" . $area_name . "','" . $region_number . "','" . floatval($region_price) . "','" . floatval($region_promote_price) . "','" . floatval($give_integral) . "','" . floatval($rank_integral) . "','" . floatval($pay_integral) . "','" . $goodsInfo["user_id"] . "','$add_time')";

                if ($GLOBALS['db']->query($sql) == true) {
                    $result['error'] = '2';
                    $warehouse_area_goods_list = get_warehouse_area_goods_list($goods_id);
                    $warehouse_id = '';
                    if (!empty($warehouse_area_goods_list)) {
                        foreach ($warehouse_area_goods_list as $k => $v) {
                            $warehouse_id.=$v['a_id'] . ",";
                        }
                    }
                    $warehouse_area_id = substr($warehouse_id, 0, strlen($warehouse_id) - 1);
                    $smarty->assign("warehouse_area_id", $warehouse_area_id);
                    $smarty->assign("warehouse_area_goods_list", $warehouse_area_goods_list);
                    $smarty->assign("goods", $goodsInfo);
                    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_region.lbi');
                }
            }
        }
    }
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 批量添加商品地区 ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'addBatchRegion') {
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '', 'error' => 0, 'massege' => '');
    $warehouse_area_name = !empty($_POST['warehouse_area_name']) ? explode(',', $_POST['warehouse_area_name']) : array();
    $area_name = !empty($_POST['warehouse_area_list']) ? explode(',', $_POST['warehouse_area_list']) : array();
    $region_number = !empty($_POST['region_number']) ? explode(',', $_POST['region_number']) : array();
    $region_price = !empty($_POST['region_price']) ? explode(',', $_POST['region_price']) : array();
    $region_promote_price = !empty($_POST['region_promote_price']) ? explode(',', $_POST['region_promote_price']) : array();
    $goods_id = !empty($_POST['goods_id']) ? intval($_POST['goods_id']) : 0;
    //$sql = "select user_id from " .$ecs->table('goods'). " where goods_id = '$goods_id'";
    //$user_id = $db->getOne($sql);
    if (empty($area_name)) {
        $result['error'] = '1';
        $result['massege'] = "请选择地区";
    } else {
        if (empty($region_number)) {
            $result['error'] = '1';
            $result['massege'] = "地区库存不能为0";
        } elseif (empty($region_price)) {
            $result['error'] = '1';
            $result['massege'] = "地区价格不能为0";
        } else {
            $add_time = gmtime();
            $goodsInfo = get_goods_info($goods_id, array('goods_id', 'user_id', 'model_inventory', 'model_attr'));
            $goodsInfo['user_id'] = !empty($goodsInfo['user_id']) ? $goodsInfo['user_id'] : $adminru['ru_id'];

            for ($i = 0; $i < count($area_name); $i++) {
                if (!empty($area_name[$i])) {
                    $sql = "select a_id from " . $GLOBALS['ecs']->table('warehouse_area_goods') . " where goods_id = '$goods_id' and region_id = '" . $area_name[$i] . "'";
                    $a_id = $GLOBALS['db']->getOne($sql, true);
                    if ($a_id > 0) {
                        $result['error'] = '1';
                        $result['massege'] = "该商品的地区价格已存在";
                        break;
                    } else {
                        $ware_number[$i] = intval($ware_number[$i]);
                        $ware_price[$i] = floatval($ware_price[$i]);
                        $region_promote_price[$i] = floatval($region_promote_price[$i]);
                        $number = '+ ' . $ware_number[$i];
                        $use_storage = 13;
                        $logs_other = array('goods_id' => $goods_id, 
                                            'order_id' => 0, 
                                            'use_storage' => $use_storage, 
                                            'admin_id' => $_SESSION['seller_id'], 
                                            'number' => $number, 
                                            'model_inventory' => $goodsInfo['model_inventory'], 
                                            'model_attr' => $goodsInfo['model_attr'], 
                                            'product_id' => 0, 
                                            'warehouse_id' => 0, 
                                            'area_id' => $area_name[$i], 
                                            'add_time' => $add_time
                                            );
                        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_inventory_logs'), $logs_other, 'INSERT');

                        $sql = "insert into " . $GLOBALS['ecs']->table('warehouse_area_goods') . 
                                "(goods_id, region_id, region_number, region_price, region_promote_price, user_id, add_time)VALUES('" . 
                                $goods_id . "','" . $area_name[$i] . "','" . $region_number[$i] . "','" . $region_price[$i] . "','" . $region_promote_price[$i] . "','" . $goodsInfo["user_id"] . "','$add_time')";

                        $GLOBALS['db']->query($sql);
                        $get_warehouse_area_goods_list = get_warehouse_area_goods_list($goods_id);
                        $warehouse_id = '';
                        if (!empty($get_warehouse_area_goods_list)) {
                            foreach ($get_warehouse_area_goods_list as $k => $v) {
                                $warehouse_id.=$v['a_id'] . ",";
                            }
                        }
                        $warehouse_area_id = substr($warehouse_id, 0, strlen($warehouse_id) - 1);
                        $smarty->assign("warehouse_area_id", $warehouse_area_id);
                        $smarty->assign("warehouse_area_goods_list", $get_warehouse_area_goods_list);
                        $smarty->assign("goods", $goodsInfo);
                    }
                } else {
                    $result['error'] = '1';
                    $result['massege'] = "请选择地区";
                    break;
                }
            }
        }
    }
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_region.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 上传商品相册 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'addImg') {
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '', 'error' => 0, 'massege' => '');
    $goods_id = !empty($_REQUEST['goods_id_img']) ? $_REQUEST['goods_id_img'] : '';
    $img_desc = !empty($_REQUEST['img_desc']) ? $_REQUEST['img_desc'] : '';
    $img_file = !empty($_REQUEST['img_file']) ? $_REQUEST['img_file'] : '';
    $php_maxsize = ini_get('upload_max_filesize');
    $htm_maxsize = '2M';
    if ($_FILES['img_url']) {
        foreach ($_FILES['img_url']['error'] AS $key => $value) {
            if ($value == 0) {
                if (!$image->check_img_type($_FILES['img_url']['type'][$key])) {
                    $result['error'] = '1';
                    $result['massege'] = sprintf($_LANG['invalid_img_url'], $key + 1);
                } else {
                    $goods_pre = 1;
                }
            } elseif ($value == 1) {
                $result['error'] = '1';
                $result['massege'] = sprintf($_LANG['img_url_too_big'], $key + 1, $php_maxsize);
            } elseif ($_FILES['img_url']['error'] == 2) {
                $result['error'] = '1';
                $result['massege'] = sprintf($_LANG['img_url_too_big'], $key + 1, $htm_maxsize);
            }
        }
	}
            handle_gallery_image_add($goods_id, $_FILES['img_url'], $img_desc, $img_file, '', '', 'ajax');
            clear_cache_files();
            if ($goods_id > 0) {
                /* 图片列表 */
                $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
            } else {
                 $img_id = $_SESSION['thumb_img_id'.$_SESSION['seller_id']];
                $where = '';
                if($img_id){
                    $where= "AND img_id ".  db_create_in($img_id)."";
                }
                $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id='' $where ORDER BY img_desc ASC";
            }
            $img_list = $db->getAll($sql);
            /* 格式化相册图片路径 */
            if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0)) {
                foreach ($img_list as $key => $gallery_img) {
                    $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                    $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
                }
            } else {
                foreach ($img_list as $key => $gallery_img) {
                    $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
                }
            }
            $goods['goods_id'] = $goods_id;
            $smarty->assign('img_list', $img_list);
            $img_desc = array();
            foreach ($img_list as $k => $v) {
                $img_desc[] = $v['img_desc'];
            }
            $img_default = min($img_desc);
            $min_img_id = $db->getOne(" SELECT img_id   FROM " . $ecs->table("goods_gallery") . " WHERE goods_id = '$goods_id' AND img_desc = '$img_default' ORDER BY img_desc   LIMIT 1");
            $smarty->assign('min_img_id', $min_img_id);
            $smarty->assign('goods', $goods);
            $result['error'] = '2';
            $result['content'] = $GLOBALS['smarty']->fetch('goods_img_list.dwt');
			die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 修改默认相册 ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'img_default'){
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('content' => '','error'=>0, 'massege' => '', 'img_id' => '');
    $img_id=!empty($_REQUEST['img_id'])   ?  intval($_REQUEST['img_id']):'0';
    if($img_id > 0){
        $goods_id=$db->getOne(" SELECT goods_id FROM".$ecs->table('goods_gallery')." WHERE img_id= '$img_id'");
        $db->query("UPDATE".$ecs->table('goods_gallery')." SET img_desc = img_desc+1 WHERE goods_id = '$goods_id' ");
        $sql=$db->query("UPDATE".$ecs->table('goods_gallery')." SET img_desc = 1 WHERE img_id = '$img_id'");
        if($sql = true){
             if($goods_id > 0){
                 /* 图片列表 */
                $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
            }else{
                $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id='' ";
            }
            $img_list = $db->getAll($sql);
                /* 格式化相册图片路径 */
                if (isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0))
                {
                    foreach ($img_list as $key => $gallery_img)
                    {
                        $gallery_img[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                        $gallery_img[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
                    }
                }
                else
                {
                    foreach ($img_list as $key => $gallery_img)
                    {
                        $gallery_img[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
                    }
                }
                $img_desc=array();
                foreach($img_list as $k=>$v){
                    $img_desc[]=$v['img_desc'];
                }
                $img_default = min($img_desc);
                $min_img_id=$db->getOne(" SELECT img_id   FROM ".$ecs->table("goods_gallery")." WHERE goods_id = '$goods_id' AND img_desc = '$img_default' ORDER BY img_desc   LIMIT 1");
                $smarty->assign('min_img_id',$min_img_id);
            $smarty->assign('img_list',$img_list);
            $result['error']=1;
            $result['content']=$GLOBALS['smarty']->fetch('goods_img_list.dwt');
        }else{
            $result['error']=2;
            $result['massege']='修改失败';
        }
    }
    die($json->encode($result));
}
elseif($_REQUEST['act'] == 'remove_consumption'){
    require(ROOT_PATH . '/includes/cls_json.php');
    $json = new JSON;
    $result = array('error'=>0, 'massege' => '', 'con_id' => '');
    
    $con_id=!empty($_REQUEST['con_id'])   ?  intval($_REQUEST['con_id']) : '0';
    $goods_id=!empty($_REQUEST['goods_id'])   ?  intval($_REQUEST['goods_id']) : '0';
    if($con_id > 0){
        $sql="DELETE FROM".$ecs->table('goods_consumption')." WHERE id = '$con_id' AND goods_id = '$goods_id'";
        if($db->query($sql)){
            $result['error']=2;
            $result['con_id']=$con_id;
        }
    }else{
        $result['error']=1;
        $result['massege']="请选择删除目标";
    }
    die($json->encode($result));
}
/**
 * 组合 返回分类列表  图片批量处理和商品批量修改
 * 
 */
function cat_list_one_new($cat_id=0, $cat_level=0, $sel_cat)
{
    if ($cat_id == 0)
    {
        $arr = cat_list($cat_id);
        return $arr;
    }
    else
    {
        $arr = cat_list($cat_id);
        
        foreach ($arr as $key => $value)
        {
            if ($key == $cat_id)
            {
                unset($arr[$cat_id]);
            }
        }
        
        // 拼接字符串
        $str = '';
        if($arr)
        {
            $cat_level ++;
            switch ($sel_cat)
            {
                case 'sel_cat_edit':
                    $str .= "<select name='catList$cat_level' id='cat_list$cat_level' onchange='getGoods(this.value, $cat_level)' class='select'>";
                    break;
                case 'sel_cat_picture':
                    $str .= "<select name='catList$cat_level' id='cat_list$cat_level' onchange='goods_list(this, $cat_level)' class='select'>";
                    break;
                case 'sel_cat_goodslist':
                    $str .= "<select class='select mr10' name='movecatList$cat_level' id='move_cat_list$cat_level' onchange='movecatList(this.value, $cat_level)'>";
                    break;

                default:
                    break;
            }
            
            $str .= "<option value='0'>全部分类</option>";
            foreach ($arr as $key1 => $value1)
            {
                $str .= "<option value='$value1[cat_id]'>$value1[cat_name]</option>";
            }
            $str .= "</select>";
        }
        return $str;
    }
}

/**
 * 列表链接
 * @param   bool    $is_add         是否添加（插入）
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function list_link($is_add = true, $extension_code = '')
{
    $href = 'goods.php?act=list';
    if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }
    if (!$is_add)
    {
        $href .= '&' . list_link_postfix();
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['50_virtual_card_list'];
    }
    else
    {
        $text = $GLOBALS['_LANG']['01_goods_list'];
    }

    return array('href' => $href, 'text' => $text);
}

/**
 * 添加链接
 * @param   string  $extension_code 虚拟商品扩展代码，实体商品为空
 * @return  array('href' => $href, 'text' => $text)
 */
function add_link($extension_code = '')
{
    $href = 'goods.php?act=add';
    if (!empty($extension_code))
    {
        $href .= '&extension_code=' . $extension_code;
    }

    if ($extension_code == 'virtual_card')
    {
        $text = $GLOBALS['_LANG']['51_virtual_card_add'];
    }
    else
    {
        $text = $GLOBALS['_LANG']['02_goods_add'];
    }

    return array('href' => $href, 'text' => $text);
}

/**
 * 检查图片网址是否合法
 *
 * @param string $url 网址
 *
 * @return boolean
 */
function goods_parse_url($url)
{
    $parse_url = @parse_url($url);
    return (!empty($parse_url['scheme']) && !empty($parse_url['host']));
}

/**
 * 保存某商品的优惠价格
 * @param   int     $goods_id    商品编号
 * @param   array   $number_list 优惠数量列表
 * @param   array   $price_list  价格列表
 * @return  void
 */
function handle_volume_price($goods_id, $number_list, $price_list)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('volume_price') .
           " WHERE price_type = '1' AND goods_id = '$goods_id'";
    $GLOBALS['db']->query($sql);


    /* 循环处理每个优惠价格 */
    foreach ($price_list AS $key => $price)
    {
        /* 价格对应的数量上下限 */
        $volume_number = $number_list[$key];

        if (!empty($price))
        {
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('volume_price') .
                   " (price_type, goods_id, volume_number, volume_price) " .
                   "VALUES ('1', '$goods_id', '$volume_number', '$price')";
            $GLOBALS['db']->query($sql);
        }
    }
}

/**
 * 修改商品库存
 * @param   string  $goods_id   商品编号，可以为多个，用 ',' 隔开
 * @param   string  $value      字段值
 * @return  bool
 */
function update_goods_stock($goods_id, $value, $warehouse_id = 0)
{
    if ($goods_id)
    {
        /* $res = $goods_number - $old_product_number + $product_number; */
        $sql = "UPDATE " . $GLOBALS['ecs']->table('warehouse_goods') . "
                SET region_number = region_number + $value,
                    last_update = '". gmtime() ."'
                WHERE goods_id = '$goods_id' and region_id = '$warehouse_id'";
        $result = $GLOBALS['db']->query($sql);

        /* 清除缓存 */
        clear_cache_files();

        return $result;
    }
    else
    {
        return false;
    }
}

//ecmoban模板堂 --zhuo start 区域地区
function get_areaRegion_list(){
	$sql = "select ra_id, ra_name from " .$GLOBALS['ecs']->table('merchants_region_area'). " where 1 order by ra_sort asc";
	$res = $GLOBALS['db']->getAll($sql);
	
	$arr = array();
	foreach($res as $key=>$row){
		$arr[$key]['ra_id'] = $row['ra_id'];
		$arr[$key]['ra_name'] = $row['ra_name'];
		$arr[$key]['area'] = get_areaRegion_info_list($row['ra_id']);
	}
	
	return $arr;
}

function get_areaRegion_info_list($ra_id){
	
	if($ra_id > 0){
		$where_raId = " and mr.ra_id = '$ra_id'";
	}
	
	$sql = "select rw.region_id, rw.region_name from " .$GLOBALS['ecs']->table('merchants_region_info'). " as mr " . 
			" left join " .$GLOBALS['ecs']->table('region'). " as r on mr.region_id = r.region_id" . 
			" left join " .$GLOBALS['ecs']->table('region_warehouse'). " as rw on r.region_id = rw.regionId" . 
			" where 1" . $where_raId;
	return $GLOBALS['db']->getAll($sql);		
}

function get_area_goods($goods_id){
	$sql = "select rw.region_id, rw.region_name from " .$GLOBALS['ecs']->table('link_area_goods'). " as lag" . 
			" left join " .$GLOBALS['ecs']->table('region_warehouse'). " as rw on lag.region_id = rw.region_id" . 
			" where lag.goods_id = '$goods_id'";
			
	return $GLOBALS['db']->getAll($sql);				
}
//ecmoban模板堂 --zhuo end
function is_mer($goods_id){
	$sql = " SELECT user_id FROM ".$GLOBALS['ecs']->table('goods'). " WHERE goods_id = '$goods_id' ";
	$one = $GLOBALS['db']->getOne($sql);
	if($one == 0){
		return false;
	}else{
		return $one;
	}
}
//微分销
function is_distribution($ru_id)
{
    $field = get_table_file_name($GLOBALS['ecs']->table('merchants_steps_fields'), 'is_distribution');

    if ($field['bool'])
    {
        $sql = " SELECT is_distribution FROM " . $GLOBALS['ecs']->table('merchants_steps_fields') . " WHERE user_id = '$ru_id' ";
        $one = $GLOBALS['db']->getOne($sql);

        if ($one == '是')
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}
//获取商品信息
function get_goods_info($goods_id = 0, $select = array())
{
    if ($select) {
        $select = implode(',', $select);
    }
    else {
        $select = '*';
    }

    $sql = "SELECT $select FROM " . $GLOBALS['ecs']->table('goods') . " WHERE goods_id = '$goods_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}
?>