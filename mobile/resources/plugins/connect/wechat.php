<?php
defined('BASE_PATH') or exit('No direct script access allowed');

class wechat
{

    private $wechat = '';

    /**
     * 构造函数
     *
     * @param unknown $config
     */
    public function __construct($config)
    {
        $options = array(
            'appid' => $config['app_id'],
            'appsecret' => $config['app_secret'],
        );
        $this->wechat = new \ectouch\wechat\Wechat($options);
    }

    /**
     * 获取授权地址
     */
    public function redirect($callback_url)
    {
        return $this->wechat->getOauthRedirect($callback_url, 'wechat_oauth');
    }

    /**
     * 回调用户数据
     */
    public function callback($callback_url, $code)
    {
        if (!empty($code)) {
            $token = $this->wechat->getOauthAccessToken();
            $userinfo = $this->wechat->getOauthUserinfo($token['access_token'], $token['openid']);
            if (!empty($userinfo)) {
                 // 对昵称有特殊字符进行替换
                include('emoji.php');
                $userinfo['nickname'] = emoji_unified_to_html($userinfo['nickname']);
                $userinfo['nickname'] = stripslashes(htmlspecialchars_decode($userinfo['nickname'])); //过滤html标签
                $_SESSION['openid'] = $userinfo['openid'];
                $_SESSION['nickname'] = $userinfo['nickname'];
                $_SESSION['avatar'] = $userinfo['headimgurl'];
                $identify = (isset($userinfo['unionid']) && !empty($userinfo['unionid'])) ? $userinfo['unionid'] : $userinfo['openid'];
                $data = array(
                    'openid' => basename(__FILE__, '.php') . '_' . $identify,
                    'name' => $userinfo['nickname'],
                    'sex' => $userinfo['sex'],
                    'avatar' => $userinfo['headimgurl']
                );
                //更新粉丝信息
                $controller = '\http\wechat\controllers\IndexController';
                if (class_exists($controller)) $this->updateInfo($userinfo);
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 粉丝信息更新
     */
    private function updateInfo($res = array())
    {
        if (empty($res)) {
            return false;
        }
        // 移除用户特权数据
        $res['privilege'] = serialize($res['privilege']);
        $unionid = false;
        if (isset($res['unionid']) && !empty($res['unionid'])) {
            $userinfo = model()->table('wechat_user')->where(array('unionid' => $res['unionid']))->find();
            $unionid = true;
        } else {
            $userinfo = model()->table('wechat_user')->where(array('openid' => $res['openid']))->find();
        }
        if (empty($userinfo)) {
            $res['ect_uid'] = 0;
            $res['wechat_id'] = 1;
            model()->table('wechat_user')->data($res)->insert();
        } else {
            if ($unionid) {
                $condition = array('unionid' => $res['unionid']);
            } else {
                $condition = array('openid' => $res['openid']);
            }
            model()->table('wechat_user')->data($res)->where($condition)->update();
        }
    }
}
