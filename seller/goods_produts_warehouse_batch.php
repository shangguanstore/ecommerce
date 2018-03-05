<?php
/**
 * ECSHOP 商品批量上传、修改
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_batch.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require('includes/lib_goods.php');

/*------------------------------------------------------ */
//-- 批量上传
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('goods_manage');
	
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	$warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : 0;
	
	if($goods_id > 0){
		$smarty->assign('action_link',  array('text' => '返回商品货品详细页', 'href'=>'goods.php?act=product_list&goods_id=' .$goods_id));
	}
	
    /* 取得可选语言 */
    $dir = opendir('../languages');
    $lang_list = array(
        'UTF8'      => $_LANG['charset']['utf8'],
        'GB2312'    => $_LANG['charset']['zh_cn'],
        'BIG5'      => $_LANG['charset']['zh_tw'],
    );
    $download_list = array();
    while (@$file = readdir($dir))
    {
        if ($file != '.' && $file != '..' && $file != ".svn" && $file != "_svn" && is_dir('../languages/' .$file) == true)
        {
            $download_list[$file] = sprintf($_LANG['download_file'], isset($_LANG['charset'][$file]) ? $_LANG['charset'][$file] : $file);
        }
    }
    @closedir($dir);
    $smarty->assign('lang_list',     $lang_list);
    $smarty->assign('download_list', $download_list);
	$smarty->assign('goods_id', $goods_id);
	$smarty->assign('warehouse_id', $warehouse_id);
	
	$attribute_list = get_attribute_list($goods_id);
	$smarty->assign('attribute_list', $attribute_list);
	
	$goods_date = array('goods_name');
	$where = "goods_id = '$goods_id'";
	$goods_name = get_table_date('goods', $where, $goods_date,2);
	$smarty->assign('goods_name', $goods_name);
	
    /* 参数赋值 */
    $ur_here = $_LANG['13_batch_add'];
    $smarty->assign('ur_here', $ur_here);

    /* 显示模板 */
    assign_query_info();
    $smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
    $smarty->display('goods_produts_warehouse_batch.dwt');
}

/*------------------------------------------------------ */
//-- 批量上传：处理
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'upload')
{
    /* 检查权限 */
    admin_priv('goods_manage');

	//ecmoban模板堂 --zhuo start 仓库
	if($_FILES['file']['name']){

		//获得属性的个数 bylu;
		$attr_names=file($_FILES['file']['tmp_name']);
		$attr_names=explode(',',$attr_names[0]);
		$attr_names=array_slice($attr_names,2,-2);
		foreach($attr_names as $k=>$v){
			$attr_names[$k]=ecs_iconv('GBK', 'UTF8', $v);
		}
		$attr_num=count($attr_names);

		$line_number = 0;
		$arr = array();
		$goods_list = array();
		$field_list = array_keys($_LANG['upload_product']); // 字段列表
		for($i=0;$i<$attr_num;$i++){
			$field_list[]='goods_attr'.$i;
		}
		$field_list[]='product_sn';
		$field_list[]='product_number';


		$_POST['charset'] = 'GB2312';
		$data = file($_FILES['file']['tmp_name']);
		
		if(count($data) > 0){
			foreach ($data AS $line)
			{
				// 跳过第一行
				if ($line_number == 0)
				{
					$line_number++;
					continue;
				}
		
				// 转换编码
				if (($_POST['charset'] != 'UTF8') && (strpos(strtolower(EC_CHARSET), 'utf') === 0))
				{
					$line = ecs_iconv($_POST['charset'], 'UTF8', $line);
				}
		
				// 初始化
				$arr    = array();
				$buff   = '';
				$quote  = 0;
				$len    = strlen($line);
				for ($i = 0; $i < $len; $i++)
				{
					$char = $line[$i];
		
					if ('\\' == $char)
					{
						$i++;
						$char = $line[$i];
		
						switch ($char)
						{
							case '"':
								$buff .= '"';
								break;
							case '\'':
								$buff .= '\'';
								break;
							case ',';
								$buff .= ',';
								break;
							default:
								$buff .= '\\' . $char;
								break;
						}
					}
					elseif ('"' == $char)
					{
						if (0 == $quote)
						{
							$quote++;
						}
						else
						{
							$quote = 0;
						}
					}
					elseif (',' == $char)
					{
						if (0 == $quote)
						{
							if (!isset($field_list[count($arr)]))
							{
								continue;
							}
							$field_name = $field_list[count($arr)];
							$arr[$field_name] = trim($buff);
							$buff = '';
							$quote = 0;
						}
						else
						{
							$buff .= $char;
						}
					}
					else
					{
						$buff .= $char;
					}
		
					if ($i == $len - 1)
					{
						if (!isset($field_list[count($arr)]))
						{
							continue;
						}
						$field_name = $field_list[count($arr)];
						$arr[$field_name] = trim($buff);
					}
				}
				$goods_list[] = $arr;
			}

			//格式化商品数据 bylu;
			$goods_list = get_produts_warehouse_list2($goods_list,$attr_num);
		}
	}

	$_SESSION['goods_list'] = $goods_list;

	$smarty->assign('full_page', 2);
	$smarty->assign('page', 1);
	$smarty->assign('attr_names',$attr_names);//属性名称;

	/* 显示模板 */
	assign_query_info();
	$smarty->assign('ur_here','仓属属性批量上传');
    $smarty->assign('menu_select',array('action' => '02_cat_and_goods', 'current' => '01_goods_list'));
	$smarty->display('goods_produts_warehouse_batch_add.dwt');

}


/*------------------------------------------------------ */
//-- 动态添加数据入库;
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'ajax_insert')
{

	include_once(ROOT_PATH . 'includes/cls_json.php');
	$json = new JSON();

	$page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 1;

	/* 设置最长执行时间为5分钟 */
	@set_time_limit(300);

	if(isset($_SESSION['goods_list']) && $_SESSION['goods_list'])
	{
		$commission_list = $_SESSION['goods_list'];

		$commission_list = $ecs->page_array($page_size, $page, $commission_list);

		$result['list'] = $commission_list['list'][0];

		$result['page'] = $commission_list['filter']['page'] + 1;
		$result['page_size'] = $commission_list['filter']['page_size'];
		$result['record_count'] = $commission_list['filter']['record_count'];
		$result['page_count'] = $commission_list['filter']['page_count'];

		$result['is_stop'] = 1;
		if($page > $commission_list['filter']['page_count']){
			$result['is_stop'] = 0;
		}

		//查询数据是否已经存在;
		$sql = "select product_id from " .$GLOBALS['ecs']->table('products_warehouse'). " where goods_id = '" . $result['list']['goods_id'] ."'".
				" and goods_attr = '" . $result['list']['goods_attr'] ."'".
				" and warehouse_id = '" . $result['list']['warehouse_id'] . "'";
		$res = $GLOBALS['db']->getOne($sql);

		if($GLOBALS['db']->getOne($sql)){
			$result['status_lang'] = '<span style="color: red;">数据已存在</span>';
		}else{
			if($result['is_stop']){
				$other = array(
						'goods_id'       => $result['list']['goods_id'],
						'goods_attr'     => $result['list']['goods_attr'],//把属性id插入数据表 bylu;
						'product_sn'     => $result['list']['product_sn'],
						'product_number' => $result['list']['product_number'],
						'warehouse_id'   => $result['list']['warehouse_id'],
				);

				$db->autoExecute($ecs->table('products_warehouse'), $other, 'INSERT');

				if($db->insert_id()){
					$result['status_lang'] = '添加成功';
				}else{
					$result['status_lang'] = '添加失败';
				}
			}
		}
	}
	die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 下载文件
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'download')
{
    /* 检查权限 */
    admin_priv('goods_manage');
	
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;			
	$warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : 0;		
	$goods_attr = isset($_REQUEST['goods_attr']) ? explode(',', $_REQUEST['goods_attr']) : array();		

    // 文件标签
    // Header("Content-type: application/octet-stream");
    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    Header("Content-Disposition: attachment; filename=attr_info_list.csv");

    // 下载
    if ($_GET['charset'] != $_CFG['lang'])
    {
        $lang_file = '../languages/' . $_GET['charset'] . '/admin/goods_produts_warehouse_batch.php';
        if (file_exists($lang_file))
        {
            unset($_LANG['upload_product']);
            require($lang_file);
        }
    }
    if (isset($_LANG['upload_product']))
    {
        /* 创建字符集转换对象 */
        if ($_GET['charset'] == 'zh_cn' || $_GET['charset'] == 'zh_tw')
        {
            $to_charset = $_GET['charset'] == 'zh_cn' ? 'GB2312' : 'BIG5';
			$data = join(',', $_LANG['upload_product']);

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

			//获取属性名称 bylu;
			foreach($_attribute as $k=>$v){
				$data.= ','.$v['attr_name'];
			}
			$data.= ",货号";
			$data.= ",库存\t\n";

			$goods_date = array('goods_sn');
			$where = "goods_id = '$goods_id'";
			$goods_sn = get_table_date('goods', $where, $goods_date,2);

			$area_date = array('region_name');
			
			$warehouse_where = '';
			if($warehouse_id > 0){
				$warehouse_where = " and region_id = " . $warehouse_id;
			}
			
			$where = "region_type = 0" . $warehouse_where;
			$warehouse_info = get_table_date('region_warehouse', $where, $area_date,1);

			$attr_info = get_list_download($goods_sn, $warehouse_info,$_attribute,count($_attribute));

			foreach ($attr_info as $k =>$v){
					$data.=$attr_info[$k]['goods_sn'].',';
					$data.=$attr_info[$k]['region_name'].',';
					$data.=implode(',',$v['attr_value']).',';
					$data.=$attr_info[$k]['product_sn'].',';
					$data.=$attr_info[$k]['product_number'] . "\t\n";
			}
            
			echo ecs_iconv(EC_CHARSET, $to_charset, $data);
        }
        else
        {
            echo join(',', $_LANG['upload_product']);
        }
    }
    else
    {
        echo 'error: $_LANG[upload_product] not exists';
    }
}

/*
 * 整合剩余字段 bylu
 */
function get_commission_list($commission_list){

	if($commission_list){
		foreach($commission_list as $key=>$rows){
			$commission_list[$key]['suppliers_value'] = $rows['suppliers_percent'];

			$sql = "SELECT user_id FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_name = '" .$rows['user_name']. "' LIMIT 1";
			$users = $GLOBALS['db']->getRow($sql);

			$sql = "SELECT percent_id FROM " .$GLOBALS['ecs']->table('merchants_percent'). " WHERE percent_value = '" .$rows['suppliers_percent']. "' LIMIT 1";
			$percent = $GLOBALS['db']->getRow($sql);

			$commission_list[$key]['user_id'] = $users['user_id'];
			$commission_list[$key]['suppliers_percent'] = $percent['percent_id'];
			$commission_list[$key]['shop_name'] = get_shop_name($users['user_id'], 1);

			if(!$users['user_id']){
				unset($commission_list[$key]);
			}
		}
	}

	return $commission_list;
}


function get_list_download($goods_sn = '', $warehouse_info = array(),$attr_info,$attr_num){
	
	$goods_date = array('model_attr');
	$where = "goods_sn = '$goods_sn' and is_delete = 0";

	$model_attr = get_table_date('goods', $where, $goods_date,2);

	$arr = array();


	if(count($warehouse_info) > 0 && $model_attr == 1){
		//格式化数组;
		foreach($attr_info as $k =>$v){
			foreach($v as $k2 =>$v2){
				if($k2 == 'attr_values'){
						$attr[]=$v2;
				}
			}
		}
		$comb = combination(array_keys($attr), $attr_num);
		$res = array();
		foreach($comb as $r) {
			$t = array();
			foreach($r as $k){
				$t[] = $attr[$k];
			}
			$res = array_merge($res, attr_group($t));
		}
		//组合数据;
		foreach($res as $k=>$v){
			$arr[]=array(
					'goods_sn' => $goods_sn,
					'region_name' => $warehouse_info[0]['region_name'],
					'attr_value' => $v,
					'product_sn' => '',
					'product_number' => '',
			);
		}
	}
	return $arr;
}

//商品属性 start
function get_attribute_list($goods_id = 0){
	$sql = "select a.attr_id, a.attr_name from " .$GLOBALS['ecs']->table('goods_attr'). " as ga " . 
		 " left join " .$GLOBALS['ecs']->table('attribute'). " as a on ga.attr_id = a.attr_id" . 
		 " where ga.goods_id = '$goods_id' group by ga.attr_id";
	$res = $GLOBALS['db']->getAll($sql);	 
	
	$arr = array();
	foreach($res as $key=>$row){
		$arr[$key]['attr_name']= $row['attr_name'];
		$arr[$key]['goods_attr']= get_goods_attr_list($row['attr_id'], $goods_id);
	}
	
	return $arr;
}

function get_goods_attr_list($attr_id = 0, $goods_id = 0){
	$sql = "select goods_attr_id, attr_value from " .$GLOBALS['ecs']->table('goods_attr'). " where goods_id = '$goods_id' and attr_id = '$attr_id' order by goods_attr_id asc";
	$res = $GLOBALS['db']->getAll($sql);
	
	$arr = array();
	foreach($res as $key=>$row){
		$arr[$key]['goods_attr_id']= $row['goods_attr_id'];
		$arr[$key]['attr_value']= $row['attr_value'];
	}

	return $arr;
}

//商品属性 end
?>