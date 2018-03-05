<?php

/**
 * ECSHOP 支付接口函数库
 * ============================================================================
 * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: yehuaixiao $
 * $Id: lib_payment.php 17218 2011-01-24 04:10:41Z yehuaixiao $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 取得返回信息地址
 * @param   string  $code   支付方式代码
 */
function return_url($code) {
    $url = $GLOBALS['ecs']->url();

    $self = explode("/", substr(PHP_SELF, 1));
    $count = count($self);
    if ($count > 1) {
        $real_path = $self[$count - 2];
        if ($real_path == SELLER_PATH) {
            $str_len = -(str_len(SELLER_PATH) + 1);
            $url = substr($GLOBALS['ecs']->url(), 0, $str_len);
        }
    }

    return $url . 'respond.php?code=' . $code;
}

/**
 *  取得某支付方式信息
 *  @param  string  $code   支付方式代码
 */
function get_payment($code)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('payment').
           " WHERE pay_code = '$code' AND enabled = '1'";
    $payment = $GLOBALS['db']->getRow($sql);

    if ($payment)
    {
        $config_list = unserialize($payment['pay_config']);

        foreach ($config_list AS $config)
        {
            $payment[$config['name']] = $config['value'];
        }
    }

    return $payment;
}

/**
 *  通过订单sn取得订单ID
 *  @param  string  $order_sn   订单sn
 *  @param  blob    $voucher    是否为会员充值
 */
function get_order_id_by_sn($order_sn, $voucher = 'false')
{
    if ($voucher == 'true')
    {
        if(is_numeric($order_sn))
        {
              return $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id=" . $order_sn . ' AND order_type=1');
        }
        else
        {
            return "";
        }
    }
    else
    {
        if(is_numeric($order_sn))
        {
            $sql = 'SELECT order_id FROM ' . $GLOBALS['ecs']->table('order_info'). " WHERE order_sn = '$order_sn'";
            $order_id = $GLOBALS['db']->getOne($sql);
        }
        if (!empty($order_id))
        {
            $pay_log_id = $GLOBALS['db']->getOne("SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') . " WHERE order_id='" . $order_id . "'");
            return $pay_log_id;
        }
        else
        {
            return "";
        }
    }
}

/**
 *  通过订单ID取得订单商品名称
 *  @param  string  $order_id   订单ID
 */
function get_goods_name_by_id($order_id)
{
    $sql = 'SELECT goods_name FROM ' . $GLOBALS['ecs']->table('order_goods'). " WHERE order_id = '$order_id'";
    $goods_name = $GLOBALS['db']->getCol($sql);
    return implode(',', $goods_name);
}

/**
 * 检查支付的金额是否与订单相符
 *
 * @access  public
 * @param   string   $log_id      支付编号
 * @param   float    $money       支付接口返回的金额
 * @return  true
 */
function check_money($log_id, $money)
{
    if(is_numeric($log_id))
    {
        $sql = 'SELECT order_amount FROM ' . $GLOBALS['ecs']->table('pay_log') .
              " WHERE log_id = '$log_id'";
        $amount = $GLOBALS['db']->getOne($sql);
    }
    else
    {
        return false;
    }
    if ($money == $amount)
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 修改订单的支付状态
 *
 * @access  public
 * @param   string  $log_id     支付编号
 * @param   integer $pay_status 状态
 * @param   string  $note       备注
 * @return  void
 */
function order_paid($log_id, $pay_status = PS_PAYED, $note = '')
{
    /* 取得支付编号 */
    $log_id = intval($log_id);
    if ($log_id > 0)
    {
        /* 取得要修改的支付记录信息 */
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('pay_log') .
                " WHERE log_id = '$log_id'";
        $pay_log = $GLOBALS['db']->getRow($sql);
        if ($pay_log && $pay_log['is_paid'] == 0)
        {
            /* 修改此次支付操作的状态为已付款 */
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('pay_log') .
                    " SET is_paid = '1' WHERE log_id = '$log_id'";
            $GLOBALS['db']->query($sql);

            /* 根据记录类型做相应处理 */
            if ($pay_log['order_type'] == PAY_ORDER)
            {
                /* 取得订单信息 */
                $sql = 'SELECT main_order_id, order_id, user_id, order_sn, consignee, address, tel, mobile, shipping_id, pay_status, extension_code, extension_id, goods_amount, ' .
                        'shipping_fee, insure_fee, pay_fee, tax, pack_fee, card_fee, surplus, money_paid, integral_money, bonus, order_amount, discount '.
                        'FROM ' . $GLOBALS['ecs']->table('order_info') .
                       " WHERE order_id = '$pay_log[order_id]'";
                $order    = $GLOBALS['db']->getRow($sql);
                $main_order_id = $order['main_order_id'];
                $order_id = $order['order_id'];
                $order_sn = $order['order_sn'];
				
				/* 众筹状态的更改 by wu */
				update_zc_project($order_id);				
				
                //预售首先支付定金--无需分单
                if($order['extension_code'] == 'presale'){
                    $money_paid = $order['money_paid'] + $order['order_amount'];
                    
                    if($order['pay_status'] == 0){
                        /* 修改订单状态为已部分付款 */
                        $order_amount= $order['goods_amount'] 
                                        + $order['shipping_fee'] 
                                        + $order['insure_fee'] 
                                        + $order['pay_fee'] 
                                        + $order['tax'] 
                                        - $order['money_paid'] 
                                        - $order['order_amount'];
                        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                                    " SET order_status = '" . OS_CONFIRMED . "', " .
                                        " confirm_time = '" . gmtime() . "', " .
                                        " pay_status = '". PS_PAYED_PART ."', " .
                                        " pay_time = '".gmtime()."', " .
                                        " money_paid = '$money_paid'," .
                                        " order_amount = '$order_amount' ".
                               "WHERE order_id = '$order_id'";
                        $GLOBALS['db']->query($sql);

                        /* 记录订单操作记录 */
                        order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, PS_PAYED_PART, $note, $GLOBALS['_LANG']['buyer']);
                        //更新pay_log
                        update_pay_log($order_id);
                    }else{
                        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                                    " SET pay_status = '". PS_PAYED ."', " .
                                        " pay_time = '".gmtime()."', " .
                                        " money_paid = '$money_paid'," .
                                        " order_amount = 0 ".
                               "WHERE order_id = '$order_id'";
                        $GLOBALS['db']->query($sql);
                        
                        /* 记录订单操作记录 */
                        order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, PS_PAYED, $note, $GLOBALS['_LANG']['buyer']);
                    }
                }else{
                    /* 修改订单状态为已付款 */
                    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                                " SET order_status = '" . OS_CONFIRMED . "', " .
                                    " confirm_time = '" . gmtime() . "', " .
                                    " pay_status = '$pay_status', " .
                                    " pay_time = '".gmtime()."', " .
                                    " money_paid = money_paid + order_amount," .
                                    " order_amount = 0 ".
                           "WHERE order_id = '$order_id'";
                    $GLOBALS['db']->query($sql);

                    /* 记录订单操作记录 */
                    order_action($order_sn, OS_CONFIRMED, SS_UNSHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);
                }
                /* 修改子订单状态为已付款 by wanganlin */
                $sql = "SELECT COUNT(*) FROM ". $GLOBALS['ecs']->table('order_info') ." WHERE main_order_id = '$order_id'";
                $child_num = $GLOBALS['db']->getOne($sql);
                if($main_order_id == 0 && $child_num > 0){
                    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                    " SET order_status = '" . OS_CONFIRMED . "', " .
                    " confirm_time = '" . gmtime() . "', " .
                    " pay_status = '$pay_status', " .
                    " pay_time = '".gmtime()."', " .
                    " money_paid = order_amount," .
                    " order_amount = 0 ".
                    "WHERE main_order_id = '$order_id'";
                    $GLOBALS['db']->query($sql);
                    
                    $sql = 'SELECT order_sn ' . 'FROM ' . $GLOBALS['ecs']->table('order_info') .
                        " WHERE main_order_id = '$order_id'";
                    $order_res    = $GLOBALS['db']->getAll($sql);
                    foreach ($order_res AS $row)
                    {
                        /* 记录订单操作记录 */
                        order_action($row['order_sn'], OS_CONFIRMED, SS_UNSHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);
                    }
                }

                /* 如果需要，发短信 */
                $sql = "SELECT ru_id FROM ". $GLOBALS['ecs']->table('order_goods') ." WHERE order_id = '$order_id'";
                $ru_id = $GLOBALS['db']->getOne($sql, true);
                
                $shop_name = get_shop_name($ru_id, 1);
                
                if($ru_id == 0){
                    $sms_shop_mobile = $GLOBALS['_CFG']['sms_shop_mobile'];
                }else{
                    $sql = "SELECT mobile FROM ". $GLOBALS['ecs']->table('seller_shopinfo') ." WHERE ru_id = '$ru_id'";
                    $sms_shop_mobile  = $GLOBALS['db']->getOne($sql);
                }
                
                if ($GLOBALS['_CFG']['sms_order_payed'] == '1' && $sms_shop_mobile != '')
                {
                    $msg = sprintf($GLOBALS['_LANG']['order_payed_sms'], $order_sn, $order['consignee'], $order['mobile']);
                    if($GLOBALS['_CFG']['sms_type'] == 0)
                    {
                        include_once(ROOT_PATH.'includes/cls_sms.php');
                        $sms = new sms();
                        $sms->send($sms_shop_mobile, $msg,'', 13,1);
                    }
                    elseif($GLOBALS['_CFG']['sms_type'] == 1)
                    {
                        $order_region = get_flow_user_region($order_id);
                        //阿里大鱼短信接口参数
                        $smsParams = array(
                            'shop_name' => $shop_name,
                            'order_sn' => $order_sn,
                            'consignee' => $order['consignee'],
                            'order_region' => $order_region,
                            'address' => $order['address'],
                            'order_mobile' => $order['mobile'],
                            'mobile_phone' => $sms_shop_mobile
                        );

                        $result = sms_ali($smsParams, 'sms_order_payed'); //阿里大鱼短信变量传值，发送时机传值
                        $resp = $GLOBALS['ecs']->ali_yu($result);
                    }
                }

                /* 对虚拟商品的支持 */
                $virtual_goods = get_virtual_goods($order_id);
                if (!empty($virtual_goods))
                {
                    $msg = '';
                    if (!virtual_goods_ship($virtual_goods, $msg, $order_sn, true))
                    {
                        $GLOBALS['_LANG']['pay_success'] .= '<div style="color:red;">'.$msg.'</div>'.$GLOBALS['_LANG']['virtual_goods_ship_fail'];
                    }

                    /* 如果订单没有配送方式，自动完成发货操作 */
                    if ($order['shipping_id'] == -1)
                    {
                        /* 将订单标识为已发货状态，并记录发货记录 */
                        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                               " SET shipping_status = '" . SS_SHIPPED . "', shipping_time = '" . gmtime() . "'" .
                               " WHERE order_id = '$order_id'";
                        $GLOBALS['db']->query($sql);

                         /* 记录订单操作记录 */
                        order_action($order_sn, OS_CONFIRMED, SS_SHIPPED, $pay_status, $note, $GLOBALS['_LANG']['buyer']);
                        $integral = integral_to_give($order);
                        log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($GLOBALS['_LANG']['order_gift_integral'], $order['order_sn']));
                    }
                }

            }
            elseif ($pay_log['order_type'] == PAY_SURPLUS)
            {
                $sql = 'SELECT `id` FROM ' . $GLOBALS['ecs']->table('user_account') .  " WHERE `id` = '$pay_log[order_id]' AND `is_paid` = 1  LIMIT 1";
                $res_id=$GLOBALS['db']->getOne($sql);
                if(empty($res_id))
                {
                    /* 更新会员预付款的到款状态 */
                    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('user_account') .
                           " SET paid_time = '" .gmtime(). "', is_paid = 1" .
                           " WHERE id = '$pay_log[order_id]' LIMIT 1";
                    $GLOBALS['db']->query($sql);

                    /* 取得添加预付款的用户以及金额 */
                    $sql = "SELECT user_id, amount FROM " . $GLOBALS['ecs']->table('user_account') .
                            " WHERE id = '$pay_log[order_id]'";
                    $arr = $GLOBALS['db']->getRow($sql);

                    /* 修改会员帐户金额 */
                    $_LANG = array();
                    include_once(ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/user.php');
                    log_account_change($arr['user_id'], $arr['amount'], 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING);
                }
            }elseif($pay_log['order_type'] == PAY_APPLYGRADE){
                
                /*修改申请的支付状态 by kong grade*/
                $sql=" UPDATE ".$GLOBALS['ecs']->table('seller_apply_info')." SET is_paid = 1 ,pay_time = '".gmtime()."' ,pay_status = 1 WHERE apply_id= '".$pay_log['order_id']."'";
                $GLOBALS['db']->query($sql);
                
            }elseif ($pay_log['order_type'] == PAY_TOPUP) {

                $sql = "SELECT ru_id FROM " . $GLOBALS['ecs']->table('seller_account_log') . " WHERE log_id = '" . $pay_log['order_id'] . "' LIMIT 1";
                $account_log = $GLOBALS['db']->getRow($sql);

                /* 修改商家充值的支付状态 */
                $sql = " UPDATE " . $GLOBALS['ecs']->table('seller_account_log') . " SET is_paid = 1 WHERE log_id = '" . $pay_log['order_id'] . "'";
                $GLOBALS['db']->query($sql);

                /* 改变商家金额 */
                $sql = " UPDATE " . $GLOBALS['ecs']->table('seller_shopinfo') . " SET seller_money = seller_money + " . $pay_log['order_amount'] . " WHERE ru_id = '" . $account_log['ru_id'] . "'";
                $GLOBALS['db']->query($sql);
            }
        }
        else
        {
            /* 取得已发货的虚拟商品信息 */
            $post_virtual_goods = get_virtual_goods($pay_log['order_id'], true);

            /* 有已发货的虚拟商品 */
            if (!empty($post_virtual_goods))
            {
                $msg = '';
                /* 检查两次刷新时间有无超过12小时 */
                $sql = 'SELECT pay_time, order_sn FROM ' . $GLOBALS['ecs']->table('order_info') . " WHERE order_id = '$pay_log[order_id]'";
                $row = $GLOBALS['db']->getRow($sql);
                $intval_time = gmtime() - $row['pay_time'];
                if ($intval_time >= 0 && $intval_time < 3600 * 12)
                {
                    $virtual_card = array();
                    foreach ($post_virtual_goods as $code => $goods_list)
                    {
                        /* 只处理虚拟卡 */
                        if ($code == 'virtual_card')
                        {
                            foreach ($goods_list as $goods)
                            {
                                if ($info = virtual_card_result($row['order_sn'], $goods))
                                {
                                    $virtual_card[] = array('goods_id'=>$goods['goods_id'], 'goods_name'=>$goods['goods_name'], 'info'=>$info);
                                }
                            }

                            $GLOBALS['smarty']->assign('virtual_card',      $virtual_card);
                        }
                    }
                }
                else
                {
                    $msg = '<div>' .  $GLOBALS['_LANG']['please_view_order_detail'] . '</div>';
                }

                $GLOBALS['_LANG']['pay_success'] .= $msg;
            }

           /* 取得未发货虚拟商品 */
           $virtual_goods = get_virtual_goods($pay_log['order_id'], false);
           if (!empty($virtual_goods))
           {
               $GLOBALS['_LANG']['pay_success'] .= '<br />' . $GLOBALS['_LANG']['virtual_goods_ship_fail'];
           }
        }
    }
}



/**
 * 更新订单对应的 pay_log
 * 如果未支付，修改支付金额；否则，生成新的支付log
 * @param   int     $order_id   订单id
 */
function update_pay_log($order_id)
{
    $order_id = intval($order_id);
    if ($order_id > 0)
    {
        $sql = "SELECT order_amount FROM " . $GLOBALS['ecs']->table('order_info') .
                " WHERE order_id = '$order_id'";
        $order_amount = $GLOBALS['db']->getOne($sql);
        if (!is_null($order_amount))
        {
            $sql = "SELECT log_id FROM " . $GLOBALS['ecs']->table('pay_log') .
                    " WHERE order_id = '$order_id'" .
                    " AND order_type = '" . PAY_ORDER . "'" .
                    " AND is_paid = 0";
            $log_id = intval($GLOBALS['db']->getOne($sql));
            if ($log_id > 0)
            {
                /* 未付款，更新支付金额 */
                $sql = "UPDATE " . $GLOBALS['ecs']->table('pay_log') .
                        " SET order_amount = '$order_amount' " .
                        "WHERE log_id = '$log_id' LIMIT 1";
            }
            else
            {
                /* 已付款，生成新的pay_log */
                $sql = "INSERT INTO " . $GLOBALS['ecs']->table('pay_log') .
                        " (order_id, order_amount, order_type, is_paid)" .
                        "VALUES('$order_id', '$order_amount', '" . PAY_ORDER . "', 0)";
            }
            $GLOBALS['db']->query($sql);
        }
    }
}


?>