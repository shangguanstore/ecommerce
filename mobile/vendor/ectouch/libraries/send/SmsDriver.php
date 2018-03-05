<?php

/**
 * 短信发送驱动
 */

namespace libraries\send;

class SmsDriver implements SendInterface
{

    protected $config = array(
        'sms_name' => '',
        'sms_password' => '',
    );
    protected $errorMsg = '';
    protected $sms;

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
        if (empty($this->config['sms_type'])) {
            $sms_type = '\libraries\sms\Ihuyi';
        } else {
            $sms_type = '\libraries\sms\\' . $this->config['sms_type'];
        }
        $this->sms = new $sms_type($this->config);
    }

    /**
     * 发送短信
     * @param  string $to 收信人
     * @param  string $title 标题
     * @param  string $content 内容
     * @param  array $data 其他数据
     * @return array
     */
    public function push($to, $title, $content, $time = '', $data = array())
    {
        return $this->sms->setSms($content)->sendSms($to);
    }

    public function getError()
    {
        return $this->sms->getError();
    }
}
