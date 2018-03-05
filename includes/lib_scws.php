<?php

/**
 * ECSHOP
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: lib_insert.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 中文分词处理方法
 *+---------------------------------
 * @author guan
 * @access public
 * @version 1.0
 *+---------------------------------
 * @param stirng  $string 要处理的字符串
 * @param boolers $sort=false 根据value进行倒序
 * @param Numbers $top=0 返回指定数量，默认返回全部
 *+---------------------------------
 * @return void
 */
function scws($text, $top = 5, $return_array = false, $sep = ',') {
	if (!class_exists('pscws4')){
    	include(dirname(__FILE__) . '/pscws4/pscws4.php');
	}
    $cws = new pscws4('utf-8');
    $cws -> set_charset('utf-8');
    $cws -> set_dict(ROOT_PATH . 'includes/pscws4/etc/dict.utf8.xdb');
    $cws -> set_rule(ROOT_PATH . 'includes/pscws4/etc/rules.utf8.ini');
    //$cws->set_multi(3);
    $cws -> set_ignore(true);
    //$cws->set_debug(true);
    //$cws->set_duality(true);
    $cws -> send_text($text);
    $ret = $cws -> get_tops($top, 'r,v,p');
    $result = null;
    foreach ($ret as $value) {
        if (false === $return_array) {
            $result .= $sep . $value['word'];
        } else {
            $result[] = $value['word'];
        }
    }
    return false === $return_array ? substr($result, 1) : $result;
}
?>