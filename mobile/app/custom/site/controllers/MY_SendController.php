<?php
namespace custom\site\controllers;

use http\site\controllers\IndexController;

class MY_SendController extends IndexController
{
    public function MY_Test()
    {
        // 短信发送测试
        $message = array(
            'code' => '1234',
            'product' => 'sitename'
        );
        $res = send_sms('18801828888', 'sms_signin', $message);
        if ($res !== true) {
            exit($res);
        };

        // 邮件发送测试
        $res = send_mail('xxx', 'wanglin@ecmoban.com', 'title', 'content');
        if ($res !== true) {
            exit($res);
        };
    }
}