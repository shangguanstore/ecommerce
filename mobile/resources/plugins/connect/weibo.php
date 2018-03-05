<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

class weibo
{

    public $api_url = 'https://api.weibo.com/2/';
    public $format = 'json';

    /**
     * 构造函数
     *
     * @param unknown $app
     * @param string $access_token
     */
    public function __construct($config, $access_token = NULL)
    {
        $this->client_id = $config['app_key'];
        $this->client_secret = $config['app_secret'];
        $this->access_token = $access_token;
    }

    /**
     * 获取授权地址
     *
     * @param unknown $info
     * @param unknown $url
     * @return mixed
     */
    public function redirect($callback_url)
    {
        return $this->login_url($callback_url, $this->scope);
    }

    /**
     * 回调用户数据
     *
     * @param unknown $info
     * @param unknown $url
     * @param unknown $code
     * @return boolean
     */
    public function callback($callback_url, $code)
    {
        $result = $this->access_token($callback_url, $code);
        if (isset($result['access_token']) && $result['access_token'] != '') {
            // 保存登录信息，此示例中使用session保存
            $this->access_token = $result['access_token']; // access token
            $openid = $this->get_openid();
            $userinfo = $this->get_user_info($openid);
            if($userinfo['gender'] == 'f'){
                $userinfo['gender'] = 1;
            }elseif ($userinfo['gender'] == 'm'){
                $userinfo['gender'] = 2;
            }else {
                $userinfo['gender'] = 0;
            }
            $_SESSION['nickname'] = $this->get_user_name($userinfo);
            $_SESSION['avatar'] = $userinfo['profile_image_url'];
            $data = array(
                'openid' => basename(__FILE__, '.php') . '_' . $openid,
                'name' => $this->get_user_name($userinfo),
                'sex' => $userinfo['gender'],
                'avatar' => $userinfo['profile_image_url']
            );
            return $data;
        } else {
            // echo "授权失败";
            return false;
        }
    }

    /**
     * 生成授权网址
     *
     * @param unknown $callback_url
     * @return string
     */
    public function login_url($callback_url)
    {
        $params = array(
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $callback_url
        );
        return 'https://api.weibo.com/oauth2/authorize?' . http_build_query($params, '', '&');
    }

    /**
     * 获取access token
     *
     * @param unknown $callback_url
     * @param unknown $code
     * @return Ambigous <multitype:, mixed>
     */
    public function access_token($callback_url, $code)
    {
        $params = array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $callback_url
        );
        $url = 'https://api.weibo.com/oauth2/access_token';
        return $this->http($url, http_build_query($params, '', '&'), 'POST');
    }

    /**
     * 获取登录用户的uid
     *
     * @return Ambigous <>
     */
    public function get_openid()
    {
        $params = array();
        $result = $this->api('account/get_uid', $params);
        return $result['uid'];
    }

    /**
     * 根据uid获取用户信息
     *
     * @param unknown $uid
     * @return Ambigous <multitype:, mixed>
     */
    public function get_user_info($uid)
    {
        $params = array(
            'uid' => $uid
        );
        return $this->api('users/show', $params);
    }

    /**
     * 获取用户名
     *
     * @param unknown $user_info
     * @return Ambigous <multitype:, mixed>
     */
    public function get_user_name($userinfo)
    {

        if ($userinfo['screen_name'] != '') {
            return $userinfo['screen_name'];
        } else {
            return $userinfo['name'];
        }
    }

    /**
     * 发布微博
     *
     * @param unknown $img_c
     * @param string $pic
     * @return Ambigous <multitype:, mixed>
     */
    public function update($img_c, $pic = '')
    {
        $params = array(
            'status' => $img_c
        );
        if ($pic != '' && is_array($pic)) {
            $url = 'statuses/upload';
            $params['pic'] = $pic;
        } else {
            $url = 'statuses/update';
        }
        return $this->api($url, $params, 'POST');
    }

    /**
     * 根据uid获取用户微博列表
     *
     * @param unknown $uid
     * @param number $count
     * @param number $page
     * @return Ambigous <multitype:, mixed>
     */
    public function user_timeline($uid, $count = 10, $page = 1)
    {
        $params = array(
            'uid' => $uid,
            'page' => $page,
            'count' => $count
        );
        return $this->api('statuses/user_timeline', $params);
    }

    /**
     * 调用接口
     *
     * @param unknown $url
     * @param unknown $params
     * @param string $method
     * @return Ambigous <multitype:, mixed>
     *         //示例：根据uid获取用户信息
     *         $result=$sina->api('users/show', array('uid'=>$uid), 'GET');
     */
    public function api($url, $params = array(), $method = 'GET')
    {
        $url = $this->api_url . $url . '.' . $this->format;
        $params['access_token'] = $this->access_token;
        if ($method == 'GET') {
            $query = http_build_query($params, '', '&');
            $result = $this->http($url . '?' . $query);
        } else {
            if (isset($params['pic'])) {
                uksort($params, 'strcmp');
                $str_b = uniqid('------------------');
                $str_m = '--' . $str_b;
                $str_e = $str_m . '--';
                $body = '';
                foreach ($params as $k => $v) {
                    if ($k == 'pic') {
                        if (is_array($v)) {
                            $img_c = $v[2];
                            $img_n = $v[1];
                        } elseif ($v{0} == '@') {
                            $url = ltrim($v, '@');
                            $img_c = file_get_contents($url);
                            $url_a = explode('?', basename($url));
                            $img_n = $url_a[0];
                        }
                        $body .= $str_m . "\r\n";
                        $body .= 'Content-Disposition: form-data; name="' . $k . '"; filename="' . $img_n . '"' . "\r\n";
                        $body .= "Content-Type: image/unknown\r\n\r\n";
                        $body .= $img_c . "\r\n";
                    } else {
                        $body .= $str_m . "\r\n";
                        $body .= 'Content-Disposition: form-data; name="' . $k . '"' . "\r\n\r\n";
                        $body .= $v . "\r\n";
                    }
                }
                $body .= $str_e;
                $headers[] = 'Content-Type: multipart/form-data; boundary=' . $str_b;
                $result = $this->http($url, $body, 'POST', $headers);
            } else {
                $query = http_build_query($params, '', '&');
                $result = $this->http($url, $query, 'POST');
            }
        }
        return $result;
    }

    /**
     * 提交请求
     *
     * @param unknown $url
     * @param string $postfields
     * @param string $method
     * @param unknown $headers
     * @return Ambigous <multitype:, mixed>
     */
    private function http($url, $postfields = '', $method = 'GET', $headers = array())
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        if ($method == 'POST') {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '')
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        $headers[] = 'User-Agent: ECTouch.cn';
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        $json_r = array();
        if ($response != '')
            $json_r = json_decode($response, true);
        return $json_r;
    }

}
