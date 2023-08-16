<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';
require_once 'lib/friend_utils.php';

header('Content-Type: application/json');

// no potential for cache to exist when managing friend requests
header('X-Litespeed-Cache-Control: no-store');

if ($_GET['account_id_1'] === $_GET['account_id_2']) {
    echo '{"success":false,"reason":"You can\'t execute a friend request on yourself"}';
    http_response_code(400);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // One or more users don't exist
    if (!user_pair_exists($_GET['account_id_1'], $_GET['account_id_2'], $database)) {
        echo '{"success":false,"reason":"One or more specified users don\'t exist"}';
        http_response_code(400);
        return;
    }

    // Pending requests likely means that user wants to accept a friend request
    $pendingFriendRequest = get_friend_request($_GET['account_id_1'], $_GET['account_id_2'], $database);

    // User is sending a friend request
    if (!$pendingFriendRequest) {
        $created = current_zulu_time();

        $database->insert('friendRequests', array('ownerAccountId', 'accountId', 'created'), array("'{$_GET['account_id_1']}'", "'{$_GET['account_id_2']}'", "'$created'"));
        update_friend_list_caches($_GET['account_id_1'], $_GET['account_id_2'], $cache_provider, $database);

        header("X-LiteSpeed-Purge: private, tag=friendsList/{$_GET['account_id_1']}, tag=friendsList/{$_GET['account_id_2']}");

        http_response_code(204);
        return;
    }

    $pendingFriendRequest = $pendingFriendRequest[0];

    // Nothing to do
    if ($pendingFriendRequest['status'] === 'ACCEPTED') {
        http_response_code(204);
        return;
    }

    // User can't accept their own friend request
    if ($pendingFriendRequest['ownerAccountId'] === $_GET['account_id_1']) {
        http_response_code(400);
        echo '{"success":false,"reason":"You can\'t accept your own friend request"}';
        return;
    }

    // User is accepting a friend request
    $database->update('friendRequests', array('status'), array('ACCEPTED'), "WHERE friendRequest_id = {$pendingFriendRequest['friendRequest_id']}");

    header("X-LiteSpeed-Purge: private, tag=friendsList/{$_GET['account_id_1']}, tag=friendsList/{$_GET['account_id_2']}");

    // Add each other to the XMPP roster
    update_xmpp_friendship($_GET['account_id_1'], $_GET['account_id_2'], true);
    update_friend_list_caches($_GET['account_id_1'], $_GET['account_id_2'], $cache_provider, $database);

    http_response_code(204);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    header("X-LiteSpeed-Purge: private, tag=friendsList/{$_GET['account_id_1']}, tag=friendsList/{$_GET['account_id_2']}");

    unfriend($_GET['account_id_1'], $_GET['account_id_2'], $database, $cache_provider);
    update_friend_list_caches($_GET['account_id_1'], $_GET['account_id_2'], $cache_provider, $database);

    http_response_code(204);
}
