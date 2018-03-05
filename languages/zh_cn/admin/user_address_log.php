<?php

/**
 * ECSHOP 会员收货地址管理
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: account_log.php 17217 2011-01-19 06:29:08Z liubo $
 */
 
$_LANG['03_users_list'] = '收货地址列表'; 
 
$_LANG['remove_confirm_address'] = '您确定要删除该会员收货地址吗？'; 
 
$_LANG['please_select'] = '请选择';

$_LANG['country'] = '国家';
$_LANG['province'] = '省';
$_LANG['city'] = '市';
$_LANG['area'] = '区';

$_LANG['update_success'] = '已成功审核该收货地址信息。';
$_LANG['update_failure'] = '尚未审核该收货地址信息。';
$_LANG['button_remove'] = '删除收货地址';
$_LANG['list_remove_confirm'] = '您确定要删除所有选中的收货地址吗？';

$_LANG['batch_remove_success'] = '已经成功删除了 %d 个收货地址。';
$_LANG['no_select_user'] = '您现在没有需要删除的收货地址！';
$_LANG['remove_success'] = '收货地址已经删除成功。';
 
/* 收货人信息 */
$_LANG['flow_js']['consignee_not_null'] = '收货人姓名不能为空！';
$_LANG['flow_js']['country_not_null'] = '请您选择收货人所在国家！';
$_LANG['flow_js']['province_not_null'] = '请您选择收货人所在省份！';
$_LANG['flow_js']['city_not_null'] = '请您选择收货人所在城市！';
$_LANG['flow_js']['district_not_null'] = '请您选择收货人所在区域！';
$_LANG['flow_js']['invalid_email'] = '您输入的邮件地址不是一个合法的邮件地址。';
$_LANG['flow_js']['address_not_null'] = '收货人的详细地址不能为空！';
$_LANG['flow_js']['tele_not_null'] = '电话不能为空！';
$_LANG['flow_js']['shipping_not_null'] = '请您选择配送方式！';
$_LANG['flow_js']['payment_not_null'] = '请您选择支付方式！';
$_LANG['flow_js']['goodsattr_style'] = 1;
$_LANG['flow_js']['tele_invaild'] = '电话号码不有效的号码';
$_LANG['flow_js']['zip_not_num'] = '邮政编码只能填写数字';
$_LANG['flow_js']['mobile_invaild'] = '手机号码不是合法号码';


/*大商创1.5版本新增 sunle*/
$_LANG['consignee_name'] = '收货人姓名';
$_LANG['user_name'] = '会员名称';
$_LANG['phone'] = '手机';
$_LANG['telephone'] = '电话';
$_LANG['email'] = '电子邮件';
$_LANG['postcode'] = '邮政编码';
$_LANG['landmark_building'] = '标志建筑';
$_LANG['optimum_delivery_time'] = '最佳送货时间';
$_LANG['address_detail'] = '详细地址';
$_LANG['uers_updata_time'] = '会员更新时间';
$_LANG['audit_status'] = '审核状态';

$_LANG['users_edit'] = '编辑收货地址';
?>