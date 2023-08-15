<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/friend_utils.php';

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        header("X-LiteSpeed-Tag: blockList/{$_GET['accountId']}");
        $blockList = get_block_list($_GET['accountId'], $database);
        echo "{\"blockedUsers\":$blockList}";
        break;
    case 'POST':
        // no opportunity to cache
        header('X-Litespeed-Cache-Control: no-store');
        header("X-LiteSpeed-Purge: private, tag=blockList/{$_GET['accountId']}");

        // block user
        $blockList = json_decode(get_block_list($_GET['accountId'], $database), true);
        // we don't want duplicates
        if (!in_array($_GET['blocking'], $blockList)) {
            $blockList[] = $_GET['blocking'];
        }
        update_block_list($_GET['accountId'], json_encode($blockList), $database);
        unfriend($_GET['accountId'], $_GET['blocking'], $database, $cache_provider);
        break;
    case 'DELETE':
        // no opportunity to cache
        header('X-Litespeed-Cache-Control: no-store');
        header("X-LiteSpeed-Purge: private, tag=blockList/{$_GET['accountId']}");

        // unblock user
        $blockList = json_decode(get_block_list($_GET['accountId'], $database), true);
        unset($blockList[array_search($_GET['blocking'], $blockList)]);
        update_block_list($_GET['accountId'], json_encode($blockList), $database);
        http_response_code(204);
        break;
}
