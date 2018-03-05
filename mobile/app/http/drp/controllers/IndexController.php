<?php

namespace http\drp\controllers;

use http\base\controllers\FrontendController;

class IndexController extends FrontendController {

    private $user_id = 0;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        L(require(LANG_PATH  . C('shop.lang') . '/drp.php'));
        $this->checkLogin();
        $files = array(
            'order',
            'clips',
            'payment',
            'transaction'
        );
        $this->load_helper($files);
    }

    /**
     * 检测是否可以开店
     */
    public function actionIndex() {
        // 是否已经成功开店
        $sql = "SELECT id,cat_id,shop_name FROM {pre}drp_shop WHERE user_id=" . $_SESSION['user_id'];
        $drp = $this->db->getRow($sql);
        $drp_id = $drp['id'];
        $shop_name = $drp['shop_name'];
        $isbuy = $drp['isbuy'];
        $sql = "SELECT value FROM {pre}drp_config WHERE code='isbuy'";
        $code = $this->db->getOne($sql);
        $sql = "SELECT cat_id FROM {pre}drp_shop WHERE user_id=" . $_SESSION['user_id'];
        $cat_id = $this->db->getOne($sql);
        if ($code == 1) {
            $sql = "SELECT value FROM {pre}drp_config WHERE code='buy_money'";
            $value = $this->db->getOne($sql);
        }
        if (empty($drp_id) && empty($code)) {
            ecs_header("Location: " . U('drp/index/register'));
        }
        if (empty($drp_id) && $code != '' && $value > 0) {
            ecs_header("Location: " . U('drp/index/purchase'));
        }
        if (empty($drp_id) && $code != '' && empty($value)) {
            ecs_header("Location: " . U('drp/index/register'));
        }
        if (!empty($drp_id) && !empty($cat_id)) {
            ecs_header("Location: " . U('drp/user/index'));
        }
        if (!empty($drp_id) && empty($cat_id) && !empty($shop_name)) {
            ecs_header("Location: " . U('drp/index/category'));
        }
        if (!empty($drp_id) && empty($cat_id) && empty($shop_name)) {
            ecs_header("Location: " . U('drp/user/shopconfig'));
        }
    }

    public function actionRegister() {
        $sql = "SELECT id FROM {pre}drp_shop WHERE user_id=" . $_SESSION['user_id'];
        $drp_id = $this->db->getRow($sql);
        if (empty($drp_id)) {
            if (IS_POST) {
                $data = I('');
                if ($data['mobile']) {
                    $preg = preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $data['mobile']) ? true : false;
                    if ($preg == FALSE) {
                        show_message(L('msg_mobile_format_error'));
                    }
                }
                if (empty($data['shop_name'])) {
                    show_message(L('msg_shop_name_notnull'));
                }
                if (empty($data['real_name'])) {
                    show_message(L('msg_name_notnull'));
                }
                if (empty($data['mobile'])) {
                    show_message(L('msg_contact_way_notnull'));
                }
                $data['user_id'] = $_SESSION['user_id'];
                $data['create_time'] = gmtime();
                if ($this->model->table('drp_shop')->data($data)->insert()) {
                    ecs_header("Location: " . U('drp/index/category'));
                } else {
                    show_message(L('add_error'));
                }
            }
        } else {
            ecs_header("Location: " . U('drp/user/index'));
        }
        $this->assign('page_title',L('open_shop_process'));
        $this->display('distribution_open_one');
    }

    /**
     * 选择分销类别
     */
    public function actionCategory() {
        $this->checkShopName();
        if (IS_POST) {
            $cateArr = I('post.cate');
            $cat_id = '';
            if (empty($cateArr)) {
                show_message(L('category_not_null'));
            }
            $data['cat_id'] = $cateArr;
            $where['user_id'] = $_SESSION['user_id'];
            $this->model->table('drp_shop')->data($data)->where($where)->update();
            redirect(U('drp/index/finish'));
        }
        $sql = "SELECT value FROM {pre}drp_config WHERE code='notice'";
        $notic = $this->db->getOne($sql);
        $notice = $this->htmlout($notic);
        $this->assign('notice', $notice);
        $category = get_child_tree();
        $this->assign('category', $category);
        $this->assign('page_title',L('open_shop_process'));
        $this->display('distribution_open_two');
    }

    /**
     * 开店完成提示
     */
    public function actionFinish() {
        $this->checkShopCategory();
        // 设置为分销商
        $sql = "SELECT value FROM {pre}drp_config WHERE code='radio'";
        $audit = $this->db->getOne($sql);
        $data['create_time'] = gmtime();
        $sql = "SELECT value FROM {pre}drp_config WHERE code='ischeck'";
        $ischeck = $this->db->getOne($sql);
        if ($ischeck == 1) {
            $data['audit'] = 0;
            $data['status'] = 1;
        }
        if ($ischeck == '') {
            $data['audit'] = 1;
            $data['status'] = 1;
        }
        $where['user_id'] = $_SESSION['user_id'];
        $this->model->table('drp_shop')->data($data)->where($where)->update();
        if(class_exists('\http\wechat\controllers\IndexController')) {
          //模板消息  by wanglu
          $shop = $this->model->table('drp_shop')->field('shop_name, mobile, create_time')->where($where)->find();
          $pushData = array(
            'keyword1' => array('value' => $shop['shop_name']),
            'keyword2' => array('value' => $shop['mobile']),
            'keyword3' => array('value' => date('Y-m-d', $shop['create_time']))
          );
          $url = __HOST__ . U('drp/index/register');
          pushTemplate('OPENTM207126233', $pushData, $url);
        }
        $this->assign('page_title',L('set_up_shop'));
        $this->display('distribution_open_end');
    }

    /**
     * 购买成为分销商
     */
    public function actionPurchase() {
        $sql = "SELECT value FROM {pre}drp_config WHERE `code`='buy_money'";
        $price = $this->db->getOne($sql);
        $this->assign('price', price_format($price));
        $sql = "SELECT value FROM {pre}drp_config WHERE `code`='novice'";
        $novice = $this->db->getOne($sql);
        $this->assign('novice', $this->htmlout($novice));
        $this->assign('page_title', L('distribution_application'));
        $this->display('purchase');
    }

    /**
     * 购买流程
     */
    public function actionPurchasePay() {
        $sql = "SELECT value FROM {pre}drp_config WHERE `code`='buy_money'";
        $price = $this->db->getOne($sql);
        $this->assign('price', price_format($price));
        $payment_list = get_online_payment_list(false);
        if (isset($payment_list)) {
            foreach ($payment_list as $key => $payment) {
                //pc端去除ecjia的支付方式
                if (substr($payment['pay_code'], 0, 4) == 'pay_') {
                    unset($payment_list[$key]);
                    continue;
                }
                if ($payment['is_cod'] == '1') {
                    $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
                }
                /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
                if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300) {
                    unset($payment_list[$key]);
                }
                /* 如果有余额支付 */
                if ($payment['pay_code'] == 'balance') {
                    /* 如果未登录，不显示 */
                    if ($_SESSION['user_id'] == 0) {
                        unset($payment_list[$key]);
                    } else {
                        if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id']) {
                            $this->assign('disable_surplus', 1);
                        }
                    }
                }
                if (!file_exists(ADDONS_PATH . 'payment/' . $payment['pay_code'] . '.php')) {
                    unset($payment_list[$key]);
                }
            }
        }
        if (IS_AJAX) {
            $pay_id = I('pay_id');
            //生成伪订单号,
            $payment = payment_info($pay_id);
            $order = array();
            $order['order_sn'] = $_SESSION['user_id'];
            $order['user_name'] = $_SESSION['user_name'];
            $payment['pay_fee'] = pay_fee($pay_id, $price, 0);
            $order['order_amount'] = $price + $payment['pay_fee']; //计算此次预付款需要支付的总金额
            $order['log_id'] = insert_pay_log($order['order_sn'], $order['order_amount'], $type = PAY_REGISTERED, 0); //记录支付log
            $order['pay_code'] = $payment['pay_code'];
            if ($order['order_amount'] > 0) {
                include_once(ADDONS_PATH . 'payment/' . $payment['pay_code'] . '.php');
                $pay_obj = new $payment['pay_code'];
                $pay_online = $pay_obj->get_code($order, unserialize_config($payment['pay_config']));
                die($pay_online);
            }
        }
        $this->assign('payment_list', $payment_list);
        $this->assign('page_title', L('distribution_application'));
        $this->display('purchase_pay');
    }

    /**
     * 检测用户登录状态
     */
    private function checkLogin() {
        // 是否登录
        $this->user_id = $_SESSION['user_id'];
        if (!$this->user_id) {
            $url = urlencode(__HOST__ . $_SERVER['REQUEST_URI']);
            if (IS_POST) {
                $url = urlencode($_SERVER['HTTP_REFERER']);
            }
            ecs_header("Location: " . U('user/login/index', array('back_act' => $url)));
            exit;
        }
    }

    /**
     * html代码输出
     */
    private function htmlOut($str) {
        if (function_exists('htmlspecialchars_decode')) {
            $str = htmlspecialchars_decode($str);
        } else {
            $str = html_entity_decode($str);
        }
        $str = stripslashes($str);
        return $str;
    }

    /**
     * 验证店铺名是否已有店铺名
     */
    private function checkShopName() {
        $sql = "SELECT shop_name FROM {pre}drp_shop WHERE user_id=" . $_SESSION['user_id'];
        $shop_name = $this->db->getOne($sql);
        if (empty($shop_name)) {
            ecs_header("Location:" . U('drp/index/register'));
        }
    }

    /**
     * 验证店铺名是否已有店铺商品分类
     */
    private function checkShopCategory() {
        $sql = "SELECT cat_id FROM {pre}drp_shop WHERE user_id=" . $_SESSION['user_id'];
        $cat_id = $this->db->getOne($sql);
        if (empty($cat_id)) {
            ecs_header("Location:" . U('drp/index/category'));
        }
    }

    /**
     * 商品详情页通知页面
     */
    public function actionShopNotice() {
        $sql = "SELECT value FROM {pre}drp_config WHERE `code`='notice'";
        $novice = $this->db->getOne($sql);
        $this->assign('novice', $this->htmlout($novice));
        $this->display('shop_notice');
    }

}
