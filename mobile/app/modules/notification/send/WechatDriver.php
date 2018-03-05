<?php
namespace app\modules\notification\send;

use app\modules\notification\wechat\Wechat;

/**
 * 微信发送驱动
 */
class WechatDriver implements SendInterface
{

    protected $config = array();

    protected $wechat;

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
        $this->wechat = new Wechat($this->config);
    }

    /**
     * 发送微信
     * @param  string $to 收信人
     * @param  string $title 标题
     * @param  string $content 内容
     * @param  array $data 其他数据
     * @return array
     */
    public function push($to, $title, $content, $data = array())
    {
        return $this->wechat->setData($content)->send($to);
    }

    public function getError()
    {
        return $this->wechat->getError();
    }
}
