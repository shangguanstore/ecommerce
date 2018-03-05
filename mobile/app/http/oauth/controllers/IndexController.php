<?php
namespace http\oauth\controllers;

use http\base\controllers\FrontendController;

class IndexController extends FrontendController
{

    public function __construct()
    {
        parent::__construct();
        L(require(LANG_PATH  . C('shop.lang') . '/other.php'));
        $this->load_helper('passport');
    }

    public function actionIndex()
    {
        $type = I('get.type');
        $back_url = I('get.back_url', '', 'urldecode');
        $file = ADDONS_PATH . 'connect/' . $type . '.php';
        if (file_exists($file)) {
            include_once($file);
        } else {
            show_message(L('msg_plug_notapply'), L('msg_go_back'), U('user/login/index'));
        }
        $url = U('oauth/index/index', array(
            'type' => $type,
            'back_url' => empty($back_url) ? U('site/index/index') : $back_url
        ), true);
        $config = $this->getOauthConfig($type);
        // 判断是否安装
        if (!$config) {
            show_message(L('msg_plug_notapply'), L('msg_go_back'), U('user/login/index'));
        }
        $obj = new $type($config);
        // 授权回调
        if (isset($_GET['code']) && $_GET['code'] != '') {
            if ($res = $obj->callback($url, $_GET['code'])) {
                if ($this->oauthLogin($res)) {
                    $this->redirect($back_url);
                }
                parse_str($back_url);
                $res['parent_id'] = !empty($u) ? $u : 0;
                if(!empty($from)){
                    $from = 'touch';
                }
                // 自动注册
                $this->doRegister($res, $_GET['back_url']);
            } else {
                show_message(L('msg_authoriza_error'), L('msg_go_back'), U('user/login/index'));
            }
            return;
        }
        // 授权开始
        $url = $obj->redirect($url);
        ecs_header("Location: " . $url . "\n");
        exit();
    }

    /**
     * 用户绑定
     */
    public function actionBind()
    {
        if (IS_POST) {
            $username = I('username');
            // 手机号绑定
            $preg = preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $username) ? true : false;
            if ($preg === true) {
                $user_name = $this->model->table('users')->field('user_name')->where(array('mobile_phone' => $username))->find();
                $username = $user_name['user_name'];
            }
            // 邮箱绑定
            $pregg = preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/', $username) ? true : false;
            if ($pregg === true) {
                $user_name = $this->model->table('users')->field('user_name')->where(array('email' => $username))->find();
                $username = $user_name['user_name'];
            }
            $password = I('password');
            $back_url = I('back_url');
            // 数据验证
            if(empty($username) || empty($password)){
                show_message(L('msg_input_namepwd'), L('msg_go_back'), '', 'error');
            }
            // 查询用户
            $bind_user_id = $this->users->check_user($username, $password);
            if ($bind_user_id > 0) {
                // 更新微信用户是否已经绑定过
                if (class_exists('\http\wechat\controllers\IndexController')) {
                    $condition = array('ect_uid' => $bind_user_id);
                    $result = $this->db->table('wechat_user')->where($condition)->find();
                    if (!empty($result)) {
                        show_message(L('msg_account_bound'),L('msg_go_back') , '', 'error');
                    }
                }
                // 获取的旧账号信息
                $condition = array('user_id' => $_SESSION['user_id']);
                $userinfo = $this->db->table('users')->field('aite_id')->where($condition)->find();
                // 更新新账号信息
                $condition_new = array('user_id' => $bind_user_id);
                $this->db->table('users')->data($userinfo)->where($condition_new)->update();
                // 更新旧账号信息
                $userinfo_old = array('aite_id' => '');
                $this->db->table('users')->data($userinfo_old)->where($condition)->update();
                // 更新微信用户绑定
                if (class_exists('\http\wechat\controllers\IndexController')) {
                    if (isset($_SESSION['openid']) && !empty($_SESSION['openid'])) {
                        $condition = array('openid' => $_SESSION['openid']);
                        $this->db->table('wechat_user')->data(array('ect_uid' => $bind_user_id))->where($condition)->update();
                    }
                }
                // 重新登录
                $this->doLogin($username);
                $back_url = empty($back_url) ? U('user/index/index') : $back_url;
                $this->redirect($back_url);
            } else {
                show_message(L('msg_account_bound_fail'), L('msg_rebound'), '', 'error');
            }
        }
        $this->assign('page_title', L('msg_bound_account'));
        $this->display();
    }

    /**
     * 用户资料完善
     */
    /*
    public function actionRegister()
    {
        if (IS_POST) {
            $condition = array('openid' => $_SESSION['openid']);
            $userinfo = $this->db->table('wechat_user')->where($condition)->find();
            $username = I('username');
            $password = I('password');
            $email = time() . rand(1, 9999) . '@' . get_top_domain();
            $back_url = I('back_url');
            if (register($username, $password, $email) !== false) {
                $data = array(
                    'aite_id' => $userinfo['openid'],
                    'sex' => $userinfo['sex'],
                    'user_picture' => $userinfo['avatar']
                );
                $condition['user_name'] = $username;
                $this->db->table('users')->data($data)->where($condition)->update();
                $back_url = empty($back_url) ? U('site/index/index') : $back_url;
                $this->redirect($back_url);
            } else {
                show_message('授权注册失败', '重新注册', '', 'error');
            }
            return;
        }
        $this->assign('page_title', '完善信息');
        $this->display();
    }
    */

    /**
     * 获取第三方登录配置信息
     *
     * @param type $type
     * @return type
     */
    private function getOauthConfig($type)
    {
        $sql = "SELECT auth_config FROM {pre}touch_auth WHERE `type` = '$type'";
        $info = $this->db->getRow($sql);
        if ($info) {
            $res = unserialize($info['auth_config']);
            $config = array();
            foreach ($res as $key => $value) {
                $config[$value['name']] = $value['value'];
            }
            return $config;
        }
        return false;
    }

    /**
     * 授权自动登录
     * @param unknown $res
     */
    private function oauthLogin($res)
    {
        $condition['aite_id'] = $res['openid']; // 登录标识
        $userinfo = $this->db->table('users')
            ->field('user_name')
            ->where($condition)
            ->find();
        if ($userinfo) {
            $this->doLogin($userinfo['user_name']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置成登录状态
     * @param unknown $username
     */
    private function doLogin($username)
    {
        $this->users->set_session($username);
        $this->users->set_cookie($username);
        update_user_info();
        recalculate_price();
    }

    /**
     * 授权注册
     * @param $res
     * @param string $back_url
     */
    private function doRegister($res, $back_url = '')
    {
        $username = substr(md5($res['openid']), -2) . time() . rand(100, 999);
        $password = mt_rand(100000, 999999);
        $email = $username . '@' . get_top_domain();
        $extends = array(
            'nick_name' => $res['name'],
            'aite_id' => $res['openid'],
            'sex' => $res['sex'],
            'user_picture' => $res['avatar'],
            'parent_id' => $res['parent_id']
        );
        if (register($username, $password, $email, $extends) !== false) {
            // 更新微信用户绑定信息
            if (class_exists('\http\wechat\controllers\IndexController')) {
                if (isset($_SESSION['openid']) && !empty($_SESSION['openid'])) {
                    $data = array('ect_uid' => $_SESSION['user_id']);
                    $condition = array('openid' => $_SESSION['openid']);
                    $this->db->table('wechat_user')->data($data)->where($condition)->update();
                    //关注送红包
                    $this->sendBonus();
                }
            }
            // 跳转链接
            $back_url = empty($back_url) ? U('site/index/index') : $back_url;
            $this->redirect($back_url);
        } else {
            show_message(L('msg_author_register_error'), L('msg_re_registration'), '', 'error');
        }
        return;
    }

    /**
     * 关注送红包
     */
    private function sendBonus()
    {
        $rs = $this->db->query("SELECT name, keywords, command, config FROM {pre}wechat_extend WHERE command = 'bonus' and enable = 1 and wechat_id = 1 ORDER BY id ASC");
        $addons = reset($rs);
        $file = ADDONS_PATH . 'wechat/' . $addons['command'] . '/' . $addons['command'] . '.class.php';
        if (file_exists($file)) {
            require_once($file);
            $wechat = new $addons['command']();
            $data = $wechat->show($_SESSION['openid'], $addons);
            if (!empty($data)) {
                $wxinfo = model()->table('wechat')
                    ->field('id, token, appid, appsecret, encodingaeskey')
                    ->where(array('id' => 1, 'status' => 1))
                    ->find();
                $config['token'] = $wxinfo['token'];
                $config['appid'] = $wxinfo['appid'];
                $config['appsecret'] = $wxinfo['appsecret'];
                $config['encodingaeskey'] = $wxinfo['encodingaeskey'];
                $weObj = new \ectouch\wechat\Wechat($config);
                $weObj->sendCustomMessage($data['content']);
            }
        }
    }
}
