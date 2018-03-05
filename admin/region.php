<?php

/**
 * ECSHOP 地区切换程序
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: region.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);
$data = array('content'=>'','region_name'=>'');
$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
$shipping = !empty($_REQUEST['shipping']) ? intval($_REQUEST['shipping']) : 0;
$region = get_regions($type, $parent);
$value = '';
$type = $type+1;
foreach($region as $k=>$v){
    if($v['region_id'] > 0){
        if($shipping == 1){
            $value .= '<div class="region_item"><input type="checkbox" name="region_name" data-region="'.$v['region_name'].'" value="'.$v['region_id'].'" class="ui-checkbox" id="region_'.$v['region_id'].'" /><label for="region_'.$v['region_id'].'" class="ui-label">'.$v['region_name'].'</label></div>';
        }else{
            $value .= '<span class="liv" data-text="'.$v['region_name'].'" data-type="'.$type.'"  data-value="'.$v['region_id'].'">'.$v['region_name'].'</span>';
        }
    }
}
if($parent > 0){
	$sql = "SELECT region_name FROM".$ecs->table("region")." WHERE region_id = '$parent'";
	$data['region_name'] = $db->getOne($sql);
}
$json = new JSON;
$data['content']=$value;

echo $json->encode($data);

?>