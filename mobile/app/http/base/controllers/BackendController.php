<?php
namespace http\base\controllers;

use base\Config;
use classes\ecshop;
use classes\mysql;
use classes\error;
use classes\session;
use classes\session_memcached;

abstract class BackendController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        define('__TPL__', __ROOT__ . 'resources/assets/console/');
        // 全局常量
        require CONF_PATH . 'constant.php';

        // 加载helper文件
        $helper_list = array('time', 'base', 'common', 'main', 'insert', 'goods');
        $this->load_helper($helper_list);
        // 全局对象
        $this->db_config = Config::get('DB.default');
        $this->ecs = $GLOBALS['ecs'] = new ecshop($this->db_config['DB_NAME'], $this->db_config['DB_PREFIX']);
        $this->db = $GLOBALS['db'] = new mysql();
        if (!defined('INIT_NO_USERS')) {
            $this->sess = $GLOBALS['sess'] = new session($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'));
            /*if($this->cache->cache == 'memcached' && C('CACHE.memcached.CACHE_TYPE') == 'Memcached'){
                $this->sess = $GLOBALS['sess'] = new session_memcached($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'));
            }
            else{
                $this->sess = $GLOBALS['sess'] = new session($this->db, $this->ecs->table('sessions'), $this->ecs->table('sessions_data'));
            }*/
            define('SESS_ID', $this->sess->get_session_id());
            // 优化pc端在部分服务器下session admin_id 丢失
            if((isset($_SESSION['admin_id']) && empty($_SESSION['admin_id'])) && (isset($_SESSION['admin_name']) && !empty($_SESSION['admin_name']))){
              $condition['user_name'] = $_SESSION['admin_name'];
              $_SESSION['admin_id'] = $this->model->table('admin_user')->field('user_id')->where($condition)->one();
            }
        }
        // 全局配置
        $GLOBALS['_CFG'] = load_config();
        $GLOBALS['_CFG']['template'] = 'default';
        C('shop', $GLOBALS['_CFG']);
        $this->checkLogin();

        // 全局语言包
        L(require(LANG_PATH  . C('shop.lang') . '/common.php'));
    }

    public function display($tpl = '', $return = false, $isTpl = true)
    {
        $tpl = $this->getTpl($tpl, $isTpl);
        return parent::display($tpl, $return, $isTpl);
    }

    /**
     * 操作成功之后跳转,默认三秒钟跳转
     *
     * @param unknown $msg
     * @param unknown $url
     * @param string $type
     * @param number $waitSecond
     */
    public function message($msg, $url = NULL, $type = '1', $waitSecond = 3)
    {
        if ($url == NULL)
            $url = 'javascript:history.back();';
        if ($type == '2') {
            $title = L('error_information');
        } else {
            $title = L('prompt_information');
        }
        $data['title'] = $title;
        $data['message'] = $msg;
        $data['type'] = $type;
        $data['url'] = $url;
        $data['second'] = $waitSecond;
        $this->assign('data', $data);
        $this->display('admin/message');
        exit();
    }

    //上传文件
    protected function ectouchUpload($key = '', $upload_dir = 'images', $size = 2) {
        $config = array(
            'maxSize' => 1024 * 1024 * $size,
            'allowExts' => explode(',', 'jpg,jpeg,gif,png,bmp,mp3,amr,mp4'),
            'rootPath' => dirname(ROOT_PATH) . '/',
            'savePath' => 'data/attached/' . $upload_dir . "/",
        );
        $upload = new \libraries\Upload($config);

        if (!$upload->upload($key)) {
            return array('error' => 1, 'message' => $upload->getError());
        }
        else{
            //$fileInfo = $upload->getUploadFileInfo();
            return array('error' => 0, 'message' => $upload->getUploadFileInfo());
        }
    }

    private function checkLogin()
    {
        $condition['user_id'] = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']):0;
        $action_list = $this->model->table('admin_user')->field('action_list')->where($condition)->one();
        //手机端权限
        if(empty($action_list) && strpos(APP_NAME, $action_list) === FALSE && $action_list != 'all'){
            $this->redirect('../admin/index.php?act=main');
        }
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
            } elseif (file_exists($base_views . 'base/' . $tpl . C('TPL.TPL_SUFFIX'))) {
                $tpl = 'resources/views/base/' . $tpl;
            } elseif (file_exists($base_views . $extends_tpl)) {
                $tpl = 'resources/views/' . APP_NAME . '/' . $tpl;
            } else {
                $tpl = 'app/http/' . APP_NAME . '/views/' . $tpl;
            }
        }
        return strtolower($tpl);
    }
    /**
     * 判断管理员对某一个操作是否有权限。
     *
     * 根据当前对应的action_code，然后再和用户session里面的action_list做匹配，以此来决定是否可以继续执行。
     * @param     string    $priv_str    操作对应的priv_str
     * @param     string    $msg_type       返回的类型
     * @return true/false
     */
    public function admin_priv($priv_str, $msg_type = '' , $msg_output = true)
    {
        $condition['user_id'] = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']):0;
        $action_list = $this->model->table('admin_user')->field('action_list')->where($condition)->one();
        //手机端权限
        if(empty($action_list) || stripos($action_list,$priv_str) === FALSE && $action_list != 'all'){
            $this->redirect('../admin/index.php?act=main');
        }
    }
}
