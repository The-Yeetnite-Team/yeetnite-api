<?php
require_once 'config.php';

class CacheProvider {
    private Memcached $cache_provider;

    public function __construct() {
        $this->cache_provider = new Memcached();
        if (!$this->cache_provider->addServer(MEMCACHED_HOST, MEMCACHED_PORT)) {
            echo 'Failed to add memcached host';
            return;
        }

        // we presumably have a server connection
        // let's add some default keys that we know we'll need
        foreach (DEFAULT_MEMCACHED_KEYS as $key => $value) {
            if (str_starts_with($value, "file|")) {
                $value = file_get_contents(explode("file|", $value)[1], true);
            }

            $this->cache_provider->set($key, $value);
        }
    }

    public function get(string $key) {
        return $this->cache_provider->get($key);
    }

    public function set(string $key, string $value): void
    {
        $this->cache_provider->set($key, $value);
    }
}

$cache_provider = new CacheProvider();