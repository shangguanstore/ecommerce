<?php

/* 发送短信时机数组 */
$send_time = array(
    '客户下单时' => 'sms_order_placed',
    '客户付款时' => 'sms_order_payed',
    '商家发货时' => 'sms_order_shipped',
    '客户注册时' => 'sms_signin',
    '客户密码找回时' => 'sms_find_signin',
    '验证码通知' => 'sms_code',
    '商品降价时' => 'sms_price_notic',
    '修改商家密码时' => 'sms_seller_signin'
);
/* 默认模板数组 */
$template = array(
    'sms_order_placed' => '您有新订单，收货人：${consignee}，联系方式：${order_mobile}，请您及时查收.',
    'sms_order_payed' => '您有新订单，收货人：${consignee}，联系方式：${order_mobile}，请您及时查收.',
    'sms_order_shipped' => '尊敬的${user_name}用户，您的订单已发货，收货人${consignee}，请您及时查收.',
    'sms_signin' => '验证码${code}，您正在注册成为${product}用户，感谢您的支持！',
    'sms_find_signin' => '验证码${code}，用于密码找回，如非本人操作，请及时检查账户安全',
    'sms_code' => '您的验证码是：${code}，请不要把验证码泄露给其他人，如非本人操作，可不用理会',
    'sms_price_notic' => '尊敬的${user_name}用户，您关注的商品${goods_sn}，赶快下单吧！',
    'sms_seller_signin' => '亲爱的${seller_name}，您的新账号：${login_name}，新密码 ：${password}，如非本人操作，请及时核对。',
);
?>