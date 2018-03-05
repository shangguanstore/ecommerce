<?php
namespace http\base\controllers;

abstract class PluginWechatController extends BaseController
{
    protected $_data = array();

    /**
     * 数据显示返回
     */
    abstract protected function show($fromusername, $info);

    /**
     * 积分加减
     */
    abstract protected function give_point($fromusername, $info);

    /**
     * 行为处理
     */
    abstract protected function action();

    /**
     * 积分赠送处理
     */
    public function do_point($fromusername, $info, $rank_points = 0, $pay_points = 0)
    {
        $time = gmtime();
        $user_id = model()->table('wechat_user')
            ->field('ect_uid')
            ->where(array('openid'=>$fromusername))
            ->one();
        if($user_id){
            // 增加等级积分
            $sql = "UPDATE {pre}users SET rank_points = rank_points + ".intval($rank_points)." WHERE user_id = $user_id";
            model()->query($sql);
            // 增加消费积分
            $sql = "UPDATE {pre}users SET pay_points = pay_points + ".intval($pay_points)." WHERE user_id = $user_id";
            model()->query($sql);
            // 积分记录
            $data['user_id'] = $user_id;
            $data['user_money'] = 0;
            $data['frozen_money'] = 0;
            $data['rank_points'] = intval($rank_points);
            $data['pay_points'] = intval($pay_points);
            $data['change_time'] = $time;
            $data['change_desc'] = $info['name'] . '积分赠送';
            $data['change_type'] = ACT_OTHER;

            $log_id = model()->table('account_log')->data($data)->insert();
            // 从表记录
            $data1['log_id'] = $log_id;
            $data1['openid'] = $fromusername;
            $data1['keywords'] = $info['command'];
            $data1['createtime'] = $time;
            $log_id = model()->table('wechat_point')->data($data1)->insert();
        }

    }
    /**
     * 积分扣除处理
     */
    public function do_takeout_point($fromusername, $info, $point_value)
    {
        $time = gmtime();
        $user_id = model()->table('wechat_user')
            ->field('ect_uid')
            ->where(array('openid'=>$fromusername))
            ->one();
        if($user_id){
            // 扣除处理
           $usable_points= model()->table('users')
                    ->field('pay_points')
                    ->where(array('user_id' => $user_id))
                    ->one();
            // 判断用户消费积分 大于扣除消费积分
            if(intval($usable_points)  >=  intval($point_value)) {
                $sql = "UPDATE {pre}users SET pay_points = pay_points - ".intval($point_value)." WHERE user_id = $user_id";
                model()->query($sql);
                // 积分记录
                $data['user_id'] = $user_id;
                $data['user_money'] = 0;
                $data['frozen_money'] = 0;
                $data['rank_points'] = 0;
                $data['pay_points'] = $point_value;
                $data['change_time'] = $time;
                $data['change_desc'] = $info['name'] . '积分扣除';
                $data['change_type'] = ACT_OTHER;

                $log_id = model()->table('account_log')->data($data)->insert();
                // 从表记录
                $data1['log_id'] = $log_id;
                $data1['openid'] = $fromusername;
                $data1['keywords'] = $info['command'];
                $data1['createtime'] = $time;
                $log_id = model()->table('wechat_point')->data($data1)->insert();
                return true;
            }else{
                return false;
            }

        }

    }

    public function plugin_display($tpl = '', $config = array())
    {
        $this->_data['config'] = $config;
        L(require(LANG_PATH  . C('shop.lang') . '/wechat.php'));
        $this->_data['lang'] = array_change_key_case(L());
        //插件视图目录
        $this->assign($this->_data);
        $tpl = 'app/modules/wechat/'.$this->plugin_name.'/view/'.$tpl.C('TPL.TPL_SUFFIX');
        $content = file_get_contents(ROOT_PATH . $tpl);
        $content = str_replace('\\', '', $content);
        $this->template_content = $this->display($content, true, false);
        //layout目录
        $tpl_l = 'app/http/'.APP_NAME. '/views/wechat_layout';

        $this->assign($this->_data);
        return parent::display($tpl_l);
    }

    /**
     * 中奖概率计算
     *
     * @param unknown $proArr
     * @return Ambigous <string, unknown>
     */
    function get_rand($proArr)
    {
        $result = '';
        // 概率数组的总概率精度
        $proSum = array_sum($proArr);
        // 概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }

    public function __get($name) {
        return isset($this->_data [$name]) ? $this->_data [$name] : NULL;
    }

    public function __set($name, $value) {
        $this->_data [$name] = $value;
    }
}
