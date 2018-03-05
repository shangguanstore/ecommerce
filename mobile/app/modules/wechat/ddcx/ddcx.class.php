<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 订单查询类
 *
 * @author wanglu
 *
 */
class ddcx extends PluginWechatController
{
    // 插件名称
    protected $plugin_name = '';
    // 配置
    protected $cfg = array();

    /**
     * 构造方法
     *
     * @param unknown $cfg
     */
    public function __construct($cfg = array())
    {
        $name = basename(__FILE__, '.class.php');
        $this->plugin_name = $name;
        $this->cfg = $cfg;
    }

    /**
     * 安装
     */
    public function install()
    {
        $this->plugin_display('install', $this->cfg);
    }

    /**
     * 获取数据
     */
    public function show($fromusername, $info)
    {
        $this->load_helper(array('order'));
        $articles = array('type'=>'text', 'content'=>'暂无订单信息');
        $uid = model()->table('wechat_user')->field('ect_uid')->where(array('openid'=>$fromusername))->one();
        if(!empty($uid)){
            //订单ID
            $order_id_arr = model()->query("SELECT o.order_id FROM {pre}order_info o WHERE o.user_id = '".$uid."' AND (SELECT count(*) FROM {pre}order_info oi WHERE o.order_id = oi.main_order_id ) = 0 ORDER BY o.add_time DESC");
            if (isset($order_id_arr[0]) && !empty($order_id_arr[0]['order_id'])) {
                $order_id = $order_id_arr[0]['order_id'];
                //订单信息
                $order = order_info($order_id);
                //订单商品
                $order_goods = order_goods($order_id);
                $goods = '';
                if(!empty($order_goods)){
                    foreach($order_goods as $key=>$val){
                        if($key == 0){
                            $goods .= $val['goods_name'].'('.$val['goods_attr'].')('.$val['goods_number'].')';
                        }
                    }
                }
                if (file_exists(LANG_PATH . C('shop.lang') . '/user.php')) {
                    L(require(LANG_PATH . C('shop.lang') . '/user.php'));
                }
                $os = L('os');
                $ps = L('ps');
                $ss = L('ss');
                $order['order_status'] = $os[$order['order_status']];
                $order['pay_status'] = $ps[$order['pay_status']];
                $order['shipping_status'] = $ss[$order['shipping_status']];

                $articles = array();
                $articles['type'] = 'news';
                $articles['content'][0]['Title'] = '订单号：'.$order['order_sn'];
                $articles['content'][0]['Description'] = '商品信息：'. $goods ."\r\n". '总金额：'. $order['total_fee'] ."\r\n". '支付状态：'. $order['order_status'] . $order['pay_status'] . $order['shipping_status'] ."\r\n". '快递公司：'. $order['shipping_name'] ."\r\n". '物流单号：' . $order['invoice_no'];
                $articles['content'][0]['Url'] = __HOST__ . U('user/order/detail', array('order_id'=>$order['order_id']));
                // 积分赠送
                $this->give_point($fromusername, $info);
            }
        }
        return $articles;
    }

    /**
     * 积分赠送
     *
     * @param unknown $fromusername
     * @param unknown $info
     */
    public function give_point($fromusername, $info)
    {
        if (! empty($info)) {
            // 配置信息
            $config = array();
            $config = unserialize($info['config']);
            // 开启积分赠送
            if (isset($config['point_status']) && $config['point_status'] == 1) {
                $where = 'openid = "' . $fromusername . '" and keywords = "' . $info['command'] . '" and createtime > (UNIX_TIMESTAMP(NOW())- ' . $config['point_interval'] . ')';
                $sql = 'SELECT count(*) as num FROM {pre}wechat_point WHERE '.$where.'ORDER BY createtime DESC';
                $num = model()->query($sql);
                // 当前时间减去时间间隔得到的历史时间之后赠送的次数
                if ($num[0]['num'] < $config['point_num']) {
                    $this->do_point($fromusername, $info, $config['point_value']);
                }
            }
        }
    }

    /**
     * 行为操作
     */
    public function action()
    {}
}
