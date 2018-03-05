<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init_table.php');

/*------------------------------------------------------ */
//-- 商品列表，商品回收站
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'edit') {

    $smarty->assign('prefix', $prefix);
    $smarty->assign('ur_here', $_LANG['05_table_prefix']);
    assign_query_info();
    $smarty->display('table_prefix.dwt');
} else if ($_REQUEST['act'] == 'update') {
    $new_prefix = !empty($_REQUEST['new_prefix']) ? trim($_REQUEST['new_prefix']) : ''; //新的表前缀
    $smarty->assign('ur_here', $_LANG['05_table_prefix']);
    $db_name = $db->dbname;  //数据库名称

    $sql = "SELECT CONCAT( 'ALTER TABLE ', table_name, ' RENAME TO ', replace(table_name,'$prefix','$new_prefix'),';') AS prefix FROM information_schema.tables WHERE TABLE_SCHEMA = '$db_name' and table_name LIKE '$prefix%';";
    $res = $db->getAll($sql);

    $list = array();
    foreach ($res as $k => $v) {
        $list[$k]['prefix'] = $prefix;
        $list[$k]['new_prefix'] = $new_prefix;
        $list[$k]['edit_table'] = $v['prefix'];
    }

    if ($list) {
        write_static_cache('table_prefix', $list, '/data/sc_file/');
    }

    $table_list = read_static_cache('table_prefix', '/data/sc_file/');

    if ($table_list !== false) {
        $table_list = $ecs->page_array(1, 1, $table_list);
        $smarty->assign('record_count', $table_list['filter']['record_count']);
    }

    $smarty->assign('page', 1);

    assign_query_info();
    $smarty->display('table_list.dwt');
} else if ($_REQUEST['act'] == 'ajax_update') {

    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON();

    $page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $page_size = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) : 1;

    $table_list = read_static_cache('table_prefix', '/data/sc_file/');

    /* 设置最长执行时间为5分钟 */
    @set_time_limit(300);

    if ($table_list !== false) {
        $table_list = $ecs->page_array($page_size, $page, $table_list);
    }

    $result['list'] = $table_list['list'][0];

    $result['page'] = $table_list['filter']['page'] + 1;
    $result['page_size'] = $table_list['filter']['page_size'];
    $result['record_count'] = $table_list['filter']['record_count'];
    $result['page_count'] = $table_list['filter']['page_count'];

    $result['is_stop'] = 1;
    if ($page > $table_list['filter']['page_count']) {
        $result['is_stop'] = 0;
    }else{
        $db->query($table_list['list'][0]['edit_table']);
        $result['filter_page'] = $table_list['filter']['page'];
    }

    die($json->encode($result));
}

/**
 * 写结果缓存文件
 *
 * @params  string  $cache_name
 * @params  string  $caches
 *
 * @return
 */
function write_static_cache($cache_name, $caches, $cache_file_path = '')
{
    if ((DEBUG_MODE & 2) == 2)
    {
        return false;
    }
    
    if(!empty($cache_file_path)){
		$cache_file_path = ROOT_PATH . $cache_file_path . $cache_name . '.php';
	}else{
		$cache_file_path = ROOT_PATH . '/temp/static_caches/' . $cache_name . '.php';
	}

	$content = "<?php\r\n";
	$content .= "\$data = " . var_export($caches, true) . ";\r\n";
	$content .= "?>";

	file_put_contents($cache_file_path, $content, LOCK_EX);
}

/**
 * 读结果缓存文件
 *
 * @params  string  $cache_name
 *
 * @return  array   $data
 */
function read_static_cache($cache_name, $cache_file_path = '')
{
    if ((DEBUG_MODE & 2) == 2)
    {
        return false;
    }
    static $result = array();
    if (!empty($result[$cache_name]))
    {
        return $result[$cache_name];
    }
    
    if(!empty($cache_file_path)){
		$cache_file_path = ROOT_PATH . $cache_file_path . $cache_name . '.php';
	}else{
		$cache_file_path = ROOT_PATH . '/temp/static_caches/' . $cache_name . '.php';
	}

	if (file_exists($cache_file_path))
	{
		include_once($cache_file_path);
		$result[$cache_name] = $data;
		return $result[$cache_name];
	}
	else
	{
		return false;
	}
}

/**
 * 获得查询时间和次数，并赋值给smarty
 *
 * @access  public
 * @return  void
 */
function assign_query_info()
{
    if ($GLOBALS['db']->queryTime == '')
    {
        $query_time = 0;
    }
    else
    {
        if (PHP_VERSION >= '5.0.0')
        {
            $query_time = number_format(microtime(true) - $GLOBALS['db']->queryTime, 6);
        }
        else
        {
            list($now_usec, $now_sec)     = explode(' ', microtime());
            list($start_usec, $start_sec) = explode(' ', $GLOBALS['db']->queryTime);
            $query_time = number_format(($now_sec - $start_sec) + ($now_usec - $start_usec), 6);
        }
    }
    $GLOBALS['smarty']->assign('query_info', sprintf($GLOBALS['_LANG']['query_info'], $GLOBALS['db']->queryCount, $query_time));

    /* 内存占用情况 */
    if ($GLOBALS['_LANG']['memory_info'] && function_exists('memory_get_usage'))
    {
        $GLOBALS['smarty']->assign('memory_info', sprintf($GLOBALS['_LANG']['memory_info'], memory_get_usage() / 1048576));
    }

    /* 是否启用了 gzip */
    $gzip_enabled = gzip_enabled() ? $GLOBALS['_LANG']['gzip_enabled'] : $GLOBALS['_LANG']['gzip_disabled'];
    $GLOBALS['smarty']->assign('gzip_enabled', $gzip_enabled);
}

/**
 * 获得系统是否启用了 gzip
 *
 * @access  public
 *
 * @return  boolean
 */
function gzip_enabled()
{
    static $enabled_gzip = NULL;

    if ($enabled_gzip === NULL)
    {
        $enabled_gzip = ($GLOBALS['_CFG']['enable_gzip'] && function_exists('ob_gzhandler'));
    }

    return $enabled_gzip;
}
?>