<?php

/**
 * ECSHOP 生成验证码
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: captcha.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');

$captcha_width = $_CFG['captcha_width'];
$captcha_height = $_CFG['captcha_height'];
$captcha_font_size = $_CFG['captcha_font_size'];
$captcha_length = $_CFG['captcha_length'];

if(isset($_REQUEST['width'])){
    $captcha_width = $_REQUEST['width'];
}
if(isset($_REQUEST['height'])){
    $captcha_height = $_REQUEST['height'];
}
if(isset($_REQUEST['font_size'])){
    $captcha_font_size = $_REQUEST['font_size'];
}
if(isset($_REQUEST['length'])){
    $captcha_length = $_REQUEST['length'];
}

$code_config =    array(
    'imageW'        =>      $captcha_width,    //验证码图片宽度  
    'imageH'        =>      $captcha_height,    //验证码图片高度  
    'fontSize'      =>      $captcha_font_size,     //验证码字体大小
    'length'        =>      $captcha_length,      //验证码位数
    'useNoise'      =>      false,  //关闭验证码杂点
);

if(isset($_REQUEST['captcha'])){
    if ($_REQUEST['captcha'] == 'is_login') { //登录
        $code_config['seKey'] = 'captcha_login';
    } elseif ($_REQUEST['captcha'] == 'is_register_email') { //注册-邮箱方式
        $code_config['seKey'] = 'register_email';
    } elseif ($_REQUEST['captcha'] == 'is_register_phone') { //注册-手机方式
        $code_config['seKey'] = 'mobile_phone';
    } elseif ($_REQUEST['captcha'] == 'is_discuss') { //网友讨论圈
        $code_config['seKey'] = 'captcha_discuss';
    } elseif ($_REQUEST['captcha'] == 'is_user_comment') { //晒单
        $code_config['seKey'] = 'user_comment';
    } elseif ($_REQUEST['captcha'] == 'is_get_password') { //忘记密码邮箱找回密码
        $code_config['seKey'] = 'get_password';
    } elseif ($_REQUEST['captcha'] == 'is_get_phone_password') { //手机找回密码
        $code_config['seKey'] = 'get_phone_password';
    }elseif ($_REQUEST['captcha'] == 'get_pwd_question') { //问题找回密码
        $code_config['seKey'] = 'psw_question';
    }elseif ($_REQUEST['captcha'] == 'is_bonus') { //红包
        $code_config['seKey'] = 'bonus';
    } elseif ($_REQUEST['captcha'] == 'admin_login') { //后台登陆
        $code_config['seKey'] = 'admin_login';
    } elseif ($_REQUEST['captcha'] == 'change_password_s') // 用户中心 修改登录密码 second
    {
        $code_config['seKey'] = 'change_password_s';
    } elseif ($_REQUEST['captcha'] == 'change_password_f') // 用户中心 修改登录密码 second
    {
        $code_config['seKey'] = 'change_password_f';
    }
}


$identify = isset($_REQUEST['identify']) ? intval($_REQUEST['identify']) : '';

$img = new Verify($code_config);
$img->entry($identify);

?>