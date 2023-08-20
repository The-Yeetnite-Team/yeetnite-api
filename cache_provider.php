<?php
require_once 'api-config.php';

class CacheProvider {
    private Memcached $cache_provider;

    public function __construct() {
        $this->cache_provider = new Memcached();
        if (!$this->cache_provider->addServer(MEMCACHED_HOST, MEMCACHED_PORT)) {
            echo 'Failed to add memcached host';
            return;
        }

        // only add cache entries if they haven't been added
        if (!file_exists(__DIR__ . '/cached.true')) {
            // we are going to refresh our cache, so set the special marker
            touch(__DIR__ . '/cached.true');
            foreach (DEFAULT_MEMCACHED_KEYS as $key => $value) {
                if (str_starts_with($value, 'file|')) {
                    $value = file_get_contents(substr_replace($value, '', 0, 5), true);
                }

                $this->cache_provider->set($key, $value);
            }
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

static $cache_provider = new CacheProvider();