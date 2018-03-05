<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 积分查询
 *
 * @author wanglu
 *
 */
class jfcx extends PluginWechatController
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
        // $this->load_helper(array('common'));
        $articles = array('type'=>'text', 'content'=>'暂无积分信息');
        $uid = model()->table('wechat_user')->field('ect_uid')->where(array('openid'=>$fromusername))->one();
        if(!empty($uid)){
            $data = model()->table('users')->field('rank_points, pay_points, user_money')->where(array('user_id'=>$uid))->find();
            if (! empty($data)) {
                $data['user_money'] = strip_tags(price_format($data['user_money'], false));
                $articles['content'] = '余额：' . $data['user_money'] . "\r\n".'等级积分：'.$data['rank_points']."\r\n".'消费积分：'.$data['pay_points'];
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
