<?php
require_once "config.php";

class CacheProvider {
    private $cache_provider;

    public function __construct() {
        $this->cache_provider = new Memcached();
        if (!$this->cache_provider->addServer(MEMCACHED_HOST, MEMCACHED_PORT))
            echo ("Failed to add memcached host");
    }

    public function get(string $key) {
        return $this->cache_provider->get($key);
    }

    public function set(string $key, string $value) {
        return $this->cache_provider->set($key, $value);
    }
}