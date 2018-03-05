<?php
defined('IN_ECTOUCH') or die('Deny Access');

$db_config = file_exists(ROOT_PATH . 'config/database.php') ? require ROOT_PATH . 'config/database.php' : array();

return array (
    'ENV' => ENVIRONMENT,
    'TIMEZONE' => $timezone,

    'DEFAULT_APP' => 'site',
    'DEFAULT_CONTROLLER' => 'Index',
    'ACTION_PREFIX' => 'action',

    'DEFAULT_FILTER' => 'htmlspecialchars',
    'VAR_FILTER' => '',
    'CACHE_EXPIRE' => 86400,

    //db config
    'DB' => array(
        'default' => $db_config,
    ),

    /* Cookie设置 */
    'COOKIE_EXPIRE'          => 3600, // Cookie有效期
    'COOKIE_DOMAIN'          => '', // Cookie有效域名
    'COOKIE_PATH'            => '/', // Cookie路径
    'COOKIE_PREFIX'          => '', // Cookie前缀 避免冲突
    'COOKIE_SECURE'          => false, // Cookie安全传输
    'COOKIE_HTTPONLY'        => '', // Cookie httponly设置

    'CACHE'=>array(
        'default' => array(
            'CACHE_TYPE' => 'FileCache',
            'CACHE_PATH' => CACHE_PATH . 'caches/',
            'GROUP' => 'd',
            'HASH_DEEP' => 0,
        ),
        'memcached' => array(
            'CACHE_TYPE' => 'FileCache',
            'CACHE_PATH' => CACHE_PATH . 'caches/',
            'GROUP' => 'f',
            'HASH_DEEP' => 0,
            /*'MEM_SERVER' => array(array('127.0.0.1', 11211)),
            'MEM_OPTIONS' => array(Memcached::OPT_COMPRESSION => false, Memcached::OPT_BINARY_PROTOCOL => true),
            'USERNAME' => '',
            'PASSWORD' => '',*/
        ),
        'TPL_CACHE' => array(
            'CACHE_TYPE' => 'FileCache',
            'CACHE_PATH' => CACHE_PATH . 'caches/',
            'GROUP' => 'f',
            'HASH_DEEP' => 0,
        ),
        'DB_CACHE' => array(
            'CACHE_TYPE' => 'FileCache',
            'CACHE_PATH' => CACHE_PATH . 'caches/',
            'GROUP' => 'd',
            'HASH_DEEP' => 0,
        ),
    )
);
