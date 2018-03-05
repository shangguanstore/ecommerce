<?php

/**
 * ECSHOP 地区列表管理语言文件
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: area_manage.php 17217 2011-01-19 06:29:08Z liubo $
*/

/* 字段信息 */
$_LANG['region_id'] = '地区编号';
$_LANG['region_name'] = '地区名称';
$_LANG['region_type'] = '地区类型';

$_LANG['05_area_list_01'] = '仓库管理';

$_LANG['area'] = '地区';
$_LANG['area_next'] = '以下';
$_LANG['country'] = '一级地区';
$_LANG['province'] = '二级地区';
$_LANG['city'] = '三级地区';
$_LANG['cantonal'] = '四级地区';
$_LANG['back_page'] = '返回上一级';
$_LANG['manage_area'] = '管理';
$_LANG['region_name_empty'] = '区域名称不能为空！';
$_LANG['add_country'] = '新增一级地区';
$_LANG['add_province'] = '新增二级地区';
$_LANG['add_city'] = '增加三级地区';
$_LANG['add_cantonal'] = '增加四级地区';

/* JS语言项 */
$_LANG['js_languages']['region_name_empty'] = '您必须输入地区的名称!';
$_LANG['js_languages']['option_name_empty'] = '必须输入调查选项名称!';
$_LANG['js_languages']['drop_confirm'] = '您确定要删除这条记录吗?';
$_LANG['js_languages']['drop'] = '删除';
$_LANG['js_languages']['country'] = '一级地区';
$_LANG['js_languages']['province'] = '二级地区';
$_LANG['js_languages']['city'] = '三级地区';
$_LANG['js_languages']['cantonal'] = '四级地区';

/* 提示信息 */
$_LANG['add_area_error'] = '添加新地区失败!';
$_LANG['region_name_exist'] = '已经有相同的地区名称存在!';
$_LANG['parent_id_exist'] = '该区域下有其它下级地区存在, 不能删除!';
$_LANG['form_notic'] = '点击查看下级地区';
$_LANG['area_drop_confirm'] = '如果订单或用户默认配送方式中使用以下地区，这些地区信息将显示为空。您确认要删除这条记录吗?';


//运费
$_LANG['fee_compute_mode'] = '费用计算方式';
$_LANG['fee_by_weight'] = '按重量计算';
$_LANG['fee_by_number'] = '按商品件数计算';
$_LANG['free_money'] = '免费额度';
$_LANG['pay_fee'] = '货到付款支付费用';

$_LANG['not_find_plugin'] = '没有找到指定的配送方式的插件。';

//大商创1.5版本新增 sunle
$_LANG['originating_place'] = '始发地';
$_LANG['reach_the_destination'] = '到达目的地';
$_LANG['logistics_distribution'] = '物流配送';
$_LANG['logistics_info'] = '物流信息';
$_LANG['select_logistics_company'] = '已选择物流公司';
$_LANG['freight'] = '运费';
$_LANG['new_add_warehouse'] = '新增仓库';
$_LANG['warehouse_new_add_region'] = '仓库新增地区';
$_LANG['freight_guanli'] = '运费管理';
$_LANG['distribution_mode'] = '配送方式';
$_LANG['not_distribution_mode'] = '未添加配送方式';
?>