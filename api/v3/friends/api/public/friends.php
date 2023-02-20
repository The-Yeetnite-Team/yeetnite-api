<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/friend_utils.php';

header('Content-Type: application/json');

$friends_list_cache = $cache_provider->get("friend_list:{$_GET['accountId']}");

if ($friends_list_cache) {
    echo $friends_list_cache;
    return;
}

update_friend_list_cache($_GET['accountId'], $cache_provider, $database);

echo $cache_provider->get("friend_list:{$_GET['accountId']}");