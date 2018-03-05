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
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']); 
require(ROOT_PATH . '/includes/cls_json.php');

/*------------------------------------------------------ */
//-- 弹出窗口
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'dialog_content') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
    $smarty->assign("temp", $temp);
    $result['sgs'] = $temp;
    $result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 仓库弹窗
/*------------------------------------------------------ */
 else if ($_REQUEST['act'] == 'dialog_warehouse') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
    $user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $smarty->assign("temp", $temp);
    $result['sgs'] = $temp;

    $grade_rank = get_seller_grade_rank($user_id);
    $smarty->assign('grade_rank', $grade_rank);
    $smarty->assign('integral_scale', $_CFG['integral_scale']);

    $warehouse_list = get_warehouse_list();
    $smarty->assign('warehouse_list', $warehouse_list);

    $smarty->assign('user_id', $user_id);
    $smarty->assign('goods_id', $goods_id);

    $result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 图片
/*------------------------------------------------------ */
 else if ($_REQUEST['act'] == 'dialog_img') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
    $smarty->assign("temp", $temp);
    $goods_id = !empty($_REQUEST['goods_id']) ? $_REQUEST['goods_id'] : '';
    $smarty->assign('goods_id', $goods_id);
    $result['sgs'] = $temp;

    $result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 添加仓库/地区
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'dialog_add') {
   $json = new JSON;
   $result = array('content' => '', 'sgs' => '');
   $temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
   $smarty->assign("temp", $temp);

   $result['sgs'] = $temp;

   /* 取得地区 */
   $country_list = get_regions();
   $smarty->assign('countries', $country_list);

   $result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
   die($json->encode($result));
}
//扩展分类
else if ($_REQUEST['act'] == 'extension_category') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
    $smarty->assign("temp", $temp);

    $result['sgs'] = $temp;

    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    
    $goods = get_admin_goods_info($goods_id, array('user_id'));
    
    /* 商家入驻分类 */
    if($goods['user_id']){
        $seller_shop_cat = seller_shop_cat($goods['user_id']);
    }
    
    /* 取得分类 */
    $level_limit = 3;
    $category_level = array();
    for ($i = 1; $i <= $level_limit; $i++) {
        $category_list = array();
        if ($i == 1) {
            if($goods['user_id']){
                $category_list = get_category_list(0, 0, $seller_shop_cat, $goods['user_id'], $i);
            }else{
                $category_list = get_category_list();
            }
        }
        $smarty->assign('cat_level', $i);
        $smarty->assign('category_list', $category_list);
        $category_level[$i] = $smarty->fetch('templates/library/get_select_category.lbi');
    }
    $smarty->assign('category_level', $category_level);

    /* 取得已存在的扩展分类 */
    if($goods_id > 0){

        $other_cat_list1 = array();
        $sql = "SELECT ga.cat_id FROM " . $ecs->table('goods_cat') . " as ga " .
                " WHERE ga.goods_id = '$goods_id'";
        $other_cat1 = $db->getCol($sql);

        $other_category = array();
        foreach ($other_cat1 as $key => $val) {
            $other_category[$key]['cat_id'] = $val;
            $other_category[$key]['cat_name'] = get_every_category($val);
        }
        $smarty->assign('other_category', $other_category);
    }
    
    $smarty->assign('goods_id', $goods_id);
    $result['content'] = $GLOBALS['smarty']->fetch('library/extension_category.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 添加属性图片 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'add_attr_img') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '','content' => '');

    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';

    //$action_link = array('href' => 'goods.php?act=edit&goods_id=' . $goods_id . '&extension_code=', 'text' => $_LANG['goods_info']);

    //$goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID

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
    //$smarty->assign('action_link', $action_link);

    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_attr_img_info.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 添加属性图片插入数据 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_attr_img')
{
    $json = new JSON;
    $result = array('error' => 0, 'message' => '','content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';
    $img_url = !empty($_REQUEST['img_url']) ? $_REQUEST['img_url'] : '';
    
    /* 允许上传的文件类型 */
    $allow_file_types = '|GIF|JPG|JEPG|PNG|';
    
    if(!empty($_FILES['attr_img_flie'])){
        $other['attr_img_flie'] = get_upload_pic('attr_img_flie');
        get_oss_add_file(array($other['attr_img_flie']));
    }else{
        $other['attr_img_flie'] = '';
    }

    $goods_attr_date = array('attr_img_flie, attr_img_site');
    $goods_attr_info = get_table_date('goods_attr', "goods_id = '$goods_id' and attr_id = '$attr_id' and goods_attr_id = '$goods_attr_id'", $goods_attr_date);

    if(empty($other['attr_img_flie'])){
            $other['attr_img_flie'] = $goods_attr_info['attr_img_flie'];
    }else{
        @unlink(ROOT_PATH  . $goods_attr_info['attr_img_flie']);
    }

    $other['attr_img_site'] = !empty($_REQUEST['attr_img_site']) ? $_REQUEST['attr_img_site'] : '';
    $other['attr_checked'] = !empty($_REQUEST['attr_checked']) ? intval($_REQUEST['attr_checked']) : 0;
    $other['attr_gallery_flie'] = $img_url;
    
    $db->autoExecute($ecs->table('goods_attr'), array('attr_checked' => 0), 'UPDATE', 'attr_id = ' . $attr_id . ' and goods_id = ' . $goods_id);
    $db->autoExecute($ecs->table('goods_attr'), $other, 'UPDATE', 'goods_attr_id = ' . $goods_attr_id . ' and attr_id = ' . $attr_id . ' and goods_id = ' . $goods_id);
    $result['goods_attr_id'] = $goods_attr_id;
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 删除属性图片 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'drop_attr_img')
{
    $json = new JSON;
    $result = array('error' => 0, 'message' => '','content' => '');
    
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_name = isset($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';
    
    $sql = "select attr_img_flie from " .$ecs->table('goods_attr'). " where goods_attr_id = '$goods_attr_id'";
    $attr_img_flie = $db->getOne($sql);
    
    get_oss_del_file(array($attr_img_flie));
    
    @unlink(ROOT_PATH  . $attr_img_flie);
    $other['attr_img_flie'] = '';
    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('goods_attr'), $other, "UPDATE", "goods_attr_id = '$goods_attr_id'");
    
    $result['goods_attr_id'] = $goods_attr_id;
    
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 选择属性图片 --zhuo
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'choose_attrImg') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
    $goods_attr_id = empty($_REQUEST['goods_attr_id']) ? 0 : intval($_REQUEST['goods_attr_id']);
    $on_img_id = isset($_REQUEST['img_id']) ? intval($_REQUEST['img_id']) : 0;

    $sql = "SELECT attr_gallery_flie FROM " . $GLOBALS['ecs']->table('goods_attr') . " WHERE goods_attr_id = '$goods_attr_id' AND goods_id = '$goods_id'";
    $attr_gallery_flie = $GLOBALS['db']->getOne($sql);

    /* 删除数据 */
    $sql = "SELECT img_id, thumb_url, img_url FROM " . $GLOBALS['ecs']->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
    $img_list = $GLOBALS['db']->getAll($sql);

    $str = "<ul>";
    foreach ($img_list as $idx => $row) {
        if ($attr_gallery_flie == $row['img_url']) {
            $str .= '<li id="gallery_' . $row['img_id'] . '" onClick="gallery_on(this,' . $row['img_id'] . ',' . $goods_id . ',' . $goods_attr_id . ')" class="on"><img src="../' . $row['thumb_url'] . '" width="87" /><i><img src="images/yes.png"></i></li>';
        } else {
            $str .= '<li id="gallery_' . $row['img_id'] . '" onClick="gallery_on(this,' . $row['img_id'] . ',' . $goods_id . ',' . $goods_attr_id . ')"><img src="../' . $row['thumb_url'] . '" width="87" /><i><img src="images/gallery_yes.png" width="30" height="30"></i></li>';
        }
    }
    $str .= "</ul>";

    $result['content'] = $str;

    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 选择属性图片 --zhuo
/*------------------------------------------------------ */
 elseif ($_REQUEST['act'] == 'insert_gallery_attr') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $goods_id = intval($_REQUEST['goods_id']);
    $goods_attr_id = intval($_REQUEST['goods_attr_id']);
    $gallery_id = intval($_REQUEST['gallery_id']);

    if (!empty($gallery_id)) {
        $sql = "SELECT img_id, img_url FROM " . $ecs->table('goods_gallery') . "WHERE img_id='$gallery_id'";
        $img = $db->getRow($sql);
        $result['img_id'] = $img['img_id'];
        $result['img_url'] = $img['img_url'];

        $sql = "UPDATE " . $ecs->table('goods_attr') . " SET attr_gallery_flie = '" . $img['img_url'] . "' WHERE goods_attr_id = '$goods_attr_id' AND goods_id = '$goods_id'";
        $db->query($sql);
    } else {
        $result['error'] = 1;
    }
    
    $result['goods_attr_id'] = $goods_attr_id;

    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加仓库价格 //ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_goods_model_price') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '','content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $warehouse_id = 0;
    $area_id = 0;
    
    $goods = get_goods_model($goods_id);
    $smarty->assign('goods', $goods);
    
    $warehouse_list = get_warehouse_list();
    if($warehouse_list){
        $warehouse_id = $warehouse_list[0]['region_id'];
        $sql = "SELECT region_id FROM " .$ecs->table('region_warehouse'). " WHERE parent_id = '" .$warehouse_list[0]['region_id']. "'";
        $area_id = $db->getOne($sql, true);
    }
    
    $smarty->assign('warehouse_list', $warehouse_list);
    $smarty->assign('warehouse_id', $warehouse_id);
    $smarty->assign('area_id', $area_id);

    $list = get_goods_warehouse_area_list($goods_id, $goods['model_attr'], $warehouse_id);  
   
    $smarty->assign('warehouse_area_list',     $list['list']);
    $smarty->assign('warehouse_area_filter',       $list['filter']);
    $smarty->assign('warehouse_area_record_count', $list['record_count']);
    $smarty->assign('warehouse_area_page_count',   $list['page_count']);
    $smarty->assign('query',            $list['query']);
    $smarty->assign('full_page',    1);

    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_price_list.lbi');
    
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加仓库价格
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'goods_wa_query') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');

    $list = get_goods_warehouse_area_list();
    
    $smarty->assign('warehouse_area_list',     $list['list']);
    $smarty->assign('warehouse_area_filter',       $list['filter']);
    $smarty->assign('warehouse_area_record_count', $list['record_count']);
    $smarty->assign('warehouse_area_page_count',   $list['page_count']);
    $smarty->assign('query',            $list['query']);
    
    $goods = get_goods_model($list['filter']['goods_id']);
    $smarty->assign('goods', $goods);
    
    make_json_result($smarty->fetch('goods_price_list.lbi'), '', array('pb_filter' => $list['filter'], 'pb_page_count' => $list['page_count'], 'class' => "goodslistDiv"));
}

/* ------------------------------------------------------ */
//-- 添加仓库属性价格 //ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_warehouse_price') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '','content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $attr_id = isset($_REQUEST['attr_id']) && !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) && !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';

    $action_link = array('href' => 'goods.php?act=edit&goods_id=' . $goods_id . '&extension_code=', 'text' => $_LANG['goods_info']);

    if (empty($goods_attr_id)) {
        $goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID
    }

    if (empty($attr_id)) {
        $attr_id = get_goods_attr_nameid($goods_id, $goods_attr_id, $goods_attr_name, 'attr_id', 1);
    }

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

    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_warehouse_price_info.lbi');
    
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加仓库属性价格 //ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'insert_warehouse_price') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

    if (isset($_REQUEST['goods_attr_id']) && is_array($_REQUEST['goods_attr_id'])) {
        $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? $_REQUEST['goods_attr_id'] : array();
    }
    else {
        $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    }


    if (isset($_REQUEST['attr_id']) && is_array($_REQUEST['attr_id'])) {
        $attr_id = !empty($_REQUEST['attr_id']) ? $_REQUEST['attr_id'] : array();
    }
    else {
        $attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;}

    if (isset($_REQUEST['warehouse_name']) && is_array($_REQUEST['warehouse_name'])) {
        $warehouse_name = !empty($_REQUEST['warehouse_name']) ? $_REQUEST['warehouse_name'] : array();
    }
    else {
        $warehouse_name = !empty($_REQUEST['warehouse_name']) ? intval($_REQUEST['warehouse_name']) : 0;
    }

    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';

    get_warehouse_area_attr_price_insert($warehouse_name, $goods_id, $goods_attr_id, "warehouse_attr");
    
    $result['goods_attr_id'] = $goods_attr_id;

    die($json->encode($result));
}    

/* ------------------------------------------------------ */
//-- 添加地区属性价格 //ecmoban模板堂 --zhuo
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_area_price') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $attr_id = isset($_REQUEST['attr_id']) && !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) && !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? trim($_REQUEST['goods_attr_name']) : '';

    $action_link = array('href' => 'goods.php?act=edit&goods_id=' . $goods_id . '&extension_code=', 'text' => $_LANG['goods_info']);

    if (empty($goods_attr_id)) {
        $goods_attr_id = get_goods_attr_nameId($goods_id, $attr_id, $goods_attr_name); //获取商品的属性ID
    }

    if (empty($attr_id)) {
        $attr_id = get_goods_attr_nameid($goods_id, $goods_attr_id, $goods_attr_name, 'attr_id', 1);
    }


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

    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_area_price_info.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 添加地区属性价格 //ecmoban模板堂 --zhuo
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert_area_price') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

    if (isset($_REQUEST['goods_attr_id']) && is_array($_REQUEST['goods_attr_id'])) {
        $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? $_REQUEST['goods_attr_id'] : array();
    }
    else {
        $goods_attr_id = !empty($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    }

    if (isset($_REQUEST['attr_id']) && is_array($_REQUEST['attr_id'])) {
        $attr_id = !empty($_REQUEST['attr_id']) ? $_REQUEST['attr_id'] : array();
    }
    else {
        $attr_id = !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    }

    if (isset($_REQUEST['area_name']) && is_array($_REQUEST['area_name'])) {
        $area_name = !empty($_REQUEST['area_name']) ? $_REQUEST['area_name'] : array();
    }
    else {
        $area_name = !empty($_REQUEST['area_name']) ? intval($_REQUEST['area_name']) : 0;
    }
    $goods_attr_name = !empty($_REQUEST['goods_attr_name']) ? $_REQUEST['goods_attr_name'] : '';
    
    get_warehouse_area_attr_price_insert($area_name, $goods_id, $goods_attr_id, 'warehouse_area_attr');

    $result['goods_attr_id'] = $goods_attr_id;

    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加商品SKU/库存
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_sku') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
    $warehouse_id = 0;
    $area_id = 0;
    
    $goods = get_goods_model($goods_id);
    
    $warehouse_list = get_warehouse_list();
    if($warehouse_list){
        $warehouse_id = $warehouse_list[0]['region_id'];
        $sql = "SELECT region_id FROM " .$ecs->table('region_warehouse'). " WHERE parent_id = '" .$warehouse_list[0]['region_id']. "'";
        $area_id = $db->getOne($sql, true);
    }
    
    $smarty->assign('warehouse_id', $warehouse_id);
    $smarty->assign('area_id', $area_id);
    
    $smarty->assign('goods', $goods);
    
    $smarty->assign('warehouse_list', $warehouse_list);
    $smarty->assign('goods_id', $goods_id);
    $smarty->assign('user_id', $user_id);
    $smarty->assign('goods_attr_price', $GLOBALS['_CFG']['goods_attr_price']);

    $product_list = get_goods_product_list($goods_id, $goods['model_attr'], $warehouse_id, $area_id);
    $smarty->assign('product_list',     $product_list['product_list']);
    $smarty->assign('sku_filter',       $product_list['filter']);
    $smarty->assign('sku_record_count', $product_list['record_count']);
    $smarty->assign('sku_page_count',   $product_list['page_count']);
    $smarty->assign('query',            $product_list['query']);
    $smarty->assign('full_page',    1);
    
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_attr_list.lbi');
    die($json->encode($result));
}

/* ------------------------------------------------------ */
//-- 添加商品SKU/库存
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'sku_query') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    $smarty->assign('goods_attr_price', $GLOBALS['_CFG']['goods_attr_price']);

    $product_list = get_goods_product_list();
    
    $smarty->assign('product_list',     $product_list['product_list']);
    $smarty->assign('sku_filter',       $product_list['filter']);
    $smarty->assign('sku_record_count', $product_list['record_count']);
    $smarty->assign('sku_page_count',   $product_list['page_count']);
    $smarty->assign('query',            $product_list['query']);
    $goods = array('goods_id' => $product_list['filter']['goods_id'], 
                    'model_attr' => $product_list['filter']['model'], 
                    'warehouse_id' => $product_list['filter']['warehouse_id'], 
                    'area_id' => $product_list['filter']['area_id']);
    $smarty->assign('goods', $goods);    
    make_json_result($smarty->fetch('goods_attr_list.lbi'), '', array('pb_filter' => $product_list['filter'], 'pb_page_count' => $product_list['page_count'], 'class' => "attrlistDiv"));
}
/* ------------------------------------------------------ */
//-- 添加商品属性SKU
/* ------------------------------------------------------ */ 
elseif ($_REQUEST['act'] == 'add_attr_sku') {
    $json = new JSON;
    $result = array('error' => 0, 'message' => '', 'content' => '');
    $goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $product_id = !empty($_REQUEST['product_id']) ? intval($_REQUEST['product_id']) : 0;
    $goods_info = get_admin_goods_info($goods_id, array('goods_id', 'goods_name', 'goods_sn', 'model_attr'));
    $smarty->assign('product_id', $product_id);
    $editInput = '';
    $method = '';
    $filed = '';

    if ($goods_info['model_attr'] == 1) {
        $filed = ', warehouse_id';
        $method = 'insert_warehouse_price';
    }
    else if ($goods_info['model_attr'] == 2) {
        $filed = ', area_id';
        $method = 'insert_area_price';
    }
    else {
        $editInput = 'edit_attr_price';
    }

    $product = get_product_info($product_id, 'product_id, product_number, goods_id, product_sn, goods_attr' . $filed, $goods_info['model_attr'], 1);
    $smarty->assign('goods_info', $goods_info);
    $smarty->assign('product', $product);
    $smarty->assign('editInput', $editInput);
    $smarty->assign('method', $method);
    $warehouse_id = isset($product['warehouse_id']) && !empty($product['warehouse_id']) ? $product['warehouse_id'] : 0;
    $area_id = isset($product['area_id']) && !empty($product['area_id']) ? $product['area_id'] : 0;

    if (!empty($warehouse_id)) {
        $warehouse_area_id = $warehouse_id;
    }
    else if (!empty($area_id)) {
        $warehouse_area_id = $area_id;
    }

    $warehouse = get_area_info($warehouse_area_id, 1);
    $smarty->assign('warehouse_id', $warehouse_id);
    $smarty->assign('area_id', $area_id);
    $smarty->assign('warehouse', $warehouse);
    $result['method'] = $method;
    $result['content'] = $GLOBALS['smarty']->fetch('library/goods_list_product.lbi');
    die($json->encode($result));
}
/*页面加载动作*/
elseif($_REQUEST['act'] == 'getload_url'){
	
	$smarty->assign("temp","load_url");
	
	$smarty->display("library/dialog.lbi");
}

/*------------------------------------------------------ */
//-- 升级弹窗
/*------------------------------------------------------ */
 else if ($_REQUEST['act'] == 'dialog_upgrade') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    
    $result['content'] = $GLOBALS['smarty']->fetch('library/upgrade.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 商品单选复选属性手工录入
/*------------------------------------------------------ */
 else if ($_REQUEST['act'] == 'attr_input_type') {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    
    $attr_id = isset($_REQUEST['attr_id']) && !empty($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_id = isset($_REQUEST['goods_id']) && !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    
    $smarty->assign('attr_id',     $attr_id);
    $smarty->assign('goods_id',     $goods_id);
    
    $goods_attr = get_dialog_goods_attr_type($attr_id, $goods_id);
    $smarty->assign('goods_attr',     $goods_attr);

    $result['content'] = $GLOBALS['smarty']->fetch('library/attr_input_type.lbi');
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 商品单选复选属性手工录入
/*------------------------------------------------------ */
 else if ($_REQUEST['act'] == 'insert_attr_input')
 {
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) ? $_REQUEST['goods_attr_id'] : array();
    $attr_value_list = isset($_REQUEST['attr_value_list']) ? $_REQUEST['attr_value_list'] : array();
    
    /* 插入、更新、删除数据 */
    foreach ($attr_value_list as $key => $attr_value)
    {
        if($attr_value){
            if($goods_attr_id[$key]){
            $sql = "UPDATE " .$ecs->table('goods_attr'). " SET attr_value = '$attr_value' WHERE goods_attr_id = '" .$goods_attr_id[$key]. "' LIMIT 1";
            }else{
                $sql = "INSERT INTO " .$ecs->table('goods_attr'). " (attr_id, goods_id, attr_value)".
                        "VALUES ('$attr_id', '$goods_id', '$attr_value')";
            }
            
            $db->query($sql);
        }
    }
    
    $result['attr_id'] = $attr_id;
    $result['goods_id'] = $goods_id;
    
    $goods_attr = get_dialog_goods_attr_type($attr_id, $goods_id);
    $smarty->assign('goods_attr',     $goods_attr);
    $smarty->assign('attr_id',     $attr_id);
    
    $result['content'] = $GLOBALS['smarty']->fetch('library/attr_input_type_list.lbi');
    
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 商品单选复选属性手工录入
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'del_input_type')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $attr_id = isset($_REQUEST['attr_id']) ? intval($_REQUEST['attr_id']) : 0;
    $goods_attr_id = isset($_REQUEST['goods_attr_id']) ? intval($_REQUEST['goods_attr_id']) : 0;
    
    $sql = "DELETE FROM " .$ecs->table('goods_attr'). " WHERE goods_attr_id = '$goods_attr_id'";
    $db->query($sql);
    
    $goods_info = get_admin_goods_info($goods_id, array('model_attr'));
    if($goods_info['model_attr'] == 1){
        $table = 'products_warehouse';
    }elseif($goods_info['model_attr'] == 2){
        $table = 'products_area';
    }else{
        $table = 'products';
    }
    
    $where = " AND goods_id = '$goods_id'";
    $ecs->get_del_find_in_set($goods_attr_id, $where, $table, 'goods_attr');
    
    $goods_attr = get_dialog_goods_attr_type($attr_id, $goods_id);
    $smarty->assign('goods_attr',     $goods_attr);
    $smarty->assign('attr_id',     $attr_id);
    
    $result['attr_id'] = $attr_id;
    
    $result['attr_content'] = $GLOBALS['smarty']->fetch('library/attr_input_type_list.lbi');
    die($json->encode($result));
}

else if ($_REQUEST['act'] == 'del_volume')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $volume_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $sql = "DELETE FROM " . $ecs->table('volume_price') . " WHERE id = '$volume_id'";
    $db->query($sql);
    $volume_price_list = get_volume_price_list($goods_id);

    if (!$volume_price_list) {
        $sql = "UPDATE " . $ecs->table('goods') . " SET is_volume = 0 WHERE goods_id = '$goods_id'";
        $db->query($sql);
    }

    die($json->encode($result));
}
else if ($_REQUEST['act'] == 'del_cfull')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '');
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $volume_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $sql = "DELETE FROM " . $ecs->table('goods_consumption') . " WHERE id = '$volume_id'";
    $db->query($sql);
    $consumption_list = get_goods_con_list($goods_id, 'goods_consumption');

    if (!$consumption_list)
    {
        $sql = "UPDATE " . $ecs->table('goods') . " SET is_fullcut = 0 WHERE goods_id = '$goods_id'";
        $db->query($sql);
    }

    die($json->encode($result));
}
else if ($_REQUEST['act'] == 'add_external_url')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '', 'error' => 0);
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $smarty->assign('goods_id', $goods_id);
    $result['content'] = $GLOBALS['smarty']->fetch('library/external_url_list.lbi');
    $result['goods_id'] = $goods_id;
    die($json->encode($result));
}
else if ($_REQUEST['act'] == 'insert_external_url')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '', 'error' => 0);
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $external_url_list = isset($_REQUEST['external_url_list']) ? $_REQUEST['external_url_list'] : array();
    $proc_thumb = isset($GLOBALS['shop_id']) && ($GLOBALS['shop_id'] > 0) ? false : true;

    if ($external_url_list)
    {
        $sql = "SELECT MAX(img_desc) FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id'";
        $desc = $db->getOne($sql, true);

        foreach ($external_url_list as $key => $image_urls )
        {
            if ($image_urls)
            {
                if (!empty($image_urls) && $image_urls != $GLOBALS['_LANG']['img_file'] && $image_urls != 'http://' && copy(trim($image_urls), ROOT_PATH . 'temp/' . basename($image_urls)))
                {
                    $image_url = trim($image_urls);
                    $down_img = ROOT_PATH . 'temp/' . basename($image_url);

                    if ($proc_thumb)
                    {
                        $thumb_url = $GLOBALS['image']->make_thumb($down_img, $GLOBALS['_CFG']['thumb_width'], $GLOBALS['_CFG']['thumb_height']);
                        $thumb_url = (is_string($thumb_url) ? $thumb_url : '');
                        $thumb_url = reformat_image_name('gallery_thumb', $goods_id, $thumb_url, 'thumb');
                    }

                    if (!$proc_thumb)
                    {
                        $thumb_url = htmlspecialchars($image_url);
                    }

                    $desc += 1;
                    $img_url = $img_original = htmlspecialchars($image_url);
                    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('goods_gallery') . " (goods_id, img_url, img_desc, thumb_url, img_original) VALUES ('$goods_id', '$img_url', '$desc', '$thumb_url', '$img_original')";
                    $GLOBALS['db']->query($sql);
                    $thumb_img_id[] = $GLOBALS['db']->insert_id();
                    @unlink($down_img);
                }

                get_oss_add_file(array($img_url, $thumb_url, $img_original));
            }
        }

        if (!empty($_SESSION['thumb_img_id' . $_SESSION['admin_id']]))
        {
            $_SESSION['thumb_img_id' . $_SESSION['admin_id']] = array_merge($thumb_img_id, $_SESSION['thumb_img_id' . $_SESSION['admin_id']]);
        }
        else
        {
            $_SESSION['thumb_img_id' . $_SESSION['admin_id']] = $thumb_img_id;
        }
    }

    $sql = "SELECT * FROM " . $ecs->table('goods_gallery') . " WHERE goods_id = '$goods_id' ORDER BY img_desc";
    $img_list = $db->getAll($sql);
    if (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)
    {
        foreach ($img_list as $key => $gallery_img )
        {
            $img_list[$key] = $gallery_img;

            if (!empty($gallery_img['external_url']))
            {
                $img_list[$key]['img_url'] = $gallery_img['external_url'];
                $img_list[$key]['thumb_url'] = $gallery_img['external_url'];
            }
            else
            {
                $img_list[$key]['img_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], false, 'gallery');
                $img_list[$key]['thumb_url'] = get_image_path($gallery_img['goods_id'], $gallery_img['img_original'], true, 'gallery');
            }
        }
    }
    else
    {
        foreach ($img_list as $key => $gallery_img )
        {
            $img_list[$key] = $gallery_img;

            if (!empty($gallery_img['external_url']))
            {
                $img_list[$key]['img_url'] = $gallery_img['external_url'];
                $img_list[$key]['thumb_url'] = $gallery_img['external_url'];
            }
            else if ($proc_thumb)
            {
                $img_list[$key]['thumb_url'] = '../' . (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
            else 
            {
                $img_list[$key]['thumb_url'] = (empty($gallery_img['thumb_url']) ? $gallery_img['img_url'] : $gallery_img['thumb_url']);
            }
        }
    }

    $smarty->assign('img_list', $img_list);
    $smarty->assign('goods_id', $goods_id);
    $result['content'] = $GLOBALS['smarty']->fetch('library/gallery_img.lbi');
    $result['goods_id'] = $goods_id;
    die($json->encode($result));
}
else if ($_REQUEST['act'] == 'insert_gallery_url')
{
    $json = new JSON;
    $result = array('content' => '', 'sgs' => '', 'error' => 0);
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    $img_id = isset($_REQUEST['img_id']) ? intval($_REQUEST['img_id']) : 0;
    $external_url = isset($_REQUEST['external_url']) ? addslashes(trim($_REQUEST['external_url'])) : '';
    $sql = "SELECT img_id FROM " . $ecs->table('goods_gallery') . " WHERE external_url = '$external_url' AND goods_id = '$goods_id' AND img_id <> $img_id";
    if ($db->getOne($sql, true) && !empty($external_url))
    {
        $result['error'] = 1;
    }
    else
    {
        $sql = "UPDATE " . $ecs->table('goods_gallery') . " SET external_url = '$external_url' WHERE img_id = '$img_id'";
        $db->query($sql);
    }

    $result['img_id'] = $img_id;
    $result['external_url'] = $external_url;
    die($json->encode($result));
}
/**
 * 获取商品模式
 * model
 */
function get_goods_model($goods_id){
    $sql = "SELECT goods_id, goods_sn, model_attr, user_id FROM " .$GLOBALS['ecs']->table('goods'). " WHERE goods_id = '$goods_id' LIMIT 1";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 获取商品货品SKU列表
 */
function get_goods_product_list($goods_id = 0, $model = 0, $warehouse_id = 0, $area_id = 0){
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        
        $filter['product_sn'] = !isset($_REQUEST['product_sn']) ? '' : addslashes(trim($_REQUEST['product_sn']));
        $filter['goods_id'] = !isset($_REQUEST['goods_id']) ? $goods_id : intval($_REQUEST['goods_id']);
        $filter['model'] = !isset($_REQUEST['model']) ? $model : intval($_REQUEST['model']);
        
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'product_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);
        
        $where = "";
        if($filter['model'] == 1){
            $filter['warehouse_id'] = !isset($_REQUEST['warehouse_id']) ? $warehouse_id : intval($_REQUEST['warehouse_id']);
            $table = "products_warehouse";
            $where .= " AND warehouse_id = '" .$filter['warehouse_id']. "'";
        }elseif($filter['model'] == 2){
            $filter['area_id'] = !isset($_REQUEST['area_id']) ? $area_id : intval($_REQUEST['area_id']);
            $table = "products_area";
            $where .= " AND area_id = '" .$filter['area_id']. "'";
        }else{
            $table = "products";
        }
        
        if($filter['product_sn']){
            $where .= " AND product_sn = '" .$filter['product_sn']. "'";
        }

        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table($table). " WHERE goods_id = '" .$filter['goods_id']. "' $where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        
        /* 分页大小 */
        $filter = page_and_size($filter, 1);

        $sql = "SELECT * FROM " .$GLOBALS['ecs']->table($table). " WHERE goods_id = '" .$filter['goods_id']. "' $where ORDER BY " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
               " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
        
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    
    $res = $GLOBALS['db']->getAll($sql);

    for($i=0; $i<count($res); $i++){
        $goods_attr_id = str_replace('|', ',', $res[$i]['goods_attr']);
        $sql = "SELECT attr_value FROM " .$GLOBALS['ecs']->table('goods_attr') . " WHERE goods_id = '" .$res[$i]['goods_id']. "' AND goods_attr_id IN($goods_attr_id)";
        $attr_value = $GLOBALS['db']->getAll($sql);
        $res[$i]['attr_value'] = get_goods_attr_value($attr_value);
    }
    
    $arr = array('product_list' => $res, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'query' => "sku_query");
    
    return $arr;
}

function get_goods_attr_value($attr_value){
    
    $str = "";
    if($attr_value){
        foreach($attr_value as $key=>$val){
            $str .= "【" . $val['attr_value'] . "】";
        }
    }
    
    return $str;
}


/**
 * 获取商品仓库价格
 */
function get_goods_warehouse_area_list($goods_id = 0, $model = 0, $warehouse_id = 0){
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤条件 */
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }
        
        $filter['goods_id'] = !isset($_REQUEST['goods_id']) ? $goods_id : intval($_REQUEST['goods_id']);
        $filter['warehouse_id'] = !isset($_REQUEST['warehouse_id']) ? $warehouse_id : intval($_REQUEST['warehouse_id']);
        $filter['model'] = !isset($_REQUEST['model']) ? $model : intval($_REQUEST['model']);
        $filter['region_sn'] = !isset($_REQUEST['region_sn']) ? '' : addslashes(trim($_REQUEST['region_sn']));
        
        $filter['sort_by']    = empty($_REQUEST['sort_by'])    ? ' rw.region_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC'     : trim($_REQUEST['sort_order']);
        
        $where = " 1 ";
        $sql_where = '';
        $select = "";
        $leftJion = "";
        if($filter['model'] == 1){
            $where .= " AND rw.region_type = 0";
            
            $leftJion = ", " . $GLOBALS['ecs']->table('warehouse_goods') . " AS wg ";
            $sql_where .= " AND rw.region_id = wg.region_id AND wg.goods_id = '" .$filter['goods_id']. "'";
            
            $select .= ", wg.w_id, wg.region_sn, wg.region_number, wg.warehouse_price, wg.warehouse_promote_price";
            
            if($filter['region_sn']){
                $where .= " AND wg.region_sn = '" .$filter['region_sn']. "'";
            }

        }elseif($filter['model'] == 2){
            
            if($filter['warehouse_id']){
                $where .= " AND rw.parent_id = '" .$filter['warehouse_id']. "'";
            }
            
            $where .= " AND rw.region_type = 1";
            $select .= ", (SELECT rw2.region_name FROM " .$GLOBALS['ecs']->table('region_warehouse'). " AS rw2 WHERE rw2.region_id = rw.parent_id) AS warehouse_name ";
            
            $leftJion = ", " . $GLOBALS['ecs']->table('warehouse_area_goods') . " AS wag ";
            $sql_where .= " AND rw.region_id = wag.region_id AND wag.goods_id = '" .$filter['goods_id']. "'";
            
            $select .= ", wag.a_id, wag.region_sn, wag.region_number, wag.region_price, wag.region_promote_price";
            
            if($filter['region_sn']){
                $where .= " AND wag.region_sn = '" .$filter['region_sn']. "'";
            }
        }

        $sql = "SELECT count(*) FROM " .$GLOBALS['ecs']->table('region_warehouse'). " AS rw " . 
               $leftJion .
               " WHERE $where $sql_where";
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
        
        /* 分页大小 */
        $filter = page_and_size($filter, 1);

        $sql = "SELECT rw.* $select FROM " .$GLOBALS['ecs']->table('region_warehouse') . " AS rw " .
               $leftJion .  
               " WHERE $where $sql_where ORDER BY " . $filter['sort_by'] . ' ' . $filter['sort_order'] .
               " LIMIT " . $filter['start'] . ',' . $filter['page_size'];
        
        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    
    $res = $GLOBALS['db']->getAll($sql);
    
    $arr = array('list' => $res, 'filter' => $filter,
        'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'], 'query' => "goods_wa_query");
    
    return $arr;
}

?>