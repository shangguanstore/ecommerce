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
require(ROOT_PATH . '/includes/cls_json.php');

$adminru = get_admin_ru_id();

/*------------------------------------------------------ */
//-- 弹出窗口
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'dialog_content')
{
	$json = new JSON;
	$result = array('content' => '','sgs'=>'');
	$temp=!empty($_REQUEST['temp'])?$_REQUEST['temp']:'';
	$smarty->assign("temp",$temp);
	$result['sgs']=$temp;
	$result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
	die($json->encode($result));
}
else if($_REQUEST['act'] == 'dialog_warehouse')
{
	$json = new JSON;
	$result = array('content' => '','sgs'=>'');
	$temp=!empty($_REQUEST['temp'])?$_REQUEST['temp']:'';

	$user_id = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : $adminru['ru_id'];
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;

	$smarty->assign("temp",$temp);
	$result['sgs']=$temp;
        
    $grade_rank = get_seller_grade_rank($user_id);    
    $smarty->assign('grade_rank',$grade_rank);
    $smarty->assign('integral_scale',$_CFG['integral_scale']);
	
	$warehouse_list = get_warehouse_list();
    $smarty->assign('warehouse_list', $warehouse_list);
	$smarty->assign('user_id', $user_id);
	$smarty->assign('goods_id', $goods_id);
	
	$result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
	die($json->encode($result));
}
else if($_REQUEST['act'] == 'dialog_img')
{
	$json = new JSON;
	$result = array('content' => '','sgs'=>'');
	$temp=!empty($_REQUEST['temp'])?$_REQUEST['temp']:'';
	$smarty->assign("temp",$temp);
        $goods_id=!empty($_REQUEST['goods_id'])    ? $_REQUEST['goods_id']:'';
        $smarty->assign('goods_id',$goods_id);
	$result['sgs']=$temp;
	
	$result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
	die($json->encode($result));
}
else if($_REQUEST['act'] == 'dialog_add')
{
	$json = new JSON;
	$result = array('content' => '','sgs'=>'');
	$temp=!empty($_REQUEST['temp'])?$_REQUEST['temp']:'';
	$smarty->assign("temp",$temp);
	
	$result['sgs']=$temp;
	
	/* 取得地区 */
    $country_list = get_regions();
	$smarty->assign('countries', $country_list);
	
	$result['content'] = $GLOBALS['smarty']->fetch('library/dialog.lbi');
	die($json->encode($result));
}

else if ($_REQUEST['act'] == 'extension_category')
{
	$json = new JSON;
	$result = array('content' => '', 'sgs' => '');
	$temp = !empty($_REQUEST['temp']) ? $_REQUEST['temp'] : '';
	$smarty->assign('temp', $temp);
	$result['sgs'] = $temp;
	$goods_id = empty($_REQUEST['goods_id']) ? 0 : intval($_REQUEST['goods_id']);
	$goods = get_admin_goods_info($goods_id, array('user_id'));
	$goods['user_id'] = empty($goods['user_id']) ? $adminru['ru_id'] : $goods['user_id'];

	if ($goods['user_id'])
	{
		$seller_shop_cat = seller_shop_cat($goods['user_id']);
	}

	$level_limit = 3;
	$category_level = array();

	for ($i = 1; $i <= $level_limit; $i++)
	{
		$category_list = array();

		if ($i == 1)
		{
			if ($goods['user_id'])
			{
				$category_list = get_category_list(0, 0, $seller_shop_cat, $goods['user_id'], $i);
			}
			else
			{
				$category_list = get_category_list();
			}
		}

		$smarty->assign('cat_level', $i);
		$smarty->assign('category_list', $category_list);
		$category_level[$i] = $smarty->fetch('templates/library/get_select_category.lbi');
	}

	$smarty->assign('category_level', $category_level);

	if ($goods_id > 0)
	{
		$other_cat_list1 = array();
		$sql = "SELECT ga.cat_id FROM " . $ecs->table('goods_cat') . " as ga  WHERE ga.goods_id = '$goods_id'";
		$other_cat1 = $db->getCol($sql);
		$other_category = array();

		foreach ($other_cat1 as $key => $val )
		{
			$other_category[$key]['cat_id'] = $val;
			$other_category[$key]['cat_name'] = get_every_category($val);
		}

		$smarty->assign('other_category', $other_category);
	}

	$smarty->assign('goods_id', $goods_id);
	$result['content'] = $GLOBALS['smarty']->fetch('library/extension_category.lbi');
	die($json->encode($result));
}
?>
