<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

require_once(ROOT_PATH . 'includes/cls_image.php');

/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("entry_criteria"), $db, 'id', 'criteria_name');

if($_REQUEST['act'] == 'list'){
    admin_priv('seller_grade');//by kong
    $smarty->assign('ur_here',      $_LANG['entry_criteria_list']);
    
    $smarty->assign('action_link',  array('text' => $_LANG['seller_garde_list'], 'href' => 'seller_grade.php?act=list'));
    $smarty->assign('action_link2',  array('text' => $_LANG['add_entry_criteria'], 'href' => 'entry_criteria.php?act=add'));
    
	$parent_id = isset($_REQUEST['parent_id']) ? intval($_REQUEST['parent_id']) : 0;
	
    $articlecat = get_cat_level($parent_id);

    foreach($articlecat as $k=>$v){
        
        /*获取父级名称*/
        if($v['parent_id'] > 0){
            $articlecat[$k]['parent_name']=$db->getOne(" SELECT criteria_name FROM ".$ecs->table('entry_criteria')." WHERE id = '".$v['parent_id']."'");
        }
        
        switch ($v['type'])
        {
            case 'text':
                $articlecat[$k]['type']=$_LANG['text'];
                break;
            case 'select':
                $articlecat[$k]['type']=$_LANG['select'];
                break;
            case 'textarea':
                $articlecat[$k]['type']=$_LANG['textarea'];
                break;
            case 'file':
                $articlecat[$k]['type']=$_LANG['file'];
                break;
            case 'charge':
                $articlecat[$k]['type']=$_LANG['charge'];
                break;
        }
    }
    $smarty->assign('entry_criteria', $articlecat);
    
    $smarty->display("entry_criteria.dwt");
}
elseif($_REQUEST['act'] == 'query'){
    admin_priv('seller_grade');//by kong

    $smarty->assign('ur_here',      $_LANG['entry_criteria_list']);
    
    $smarty->assign('action_link',  array('text' => $_LANG['seller_garde_list'], 'href' => 'seller_grade.php?act=list'));
    $smarty->assign('action_link2',  array('text' => $_LANG['add_entry_criteria'], 'href' => 'entry_criteria.php?act=add'));
	
	$parent_id = isset($_REQUEST['parent_id']) ? intval($_REQUEST['parent_id']) : 0;
	
    $articlecat = get_cat_level($parent_id);
    foreach($articlecat as $k=>$v){
        
        /*获取父级名称*/
        if($v['parent_id'] > 0){
            $articlecat[$k]['parent_name']=$db->getOne(" SELECT criteria_name FROM ".$ecs->table('entry_criteria')." WHERE id = '".$v['parent_id']."'");
        }
        
        switch ($v['type'])
        {
            case 'text':
                $articlecat[$k]['type']=$_LANG['text'];
                break;
            case 'select':
                $articlecat[$k]['type']=$_LANG['select'];
                break;
            case 'textarea':
                $articlecat[$k]['type']=$_LANG['textarea'];
                break;
            case 'file':
                $articlecat[$k]['type']=$_LANG['file'];
                break;
            case 'charge':
                $articlecat[$k]['type']=$_LANG['charge'];
                break;
        }
    }
    $smarty->assign('entry_criteria', $articlecat);
    
//跳转页面  
    make_json_result($smarty->fetch('entry_criteria.dwt'));
    
}
elseif($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit'){
    admin_priv('seller_grade');//by kong
    $smarty->assign('ur_here',      $_LANG['add_entry_criteria']);
    $smarty->assign('action_link',  array('text' => $_LANG['entry_criteria_list'], 'href' => 'entry_criteria.php?act=list'));
    $act =  ($_REQUEST['act'] == 'add')  ?  'insert' : 'update';
    $smarty->assign('act',$act);
    $entry_criteria=$db->getAll(" SELECT id,criteria_name FROM ".$ecs->table('entry_criteria')." WHERE  parent_id = 0");//获取所有上级
    $smarty->assign('criteria',$entry_criteria);
    
    if($_REQUEST['id']){
        $entry_criteria=$db->getRow("SELECT * FROM ".$ecs->table("entry_criteria")." WHERE id = '".$_REQUEST['id']."'");
        $entry_criteria['option_value'] = explode(',', $entry_criteria['option_value']);
        $smarty->assign('entry_criteria',$entry_criteria);
    }
    
    $smarty->display("entry_criteria_info.dwt");
}

elseif($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update'){
    admin_priv('seller_grade');//by kong
    $smarty->assign('ur_here',      $_LANG['edit_entry_criteria']);
    $smarty->assign('action_link',  array('text' => $_LANG['entry_criteria_list'], 'href' => 'entry_criteria.php?act=list'));
    
    $entry_criteria=$db->getAll(" SELECT id,criteria_name FROM ".$ecs->table('entry_criteria')." WHERE  parent_id = 0");//获取所有上级
    $smarty->assign('criteria',$entry_criteria);
    
    $criteria_name=!empty($_REQUEST['criteria_name'])       ?  $_REQUEST['criteria_name'] : '';
    $parent_id = !empty($_REQUEST['parent_id'])             ?  intval($_REQUEST['parent_id']) : 0;
    $charge=!empty($_REQUEST['charge'])                     ?  round($_REQUEST['charge'] ,2)   :0.00;
    $type = !empty($_REQUEST['type'])                       ?  $_REQUEST['type'] : '';
    $is_mandatory  = !empty($_REQUEST['is_mandatory'])      ?  $_REQUEST['is_mandatory'] : 0;
    
    $option_value = !empty($_REQUEST['option_value'])       ?  implode(',',array_unique($_REQUEST['option_value'])) : '';
    if($_REQUEST['act'] == 'update'){
        /*检查标准是否重复*/
        $is_only = $exc->is_only('criteria_name', $criteria_name,0,"id != $_POST[id]");
        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['criteria_name_repeat'], stripslashes($criteria_name)), 1);
        }
        
        $sql=" UPDATE ".$ecs->table('entry_criteria')." SET criteria_name = '$criteria_name' , parent_id = '$parent_id' ,charge='$charge',type='$type',is_mandatory='$is_mandatory' ,option_value='$option_value' WHERE id = '".$_REQUEST['id']."'";
        
        if($db->query($sql) == true){
            $link[0]['text'] = $_LANG['bank_list'];
            $link[0]['href'] = 'entry_criteria.php?act=list';
            $lang=$_LANG['edit_succeed'];
         }
         
    }elseif($_REQUEST['act'] == 'insert'){
        /*检查标准是否重复*/
        $is_only = $exc->is_only('criteria_name', $criteria_name,0);
        
        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['criteria_name_repeat'], stripslashes($criteria_name)), 1);
        }
       
        $sql=" INSERT INTO ".$ecs->table('entry_criteria')." (`criteria_name`,`parent_id`,`charge`,`type`,`is_mandatory`,`option_value`) values  ( '$criteria_name','$parent_id','$charge','$type','$is_mandatory','$option_value')";
        
        if($db->query($sql) == true){
            $link[0]['text'] = $_LANG['GO_add'];
            $link[0]['href'] = 'entry_criteria.php?act=add';
            
            $link[1]['text'] = $_LANG['bank_list'];
            $link[1]['href'] = 'entry_criteria.php?act=list';
            
            $lang=$_LANG['add_succeed'];
         }
    }
     clear_cache_files(); // 清除相关的缓存文件
     sys_msg($lang,0, $link);
}
/*删除*/
elseif($_REQUEST['act'] == 'remove'){
     $id = intval($_GET['id']);
     
    
    /* 删除原来的文件 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('entry_criteria') . " WHERE parent_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        /* 还有子分类，不能删除 */
        make_json_error($_LANG['is_fullentry']);
    }
    $exc->drop($id);
    clear_cache_files(); // 清除相关的缓存文件
    $url = 'entry_criteria.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}
elseif($_REQUEST['act'] == 'edit_charge'){
    check_authz_json('seller_grade');
    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    if ($exc->edit("charge = '$order'", $id))
    {
      clear_cache_files();
      make_json_result(stripslashes($order));
    }
    else
    {
      make_json_error($db->error());
    }
}
elseif($_REQUEST['act'] == 'toggle_show'){
        check_authz_json('seller_grade');
    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));
        if ($exc->edit("is_mandatory = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
        else
        {
            make_json_error($db->error());
        }
}

/**
 * 获得指定标准下的子标准的数组
 *
 * @access  public
 * @param   int     $cat_id     标准的ID
 * @param   int     $selected   当前选中标准的ID
 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
 * @param   int     $level      限定返回的级数。为0时返回所有级数
 * @return  mix
 */
function entry_criteria_list($id = 0, $selected = 0, $re_type = true, $level = 0)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('entry_criteria_releate');
			
        if ($data === false)
        {
            $sql = "SELECT c.*, COUNT(s.id) AS has_children ".
               ' FROM ' . $GLOBALS['ecs']->table('entry_criteria') . " AS c".
               " LEFT JOIN " . $GLOBALS['ecs']->table('entry_criteria') . " AS s ON s.parent_id=c.id".
               " GROUP BY c.id ".
               " ORDER BY parent_id ASC";
            $res = $GLOBALS['db']->getAll($sql);
			
            write_static_cache('entry_criteria_releate', $res);
			
        }
        else
        {
            $res = $data;
        }
    }

    if (empty($res) == true)
    {
        return $re_type ? '' : array();
    }

    $options = entry_criteria_options($id, $res); // 获得指定标准下的子标准的数组
	
    /* 截取到指定的缩减级别 */
    if ($level > 0)
    {
        if ($id == 0)
        {
            $end_level = $level;
        }
        else
        {
            $first_item = reset($options); // 获取第一个元素
            $end_level  = $first_item['level'] + $level;
        }

        /* 保留level小于end_level的部分 */
        foreach ($options AS $key => $val)
        {
            if ($val['level'] >= $end_level)
            {
                unset($options[$key]);
            }
        }
    }
        return $options;
}
/**
 * 过滤和排序所有标准，返回一个带有缩进级别的数组
 *
 * @access  private
 * @param   int     $cat_id     上级标准ID
 * @param   array   $arr        含有所有标准的数组
 * @param   int     $level      级别
 * @return  void
 */
function entry_criteria_options($spec_cat_id, $arr)
{
    static $cat_options = array();

    if (isset($cat_options[$spec_cat_id]))
    {
        return $cat_options[$spec_cat_id];
    }

    if (!isset($cat_options[0]))
    {
        $level = $last_cat_id = 0;
        $options = $cat_id_array = $level_array = array();
        while (!empty($arr))
        {
            foreach ($arr AS $key => $value)
            {
                $cat_id = $value['id'];
                if ($level == 0 && $last_cat_id == 0)
                {
                    if ($value['parent_id'] > 0)
                    {
                        break;
                    }

                    $options[$cat_id]          = $value;
                    $options[$cat_id]['level'] = $level;
                    $options[$cat_id]['id']    = $cat_id;
                    $options[$cat_id]['name']  = $value['criteria_name'];
                    unset($arr[$key]);

                    if ($value['has_children'] == 0)
                    {
                        continue;
                    }
                    $last_cat_id  = $cat_id;
                    $cat_id_array = array($cat_id);
                    $level_array[$last_cat_id] = ++$level;
                    continue;
                }

                if ($value['parent_id'] == $last_cat_id)
                {
                    $options[$cat_id]          = $value;
                    $options[$cat_id]['level'] = $level;
                    $options[$cat_id]['id']    = $cat_id;
                    $options[$cat_id]['name']  = $value['criteria_name'];
                    unset($arr[$key]);

                    if ($value['has_children'] > 0)
                    {
                        if (end($cat_id_array) != $last_cat_id)
                        {
                            $cat_id_array[] = $last_cat_id;
                        }
                        $last_cat_id    = $cat_id;
                        $cat_id_array[] = $cat_id;
                        $level_array[$last_cat_id] = ++$level;
                    }
                }
                elseif ($value['parent_id'] > $last_cat_id)
                {
                    break;
                }
            }

            $count = count($cat_id_array);
            if ($count > 1)
            {
                $last_cat_id = array_pop($cat_id_array);
            }
            elseif ($count == 1)
            {
                if ($last_cat_id != end($cat_id_array))
                {
                    $last_cat_id = end($cat_id_array);
                }
                else
                {
                    $level = 0;
                    $last_cat_id = 0;
                    $cat_id_array = array();
                    continue;
                }
            }

            if ($last_cat_id && isset($level_array[$last_cat_id]))
            {
                $level = $level_array[$last_cat_id];
            }
            else
            {
                $level = 0;
            }
        }
        $cat_options[0] = $options;
    }
    else
    {
        $options = $cat_options[0];
    }

    if (!$spec_cat_id)
    {
        return $options;
    }
    else
    {
        if (empty($options[$spec_cat_id]))
        {
            return array();
        }

        $spec_cat_id_level = $options[$spec_cat_id]['level'];

        foreach ($options AS $key => $value)
        {
            if ($key != $spec_cat_id)
            {
                unset($options[$key]);
            }
            else
            {
                break;
            }
        }

        $spec_cat_id_array = array();
        foreach ($options AS $key => $value)
        {
            if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                ($spec_cat_id_level > $value['level']))
            {
                break;
            }
            else
            {
                $spec_cat_id_array[$key] = $value;
            }
        }
        $cat_options[$spec_cat_id] = $spec_cat_id_array;

        return $spec_cat_id_array;
    }
}

function get_cat_level($parent_id = 0)
{
	$res = array();

	$sql = "SELECT c.*, COUNT(s.id) AS has_children ".
		   ' FROM ' . $GLOBALS['ecs']->table('entry_criteria') . " AS c".
		   " LEFT JOIN " . $GLOBALS['ecs']->table('entry_criteria') . " AS s ON s.parent_id=c.id".
		   " WHERE c.parent_id = '$parent_id' ".
		   " GROUP BY c.id ".
		   " ORDER BY c.id ASC ";
	
	$res = $GLOBALS['db']->getAll($sql);
	
    if($res){
        foreach($res as $k=>$row){
            $res[$k]['level'] = $level;
        }
		
    }
    
    return $res;
}