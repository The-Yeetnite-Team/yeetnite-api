<?php
define('DB_HOST', getenv('TEST_DB_HOST'));
define('DB_USERNAME', getenv('TEST_DB_USERNAME'));
define('DB_PASSWORD', getenv('TEST_DB_PASSWORD'));
define('DB_NAME', 'yeetnite_test');
define('MEMCACHED_HOST', 'memcached');
define('MEMCACHED_PORT', 11211);
define('TIGASE_API_KEY', getenv('TIGASE_API_KEY'));
define('TIGASE_HTTP_AUTHORIZATION', getenv('TIGASE_HTTP_AUTHORIZATION'));