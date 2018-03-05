<?php
namespace http\captcha\controllers;

use http\base\controllers\FrontendController;
use ectouch\verify\Verify;

class IndexController extends FrontendController {

    /**
     * 验证码
     */
    public function actionIndex()
    {
        $params = array(
            'fontSize' => 14, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'fontttf' => '4.ttf',
            'bg' => array(255, 255, 255)
        );
        $verify = new Verify($params);
        $verify->entry();
    }
}