<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：facebook.php
 * ----------------------------------------------------------------------------
 * 功能描述：facebook登录插件
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

$payment_lang = LANG_PATH  . C('shop.lang') . '/connect/' . basename(__FILE__);

if (file_exists($payment_lang)) {
    include_once ($payment_lang);
    L($_LANG);
}
/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;
    /* 类名 */
    $modules[$i]['name'] = 'Facebook';
    // 文件名，不包含后缀
    $modules[$i]['type'] = 'facebook';

    $modules[$i]['className'] = 'facebook';
    // 作者信息
    $modules[$i]['author'] = 'ECTouch';

    // 作者QQ
    $modules[$i]['qq'] = '800007167';

    // 作者邮箱
    $modules[$i]['email'] = 'support@ecmoban.com';

    // 申请网址
    $modules[$i]['website'] = 'http://open.facebook.com';

    // 版本号
    $modules[$i]['version'] = '1.0';

    // 更新日期
    $modules[$i]['date'] = '2016-09-03';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('type' => 'text', 'name' => 'client_id', 'value' => ''),
        array('type' => 'text', 'name' => 'client_secret', 'value' => ''),
    );
    return;
}

class facebook
{
    /* Verify SSL Cert. */
    public $verifypeer = FALSE;
    /* Decode returned json data. */
    public $decode_JSON = TRUE;
    /* Set connect timeout. */
    public $connecttimeout = 30;
    /* Set timeout default. */
    public $timeout = 30;
    /* Set the useragent. */
    public $useragent = "ECTouch | http://www.ectouch.cn";
    /* HTTP Proxy settings (will only take effect if you set 'behind_proxy' to true) */
    public $proxy_settings = array(
        'behind_proxy' => false,
        'host' => '',
        'port' => '',
        'user' => '',
        'pass' => '',
        'type' => CURLPROXY_HTTP,
        'auth' => CURLAUTH_BASIC
    );
    /* Contains the last HTTP status code returned. */
    public $http_code;
    /* Contains the last HTTP headers returned. */
    public $http_info = array();
    /* Contains the last API call. */
    public $url;
    /* Contains last http_headers */
    public $http_header = array();

    /* Variables used internally by the class and subclasses */
    protected $client_id, $client_secret, $access_token;
    protected $callback_url;

    protected static $METHOD_GET = "GET";
    protected static $METHOD_POST = "POST";
    protected static $METHOD_DELETE = "DELETE";

    /* Set API URLS */
    const AuthorizeUrl = 'https://graph.facebook.com/oauth/authorize';
    const AccessTokenUrl = 'https://graph.facebook.com/oauth/access_token';
    const GraphUrl = 'https://graph.facebook.com/';

    public function __construct($config, $access_token = NULL)
    {
        $this->client_id = $config['client_id'];
        $this->client_secret = $config['client_secret'];
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
        return $this->getAuthorizeUrl($callback_url);
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
        $this->access_token = $this->getAccessToken($callback_url, $code);
        if (!empty($this->access_token)) {
            // 获取用户信息
            $userinfo = $this->get('/me');
            if ($userinfo['gender'] == 'male') {
                $userinfo['gender'] = 1;
            } elseif ($userinfo['gender'] == 'female') {
                $userinfo['gender'] = 2;
            } else {
                $userinfo['gender'] = 0;
            }
            $_SESSION['nickname'] = $userinfo['name'];
            $_SESSION['avatar'] = isset($userinfo['picture']) ? $userinfo['picture'] : __ROOT__ . 'data/avatar/default/big.png';
            $data = array(
                'openid' => basename(__FILE__, '.php') . '_' . $userinfo['id'],
                'name' => $userinfo['name'],
                'sex' => $userinfo['gender'],
                'avatar' => isset($userinfo['picture']) ? $userinfo['picture'] : __ROOT__ . 'data/avatar/default/big.png'
            );
            return $data;
        } else {
            // echo "授权失败";
            return false;
        }
    }

    /* Get the authorize URL @returns a string */
    public function getAuthorizeUrl($callback_url = NULL)
    {
        $params = array();
        $params["client_id"] = $this->client_id;
        if (!empty($callback_url)) {
            $params["redirect_uri"] = $callback_url;
        }
        $params["scope"] = 'public_profile';
        return self::AuthorizeUrl . "?" . OAuthUtils::build_http_query($params);
    }

    /* Exchange verify code for an access token @returns string access token */
    public function getAccessToken($callback_url, $code)
    {
        $params = array();
        $params["client_id"] = $this->client_id;
        $params["client_secret"] = $this->client_secret;
        $params["code"] = $code;
        if (!empty($callback_url)) {
            $params["redirect_uri"] = $callback_url;
        }
        $url = self::AccessTokenUrl . "?" . OAuthUtils::build_http_query($params);
        $contents = $this->http($url, self::$METHOD_GET);
        parse_str($contents, $output);

        if (array_key_exists('access_token', $output)) {
            $this->access_token = $output['access_token'];
        }

        return $this->access_token;
    }

    /* GET wrapper for http. */
    public function get($location, $fields = NULL, $introspection = FALSE)
    {
        $params = array();
        if (!empty($this->access_token)) {
            $params["access_token"] = $this->access_token;
        }
        if (!empty($fields)) {
            $params["fields"] = $fields;
        }
        if ($introspection) {
            $params["metadata"] = 1;
        }
        $url = self::GraphUrl . OAuthUtils::urlencode_rfc3986($location) . "?" . OAuthUtils::build_http_query($params);
        $response = $this->http($url, self::$METHOD_GET);
        return $this->decode_JSON ? json_decode($response, true) : $response;
    }

    /* GET IDS wrapper for http. @ids comma separated list of ids */
    public function get_ids($ids)
    {
        $params = array();

        if (is_array($ids)) {
            $params["ids"] = implode(",", $ids);
        } else {
            $params["ids"] = $ids;
        }

        if (!empty($this->access_token)) {
            $params["access_token"] = $this->access_token;
        }

        $url = self::GraphUrl . "?" . OAuthUtils::build_http_query($params);
        $response = $this->http($url, self::$METHOD_GET);

        return $this->decode_JSON ? json_decode($response, true) : $response;
    }

    /* POST wrapper for http.*/
    public function post($location, $postfields = array())
    {
        $url = self::GraphUrl . OAuthUtils::urlencode_rfc3986($location);
        if (!empty($this->access_token)) {
            $postfields["access_token"] = $this->access_token;
        }

        $response = $this->http($url, self::$METHOD_POST, $postfields);

        return $this->decode_JSON ? json_decode($response, true) : $response;
    }

    /* DELETE wrapper for http. */
    public function delete($location, $postfields = array())
    {
        $url = self::GraphUrl . OAuthUtils::urlencode_rfc3986($location);
        $postfields = array();

        if (!empty($this->access_token)) {
            $postfields["access_token"] = $this->access_token;
        }

        $response = $this->http($url, self::$METHOD_DELETE, $postfields);
        return $this->decode_JSON ? json_decode($response, true) : $response;
    }

    /**
     * Make an HTTP request
     *
     * @return API results
     */
    private function http($url, $method = "GET", $postfields = NULL)
    {
        $this->http_info = array();
        $handle = curl_init();
        /* Curl settings */
        curl_setopt($handle, CURLOPT_HEADER, FALSE);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($handle, CURLOPT_PROTOCOLS, "CURLPROTO_HTTPS");
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $this->verifypeer);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($handle, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));

        if ($this->proxy_settings['behind_proxy']) {
            curl_setopt($ci, CURLOPT_PROXY, $this->proxy_settings['host']);
            curl_setopt($ci, CURLOPT_PROXYPORT, $this->proxy_settings['port']);
            curl_setopt($ci, CURLOPT_PROXYUSERPWD, "{$this->proxy_settings['user']}:{$this->proxy_settings['pass']}");
            curl_setopt($ci, CURLOPT_PROXYTYPE, $this->proxy_settings['type']);
            curl_setopt($ci, CURLOPT_PROXYAUTH, $this->proxy_settings['auth']);
        }

        switch ($method) {
            case self::$METHOD_POST:
                curl_setopt($handle, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case self::$METHOD_DELETE:
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($postfields)) {
                    $url .= "?" . OAuthUtils::build_http_query($postfields);
                }
                break;
        }
        curl_setopt($handle, CURLOPT_URL, $url);
        $response = curl_exec($handle);
        $this->http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($handle));
        $this->url = $url;
        curl_close($handle);
        return $response;
    }

    /**
     * Get the header info to store.
     */
    function getHeader($ch, $header)
    {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
}

/**
 *  OAuthUtils
 *  Copied and adapted from http://oauth.googlecode.com/svn/code/php/
 */
class OAuthUtils
{
    public static function urlencode_rfc3986($input)
    {
        if (is_array($input)) {
            return array_map(array('OAuthUtils', 'urlencode_rfc3986'), $input);
        } else if (is_scalar($input)) {
            return str_replace(
                '+',
                ' ',
                str_replace('%7E', '~', rawurlencode($input))
            );
        } else {
            return '';
        }
    }

    public static function build_http_query($params)
    {
        if (!$params) return '';
        // Urlencode both keys and values
        $keys = OAuthUtils::urlencode_rfc3986(array_keys($params));
        $values = OAuthUtils::urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);

        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                foreach ($value as $duplicate_value) {
                    $pairs[] = $parameter . '=' . $duplicate_value;
                }
            } else {
                $pairs[] = $parameter . '=' . $value;
            }
        }
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        return implode('&', $pairs);
    }
}
