<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 物流查询类
 *
 * @author wanglu
 *
 */
class wlcx extends PluginWechatController
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
        $articles = array('type'=>'text', 'content'=>'暂无物流信息');
        $uid = model()->table('wechat_user')->field('ect_uid')->where(array('openid'=>$fromusername))->one();
        if(!empty($uid)){
            //订单ID
            $order_arr = model()->query("SELECT o.order_id, o.order_sn, o.invoice_no, o.shipping_name, o.shipping_id, o.shipping_status FROM {pre}order_info o WHERE o.user_id = '".$uid."' AND (SELECT count(*) FROM {pre}order_info oi WHERE o.order_id = oi.main_order_id ) = 0 ORDER BY o.add_time DESC LIMIT 1");
            if (! empty($order_arr)) {
                //已发货
                if($order_arr[0]['shipping_status'] > 0){
                    $articles = array();
                    $articles['type'] = 'news';
                    $articles['content'][0]['Title'] = '物流信息';
                    $articles['content'][0]['Description'] = '快递公司：'. $order[0]['shipping_name'] ."\r\n". '物流单号：' . $order[0]['invoice_no'];
                    $articles['content'][0]['Url'] =  __HOST__ . U('user/order/order_tracking', array('order_id'=>$order[0]['order_id']));
                }
            }
            // 积分赠送
            $this->give_point($fromusername, $info);
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
