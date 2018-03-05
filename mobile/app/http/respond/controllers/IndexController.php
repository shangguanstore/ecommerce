<?php
namespace http\respond\controllers;

use http\base\controllers\FrontendController;

class IndexController extends FrontendController
{

    private $data = array();

    public function __construct()
    {
        parent::__construct();

        // 获取参数
        $this->data['code'] = I('get.code');
        $this->data['type'] = I('get.type');
        if (isset($_GET['code'])) {
            unset($_GET['code']);
        }
        if (isset($_GET['type'])) {
            unset($_GET['type']);
        }
    }

    // 发送
    public function actionIndex()
    {
        /* 判断是否启用 */
        $condition['pay_code'] = $this->data['code'];
        $condition['enabled'] = 1;
        $enabled = $this->db->table('payment')->where($condition)->count();
        //提示
        $msg_type = 2;
        if ($enabled == 0) {
            $msg = L('pay_disabled');
        } else {
            $plugin_file = ADDONS_PATH . 'payment/' . $this->data['code'] . '.php';
            /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
            if (file_exists($plugin_file)) {
                /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
                include_once($plugin_file);
                $payobj = new $this->data['code']();
                /* 处理异步请求 */
                if ($this->data['type'] == 'notify') {
                    @$payobj->notify($this->data);
                }
                if ((@$payobj->callback($this->data))) {
                    $msg = L('pay_success');
                    $msg_type = 0;
                } else {
                    $msg = L('pay_fail');
                    $msg_type = 1;
                }
            } else {
                $msg = L('pay_not_exist');
            }
        }
        //显示页面
        $this->assign('message', $msg);
        $this->assign('msg_type', $msg_type);
        $this->assign('shop_url', __URL__);
        $this->display('index');
    }
}
