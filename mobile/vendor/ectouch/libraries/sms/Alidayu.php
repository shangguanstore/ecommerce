<?php

namespace libraries\sms;

class Alidayu {

    /**
     * 短信类配置
     * @var array
     */
    protected $config = array(
        'sms_name' => '',
        'sms_password' => '',
    );

    /**
     * @var objcet 短信对象
     */
    protected $sms;
    protected $sms_api = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
    protected $content = array();
    protected $phones = array();
    protected $errorInfo = '';

    /**
     * 构建函数
     * @param array $config 短信配置
     */
    public function __construct($config = array()) {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 设置短信信息
     * @access public
     * @param string $body  邮件内容
     * @return boolean
     */
    public function setSms($msg) {
        if (is_array($msg)) {
            if (empty($this->config['SmsCdoe'])) {
                $str_content = array(
                    'mobile_phone' => $this->phones,
                    'mobile_code' => $msg['mobile_code'],
                    'user_name' => $msg['user_name']
                );
                $this->content = $this->get_register_lang($str_content);
            } else {
                $str_content = array(
                    'mobile_phone' => $this->phones,
                    'mobile_code' => $msg['mobile_code'],
                    'user_name' => $msg['user_name']
                );
                $this->content = $this->get_register($str_content);
            }
        }
        return $this;
    }

    /**
     * 发送短信
     * @param  string $to 收件人
     * @return boolean
     */
    public function sendSms($to) {
        $sendTo = explode(",", $to);
        foreach ($sendTo as $add) {
            if (preg_match("/^0?1((3|7|8)[0-9]|5[0-35-9]|4[57])\d{8}$/", $add)) {
                array_push($this->phones, $add);
            }
        }

        if ($this->phones && $this->content) {
            foreach ($this->phones as $phone) {
                return $this->send($phone);
            }
        }
        return false;
    }

    /**
     * 发送操作
     */
    public function send($phone) {
        require_once(__DIR__ . '/../aliyunyu/TopSdk.php');
        $c = new \TopClient;
        $c->appkey = $GLOBALS['_CFG']['ali_appkey'];
        $c->secretKey = $GLOBALS['_CFG']['ali_secretkey'];
        $c->format = 'json';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType($this->content['SmsType']);
        $req->setSmsFreeSignName($this->content['SignName']);
        $req->setSmsParam($this->content['smsParams']);
        $req->setRecNum("{$phone}");
        $req->setSmsTemplateCode($this->content['SmsCdoe']);
        $resp = $c->execute($req);
        if ($resp->code == 0) {
            return true;
        } elseif ($resp->sub_msg) {
            $this->errorInfo = $resp->sub_msg;
        } else {
            $this->errorInfo = $resp->msg;
        }
        return false;
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function getError() {
        return $this->errorInfo;
    }

    /**
     * 【阿里大鱼】
     *  会员注册发送短信功能.
     * 【变量】mobile_code 验证码
     * 【变量】user_name 会员名称
     * 【变量】mobile_phone 注册会员手机号
     */
    public function get_register_lang($str_centent = array()) {

        //短信内容参数（注意短信模板参数要和此参数名称一致）
        $smsParams = array(
            'code' => $str_centent['mobile_code'],
            'product' => $str_centent['user_name']
        );
        $result = array(
            'SmsType' => 'normal', //短信类型，一般默认
            //接口调用对应修改部分 start
            'SignName' => $this->config['SignName'], //短信签名
            'SmsCdoe' => 'SMS_1000000', //短信模板ID
            //接口调用对应修改部分 end
            'smsParams' => json_encode($smsParams),
            'mobile_phone' => $str_centent['mobile_phone']
        );

        return $result;
    }

    /**
     * 【阿里大鱼】
     *  会员注册发送短信功能.
     * 【变量】mobile_code 验证码
     * 【变量】user_name 会员名称
     * 【变量】mobile_phone 注册会员手机号
     */
    public function get_register($str_centent = array()) {

        //短信内容参数（注意短信模板参数要和此参数名称一致）
        $smsParams = array(
            'code' => $str_centent['mobile_code'],
            'product' => $str_centent['user_name']
        );
        $result = array(
            'SmsType' => 'normal', //短信类型，一般默认
            //接口调用对应修改部分 start
            'SignName' => $this->config['SignName'], //短信签名
            'SmsCdoe' => $this->config['SmsCdoe'], //短信模板ID
            //接口调用对应修改部分 end
            'smsParams' => json_encode($smsParams),
            'mobile_phone' => $str_centent['mobile_phone']
        );

        return $result;
    }

    /**
     * 析构函数
     */
    public function __destruct() {
        $this->sms = null;
    }

}
