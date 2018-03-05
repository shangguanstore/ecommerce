<?php
defined('IN_ECTOUCH') or die('Deny Access');
use http\base\controllers\PluginWechatController;

/**
 * 砸金蛋
 *
 * @author wanglu
 *
 */
class zjd extends PluginWechatController
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
        // 编辑
        if (! empty($this->cfg['handler']) && is_array($this->cfg['config'])) {
            // url处理
            if (! empty($this->cfg['config']['plugin_url'])) {
                $this->cfg['config']['plugin_url'] = html_out($this->cfg['config']['plugin_url']);
            }
            // 奖品处理
            if (is_array($this->cfg['config']['prize_level']) && is_array($this->cfg['config']['prize_count']) && is_array($this->cfg['config']['prize_prob']) && is_array($this->cfg['config']['prize_name'])) {
                foreach ($this->cfg['config']['prize_level'] as $key => $val) {
                    $this->cfg['config']['prize'][] = array(
                        'prize_level' => $val,
                        'prize_name' => $this->cfg['config']['prize_name'][$key],
                        'prize_count' => $this->cfg['config']['prize_count'][$key],
                        'prize_prob' => $this->cfg['config']['prize_prob'][$key]
                    );
                }
            }
        }
        $this->plugin_display('install', $this->cfg);
    }

    public function give_point($fromusername, $info)
    {
    }


    /**
     * 获取数据
     */
    public function show($fromusername, $info)
    {
        $articles = array('type'=>'text', 'content'=>'未启用砸金蛋');
        // 插件配置
        $config = $this->get_config($this->plugin_name);
        // 页面信息
        if (isset($config['media']) && ! empty($config['media'])) {
            $articles = array();
            // 数据
            $articles['type'] = 'news';
            $articles['content'][0]['Title'] = $config['media']['title'];
            $articles['content'][0]['Description'] = $config['media']['content'];
            // 不是远程图片
            if (! preg_match('/(http:|https:)/is', $config['media']['file'])) {
                $articles['content'][0]['PicUrl'] =  __URL__ . '/' . $config['media']['file'];
            } else {
                $articles['content'][0]['PicUrl'] = $config['media']['file'];
            }
            $articles['content'][0]['Url'] = html_out($config['media']['link']);

        }
        return $articles;
    }


    /**
     * 页面显示
     */
    public function html_show()
    {
        // 插件配置
        $config = $this->get_config($this->plugin_name);
        if (! empty($config)) {
            $num = count($config['prize']);
            if($num > 0){
                foreach ($config['prize'] as $key => $val) {
                    // 删除最后一项未中奖
                    if ($key == ($num - 1)) {
                        unset($config['prize'][$key]);
                    }
                }
            }
        }

        $starttime = strtotime($config['starttime']);
        $endtime = strtotime($config['endtime']);
        $nowtime = gmtime();
        if($starttime > $nowtime || $endtime < $nowtime) {
            $config['prize_num'] = 0;
        }else{
            // 用户抽奖剩余的次数
            $openid = $_SESSION['openid'];
            $sql = "SELECT count(*) as num FROM {pre}wechat_prize WHERE openid = '".$openid."' AND activity_type = '".$this->plugin_name."' AND dateline > '".($nowtime - $config['point_interval'])."' ";
            $num = model()->query($sql);
            $count = isset($num[0]) ? $num[0]['num'] : 0;
            $config['prize_num'] = ($config['prize_num'] - $count) < 0 ? 0 : $config['prize_num'] - $count;
        }
        // 中奖记录 但不含用户本人
        $sql = 'SELECT u.nickname, p.prize_name, p.id FROM {pre}wechat_prize p LEFT JOIN {pre}wechat_user u ON p.openid = u.openid where p.openid != "'.$openid.'" and dateline between "' . $starttime . '" and "' . $endtime . '" and p.prize_type = 1 and p.activity_type = "'.$this->plugin_name.'" ORDER BY dateline desc limit 10';
        $list = model()->query($sql);

        // 用户个人中奖记录 显示1条在前面, 并显示链接跳转到填写中奖地址页面
        $sql_one = 'SELECT u.nickname, p.prize_name, p.id, p.winner  FROM {pre}wechat_prize p LEFT JOIN {pre}wechat_user u ON p.openid = u.openid WHERE p.openid = "'.$openid.'"  and dateline between "' . $starttime . '" and "' . $endtime . '" and p.prize_type = 1 and p.activity_type = "'.$this->plugin_name.'" ORDER BY dateline desc limit 1';
        $list_oneself = model()->query($sql_one);
        if(!empty($list_oneself) && empty($list_oneself[0]['winner'])){
            $list_oneself[0]['winner_url'] = U('wechat/index/plugin_action', array('name' => $this->plugin_name,'id' => $list_oneself[0]['id']));
        }

        $file = ADDONS_PATH . 'wechat/' . $this->plugin_name . '/view/index.html';
        if (file_exists($file)) {
            require_once ($file);
        }
    }

    /**
     * 行为操作
     */
    public function action()
    {
        // 信息提交
        if (IS_POST) {
            $id = I('post.id');
            $data = I('post.data');
            if (empty($id)) {
                show_message('请选择中奖的奖品', '', '', 'error');
            }
            if (empty($data['phone'])) {
                show_message('请填写手机号', '', '', 'error');
            }
            if (empty($data['address'])) {
                show_message('请填写详细地址', '', '', 'error');
            }
            $winner['winner'] = serialize($data);

            model()->table('wechat_prize')
                ->data($winner)
                ->where(array('id'=>$id))
                ->update();
            show_message('资料提交成功，请等待发放奖品', '继续抽奖', U('wechat/index/plugin_show', array(
                'name' => $this->plugin_name
            )));
            exit();
        }
        // 获奖用户资料填写页面
        if (! empty($_GET['id']) && ! IS_AJAX) {
            $id = I('get.id');
            $rs = model()->table('wechat_prize')
                ->field('winner,issue_status')
                ->where(array('openid'=>$_SESSION['openid'], 'id'=>$id, 'prize_type'=>1 ,'activity_type' => $this->plugin_name))
                ->find();
            $winner_result = array();

            if (!empty($rs) && $rs['issue_status'] != 1) {
                if(!empty($rs['winner'])){
                    $winner_result = unserialize($rs['winner']);
                }else{
                    // 查询上一次中奖记录 联系地址
                    $sql = "SELECT winner FROM {pre}wechat_prize WHERE openid = '".$_SESSION['openid']."' AND activity_type = '".$this->plugin_name."' AND id < '".$id."'  ORDER by dateline DESC LIMIT 1";
                    $rs1 = model()->query($sql);
                    if(!empty($rs1)){
                        $previous_winner_result = unserialize($rs1[0]['winner']);
                    }
                }
            }else{
                show_message('本次奖品已经领取过了哦', '', '', 'error');
            }
            // 如果有上一次的中奖地址，获取上一次的，默认取本次填写的
            $winner_result = !empty($previous_winner_result) ? $previous_winner_result : $winner_result;

            $file = ADDONS_PATH . 'wechat/' . $this->plugin_name . '/view/user_info.html';
            if (file_exists($file)) {
                require_once ($file);
            }
            exit();
        }
        // 抽奖操作
        if (IS_GET && IS_AJAX) {
            $rs = array();
            // 未登录
            $openid = $_SESSION['openid'];
            if (empty($openid)) {
                $rs['status'] = 2;
                $rs['msg'] = '请先登录';
                echo json_encode($rs);
                exit();
            }

            // 插件配置
            $config = $this->get_config($this->plugin_name);
            // 活动过期
            $starttime = strtotime($config['starttime']);
            $endtime = strtotime($config['endtime']);

            $nowtime = gmtime();
            if ($nowtime < $starttime) {
                $rs['status'] = 2;
                $rs['msg'] = '活动未开始';
                echo json_encode($rs);
                exit();
            }
            if ($nowtime > $endtime) {
                $rs['status'] = 2;
                $rs['msg'] = '活动已结束';
                echo json_encode($rs);
                exit();
            }
            // 超过次数
            if (! empty($openid)) {
                $sql = "SELECT count(*) as num FROM {pre}wechat_prize WHERE openid = '".$openid."' AND activity_type = '".$this->plugin_name."' AND dateline > '".($nowtime - $config['point_interval'])."'";
                $count_num = model()->query($sql);
                $num = isset($count_num[0]) ? $count_num[0]['num'] : 0;
                if ($num <= 0) {
                    $num = 1;
                } else {
                    $num = $num + 1;
                }
            }

            if ($num > $config['prize_num']) {
                $rs['status'] = 2;
                $rs['num'] = 0;
                $rs['msg'] = '你已经用光了抽奖次数';
                echo json_encode($rs);
                exit();
            }
            // 积分扣除
            if($config['point_status']){
                $res = $this->do_takeout_point($openid, $config,$config['point_value']);
                if ($res === false){
                    $rs['status'] = 2;
                    $rs['msg'] = "积分不够了";
                    echo json_encode($rs);
                    exit();
                }
            }
            $prize = $config['prize'];
            if (! empty($prize)) {
                $arr = array();
                $prize_name = array();
                // 默认公众号信息
                $wxid = model()->table('wechat')
                    ->field('id')
                    ->where(array('default_wx'=>1))
                    ->one();
                foreach ($prize as $key => $val) {
                    // 删除数量不足的奖品
                    $count = model()->table('wechat_prize')
                        ->where(array('prize_name'=>$val['prize_name'], 'activity_type'=>$this->plugin_name, 'wechat_id'=>$wxid))
                        ->count();
                    if ($count >= $val['prize_count']) {
                        unset($prize[$key]);
                    } else {
                        $arr[$val['prize_level']] = $val['prize_prob'];
                        $prize_name[$val['prize_level']] = $val['prize_name'];
                    }
                }
                // 最后一个奖项
                $lastarr = end($prize);
                // 获取中奖项
                $level = $this->get_rand($arr);
                // 0为未中奖,1为中奖
                if ($level == $lastarr['prize_level']) {
                    $rs['status'] = 0;
                    $data['prize_type'] = 0;
                } else {
                    $rs['status'] = 1;
                    $data['prize_type'] = 1;
                }
                $rs['msg'] = $prize_name[$level];
                $rs['num'] = $config['prize_num'] - $num > 0 ? $config['prize_num'] - $num : 0;
                // 抽奖记录
                $data['wechat_id'] = $wxid;
                $data['openid'] = $openid;
                $data['prize_name'] = $prize_name[$level];
                $data['dateline'] = gmtime();
                $data['activity_type'] = $this->plugin_name;
                $id = model()->table('wechat_prize')->data($data)->insert();
                if ($level != $lastarr['prize_level'] && !empty($id)) {
                    // 获奖链接
                    $rs['link'] = U('wechat/index/plugin_action', array(
                        'name' => $this->plugin_name,
                        'id' => $id
                    ));
                    $rs['link'] = str_replace('&amp;', '&', $rs['link']);
                }
            }

            echo json_encode($rs);
            exit();
        }
    }

    /**
     * 获取插件配置信息
     *
     * @param string $code
     * @return multitype:unknown
     */
    private function get_config($code = '')
    {
        // 默认公众号信息
        $config = array();
        $wxid = model()->table('wechat')
            ->field('id')
            ->where(array('default_wx'=>1))
            ->one();
        if (! empty($wxid)) {
            $plugin_config = model()->table('wechat_extend')
                ->field('config')
                ->where(array('wechat_id'=>$wxid, 'command'=>$code, 'enable'=>1))
                ->one();
            if (! empty($plugin_config)) {
                $config = unserialize($plugin_config);
                // 素材
                if (! empty($config['media_id'])) {
                    $media = model()->table('wechat_media')
                    ->field('id, title, file, file_name, type, content, add_time, article_id, link')
                    ->where(array('id'=>$config['media_id']))
                    ->find();
                    // 单图文
                    if (empty($media['article_id'])) {
                        $media['content'] = strip_tags(html_out($media['content']));
                        $config['media'] = $media;
                    }
                }
                // url处理
                if (! empty($config['plugin_url'])) {
                    $config['plugin_url'] = html_out($config['plugin_url']);
                }
                // 奖品处理
                if (is_array($config['prize_level']) && is_array($config['prize_count']) && is_array($config['prize_prob']) && is_array($config['prize_name'])) {
                    foreach ($config['prize_level'] as $key => $val) {
                        $config['prize'][] = array(
                            'prize_level' => $val,
                            'prize_name' => $config['prize_name'][$key],
                            'prize_count' => $config['prize_count'][$key],
                            'prize_prob' => $config['prize_prob'][$key]
                        );
                    }
                }
            }
        }
        return $config;
    }
}