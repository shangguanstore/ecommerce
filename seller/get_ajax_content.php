<?php

/*
 * ECSHOP 获取ajax数据
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$_REQUEST['act'] = trim($_REQUEST['act']);

$data=array('error'=>0,'message'=>'','content'=>'');
$smarty->assign('menus',$_SESSION['menus']);
/*------------------------------------------------------ */
//-- 获取下级分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_select_category')
{
	$cat_id=empty($_REQUEST['cat_id'])? 0:intval($_REQUEST['cat_id']);
	$child_cat_id=empty($_REQUEST['child_cat_id'])? 0:intval($_REQUEST['child_cat_id']);
	$cat_level=empty($_REQUEST['cat_level'])? 0:intval($_REQUEST['cat_level']);
	$select_jsId=empty($_REQUEST['select_jsId'])? 'cat_parent_id':trim($_REQUEST['select_jsId']);
	$type=empty($_REQUEST['type'])? 0:intval($_REQUEST['type']);
	$table = empty($_REQUEST['table']) ? 0 : intval($_REQUEST['table']);
	
	if ($table == 1)
	{
		$adminru = get_admin_ru_id();
		$content = insert_seller_select_category($cat_id, $child_cat_id, $cat_level, $select_jsId, $type, "merchants_category", array(), $adminru["ru_id"]);
	}
	else
	{
		$content=insert_select_category($cat_id,$child_cat_id,$cat_level,$select_jsId,$type, $table);
	}
	if(!empty($content))
	{
		$data['error']= 1;
		$data['content']=$content;		
	}
	
	die(json_encode($data));
}


/*------------------------------------------------------ */
//-- 函数相关
/*------------------------------------------------------ */

else if ($_REQUEST['act'] == 'filter_category')
{
	$cat_id = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
	$cat_type_show = empty($_REQUEST['cat_type_show']) ? 0 : intval($_REQUEST['cat_type_show']);
	$user_id = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
	$result = array('error' => 0, 'message' => '', 'content' => '');
	$cat_nav = '';

	if ($cat_type_show == 1)
	{
		if ($cat_id)
		{
			$parent_cat_list = get_seller_select_category($cat_id, 1, true, $user_id);
			$filter_category_navigation = get_seller_array_category_info($parent_cat_list);
		}
	}
	else if ($cat_id)
	{
		$parent_cat_list = get_select_category($cat_id, 1, true, $user_id);
		$filter_category_navigation = get_array_category_info($parent_cat_list);

		if ($filter_category_navigation)
		{
			foreach ($filter_category_navigation as $key => $val )
			{
				if ($key == 0)
				{
					$cat_nav .= $val['cat_name'];
				}
				else if ($key > 0)
				{
					$cat_nav .= ' > ' . $val['cat_name'];
				}
			}
		}
		else
		{
			$cat_nav = '请选择分类';
		}
	}

	if ($cat_id)
	{
		$cat_level = count($parent_cat_list);
		$result['cat_nav'] = $cat_nav;
	}
	else
	{
		$cat_level = 0;
		$result['cat_nav'] = '请选择分类';
	}

	if ($cat_type_show == 1)
	{
		if ($cat_level <= 3)
		{
			$filter_category_list = get_seller_category_list($cat_id, 2, $user_id);
		}
		else
		{
			$filter_category_list = get_seller_category_list($cat_id, 0, $user_id);
			$cat_level -= 1;
		}
	}
	else {
		if ($user_id)
		{
			$seller_shop_cat = seller_shop_cat($user_id);
		}
		else
		{
			$seller_shop_cat = array();
		}

		if ($cat_level <= 3)
		{
			$filter_category_list = get_category_list($cat_id, 2, $seller_shop_cat, $user_id, $cat_level);
		}
		else
		{
			$filter_category_list = get_category_list($cat_id, 0, $seller_shop_cat, $user_id, $cat_level);
			$cat_level -= 1;
		}
	}

	$smarty->assign('user_id', $user_id);
	$smarty->assign('cat_type_show', $cat_type_show);
	$smarty->assign('filter_category_level', $cat_level);

	if ($cat_type_show)
	{
		if ($cat_id)
		{
			$smarty->assign('seller_filter_category_navigation', $filter_category_navigation);
		}

		$smarty->assign('seller_filter_category_list', $filter_category_list);
		$result['content'] = $smarty->fetch('templates/library/filter_category_seller.lbi');
	}
	else
	{
		if ($cat_id)
		{
			$smarty->assign('filter_category_navigation', $filter_category_navigation);
		}

		$smarty->assign('filter_category_list', $filter_category_list);
		$result['content'] = $smarty->fetch('templates/library/filter_category.lbi');
	}

	die(json_encode($result));
}
?>