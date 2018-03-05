<?php

/**
 * ECSHOP 自动发送短信、邮件
 * ===========================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: auto_sms.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
$cron_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/cron/auto_sms.php';
if (file_exists($cron_lang))
{
    global $_LANG;

    include_once($cron_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'auto_sms_desc';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecmoban.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'auto_sms_count', 'type' => 'select', 'value' => '10'),
    );

    return;
}

$where = " where 1 ";
$sort = " order by item_id DESC ";
$limit = !empty($cron['auto_sms_count']) ? $cron['auto_sms_count'] : 5;

$user_id = empty($_SESSION['user_id'])? 0:$_SESSION['user_id'];
$adminru = get_admin_ru_id();

if(!empty($user_id))
{
	$where.=" and user_id= ".$user_id;
}

if($user_id>0 || $adminru)
{
	//获取队列(倒序，优先处理新订单)
	$sql=" select * from ".$GLOBALS['ecs']->table('auto_sms'). $where. $sort. " LIMIT ".$limit;
	$item_list=$GLOBALS['db']->getAll($sql);
	
	if(count($item_list)>0)
	{
		//循环处理
		foreach($item_list as $key=>$val)
		{
			//获取订单信息
			$sql=" select * from ".$GLOBALS['ecs']->table('order_info')." where order_id='".$val['order_id']."' ";
			$row=$GLOBALS['db']->getRow($sql);
			
			//获取商家手机、邮箱
			if($val['ru_id'] == 0){
				$sms_shop_mobile = $_CFG['sms_shop_mobile']; //手机
				$service_email = $_CFG['service_email']; //邮箱
                                $shop_name = $GLOBALS['_CFG']['shop_name'];
			}else{
				$sql = "SELECT mobile FROM ". $GLOBALS['ecs']->table('seller_shopinfo') ." WHERE ru_id = '".$val['ru_id']."'";
				$sms_shop_mobile = $GLOBALS['db']->getOne($sql); //手机
				$sql = "SELECT seller_email FROM " .$GLOBALS['ecs']->table('seller_shopinfo'). " WHERE ru_id = '" .$val['ru_id']. "'";
				$service_email = $GLOBALS['db']->getOne($sql); //邮箱
                                
                                $seller_name = get_shop_name($val['ru_id'], 1);
                                $shop_name = $seller_name;
			}
		
			//给商家发短信
			if ($_CFG['sms_order_placed'] == '1' && $sms_shop_mobile != '' && $val['item_type']==1)
			{
                            $msg = $row['pay_status'] == PS_UNPAYED ?
					$_LANG['order_placed_sms'] : $_LANG['order_placed_sms'] . '[' . $_LANG['sms_paid'] . ']';
                            if($GLOBALS['_CFG']['sms_type'] == 0)
                            {
                                include_once(ROOT_PATH.'includes/cls_sms.php');
				$sms = new sms();
				if($sms->send($sms_shop_mobile, sprintf($msg, $row['consignee'], $row['mobile']),'', 13,1))
				{
					//发送成功则删除该条数据
					$sql=" delete from ".$GLOBALS['ecs']->table('auto_sms')." where item_id='".$val['item_id']."' ";
					$GLOBALS['db']->query($sql);	
				}		
                            }
                            elseif($GLOBALS['_CFG']['sms_type'] == 1)
                            {
                                $str_centent = array(
                                    'shop_name' => '',
                                    'user_name' => $shop_name,
                                    'order_msg' => $msg,
                                    'mobile_phone' => $sms_shop_mobile
                                );

                                //阿里大鱼短信接口
                                $result = get_order_info_lang($str_centent);
                                $resq = $GLOBALS['ecs']->ali_yu($result);
                            }	
			}
			
			//给商家发邮件
			if ((($val['ru_id']==0 && $_CFG['send_service_email'] == '1')
				|| ($val['ru_id']>0 && $_CFG['seller_email'] == '1'))
				&& $service_email != '' && $val['item_type']==2)
			{
				//获取订单商品信息
				$sql=" select * from ".$GLOBALS['ecs']->table('order_goods')." where order_id='".$val['order_id']."' ";		
				$cart_goods=$GLOBALS['db']->getAll($sql);
				
				$tpl = get_mail_template('remind_of_new_order');
				$smarty->assign('order', $row);
				$smarty->assign('goods_list', $cart_goods);
				$smarty->assign('shop_name', $_CFG['shop_name']);
				$smarty->assign('send_date', local_date($GLOBALS['_CFG']['time_format'], gmtime()));
				$content = $smarty->fetch('str:' . $tpl['template_content']);				
				if(send_mail($_CFG['shop_name'], $service_email, $tpl['template_subject'], $content, $tpl['is_html']))
				{
					//发送成功则删除该条数据
					$sql=" delete from ".$GLOBALS['ecs']->table('auto_sms')." where item_id='".$val['item_id']."' ";
					$GLOBALS['db']->query($sql);	
				}				
			}	
		}
	}
}


?>