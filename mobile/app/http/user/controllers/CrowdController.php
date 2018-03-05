<?php

namespace http\user\controllers;

use http\base\controllers\FrontendController;

class CrowdController extends FrontendController {
     /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->user_id = $_SESSION['user_id'];
        $this->actionchecklogin();
        L(require(LANG_PATH  . C('shop.lang') . '/user.php'));
        L(require(LANG_PATH  . C('shop.lang') . '/flow.php'));
        $files = array(
            'clips',
            'transaction',
            'main'
        );
        $this->load_helper($files); 
    }

     /**
     * 我的微筹
     */
    public function actionIndex() {	
		// 用户信息
        $this->assign('info', get_user_default($this->user_id));
		$this->assign('rank', get_rank_info()); // 用户等级
        if ($rank = get_rank_info()) {
            $this->assign('rank', $rank); // 用户等级
            if (empty($rank)) {
                $this->assign('next_rank_name', sprintf(L('next_level'), $rank['next_rank'], $rank['next_rank_name']));
            }
        }
		
		$best_list = zc_best_list();//推荐项目
		$this->assign('best_list', $best_list);

        $this->display('raise_user');
    }
	
	
	 /**
     * 众筹订单
     */
    public function actionOrder() {
		$this->status = I('request.status') ? intval(I('request.status')) : 1 ;		
		if(IS_AJAX){
			//$this->status = I('request.status') ? intval(I('request.status')) : 1 ;
			$size = 10;
			$page = I('page', 1, 'intval');
			$order_list = zc_get_user_orders($this->user_id, $size, $page,$this->status);

			exit(json_encode(array('list'=>$order_list['list'], 'totalPage'=>$order_list['totalpage'])));
		}

		$this->assign('status', $this->status);
        $this->display('raise_order');
    }
	
	
	 /**
     * 众筹订单详情
     */
    public function actionDetail() {	
		$order_id = I('order_id', 0, 'intval');

		 /* 订单详情 */
        $order = zc_get_order_detail($order_id, $this->user_id);
        if ($order === false) {
            $this->err->show(L('back_home_lnk'), './');
            exit();
        }
		
		 /* 订单 支付 配送 状态语言项 */
        $os = L('os');
        $ps = L('ps');
        $ss = L('ss');

        if ($order['order_status'] == OS_UNCONFIRMED) {
            $order['handler'] = "<span class=\"box-flex text-right\"></span><a class=\"btn-default box-flex\" type=\"button\" href=\"" . U('user/crowd/cancel', array('order_id' => $order['order_id'])) . "\" onclick=\"if (!confirm('" . L('confirm_cancel') . "')) return false;\">" . L('cancel') . "</a>";
        } else if ($order['order_status'] == OS_SPLITED) {

            /* 对配送状态的处理 */
            if ($order['shipping_status'] == SS_SHIPPED) {
                @$order['handler'] = "<a class=\"btn-submit\" href=\"" . U('user/crowd/affirmreceived', array('order_id' => $order['order_id'])) . "\" onclick=\"if (!confirm('" . L('confirm_received') . "')) return false;\">" . L('received') . "</a>";
            } elseif ($order['shipping_status'] == SS_RECEIVED) {
                @$order['handler'] = '<span class="order-checkout-text box">' . L('ss_received') . '</span>';;
            } else {
                if ($order['pay_status'] == PS_UNPAYED) {
                    @$order['handler'] = "<span class=\"box-flex text-right\"></span><a class=\"btn-submit\" href=\"" . U('user/order/detail', array('order_id' => $order['order_id'])) . '" >' . L('pay_money') . '</a>';
                } else {
                    $order['handler'] = '<span class="order-checkout-text box">' . $ss[$order['shipping_status']] . '</span>';
                }
            }

        } else {
            if ($order['order_status'] == OS_CONFIRMED && $order['pay_status'] == PS_UNPAYED) {
                $order['handler'] = "<span class=\"box-flex text-right\"></span><a class=\"btn-default box-flex\" type=\"button\" >" . $ps[$order['pay_status']] . "</a>";
            } elseif ($order['pay_status'] == PS_PAYED_PART) {
                if($order['extension_code'] == 'presale' ){
                    $result = presale_settle_status($order['extension_id']);
                    if($result['settle_status'] == 1 ){
                        $msg = sprintf(L('presale_tip_1'), $result['start_time'], $result['end_time']);
                        @$order['handler'] = '<span class=\"box-flex text-right\">' . $msg . '</span>';
                    }
                    if($result['settle_status'] == 0){
                        $msg = sprintf(L('presale_tip_1'), $result['start_time'], $result['end_time']);
                        $order['hidden_pay_button'] = 1;
                        @$order['handler'] = '<span class=\"box-flex text-right\">' . $msg . '</span>';
                    }
                    if($result['settle_status'] == -1){
                        $order['hidden_pay_button'] = 1;
                        $msg = sprintf(L('presale_tip_2'), $result['end_time']);
                        @$order['handler'] = '<span class=\"box-flex text-right\">' . $msg . '</span>';
                    }
                }
            } else {
                $order['handler'] = $order['handler'] = '<span class="order-checkout-text box">' . $os[$order['order_status']] . '</span>';
            }
        }

        //$order['order_status'] = $os[$order[order_status]];
        //$order['pay_status'] = $ps[$order[pay_status]];
       // $order['shipping_status'] = $ss[$order['shipping_status']];

        $order['c'] = get_region_name($order['country']);
        $order['detail_address'] .= $order['c']['region_name'];

        $order['p'] = get_region_name($order['province']);
        $order['detail_address'] .= $order['p']['region_name'];

        $order['cc'] = get_region_name($order['city']);
        $order['detail_address'] .= $order['cc']['region_name'];

        $order['dd'] = get_region_name($order['district']);
        $order['detail_address'] .= $order['dd']['region_name'];
        $order['detail_address'] .= $order['address'];
		$this->assign('order', $order);
        $this->display('raise_order_detail');
    }
	
	
	
	 /* 确认收货 */
    public function actionAffirmReceived()
    {
        $user_id = $this->user_id;
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if (affirm_received($order_id, $user_id)) {
            ecs_header("Location: " . U('user/crowd/order'));
            exit();
        } else {
            show_message("还未发货或者已收货");
        }
    }
	
	/**
     * 取消订单
     */
    public function actionCancel()
    {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        if (zc_cancel_order($order_id, $this->user_id)) {
            ecs_header("Location: " . U('user/crowd/order'));
            exit();
        } else {
            $this->err->show(L('order_list_lnk'), U('user/crowd/order'));
        }
    }
	
	
	
	 /**
     * 用户关注的众筹项目列表
     */
    public function actionFocus() {	
		
		$this->type = I('request.type') ? intval(I('request.type')) : 1 ;
		$zc_focus = zc_focus_list($this->user_id,$this->type);//关注的众筹项目列表

		$this->assign('zc_focus', $zc_focus);
		$this->assign('type', $this->type);
        $this->display('raise_follow');
    }
	
	
	/**
     * 用户支持的众筹项目列表
     */
    public function actionCrowdbuy() {	
		
		$this->type = I('request.type') ? intval(I('request.type')) : 1 ;
		if(IS_AJAX){
			$size = 10;
			$page = I('page', 1, 'intval');
			$crowd_buy = crowd_buy_list($this->user_id, $size, $page, $this->type);//关注的众筹项目列表
			exit(json_encode(array('list'=>$crowd_buy['list'], 'totalPage'=>$crowd_buy['totalpage'])));
		}

		$this->assign('type', $this->type);
        $this->display('raise_support');
    }
	

	
	/**
     * 订单评论
     */
    public function actionAddComment() {	
		if (IS_POST) {
			$user_id = $_SESSION['user_id'];
            $topic_content = I('content');
            $order_id = I('order_id', 0, 'intval');
            $pid = I('goods_id', 0, 'intval');            
            $addtime = gmtime();
			if (empty($topic_content)) {
                show_message('评论内容不可为空', '返回', '', 'warning');
            } 
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('zc_topic') . "(topic_status,topic_content,pid,add_time,user_id)VALUES('1','$topic_content ', '$pid', ' $addtime', '$user_id')";
            $GLOBALS['db']->query($sql);
			
			show_message('商品评论成功', '返回上一页', U('user/crowd/order'), 'success');
			 
		}
		
		$order_id = I('order_id', 0, 'intval');
		$sql="select zp.id,zp.title,zp.title_img,zg.content,zg.price,oi.order_id from ". $this->ecs->table('zc_goods') ." as zg left join " . $this->ecs->table('zc_project') . " as zp on zg.pid=zp.id
			left join " . $this->ecs->table('order_info') . "as oi on zg.id=oi.zc_goods_id where oi.order_id='$order_id' and oi.is_zc_order=1 ";
	

		$goods_info = $this->db->getRow($sql);
		if (empty($goods_info)) {
            show_message('评论商品数据不完整', '返回', '', 'warning');
        }
		$goods_info['title_img'] = '../'.$goods_info['title_img'];
		$goods_info['price'] = price_format($goods_info['price']);
		$this->assign('order_id', $order_id);
		$this->assign('goods_info', $goods_info);
		$this->assign('page_title', '商品评论');
        $this->display('raise_user_evaluation_info');
	}
	
	

	
	/**
     * 验证是否登录
     */
    public function actionchecklogin()
    {

        if (!$this->user_id) {
            $url = urlencode(__HOST__ . $_SERVER['REQUEST_URI']);
            if (IS_POST) {
                $url = urlencode($_SERVER['HTTP_REFERER']);
            }
            ecs_header("Location: " . U('user/login/index', array('back_act' => $url)));
            exit;
        }
    }
	
	

}
