<?php

/**
 * ECTouch E-Commerce Project
 * ============================================================================
 * Copyright (c) 2014-2015 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * This is NOT a freeware, use is subject to license terms
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/license )
 * ----------------------------------------------------------------------------
 */

define('APP_NAME', 'respond');
$_GET['code'] = 'wxpay';
$_GET['type'] = 'notify';
$_POST['postStr'] = file_get_contents("php://input");
require __DIR__ . '/../../index.php';
