<?php
//缓存类
class cls_cache{
	protected $cache = NULL;
	
    public function __construct( $config = array() ) {
		$cacheDriver = ucfirst($config['type']);
		require_once(dirname(__FILE__) . '/caches/' . $cacheDriver . '.class.php');
		$hander = 'Cache\\' . $cacheDriver;
		$this->cache = new $hander( $config[strtolower($cacheDriver)] );
    }

	//读取缓存
    public function get($key) {
		return $this->cache->get($key);
    }

	//设置缓存
    public function set($key, $value, $expire = 1800) {
		return $this->cache->set($key, $value, $expire);
    }

	//更新缓存
    public function replace($key, $value, $expire = 1800) {
		return $this->cache->replace($key, $value, $expire);
    }

	//删除
	public function rm($key) {
		return $this->cache->rm($key);
	}

	//清空缓存
    public function clear() {
		return $this->cache->clear();
	}
}