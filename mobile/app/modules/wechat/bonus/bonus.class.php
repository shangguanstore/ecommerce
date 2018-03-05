<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 关注送红包
 *
 * @author wanglu
 *
 */
class bonus extends PluginWechatController
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
        //红包数据，线下发放类型
        $time = time();
        $sql = "SELECT type_id, type_name, type_money FROM {pre}bonus_type WHERE send_type = 3 AND send_end_date > $time";
        $bonus = model()->query($sql);
        $this->cfg['bonus'] = $bonus;
        $this->plugin_display('install', $this->cfg);
    }

    /**
     * 获取数据
     */
    public function show($fromusername, $info)
    {
        $articles = array('type'=>'text', 'content'=>'');
        if(!empty($info)){
            // 配置信息
            $config = array();
            $config = unserialize($info['config']);
            //开启红包赠送
            if(isset($config['bonus_status']) && $config['bonus_status'] == 1){
                //用户第一次关注赠送红包并且设置了赠送的红包
                $uid = model()->table('wechat_user')->field('ect_uid')->where(array('openid'=>$fromusername))->one();
                if(!empty($uid) && !empty($config['bonus'])){
                    $sql = "SELECT count(*) as num FROM {pre}user_bonus u LEFT JOIN {pre}bonus_type b ON u.bonus_type_id = b.type_id WHERE u.user_id = $uid AND b.send_type = 3";
                    $bonus_num = model()->query($sql);
                    if($bonus_num[0]['num'] > 0){
                        $articles['content'] = '红包已经赠送过了，不要重复领取哦！';
                    }
                    else{
                      $data['bonus_type_id'] = $config['bonus'];
                      $data['bonus_sn'] = 0;
                      $data['user_id'] = $uid;
                      $data['used_time'] = 0;
                      $data['order_id'] = 0;
                      $data['emailed'] = 0;
                      model()->table('user_bonus')->data($data)->insert();

                      // $type_money = model()->table('bonus_type')->field('type_money')->where('send_type = 3 and type_id = '.$config['bonus'])->one();
                      $articles['content'] = '感谢您的关注，赠送您一个红包';
                      // 积分赠送
                      $this->give_point($fromusername, $info);
                    }
                }
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
