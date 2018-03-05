<?php
namespace http\base\controllers;

use base\Config;
use classes\ecshop;
use classes\mysql;
use classes\error;
use classes\session;
use classes\session_memcached;
use libraries\Http;

abstract class FrontendController extends BaseController
{
    public $province_id = 0;
    public $city_id = 0;
    public $district_id = 0;
    public $caching = false;
    public $custom = '';
    public $customs = '';

    public function __construct()
    {
        parent::__construct();
        $this->start();
        // 当前城市
        $this->geocoding();
    }

    /**
     * 根据ip地址获取当前城市信息并定位
     */
    private function geocoding()
    {
        $current_city_id = cookie('lbs_city');
        $current_city_info = get_region_name(intval($current_city_id));
        if (empty($current_city_info)) {
            // 请求API
            $res_city_name = $this->getApiCityName();

            // 获取城市信息
            $sql = "select `region_id`, `region_name`, `parent_id` from " . $GLOBALS['ecs']->table('region') .
                " where region_type = 2 and region_name = '{$res_city_name}'";
            $current_city_info = $GLOBALS['db']->getRow($sql);

            // 强制手动定位            
            if (empty($current_city_info)) {
                $current_city_info = get_region_name(C('shop.shop_city'));
            }

            // 保存cookie
            setcookie('lbs_city', $current_city_info['region_id'], gmtime() + 3600 * 24 * 30);
            setcookie('province', $current_city_info['parent_id'], gmtime() + 3600 * 24 * 30);
            setcookie('city', $current_city_info['region_id'], gmtime() + 3600 * 24 * 30);
            setcookie('district', 0, gmtime() + 3600 * 24 * 30);
        }
        $this->assign('current_city', $current_city_info);
    }

    public function fetch($tpl = '', $return = true, $isTpl = false)
    {
        return $this->display($tpl, $return, $isTpl);
    }

    public function display($tpl = '', $return = false, $isTpl = true)
    {
        $tpl = $this->getTpl($tpl, $isTpl);
        if ($this->caching && $isTpl) {
            $return = true;
            $cacheKey = md5($tpl . 'display');
            $html = $this->cache->get($cacheKey);
            if (empty($html)) {
                $html = parent::display($tpl, $return, $isTpl);
                $expire = C('CACHE_EXPIRE');
                $expire = !empty($expire) ? $expire : 86400;
                $this->cache->set($cacheKey, $html, $expire);
            }
            echo $html;
        } else {
            return parent::display($tpl, $return, $isTpl);
        }
    }

    /**
     * 输出JSON数据
     * @param array $data
     */
    protected function response($data = array(), $block = ACTION_NAME)
    {
        $result = array();
        $tpl = $this->getTpl('widget', true) . C('TPL.TPL_SUFFIX');
        $widget = file_get_contents(ROOT_PATH . $tpl);
        // 替换block标签
        preg_replace('/<block\sname="(.+?)"\s*?>(.*?)<\/block>/eis', "\$this->parseBlock('\\1','\\2')", $widget);
        $content = str_replace('\\', '', $this->block[$block]);
        foreach ($data as $vo) {
            $this->assign($vo);
            $result[] = $this->fetch($content);
        }
        exit(json_encode($result));
    }

    private function getTpl($tpl = '', $isTpl = false)
    {
        if ($isTpl) {
            $tpl = empty($tpl) ? strtolower(CONTROLLER_NAME) . C('TPL.TPL_DEPR') . ACTION_NAME : $tpl;
            $base_themes = ROOT_PATH . 'statics/';
            $base_views = ROOT_PATH . 'resources/views/';
            $base_custom = ROOT_PATH . 'app/custom/' . APP_NAME . '/views/' . $tpl . C('TPL.TPL_SUFFIX');
            $extends_tpl = APP_NAME . '/' . $tpl . C('TPL.TPL_SUFFIX');

            if (file_exists($base_custom)) {
                $tpl = 'app/custom/' . APP_NAME . '/views/' . $tpl;
            } elseif (file_exists($base_themes . $extends_tpl)) {
                $tpl = 'statics/' . APP_NAME . '/' . $tpl;
            } elseif (file_exists($base_views . $extends_tpl)) {
                $tpl = 'resources/views/' . APP_NAME . '/' . $tpl;
            } else {
                $tpl = 'app/http/' . APP_NAME . '/views/' . $tpl;
            }
        }
        return $tpl;
    }

    /**
     * 记录当前页面中的block标签
     * @access private
     * @param string $name block名称
     * @param string $content 模板内容
     * @return string
     */
    private function parseBlock($name, $content)
    {
        $this->block[$name] = $content;
    }

    /**
     * 替换继承模板中的block标签
     * @access private
     * @param string $name block名称
     * @param string $content 模板内容
     * @return string
     */
    private function replaceBlock($name, $content)
    {
        // 替换block标签 没有重新定义则使用原来的
        $replace = isset($this->block[$name]) ? $this->block[$name] : $content;
        return stripslashes($replace);
    }

    private function start()
    {
        $this->set_ini();
        $this->init();
        //$this->init_first();
        $this->init_user();
        $this->init_gzip();
        $this->init_assign();
        $this->init_area();
        $this->init_oauth();
        //$this->replace_lang();  //替换分销语言   2018/01/05  有未知错误，目前用不到分销的功能，注释掉
        $this->assign('lang', array_change_key_case(L()));
        $this->assign('charset', CHARSET);
    }

    /**
     * 初始化设置
     */
    private function set_ini()
    {
        @ini_set('memory_limit', '512M');
        @ini_set('session.cache_expire', 180);
        @ini_set('session.use_trans_sid', 0);
        @ini_set('session.use_cookies', 1);
        @ini_set('session.auto_start', 0);
        @ini_set('display_errors', 0);
    }

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    private function init()
    {
        // 全局常量
        require CONF_PATH . 'constant.php';
        // 加载helper文件
        $helper_list = array('time', 'base', 'common', 'main', 'insert', 'goods');
        $this->load_helper($helper_list);
        // 全局对象
        $this->db_config = Config::get('DB.default');
        $this->ecs = $GLOBALS['ecs'] = new ecshop($this->db_config['DB_NAME'], $this->db_config['DB_PREFIX']);
        $this->db = $GLOBALS['db'] = new mysql();
        $this->err = $GLOBALS['err'] = new error('message');
        // 全局配置
        $GLOBALS['_CFG'] = load_config();
        $GLOBALS['_CFG']['template'] = 'default';
        C('shop', $GLOBALS['_CFG']);
        // 应用配置
        $app_config = APP_PATH . 'config/web.php';
        C('app', file_exists($app_config) ? require $app_config : array());
        // 全局语言包
        L(require(LANG_PATH  . C('shop.lang') . '/common.php'));
        // 应用语言包
        $app_lang = APP_PATH . 'language/' . C('shop.lang') . '/' . strtolower(CONTROLLER_NAME) . '.php';
        L(file_exists($app_lang) ? require $app_lang : array());
        // 应用helper文件
        $this->load_helper('function', 'app');
        // 商店关闭了，输出关闭的消息
        if (C('shop_closed') == 1) {
            exit('<p>' . L('shop_closed') . '</p><p>' . C('close_comment') . '</p>');
        }
        if (!defined('INIT_NO_USERS')) {
            if ($this->cache->cache == 'memcached' && C('CACHE.memcached.CACHE_TYPE') == 'Memcached') {
                $this->sess = $GLOBALS['sess'] = new session_memcached($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'));
            } else {
                $this->sess = $GLOBALS['sess'] = new session($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'));
            }
            define('SESS_ID', $this->sess->get_session_id());
        }
        //加载商创helper文件
        $schelper_list = array('scecmoban', 'scfunction');
        $this->load_helper($schelper_list);
    }

    private function init_user()
    {
        if (!defined('INIT_NO_USERS')) {
            // 会员信息
            $GLOBALS['user'] = $this->users = &init_users();
            if (!isset($_SESSION['user_id'])) {
                /* 获取投放站点的名称 */
                $site_name = isset($_GET['from']) ? htmlspecialchars($_GET['from']) : addslashes(L('self_site'));
                $from_ad = !empty($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

                $wechat_from = array('timeline','groupmessage','singlemessage');//如果在微信分享链接，referer为touch
                if(in_array($site_name,$wechat_from)){
                    $site_name = addslashes(L('self_site'));
                }
                $_SESSION['from_ad'] = $from_ad; // 用户点击的广告ID
                $_SESSION['referer'] = stripslashes($site_name); // 用户来源

                unset($site_name);

                if (!defined('INGORE_VISIT_STATS')) {
                    visit_stats();
                }
            }

            if (empty($_SESSION['user_id'])) {
                if ($this->users->get_cookie()) {
                    /* 如果会员已经登录并且还没有获得会员的帐户余额、积分以及优惠券 */
                    if ($_SESSION['user_id'] > 0) {
                        update_user_info();
                    }
                } else {
                    $_SESSION['user_id'] = 0;
                    $_SESSION['user_name'] = '';
                    $_SESSION['email'] = '';
                    $_SESSION['user_rank'] = 0;
                    $_SESSION['discount'] = 1.00;
                    if (!isset($_SESSION['login_fail'])) {
                        $_SESSION['login_fail'] = 0;
                    }
                }
            }

            // 设置推荐会员
            if (isset($_GET['u'])) {
                set_affiliate();
            }

            // session 不存在，检查cookie
            if (!empty($_COOKIE['ECS']['user_id']) && !empty($_COOKIE['ECS']['password'])) {
                // 找到了cookie, 验证cookie信息
                $condition = array(
                    'user_id' => intval($_COOKIE['ECS']['user_id']),
                    'password' => $_COOKIE['ECS']['password']
                );
                $row = $this->db->table('users')->where($condition)->find();

                if (!$row) {
                    $time = time() - 3600;
                    setcookie("ECS[user_id]", '', $time, '/');
                    setcookie("ECS[password]", '', $time, '/');
                } else {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user_name'] = $row['user_name'];
                    update_user_info();
                }
            }

            if (isset($this->tpl)) {
                $this->tpl->assign('ecs_session', $_SESSION);
            }
        }
    }

    //映射公用模板的值
    private function init_assign()
    {
        //热搜
        $search_keywords = C('shop.search_keywords');
        $hot_keywords = array();
        if ($search_keywords) {
            $hot_keywords = explode(',', $search_keywords);
        }

        $this->assign('hot_keywords', $hot_keywords);
        $history = '';
        if (!empty($_COOKIE['ECS']['keywords'])) {
            $history = explode(',', $_COOKIE['ECS']['keywords']);
            $history = array_unique($history);  //移除数组中的重复的值，并返回结果数组。
        }
        $this->assign('history_keywords', $history);
        // WXSDK  微信浏览器内访问并安装了微信通
        if(is_wechat_browser() && is_dir(APP_WECHAT_PATH)) $is_wechat = 1;
        $this->assign('is_wechat', $is_wechat);
    }

    /**
     * 地区选择
     */
    public function init_area()
    {
        //判断地区关联是否选择完毕 start
        $city_district_list = get_isHas_area($_COOKIE['type_city']);
        if (!$city_district_list) {
            setcookie('type_district', 0, gmtime() + 3600 * 24 * 30);
            $_COOKIE['type_district'] = 0;
        }

        $provinceT_list = get_isHas_area($_COOKIE['type_province']);
        $cityT_list = get_isHas_area($_COOKIE['type_city'], 1);
        $districtT_list = get_isHas_area($_COOKIE['type_district'], 1);

        if ($_COOKIE['type_province'] > 0 && $provinceT_list) {
            if ($city_district_list) {
                if ($cityT_list['parent_id'] == $_COOKIE['type_province'] && $_COOKIE['type_city'] == $districtT_list['parent_id']) {
                    $_COOKIE['province'] = $_COOKIE['type_province'];
                    if ($_COOKIE['type_city'] > 0) {
                        $_COOKIE['city'] = $_COOKIE['type_city'];
                    }

                    if ($_COOKIE['type_district'] > 0) {
                        $_COOKIE['district'] = $_COOKIE['type_district'];
                    }
                }
            } else {
                if ($cityT_list['parent_id'] == $_COOKIE['type_province']) {
                    $_COOKIE['province'] = $_COOKIE['type_province'];
                    if ($_COOKIE['type_city'] > 0) {
                        $_COOKIE['city'] = $_COOKIE['type_city'];
                    }

                    if ($_COOKIE['type_district'] > 0) {
                        $_COOKIE['district'] = $_COOKIE['type_district'];
                    }
                }
            }
        }
        //判断地区关联是否选择完毕 end
        $this->province_id = isset($_COOKIE['province']) ? $_COOKIE['province'] : 0;
        $this->city_id = isset($_COOKIE['city']) ? $_COOKIE['city'] : 0;
        $this->district_id = isset($_COOKIE['district']) ? $_COOKIE['district'] : 0;

        //判断仓库是否存在该地区
        $warehouse_date = array('region_id', 'region_name');
        $warehouse_where = "regionId = '$this->province_id'";
        $warehouse_province = get_table_date('region_warehouse', $warehouse_where, $warehouse_date);

        $sellerInfo = get_seller_info_area();
        if (!$warehouse_province) {
            $this->province_id = $sellerInfo['province'];
            $this->city_id = $sellerInfo['city'];
            $this->district_id = $sellerInfo['district'];
        }

        setcookie('province', $this->province_id, gmtime() + 3600 * 24 * 30);
        setcookie('city', $this->city_id, gmtime() + 3600 * 24 * 30);
        setcookie('district', $this->district_id, gmtime() + 3600 * 24 * 30);
    }

    //判断是否支持 Gzip 模式
    private function init_gzip()
    {
        if (!defined('INIT_NO_SMARTY') && gzip_enabled()) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }
    }
    /**
     * 自动授权跳转
     */
    private function init_oauth()
    {
        if (is_wechat_browser() && empty($_SESSION['openid']) && APP_NAME != 'oauth') {
            $sql = " select `auth_config` from " . $GLOBALS['ecs']->table('touch_auth') . " where `type` = 'wechat' ";
            $auth_config = $GLOBALS['db']->getOne($sql);
            if ($auth_config) {
                $back_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $this->redirect(U('oauth/index/index', array('type' => 'wechat', 'back_url' => $back_url)));
            }
        }
    }

    /*
     * 替换分销语言包
     */
    private function replace_lang(){
        $condition['code'] = 'custom_distribution';
        $condition2['code'] = 'custom_distributor';
        $this->custom = $this->model->table('drp_config')->field("value")->where($condition)->One(); // 分销->代言
        $this->customs = $this->model->table('drp_config')->field("value")->where($condition2)->One(); // 分销商->代言人
        $coustomes = L();
        if(is_array($coustomes)){
            foreach($coustomes as $key => $val) {
                L($key,str_replace("分销",$this->custom,str_replace("分销商",$this->customs,$val)));
            }

        }
        return L();
    }

}
