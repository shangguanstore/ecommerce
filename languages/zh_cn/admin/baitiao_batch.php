<?php

/**
 * ECSHOP 商品批量上传、修改语言文件
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods_batch.php 17217 2011-01-19 06:29:08Z liubo $
 */

$_LANG['csv_file'] = '上传批量csv文件：';
$_LANG['notice_file'] = '（CSV文件中一次上传商品数量最好不要超过40，CSV文件大小最好不要超过500K.）';
$_LANG['file_charset'] = '文件编码：';
$_LANG['download_file'] = '下载批量CSV文件（%s）';
$_LANG['use_help'] = '<h4>使用说明：</h4>' .
        '<ul>' .
          '<li>根据使用习惯，下载相应语言的csv文件，例如中国内地用户下载简体中文语言的文件，港台用户下载繁体语言的文件；</li>' .
          '<li>选择所上传商品的分类以及文件编码，上传csv文件</li>' .
        '</ul>';

// 批量上传商品的字段
$_LANG['upload_baitiao']['user_name'] = '会员名称';
$_LANG['upload_baitiao']['amount'] = '金融额度';
$_LANG['upload_baitiao']['repay_term'] = '信用账期';
$_LANG['upload_baitiao']['over_repay_trem'] = '信用账期缓期期限';
$_LANG['upload_baitiao']['status'] = '状态';

$_LANG['status_succeed'] = '插入成功';
$_LANG['status_failure'] = '插入失败';
$_LANG['already_show'] = '数据已存在';

$_LANG['save_products'] = '保存白条设置成功';
$_LANG['14_batch_add'] = '白条批量设置';
?>