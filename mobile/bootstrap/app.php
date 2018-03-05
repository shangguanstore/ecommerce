<?php

/**
 * ECTouch E-Commerce Project
 * ============================================================================
 * Copyright (c) 2014-2016 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * This is NOT a freeware, use is subject to license terms
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/license )
 * ----------------------------------------------------------------------------
 */

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('require PHP > 5.3.0 !');
}

// 系统信息
defined('APPNAME') or define('APPNAME', 'ECTouch');
defined('VERSION') or define('VERSION', '1.8.8');
defined('RELEASE') or define('RELEASE', '20161116');
defined('CHARSET') or define('CHARSET', 'utf-8');
header('Content-Type: text/html; charset=' . CHARSET);

// 系统目录
defined('ROOT_PATH') or define('ROOT_PATH', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/');
defined('BASE_PATH') or define('BASE_PATH', ROOT_PATH . 'app/');
defined('CONF_PATH') or define('CONF_PATH', ROOT_PATH . 'config/');
defined('DATA_PATH') or define('DATA_PATH', ROOT_PATH . 'data/');
defined('LANG_PATH') or define('LANG_PATH', ROOT_PATH . 'resources/lang/');
defined('ADDONS_PATH') or define('ADDONS_PATH', BASE_PATH . 'modules/');
defined('STORAGE_PATH') or define('STORAGE_PATH', dirname(ROOT_PATH) . '/data/attached/');
defined('CACHE_PATH') or define('CACHE_PATH', dirname(ROOT_PATH) . '/temp/');

// 请求方式
defined('NOW_TIME') or define('NOW_TIME', $_SERVER['REQUEST_TIME']);
defined('REQUEST_METHOD') or define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
defined('IS_GET') or define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
defined('IS_POST') or define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
defined('IS_AJAX') or define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST['ajax']) || !empty($_GET['ajax'])) ? true : false);
defined('IS_PJAX') or define('IS_PJAX', array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX']);

// 调试信息
defined('APP_DEBUG') or define('APP_DEBUG', (in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) || isset($_GET['debug'])) ? true : false);
defined('ENVIRONMENT') or define('ENVIRONMENT', APP_DEBUG ? 'development' : 'production');

// RESTful格式
defined('REST_EXTEND') or define('REST_EXTEND', 'JSON');
defined('PHP_SELF') or define('PHP_SELF', isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
