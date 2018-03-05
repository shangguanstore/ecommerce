<?php
namespace ectouch\wechat;

class Wechat extends SDK
{

    public function __construct($options)
    {
        parent::__construct($options);
        $this->cache = new \base\Cache();
    }

    /**
     * 日志记录
     * @param mixed $log 输入日志
     * @return mixed
     */
    public function log($log)
    {
        $log = is_array($log) ? var_export($log, true) : $log;
        if ($this->debug && function_exists('logResult')) {
            logResult($log);
        }
    }

    public function clearCache()
    {
        return $this->cache->clear();
    }

    /**
     * 设置缓存
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        return $this->cache->set($cachename, $value, $expired);
    }

    /**
     * 获取缓存
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        return $this->cache->get($cachename);
    }

    /**
     * 清除缓存
     * @param string $cachename
     * @return boolean
     */
    protected function removeCache($cachename)
    {
        return $this->cache->del($cachename);
    }

}
