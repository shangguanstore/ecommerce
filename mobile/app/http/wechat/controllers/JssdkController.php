<?php
namespace http\wechat\controllers;

use http\base\controllers\FrontendController;
use ectouch\wechat\Wechat;

class JssdkController extends FrontendController
{

    public function actionIndex()
    {
        $url = addslashes($_POST['url']);
        if ($url != '') {
            // 获取公众号配置
            $wxConf = $this->getConfig();
            $this->wechat = new Wechat($wxConf);
            $sdk = $this->wechat->getJsSign($url);
            $data = array('status' => '200', 'data' => $sdk);
        } else {
            $data = array('status' => '100', 'message' => '缺少参数');
        }
        exit(json_encode($data));
    }

    /**
     * 获取公众号配置
     *
     * @return array
     */
    private function getConfig()
    {
        return model()->table('wechat')
            ->field('id, token, appid, appsecret, encodingaeskey')
            ->where(array('status' => 1))
            ->find();
    }
}