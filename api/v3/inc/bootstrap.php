<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
// include main configuration file 
require_once PROJECT_ROOT_PATH . "/inc/config.php";
require_once PROJECT_ROOT_PATH . "/inc/database.php";
require_once PROJECT_ROOT_PATH . "/inc/cache_provider.php";

$database = new Database();
$cache_provider = new CacheProvider();