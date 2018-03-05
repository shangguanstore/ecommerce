<?php
namespace Cache;
use Memcached as MemcachedResource;

/**
 * Memcached缓存驱动
 */
class Memcached
{

    /**
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('memcached')) {
            exit('Not Support Memcached.');
        }

        $this->options = array(
            'servers' => array('127.0.0.1', 11211),
            'options' => null,
            'username' => '',
            'password' => '',
            'prefix' => '',
            'expire' => 0
        );
        $this->options = array_merge($this->options, (array) $options);

        $this->handler = new MemcachedResource;
        $this->handler->addServers($this->options['servers']);
        $this->options['options'] && $this->handler->setOptions($this->options['options']);
        $this->options['username'] && $this->handler->setSaslAuthData($this->options['username'], $this->options['password']);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        return $this->handler->get($this->options['prefix'] . $name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        if ($this->handler->set($name, $value, time() + $expire)) {
            return true;
        }
        return false;
    }

    /**
     * 更新缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function replace($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        if ($this->handler->replace($name, $value, time() + $expire)) {
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name, $ttl = false)
    {
        $name = $this->options['prefix'] . $name;
        return false === $ttl ?
        $this->handler->delete($name) :
        $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flush();
    }
}
