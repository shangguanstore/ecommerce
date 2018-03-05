<?php
defined('IN_ECTOUCH') or die('Deny Access');

$global_config = dirname(ROOT_PATH) . '/data/config.php';
if (file_exists($global_config)) {
    require $global_config;
    $db_hosts = explode(':', $db_host);
    $db_host = $db_hosts[0];
    $db_port = isset($db_hosts[1]) ? $db_hosts[1] : '3306';
} else {
    die('Unable to connect to the database.');
}

return array(
    'DB_TYPE' => 'mysql',
    'DB_HOST' => $db_host,
    'DB_USER' => $db_user,
    'DB_PWD' => $db_pass,
    'DB_NAME' => $db_name,
    'DB_PREFIX' => $prefix,
    'DB_PORT' => $db_port,
    'DB_CHARSET' => 'utf8',
);
