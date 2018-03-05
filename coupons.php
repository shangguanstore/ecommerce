<?php

/**
 * ECSHOP 优惠券
 * ============================================================================
 * * 版权所有 2005-2016 上海商创网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecmoban.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: comment.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

//ecmoban模板堂 --zhuo start
require(ROOT_PATH . '/includes/lib_area.php');  //ecmoban模板堂 --zhuo
$area_info = get_area_info($province_id);
$area_id = $area_info['region_id'];

$where = "regionId = '$province_id'";
$date = array('parent_id');
$region_id = get_table_date('region_warehouse', $where, $date, 2);
//ecmoban模板堂 --zhuo end



/*  @author-bylu 优惠券 start  */

$smarty->assign('helps',      get_shop_help());       // 网店帮助
$categories_pro = get_category_tree_leve_one();
$smarty->assign('categories_pro',  $categories_pro); // 分类树加强版
$smarty->assign('navigator_list',        get_navigator($ctype, $catlist));  //自定义导航栏
$time=gmtime();//当前时间

//领券中心-首页
if ($_REQUEST['act'] == 'coupons_index'){

    for($i=1;$i<=$_CFG['auction_ad'];$i++){
        $coupons_index   .= "'coupons_index".$i.","; //顶部广告轮播图
    }
    $smarty->assign('coupons_index',$coupons_index);
    //当前时间;
    $time=gmtime();

    //取出各条优惠券剩余总数(注册送、购物送除外)
    $sql="SELECT c.cou_id,FLOOR((c.cou_total-COUNT(cu.cou_id))/c.cou_total*100) cou_surplus FROM ".$ecs->table('coupons_user')." cu LEFT JOIN ".$ecs->table('coupons')." c ON c.cou_id=cu.cou_id  WHERE c.cou_type NOT IN(1,2) AND c.cou_end_time>$time GROUP BY c.cou_id ORDER BY c.cou_id DESC limit 6";
    $cou_surplus=$db->getAll($sql);

    //取出所有优惠券(注册送、购物送除外)
    $sql="SELECT c.*,cu.user_id,cu.is_use FROM ".$ecs->table('coupons')." c LEFT JOIN ".$ecs->table('coupons_user')." cu ON c.cou_id=cu.cou_id WHERE c.cou_type  NOT IN(1,2) AND c.cou_end_time>$time GROUP BY c.cou_id ORDER BY c.cou_id DESC limit 6";
    $cou_data=$db->getAll($sql);

    //格式化各优惠券剩余总数
    foreach($cou_data as $k=>$v){
        foreach($cou_surplus as $m=>$n){
            if($v['cou_id'] == $n['cou_id'])
                $cou_data[$k]['cou_surplus']=$n['cou_surplus'];
        }
    }

    $cou_data=fromat_coupons($cou_data);

    //秒杀券
    $seckill=$cou_data;
   foreach($seckill as $k=>$v){
       if($v['cou_goods'])
            $sort_arr[]=$v['cou_order'];
       else
            $seckill[$k]['cou_goods_name'][0]['goods_thumb']="images/coupons_default.png";//默认商品图片

   }
   array_multisort($sort_arr,SORT_DESC,$seckill);
    $seckill=array_slice($seckill,0,4);

    //任务集市(限购物券(购物满额返券))
    $sql="SELECT * FROM ".$ecs->table('coupons')." where cou_type  IN(2) AND cou_end_time>$time limit 4";
    $cou_goods=$db->getAll($sql);
    foreach($cou_goods as $k=>$v){

        //商品图片(没有指定商品时为默认图片)
        if($v['cou_ok_goods']){
            $cou_goods[$k]['cou_ok_goods_name']=$db->getAll("SELECT goods_id,goods_name,goods_thumb FROM ".$ecs->table('goods')." WHERE goods_id IN(".$v['cou_ok_goods'].")");
        }else{
            $cou_goods[$k]['cou_ok_goods_name'][0]['goods_thumb']="images/coupons_default.png";
        }

        $cou_goods[$k]['cou_end_time_format']=local_date('Y-m-d H:i:s',$v['cou_end_time']);
    }

    //好券集市(用户登入了的话,重新获取用户优惠券的使用情况)
    if($_SESSION['user_id']){
        foreach ($cou_data as $k=>$v ){
            $cou_data[$k]['is_use']=$db->getOne("SELECT is_use FROM".$ecs->table('coupons_user')."WHERE cou_id='".$v['cou_id']."' AND user_id='".$_SESSION['user_id']."' ORDER BY uc_id DESC LIMIT 1");
        }
    }

    $smarty->assign('seckill', $seckill);    // 秒杀券
    $smarty->assign('cou_goods', $cou_goods);    // 任务集市
    $smarty->assign('cou_data', $cou_data);    //   好券集市
    $smarty->assign('page_title', '首页-领券中心');    // 页面标题

    $smarty->display('coupons_index.dwt');

}

//好券集市
if ($_REQUEST['act'] == 'coupons_list'){

    $order_field=empty($_REQUEST['field'])?'c.cou_id':'c.'.$_REQUEST['field'];
    if(!empty($_REQUEST['type'])){
        if($_REQUEST['type']=='all'){
            $where=" AND cou_type = 3 ";
        }elseif($_REQUEST['type']=='member'){
            $where=" AND cou_type = 4 ";
        }else{
            $where=" ";
        }
    }else{
        $where=" ";
    }


    //当前时间;
    $time=gmtime();

    //取出各条优惠券剩余总数(注册送、购物送除外)
    $sql="SELECT c.cou_id,FLOOR((c.cou_total-COUNT(cu.cou_id))/c.cou_total*100) cou_surplus FROM ".$ecs->table('coupons_user')." cu LEFT JOIN ".$ecs->table('coupons')." c ON c.cou_id=cu.cou_id  WHERE c.cou_type NOT IN(1,2) AND c.cou_end_time>$time GROUP BY c.cou_id limit 6";
    $cou_surplus=$db->getAll($sql);


    //优惠券总数;
    $sql="SELECT COUNT(c.cou_id) FROM ".$ecs->table('coupons')." c  where c.cou_type  NOT IN(1,2) AND c.cou_end_time>$time $where ";
    $cou_row_total=$db->getOne($sql);

    $row_num=12;
    $page_total=ceil($cou_row_total/$row_num);
    $page=empty($_REQUEST['p']) || $page_total<$_REQUEST['p']?1:$_REQUEST['p'];
    $offset=($page-1)*$row_num;

    //取出所有优惠券(注册送、购物送除外)
    $sql="SELECT c.*,cu.user_id,cu.is_use FROM ".$ecs->table('coupons')." c LEFT JOIN ".$ecs->table('coupons_user')." cu ON c.cou_id=cu.cou_id WHERE c.cou_type  NOT IN(1,2) AND c.cou_end_time>$time $where  GROUP BY c.cou_id  ORDER BY $order_field DESC limit ".$offset." , ".$row_num."";
    $cou_data=$db->getAll($sql);

    //格式化各优惠券剩余总数
    foreach($cou_data as $k=>$v){
        foreach($cou_surplus as $m=>$n){
            if($v['cou_id'] == $n['cou_id'])
                $cou_data[$k]['cou_surplus']=$n['cou_surplus'];
        }
    }

    $cou_data=fromat_coupons($cou_data);

    //好券集市(用户登入了的话,重新获取用户优惠券的使用情况)
    if($_SESSION['user_id']){
        foreach ($cou_data as $k=>$v ){
            $cou_data[$k]['is_use']=$db->getOne("SELECT is_use FROM".$ecs->table('coupons_user')."WHERE cou_id='".$v['cou_id']."' AND user_id='".$_SESSION['user_id']."' ORDER BY uc_id DESC LIMIT 1");
        }
    }


    for($i=1;$i<=$page_total;$i++){
        $page_total2[]=$i;
    }
    $page_url=strstr($_SERVER['QUERY_STRING'],'&p',true)?strstr($_SERVER['QUERY_STRING'],'&p',true):$_SERVER['QUERY_STRING'];
    $smarty->assign('page_total2', $page_total2);
    $smarty->assign('page_total', $page_total);
    $smarty->assign('page', $page);
    $smarty->assign('prev_page', $page==1?1:$page-1);
    $smarty->assign('next_page', $page==$page_total?$page_total:$page+1);
    $smarty->assign('page_url',$page_url );
    $smarty->assign('cou_data', $cou_data);    //   好券集市
    $smarty->assign('page_title', '领券中心-好券集市');    // 页面标题
    $smarty->display('coupons_list.dwt');

}

//任务集市
if ($_REQUEST['act'] == 'coupons_goods'){

    //当前时间
    $time=gmtime();

    //任务集市数据总数(限购物券(购物满额后返的券))
    $sql="SELECT COUNT(*) FROM ".$ecs->table('coupons')." where cou_type IN(2) AND cou_end_time>$time";
    $cou_row_total=$db->getoNE($sql);

    $row_num=10;
    $page_total=ceil($cou_row_total/$row_num);
    $page=empty($_REQUEST['p']) || $page_total<$_REQUEST['p']?1:$_REQUEST['p'];
    $offset=($page-1)*$row_num;

    //任务集市(限购物券(购物满额后返的券))
    $sql="SELECT * FROM ".$ecs->table('coupons')." where cou_type IN(2) AND cou_end_time>$time limit ".$offset." , ".$row_num."";
    $cou_goods=$db->getAll($sql);
    foreach($cou_goods as $k=>$v){

        //商品图片(没有指定商品时为默认图片)
        if($v['cou_ok_goods']){
            $cou_goods[$k]['cou_ok_goods_name']=$db->getAll("SELECT goods_id,goods_name,goods_thumb FROM ".$ecs->table('goods')." WHERE goods_id IN(".$v['cou_ok_goods'].")");
        }else{
            $cou_goods[$k]['cou_ok_goods_name'][0]['goods_thumb']="images/coupons_default.png";
        }
        $cou_goods[$k]['cou_end_time_format']=local_date('Y-m-d H:i:s',$v['cou_end_time']);
    }


    for($i=1;$i<=$page_total;$i++){
        $page_total2[]=$i;
    }
    $page_url=strstr($_SERVER['QUERY_STRING'],'&p',true)?strstr($_SERVER['QUERY_STRING'],'&p',true):$_SERVER['QUERY_STRING'];
    $smarty->assign('page_total2', $page_total2);
    $smarty->assign('page_total', $page_total);
    $smarty->assign('page', $page);
    $smarty->assign('prev_page', $page==1?1:$page-1);
    $smarty->assign('next_page', $page==$page_total?$page_total:$page+1);
    $smarty->assign('page_url',$page_url );
    $smarty->assign('cou_goods', $cou_goods);    // 任务集市
    $smarty->assign('page_title', '领券中心-任务集市');    // 页面标题
    $smarty->display('coupons_goods.dwt');

}

//优惠券领取
if ($_REQUEST['act'] == 'coupons_receive'){

    $cou_id=$_REQUEST['cou_id'];
    $user_id=$_SESSION['user_id'];


    //取出当前优惠券信息(未过期,剩余总数大于0)
    $sql="SELECT c.*,c.cou_total-COUNT(cu.cou_id) cou_surplus FROM ".$ecs->table('coupons')." c LEFT JOIN ".$ecs->table('coupons_user')." cu ON c.cou_id=cu.cou_id GROUP BY c.cou_id  HAVING cou_surplus>0 AND  c.cou_id='".$cou_id."' AND c.cou_end_time>$time limit 1";
    $cou_data=$db->getRow($sql);


    //判断券是不是被领取完了
    if(!$cou_data)
        die(json_encode(array('status'=>'error','msg'=>'领取失败,券已经被领完了')));


    //判断是否已经领取了,并且还没有使用(根据创建优惠券时设定的每人可以领取的总张数为准,防止超额领取)
    $cou_user_num=$db->getOne("SELECT COUNT(cou_id) FROM ".$ecs->table('coupons_user')." WHERE user_id='".$user_id."' AND cou_id='".$cou_id."' AND is_use=0 ");
    if($cou_data['cou_user_num']<=$cou_user_num){
        die(json_encode(array('status'=>'error','msg'=>'领取失败,您已经领取过该券了!每人限领取'.$cou_data['cou_user_num'].'张')));
    }


    //判断当前会员等级能不能领取
    if(strpos(','.$cou_data['cou_ok_user'].',',','.$_SESSION['user_rank'].',')===false){
        $rank_name=$db->getOne("SELECT GROUP_CONCAT(rank_name) FROM ".$ecs->table('user_rank')." WHERE rank_id IN(".$cou_data['cou_ok_user'].")");
        die(json_encode(array('status'=>'error','msg'=>"领取失败,仅限会员等级为:{$rank_name}领取")));
    }


    //领券
    $uc_sn=$time.rand(10,99);
    $sql="INSERT INTO ".$ecs->table('coupons_user')." (`user_id`,`cou_id`,`uc_sn`) VALUES ($user_id,$cou_id,'$uc_sn') ";
    if($db->query($sql)){
        die(json_encode(array('status'=>'ok','msg'=>"领取成功！感谢您的参与，祝您购物愉快~")));
    }

}






/**格式化优惠券数据(注册送、购物送除外)
 * @param $cou_data
 * @return mixed
 */
function fromat_coupons($cou_data){

    //当前时间;
    $time=gmtime();

    //优化数据;
    foreach($cou_data as $k=>$v){

        //优惠券剩余量
        if(!isset($v['cou_surplus'])){
            $cou_data[$k]['cou_surplus']=100;
        }

        //可使用优惠券的商品; bylu
        if(!empty($v['cou_goods'])){
            $cou_data[$k]['cou_goods_name']=$GLOBALS['db']->getAll("SELECT goods_id,goods_name,goods_thumb FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id IN(".$v['cou_goods'].")");
//            $v['cou_goods_name']=$db->getOne("SELECT group_concat(goods_name) FROM ".$ecs->table('goods')." WHERE goods_id IN(".$v['cou_goods'].")");
        }

        //可领券的会员等级;
        if(!empty($v['cou_ok_user'])){
            $cou_data[$k]['cou_ok_user_name']=$GLOBALS['db']->getOne("SELECT group_concat(rank_name)  FROM ".$GLOBALS['ecs']->table('user_rank')." WHERE rank_id IN(".$v['cou_ok_user'].")");
        }

        //可使用的店铺;
        if($v['ru_id']){
            $store_info=get_shop_name($v['ru_id']);
            $cou_data[$k]['store_name']='限'.$store_info['shop_name'].'可用';
        }
        else
            $cou_data[$k]['store_name']='全平台可用';

        //时间戳转时间;
        $cou_data[$k]['cou_start_time_format']=local_date('Y/m/d',$v['cou_start_time']);
        $cou_data[$k]['cou_end_time_format']=local_date('Y/m/d',$v['cou_end_time']);

        //判断是否已过期;
        if($v['cou_end_time']<$time){
            $cou_data[$k]['is_overdue']=1;
        }else{
            $cou_data[$k]['is_overdue']=0;
        }

        //优惠券种类;
        $cou_data[$k]['cou_type_name']=$v['cou_type']==3?'全场券':($v['cou_type']==4?'会员券':'未知');

        //是否已经领取过了
        if($_SESSION['user_id']){
            $r = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('coupons_user')." WHERE cou_id='".$v['cou_id']."' AND user_id ='".$_SESSION['user_id']."'");
            $cou_data[$k]['cou_is_receive']=$r?1:0;
        }


    }

    return $cou_data;

}




/*  @author-bylu  end  */


?>