<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 签到送积分
 *
 * @author wanglu
 *
 */
class sign extends PluginWechatController
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
        $articles = array('type'=>'text', 'content'=>'签到失败');
        // 配置信息
        $config = array();
        $config = unserialize($info['config']);
        if (isset($config['point_status']) && $config['point_status'] == 1) {
            $uid = model()->table('wechat_user')->field('ect_uid')->where(array('openid'=>$fromusername))->one();
            if($uid){
                // 签到次数
                $where = 'openid = "' . $fromusername . '" and keywords = "' . $info['command'] . '" and createtime > (UNIX_TIMESTAMP(NOW())- ' . $config['point_interval'] . ')';
                $sql = 'SELECT count(*) as num FROM {pre}wechat_point WHERE '.$where.'ORDER BY createtime DESC';
                $num = model()->query($sql);
                // 当前时间减去时间间隔得到的历史时间之后赠送的次数
                if ($num[0]['num'] < $config['point_num']) {
                    // 积分赠送
                    $this->give_point($fromusername, $info);
                    if(!empty($config['rank_point_value']) || !empty($config['pay_point_value'])){
                        $tips = "系统赠送您 ";
                        $tips .= !empty($config['rank_point_value']) ? $config['rank_point_value']. "等级积分 " : '';
                        $tips .= !empty($config['pay_point_value']) ? $config['pay_point_value'] . "消费积分 " : '';
                    }
                    $articles['content'] = '签到成功！' . $tips;
                } else {
                    $articles['content'] = '签到次数已用完';
                }
            }
            else{
                $articles['content'] = '尚未绑定商城会员,请先绑定';
            }
        } else {
            $articles['content'] = '未启用签到送积分';
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
                    $this->do_point($fromusername, $info, $config['rank_point_value'], $config['pay_point_value']);
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
