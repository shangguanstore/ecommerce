<?php

/**
 * ECSHOP 管理中心菜单数组
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: inc_menu.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$modules['04_order']['11_order_detection'] = 'order.php?act=order_detection';

//ecmoban模板堂 --zhuo start 批量导入
$modules['04_order']['11_add_order']      = 'mc_order.php';
//$modules['08_members']['11_users_add']    = 'mc_user.php';
//ecmoban模板堂 --end

//by li start
$modules['02_cat_and_goods']['sale_notice']             = 'sale_notice.php?act=list'; //降价通知
//$modules['02_cat_and_goods']['notice_logs']             = 'notice_logs.php?act=list'; //降价通知日志
//by li end

//退换货 start
$modules['04_order']['11_back_cause']               = 'order.php?act=back_cause_list';
$modules['04_order']['12_back_apply']               = 'order.php?act=return_list';
//退换货 end

//@author guan 晒单评价 start
$modules['02_cat_and_goods']['discuss_circle']   = 'discuss_circle.php?act=list';
//@author guan 晒单评价 end

//@author guan start
$modules['01_system']['user_keywords_list']         = 'keywords_manage.php?act=list';
//@author guan end

//ecmoban模板堂 --zhuo start
$modules['17_merchants']['01_merchants_steps_list']       = 'merchants_steps.php?act=list';         // 申请流程列表
$modules['17_merchants']['02_merchants_users_list']       = 'merchants_users_list.php?act=list';    // 入驻商家列表
$modules['17_merchants']['03_merchants_commission']       = 'merchants_commission.php?act=list';    // 商家商品佣金结算
$modules['17_merchants']['03_users_merchants_priv']       = 'merchants_privilege.php?act=allot';    // 入驻商家默认权限
$modules['17_merchants']['04_create_seller_grade']       = 'merchants_users_list.php?act=create_seller_grade';  // 入驻商家评分
$modules['17_merchants']['09_seller_domain']       = 'seller_domain.php?act=list';         // 二级域名列表  by kong
$modules['17_merchants']['10_seller_grade']       = 'seller_grade.php?act=list';         // 商家等级 by kong grade
$modules['17_merchants']['11_seller_apply']       = 'seller_apply.php?act=list';        
$modules['17_merchants']['16_seller_users_real']    	= 'user_real.php?act=list&user_type=1';
$modules['17_merchants']['12_seller_account']    	= 'merchants_account.php?act=list'; //商家账户管理

//$modules['01_system']['09_warehouse_management']       = 'warehouse.php?act=list'; // 仓库
//$modules['01_system']['09_region_area_management']       = 'region_area.php?act=list'; // 地区所属区域
$modules['19_merchants_store']['01_merchants_basic_info']       = 'index.php?act=merchants_first';         // 店铺基本信息设置
$modules['19_merchants_store']['02_merchants_ad']       = 'seller_shop_slide.php?act=list';         // 店铺轮播图设置
$modules['19_merchants_store']['03_merchants_shop_top']       = 'index.php?act=shop_top';         // 店铺头部装修
$modules['19_merchants_store']['04_merchants_basic_nav']       = 'merchants_navigator.php?act=list';         // 店铺导航栏设置
//$modules['19_merchants_store']['05_merchants_shop_bg']       = 'seller_shop_bg.php?act=first';         // 店铺背景设置
//$modules['19_merchants_store']['06_merchants_custom']       = 'merchants_custom.php?act=list';         // 店铺自定义设置
$modules['19_merchants_store']['07_merchants_window']       = 'merchants_window.php?act=list';         // 店铺橱窗设置
$modules['19_merchants_store']['08_merchants_template']       = 'merchants_template.php?act=list';         // 店铺模板选择
//$modules['19_merchants_store']['09_merchants_upgrade']       = 'merchants_upgrade.php?act=list';         // 店铺升级  by kong grade

//$modules['18_batch_manage']['warehouse_batch']       = 'goods_warehouse_batch.php?act=add'; // 仓库库存批量上传
//$modules['18_batch_manage']['area_batch']       = 'goods_area_batch.php?act=add'; // 商品地区价格批量上传
//$modules['18_batch_manage']['area_attr_batch']       = 'goods_area_attr_batch.php?act=add'; // 商品地区属性价格批量上传
//$modules['02_cat_and_goods']['07_merchants_brand'] = 'merchants_brand.php?act=list';

//$modules['02_cat_and_goods']['03_store_category_list']    = 'category_store.php?act=list'; //店铺分类
$modules['08_members']['12_user_address_list']    = 'user_address_log.php?act=list'; //店铺分类

$modules['20_ectouch']['01_oauth_admin'] = '../mobile/index.php?r=oauth/admin'; // 授权登录
$modules['20_ectouch']['02_touch_nav_admin'] = 'touch_navigator.php?act=list'; // 导航管理
$modules['20_ectouch']['03_touch_ads'] = 'touch_ads.php?act=list';
$modules['20_ectouch']['04_touch_ad_position'] = 'touch_ad_position.php?act=list';

$modules['21_cloud']['01_cloud_services']        = 'index.php?act=cloud_services';
//ecmoban模板堂 --zhuo end

$modules['02_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表
//$modules['02_cat_and_goods']['02_goods_add']        = 'goods.php?act=add';          // 添加商品
$modules['02_cat_and_goods']['03_category_list']    = 'category.php?act=list';
$modules['02_cat_and_goods']['05_comment_manage']   = 'comment_manage.php?act=list';
$modules['02_cat_and_goods']['06_goods_brand_list'] = 'brand.php?act=list';
$modules['02_cat_and_goods']['08_goods_type']       = 'goods_type.php?act=manage';
//$modules['02_cat_and_goods']['11_goods_trash']      = 'goods.php?act=trash';        // 商品回收站
//$modules['02_cat_and_goods']['12_batch_pic']        = 'picture_batch.php';
//$modules['02_cat_and_goods']['13_batch_add']        = 'goods_batch.php?act=add';    // 商品批量上传
//$modules['02_cat_and_goods']['14_goods_export']     = 'goods_export.php?act=goods_export';
$modules['02_cat_and_goods']['15_batch_edit']       = 'goods_batch.php?act=select'; // 商品批量修改
//$modules['02_cat_and_goods']['16_goods_script']     = 'gen_goods_script.php?act=setup';
//$modules['02_cat_and_goods']['17_tag_manage']       = 'tag_manage.php?act=list';
//$modules['02_cat_and_goods']['50_virtual_card_list']   = 'goods.php?act=list&extension_code=virtual_card';
//$modules['02_cat_and_goods']['51_virtual_card_add']    = 'goods.php?act=add&extension_code=virtual_card';
//$modules['02_cat_and_goods']['52_virtual_card_change'] = 'virtual_card.php?act=change';
//$modules['02_cat_and_goods']['goods_auto']             = 'goods_auto.php?act=list';
$modules['02_cat_and_goods']['comment_seller_rank']   = 'comment_seller.php?act=list';
//$modules['01_system']['website']  = 'website.php?act=list';//ecmoban

//库存管理
$modules['02_goods_storage']['01_goods_storage_put']   = 'goods_inventory_logs.php?act=list&step=put';
$modules['02_goods_storage']['02_goods_storage_out']   = 'goods_inventory_logs.php?act=list&step=out';

$modules['03_promotion']['02_snatch_list']          = 'snatch.php?act=list';
$modules['03_promotion']['04_bonustype_list']       = 'bonus.php?act=list';
//$modules['03_promotion']['06_pack_list']            = 'pack.php?act=list';
//$modules['03_promotion']['07_card_list']            = 'card.php?act=list';
$modules['03_promotion']['08_group_buy']            = 'group_buy.php?act=list';
$modules['03_promotion']['09_topic']                = 'topic.php?act=list';
$modules['03_promotion']['10_auction']              = 'auction.php?act=list';
$modules['03_promotion']['12_favourable']           = 'favourable.php?act=list';
$modules['03_promotion']['13_wholesale']            = 'wholesale.php?act=list';
$modules['03_promotion']['14_package_list']         = 'package.php?act=list';
//$modules['03_promotion']['ebao_commend']            = 'ebao_commend.php?act=list';
$modules['03_promotion']['15_exchange_goods']       = 'exchange_goods.php?act=list';
$modules['03_promotion']['17_coupons']       = 'coupons.php?act=list';


//ecmoban模板堂 --zhuo start
$modules['03_promotion']['gift_gard_list']       = 'gift_gard.php?act=list';
//$modules['03_promotion']['take_list']       = 'gift_gard.php?act=take_list';已合并到礼品卡列表内
//ecmoban模板堂 --zhuo end

$modules['03_promotion']['16_presale']     = 'presale.php?act=list';


$modules['04_order']['02_order_list']               = 'order.php?act=list';
//$modules['04_order']['03_order_query']              = 'order.php?act=order_query';
//$modules['04_order']['04_merge_order']              = 'order.php?act=merge'; 已合并到订单列表
$modules['04_order']['05_edit_order_print']         = 'order.php?act=templates';
$modules['04_order']['06_undispose_booking']        = 'goods_booking.php?act=list_all';
//$modules['04_order']['07_repay_application']        = 'repay.php?act=list_all';
$modules['04_order']['08_add_order']                = 'order.php?act=add';
$modules['04_order']['09_delivery_order']           = 'order.php?act=delivery_list';
$modules['04_order']['10_back_order']               = 'order.php?act=back_list';

$modules['05_banner']['ad_position']                = 'ad_position.php?act=list';
$modules['05_banner']['ad_list']                    = 'ads.php?act=list';

//$modules['06_stats']['flow_stats']                  = 'flow_stats.php?act=view';
//$modules['06_stats']['searchengine_stats']          = 'searchengine_stats.php?act=view';
//$modules['06_stats']['z_clicks_stats']              = 'adsense.php?act=list';
$modules['06_stats']['report_guest']                = 'guest_stats.php?act=list';
$modules['06_stats']['report_order']                = 'order_stats.php?act=list';
//$modules['06_stats']['report_sell']                 = 'sale_general.php?act=list';
$modules['06_stats']['sale_list']                   = 'sale_list.php?act=list';
//$modules['06_stats']['sell_stats']                  = 'sale_order.php?act=goods_num';
$modules['06_stats']['report_users']                = 'users_order.php?act=order_num';
$modules['06_stats']['visit_buy_per']               = 'visit_sold.php?act=list';

$modules['07_content']['03_article_list']           = 'article.php?act=list';
$modules['07_content']['02_articlecat_list']        = 'articlecat.php?act=list';
$modules['07_content']['vote_list']                 = 'vote.php?act=list';
$modules['07_content']['article_auto']              = 'article_auto.php?act=list';
//$modules['07_content']['shop_help']                 = 'shophelp.php?act=list_cat';
//$modules['07_content']['shop_info']                 = 'shopinfo.php?act=list';


$modules['08_members']['03_users_list']             = 'users.php?act=list';
//$modules['08_members']['04_users_add']              = 'users.php?act=add';
$modules['08_members']['05_user_rank_list']         = 'user_rank.php?act=list';
$modules['08_members']['06_list_integrate']         = 'integrate.php?act=list';
$modules['08_members']['08_unreply_msg']            = 'user_msg.php?act=list_all';
$modules['08_members']['09_user_account']           = 'user_account.php?act=list';
$modules['08_members']['10_user_account_manage']    = 'user_account_manage.php?act=list';
$modules['08_members']['13_user_baitiao_info']    	= 'user_baitiao_log.php?act=list'; //@author bylu 会员白条;
$modules['08_members']['16_users_real']    	= 'user_real.php?act=list'; // 实名认证


$modules['10_priv_admin']['admin_logs']             = 'admin_logs.php?act=list';
$modules['10_priv_admin']['01_admin_list']             = 'privilege.php?act=list';
$modules['10_priv_admin']['02_admin_seller']           = 'privilege_seller.php?act=list';//by kong
$modules['10_priv_admin']['admin_role']             = 'role.php?act=list';
$modules['10_priv_admin']['agency_list']            = 'agency.php?act=list';
$modules['10_priv_admin']['suppliers_list']         = 'suppliers.php?act=list'; // 供货商

$modules['01_system']['01_shop_config']             = 'shop_config.php?act=list_edit';
$modules['01_system']['02_payment_list']            = 'payment.php?act=list';
$modules['01_system']['03_area_shipping']           = 'shipping.php?act=list';
//$modules['01_system']['shipping_date_list']         = 'shipping.php?act=date_list'; //自营指定配送时间
$modules['01_system']['04_mail_settings']           = 'shop_config.php?act=mail_settings';
//$modules['01_system']['05_area_list']               = 'area_manage.php?act=list';
//$modules['01_system']['06_plugins']                 = 'plugins.php?act=list';
$modules['01_system']['07_cron_schcron']            = 'cron.php?act=list';
$modules['01_system']['08_friendlink_list']         = 'friend_link.php?act=list';
$modules['01_system']['09_partnerlink_list'] 		= 'friend_partner.php?act=list';
$modules['01_system']['sitemap']                    = 'sitemap.php';
$modules['01_system']['check_file_priv']            = 'check_file_priv.php?act=check';
$modules['01_system']['captcha_manage']             = 'captcha_manage.php?act=main';
$modules['01_system']['ucenter_setup']              = 'integrate.php?act=setup&code=ucenter';
$modules['01_system']['navigator']                  = 'navigator.php?act=list';
//$modules['01_system']['fckfile_manage']             = 'fckfile_manage.php?act=list';
$modules['01_system']['021_reg_fields']             = 'reg_fields.php?act=list';
$modules['01_system']['api']             = 'oss_configure.php?act=list';
//$modules['01_system']['alidayu_configure']             = 'alidayu_configure.php?act=list';//阿里大鱼短信配置 liu


$modules['12_template']['02_template_select']       = 'template.php?act=list';
$modules['12_template']['03_template_setup']        = 'template.php?act=setup';
$modules['12_template']['04_template_library']      = 'template.php?act=library';
$modules['12_template']['05_edit_languages']        = 'edit_languages.php?act=list';
$modules['12_template']['06_template_backup']       = 'template.php?act=backup_setting';
$modules['12_template']['mail_template_manage']     = 'mail_template.php?act=list';


$modules['13_backup']['02_db_manage']               = 'database.php?act=backup';
$modules['13_backup']['03_db_optimize']             = 'database.php?act=optimize';
$modules['13_backup']['04_sql_query']               = 'sql.php?act=main';
//$modules['13_backup']['05_synchronous']             = 'integrate.php?act=sync';
$modules['13_backup']['convert']                    = 'convert.php?act=main';
$modules['13_backup']['05_table_prefix']            = 'table_prefix.php?act=edit';
$modules['13_backup']['06_transfer_config'] 		= 'transfer_manage.php';
$modules['13_backup']['07_transfer_choose'] 		= 'transfer_manage.php?act=choose';


//$modules['14_sms']['02_sms_my_info']                = 'sms.php?act=display_my_info';
//$modules['14_sms']['03_sms_send']                   = 'sms.php?act=display_send_ui';
//$modules['14_sms']['04_sms_charge']                 = 'sms.php?act=display_charge_ui';
//$modules['14_sms']['05_sms_send_history']           = 'sms.php?act=display_send_history_ui';
//$modules['14_sms']['06_sms_charge_history']         = 'sms.php?act=display_charge_history_ui';

$modules['15_rec']['affiliate']                     = 'affiliate.php?act=list';
$modules['15_rec']['affiliate_ck']                  = 'affiliate_ck.php?act=list';

$modules['16_email_manage']['email_list']           = 'email_list.php?act=list';
$modules['16_email_manage']['magazine_list']        = 'magazine_list.php?act=list';
$modules['16_email_manage']['attention_list']       = 'attention_list.php?act=list';
$modules['16_email_manage']['view_sendlist']        = 'view_sendlist.php?act=list';

//众筹管理 by liu
$modules['09_crowdfunding']['01_crowdfunding_list']       = 'zc_project.php?act=list';         // 众筹商品列表
$modules['09_crowdfunding']['02_crowdfunding_cat']        = 'zc_category.php?act=list';          // 众筹分类
$modules['09_crowdfunding']['03_project_initiator']       = 'zc_initiator.php?act=list';          // 项目发起人
$modules['09_crowdfunding']['04_topic_list']       		  = 'zc_topic.php?act=list';          // 众筹话题

// 左侧菜单分类
$menu_top['menuplatform'] = '05_banner,06_stats,07_content,08_members,10_priv_admin,01_system,12_template,16_email_manage,13_backup';//平台
$menu_top['menushopping'] = '02_cat_and_goods,03_promotion,04_order,09_crowdfunding,15_rec,17_merchants,18_batch_manage,19_merchants_store,02_goods_storage,10_offline_store';//商城
$menu_top['ectouch'] = '20_ectouch,22_wechat,23_drp';//手机
$menu_top['menuinformation'] = '21_cloud';//资讯

// 手机端菜单
$modules['20_ectouch']['01_oauth_admin'] = '../mobile/index.php?r=oauth/admin'; // 授权登录
$modules['20_ectouch']['02_touch_nav_admin'] = 'touch_navigator.php?act=list'; 
$modules['20_ectouch']['03_touch_ads'] = 'touch_ads.php?act=list';
$modules['20_ectouch']['04_touch_ad_position'] = 'touch_ad_position.php?act=list';

if (file_exists(MOBILE_WECHAT)) {
    //微信通菜单
    $modules['22_wechat']['01_wechat_admin'] = '../mobile/index.php?r=wechat/admin/modify';
    $modules['22_wechat']['02_mass_message'] = '../mobile/index.php?r=wechat/admin/mass_message';
    $modules['22_wechat']['03_auto_reply'] = '../mobile/index.php?r=wechat/admin/reply_subscribe';
    $modules['22_wechat']['04_menu'] = '../mobile/index.php?r=wechat/admin/menu_list';
    $modules['22_wechat']['05_fans'] = '../mobile/index.php?r=wechat/admin/subscribe_list';
    $modules['22_wechat']['06_media'] = '../mobile/index.php?r=wechat/admin/article';
    $modules['22_wechat']['07_qrcode'] = '../mobile/index.php?r=wechat/admin/qrcode_list';
    $modules['22_wechat']['08_share'] = '../mobile/index.php?r=wechat/admin/share_list';
    $modules['22_wechat']['09_extend'] = '../mobile/index.php?r=wechat/extend';
    $modules['22_wechat']['11_template'] = '../mobile/index.php?r=wechat/admin/template';
}

if (file_exists(MOBILE_DRP)) {
    //微信通菜单
    $modules['23_drp']['01_drp_config'] = '../mobile/index.php?r=drp/admin/config';
    $modules['23_drp']['02_drp_shop'] = '../mobile/index.php?r=drp/admin/shop';
    $modules['23_drp']['03_drp_list'] = '../mobile/index.php?r=drp/admin/drplist';
    $modules['23_drp']['04_drp_order_list'] = '../mobile/index.php?r=drp/admin/drporderlist';
    $modules['23_drp']['05_drp_set_config'] = '../mobile/index.php?r=drp/admin/drpsetconfig';
}

/*门店管理*/
$modules['10_offline_store']['12_offline_store']       = 'offline_store.php?act=list';         // 门店管理  by kong 
$modules['10_offline_store']['2_order_stats']       = 'offline_store.php?act=order_stats';         // 门店订单统计  by kong 
?>
