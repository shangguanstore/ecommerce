<?php

namespace http\drp\controllers;

use http\base\controllers\FrontendController;

class ShopController extends FrontendController {
    private $region_id;
    private $area_id;
    //自营
    private $isself = 0;
    //促销
    private $promotion = 0;

    public function __construct()
    {
        parent::__construct();
        L(require(LANG_PATH  . C('shop.lang') . '/drp.php'));
    }
   
    /**
     * 分销店铺
     */
    public function actionIndex() {
        $province_id = isset($_COOKIE['province']) ? $_COOKIE['province'] : 0;
        $area_info = get_area_info($province_id);
        $this->area_id = $area_info['region_id'];

        $where = "regionId = '$province_id'";
        $date = array('parent_id');
        $this->region_id = get_table_date('region_warehouse', $where, $date, 2);

        if(isset($_COOKIE['region_id']) && !empty($_COOKIE['region_id'])){
            $this->region_id = $_COOKIE['region_id'];
        }
        $shop_id = intval(I('id'));  // 获取参数  
        $status=I('status', 1, 'intval');
           // 查询分销店铺
        $shop_info = $this->getShop($shop_id);   
        $size = 10;
        $page = I('page', 1, 'intval');
        $status=I('status', 1, 'intval');
        $cat_id=I('cat_id');

        if (IS_AJAX) {
            $id_list = array();
            $cat = explode(',', $cat_id);
            foreach ($cat as $id) {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $catid = implode(',', $id_list);
            $goodslist = get_goods($catid, $this->region_id, $this->area_id, $size, $page, $status);
            exit(json_encode(array('list' => $goodslist['list'], 'totalPage' => $goodslist['totalpage'])));
        }
        $this->assign('shop_info', $shop_info);
        $res = $this->checkShop($shop_id);    // 检测店铺状态
        $this->assign('status', $status);
        $this->assign('page_title', $shop_info['shop_name']);
        $this->display('index');
    }

    /**
     * 获取分销店铺信息
     */
    private function getShop($shop_id = 0) {
        $time = gmtime();
        $sql="SELECT * FROM {pre}drp_shop WHERE id=$shop_id";
        $res=$this->db->getRow($sql);
        $sql="SELECT headimgurl FROM {pre}wechat_user WHERE ect_uid='$res[user_id]'";
        $headimgurl=$this->db->getOne($sql);
        $shop_info = '';
        if ($headimgurl) {
            $shop_info['headimgurl'] =$headimgurl;
        } else {
            $sql="SELECT user_picture FROM {pre}users WHERE user_id='$res[user_id]'";
            $user_picture = $this->db->getOne($sql);
            $shop_info['headimgurl'] = get_image_path($user_picture);
        }
        $shop_info['id'] = $res['id'];
        $shop_info['shop_name'] = C('shop_name') . $res['shop_name'];
        $shop_info['real_name'] = $res['real_name'];
        $shop_info['audit'] = $res['audit'];
        $shop_info['status'] = $res['status'];
        if(empty($res['shop_img'])){
           $shop_info['shop_img'] = __TPL__ . 'img/user-shop.png';
        }else{
           $shop_info['shop_img'] = dirname(__ROOT__) . '/data/attached/drp_logo/' . $res['shop_img'];
        }
        $shop_info['user_id'] = $res['user_id'];
        $shop_info['create_time'] = date("Y-m-d", $res['create_time']);
        if ($res['user_id'] = $_SESSION['user_id']) {
            $shop_info['url'] = U('drp/user/index', array('id' => $res['user_id']));
        }
        $cat=substr($res['cat_id'],0,-1);
        $shop_info['cat_id'] =$cat;
        $id_list = array();
            $cat_id = explode(',', $cat);
            foreach ($cat_id as $id) {
                $id_list = array_merge($id_list, array_keys(cat_list($id, 0, false)));
            }
            $catid = implode(',', $id_list);
        //全部商品数量
        $sql="SELECT count(goods_id) as sum from {pre}goods WHERE is_on_sale = 1 AND is_distribution = 1 AND dis_commission >0 AND is_alone_sale = 1 AND is_delete = 0 AND cat_id " . db_create_in($catid);
        $sum['all']=$this->db->getOne($sql);
        $shop_info['sum']=$sum['all'];
        //新品商品数量
        $sql="SELECT count(goods_id) as sum FROM {pre}goods WHERE  is_new = 1 AND is_distribution = 1 AND is_on_sale = 1 AND dis_commission >0 AND is_alone_sale = 1 AND is_delete = 0 AND cat_id " . db_create_in($catid);
        $sum['new']=$this->db->getOne($sql);
        $shop_info['new']=$sum['new'];
        //促销商品数量
        $sql="SELECT count(goods_id) as sum FROM {pre}goods WHERE is_promote = 1 AND is_distribution = 1 AND dis_commission >0 AND promote_start_date <= '$time' AND promote_end_date >= '$time' AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 AND cat_id " . db_create_in($catid);
        $sum['promote']=$this->db->getOne($sql);
        $shop_info['promote']=$sum['promote'];
        return $shop_info;
    }

    /**
     * 检测店铺状态
     */
    private function checkShop($shop_id = 0) {
        $sql = "SELECT * FROM {pre}drp_shop WHERE id='$shop_id'";
        $res = $this->db->getRow($sql);
        if ($res['audit'] != 1) {
            show_message(L('admin_check'), L('in_shop'), U('site/index/index'),'fail');
        }
        if ($res['status'] != 1) {
            show_message(L('shop_close'), L('in_shop'), U('site/index/index'),'fail');
        }
        return ture;
    }

  
    
     
    
    

}
