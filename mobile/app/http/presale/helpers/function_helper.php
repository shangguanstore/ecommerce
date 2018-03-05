<?php
// 获取预售导航信息
function get_pre_nav()
{
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." WHERE parent_cid = 0 ORDER BY sort_order ASC LIMIT 7 ";
    $res = $GLOBALS['db']->getAll($sql);
    foreach ($res as $key => $val) {
        $res[$key]['url'] = U('presale/index/list',array('id' => $val['cid']));
    }
    return $res;
}
// 获取预售时间
function get_presale_time($goods_id)
{
    $sql = "SELECT act_id, end_time FROM " . $GLOBALS['ecs']->table('presale_activity') . " WHERE goods_id = '$goods_id' LIMIT 1";
    $res = $GLOBALS['db']->getRow($sql);

    if ($res['end_time']) {
        $res['end_time'] = local_date($GLOBALS['_CFG']['time_format'], $res['end_time']);
        $res['str_time'] = substr($res['end_time'], 0, 13);
    }

    return $res;
}
/**
 * 获得预售分类商品
 *
 */
function get_pre_cat()
{
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('presale_cat')." ORDER BY sort_order ASC ";
    $cat_res = $GLOBALS['db']->getAll($sql);
    foreach ($cat_res as $key => $row)
    {
        $cat_res[$key]['goods'] = get_cat_goods($row['cid'], $row['act_id']);
        $cat_res[$key]['count_goods'] = count(get_cat_goods($row['cid']));
        $cat_res[$key]['cat_url'] = U('presale/index/list',array('id' => $row['cid']));
    }
    return $cat_res;
}

// 获取分类下商品并进行分组
function get_cat_goods($cat_id)
{
    $now = gmtime();
    $sql = "SELECT a.*, g.goods_thumb, g.goods_img, g.goods_name, g.shop_price, g.market_price, g.sales_volume FROM ".$GLOBALS['ecs']->table('presale_activity')." AS a "
        . " LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON a.goods_id = g.goods_id "
        . "WHERE a.cid = '$cat_id' AND g.is_on_sale = 0 ";

    $res = $GLOBALS['db']->getAll($sql);
    foreach ($res as $key => $row)
    {
        $res[$key]['thumb'] = get_image_path($row['goods_thumb']);
        $res[$key]['goods_img'] = get_image_path($row['goods_img']);
        $res[$key]['url'] = build_uri('presale', array('r' => 'index/detail', 'id' => $row['act_id']));
        $res[$key]['end_time_date'] = local_date('Y-m-d H:i:s', $row['end_time']);
        $res[$key]['start_time_date'] = local_date('Y-m-d H:i:s', $row['start_time']);

        if ($row['start_time'] >= $now )
        {
            $res[$key]['no_start'] = 1;
        }
    }
    return $res;
}
/**
 *
 */
function short_format_date($time = NULL) {
    $text = '';
    $t = $time - gmtime(); //时间差 （秒）
    if($t <= 0){
        return 1;
    }
    $y = date('Y', $time)-date('Y', gmtime());//是否跨年
    switch($t){
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '时'; // 一天内
            break;
        default:
            $text = floor($t/(60*60*24)) . '天'; //一年以前
            break;
    }

    return $text;
}
/**
 * 查询商品评论
 * @param $id
 * @param string $rank
 * @param int $start
 * @param int $size
 * @return bool
 */
function get_good_comment($id, $rank = NULL, $hasgoods = 0, $start = 0, $size = 10){
    if(empty($id)){
        return false;
    }
    $where = '';

    $rank = (empty($rank) && $rank !== 0) ? '' : intval($rank);

    if($rank == 4){
        //好评
        $where = ' AND  comment_rank in (4, 5)';
    }
    elseif($rank == 2){
        //中评
        $where = ' AND  comment_rank in (2, 3)';
    }
    elseif($rank === 0){
        //差评
        $where = ' AND  comment_rank in (0, 1)';
    }elseif($rank == 1){
        //差评
        $where = ' AND  comment_rank in (0, 1)';
    }else if($rank == 5){
        $where = ' AND  comment_rank in (0, 1, 2, 3, 4,5)';
    }

    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('comment'). " WHERE id_value = '".$id."' and comment_type = 0 and status = 1 and parent_id = 0 ".$where." ORDER BY comment_id DESC LIMIT $start, $size";

    $comment = $GLOBALS['db']->getAll($sql);
    $arr = array();
    if($comment){
        $ids = '';
        foreach ($comment as $key=>$row) {
            $ids .= $ids ? ",$row[comment_id]" : $row['comment_id'];
            $arr[$row['comment_id']]['id'] = $row['comment_id'];
            $arr[$row['comment_id']]['email'] = $row['email'];
            $arr[$row['comment_id']]['username'] = $row['user_name'];
            $arr[$row['comment_id']]['content'] = str_replace('\r\n', '<br />', htmlspecialchars($row['content']));
            $arr[$row['comment_id']]['content'] = nl2br(str_replace('\n', '<br />', $arr[$row['comment_id']]['content']));
            $arr[$row['comment_id']]['rank'] = $row['comment_rank'];
            $arr[$row['comment_id']]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
            if($row['order_id'] && $hasgoods){
                $sql = "SELECT o.goods_id, o.goods_name, o.goods_attr, g.goods_img FROM ".$GLOBALS['ecs']->table('order_goods')." o LEFT JOIN ".$GLOBALS['ecs']->table('goods')." g ON o.goods_id = g.goods_id WHERE o.order_id = '".$row['order_id']."' ORDER BY rec_id DESC";
                $goods = $GLOBALS['db']->getAll($sql);
                if($goods){
                    foreach($goods as $k=>$v){
                        $goods[$k]['goods_img']  = get_image_path($v['goods_img']);
                        $goods[$k]['goods_attr'] = str_replace('\r\n', '<br />', htmlspecialchars($v['goods_attr']));
                    }
                }
                $arr[$row['comment_id']]['goods'] = $goods;
            }
            $sql = "SELECT img_thumb FROM {pre}comment_img WHERE comment_id = ".$row['comment_id'];
            $comment_thumb = $GLOBALS['db']->getCol($sql);
            if(count($comment_thumb)>0){
                foreach ($comment_thumb as $k => $v){
                    $comment_thumb[$k] = get_image_path($v);
                }
                $arr[$row['comment_id']]['thumb'] = $comment_thumb;
            }else{
                $arr[$row['comment_id']]['thumb']= 0;
            }

        }

        /* 取得已有回复的评论 */
        if ($ids) {
            $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('comment') ." WHERE parent_id IN( $ids )";
            $res = $GLOBALS['db']->query($sql);
            foreach($res as $row){
                $arr[$row['parent_id']]['re_content'] = nl2br(str_replace('\n', '<br />', htmlspecialchars($row['content'])));
                $arr[$row['parent_id']]['re_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
                $arr[$row['parent_id']]['re_email'] = $row['email'];
                $arr[$row['parent_id']]['re_username'] = $row['user_name'];
            }
        }
        $arr = array_values($arr);
    }
    return $arr;
}

/*
 * 获得指定商品属性详情
 */
function get_attr_value($goods_id,$attr_id)
{
    $sql="select * from ".$GLOBALS['ecs']->table('goods_attr')." where goods_id='$goods_id' and goods_attr_id='$attr_id'";
    $re= $GLOBALS['db']->getRow($sql);

    if(!empty($re))
    {
        return $re;
    }
    else
    {
        return false;
    }
}

/**
 * 清空购物车
 * @param   int     $type   类型：默认普通商品
 */
function clear_cart($type = CART_GENERAL_GOODS, $cart_value = '')
{
    //ecmoban模板堂 --zhuo start
    if(!empty($_SESSION['user_id'])){
        $sess_id = " user_id = '" . $_SESSION['user_id'] . "' ";
    }else{
        $sess_id = " session_id = '" . real_cart_mac_ip() . "' ";
    }

    $goodsIn = '';
    if(!empty($cart_value)){
        $goodsIn = " and rec_id in($cart_value)";
    }
    //ecmoban模板堂 --zhuo end

    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
        " WHERE " .$sess_id. " AND rec_type = '$type'" . $goodsIn;
    $GLOBALS['db']->query($sql);

    if(!empty($_SESSION['user_id'])){
        $sess_id = " user_id = '" . $_SESSION['user_id'] . "' ";
    }else{
        $sess_id = " user_id = '" . real_cart_mac_ip() . "' ";
    }

    $sql1 = "DELETE FROM " . $GLOBALS['ecs']->table('cart_user_info') . " WHERE " .$sess_id;
    $GLOBALS['db']->query($sql1);
}