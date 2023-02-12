<?php
require_once 'bootstrap.php';

// we need this for Carbon
set_include_path($_SERVER['DOCUMENT_ROOT']);
require 'vendor/autoload.php';

use Carbon\Carbon;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pending requests likely means that user wants to accept a friend request
    $pendingFriendRequest = find_existing_friend_request($_GET['account_id_1'], $_GET['account_id_2'], $database);

    // User is sending a friend request
    if (!$pendingFriendRequest) {
        $created = Carbon::now()->toIso8601ZuluString('millisecond');

        $database->insert('friendRequests', array('ownerAccountId', 'accountId', 'created'), array("'{$_GET['account_id_1']}'", "'{$_GET['account_id_2']}'", "'$created'"));
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
        echo $cache_provider->get('friend_request_error_self_accept');
        return;
    }

    // User is accepting a friend request
    $database->update('friendRequests', array('status'), array('ACCEPTED'), "WHERE friendRequest_id = {$pendingFriendRequest['friendRequest_id']}");

    // Add each other to the XMPP roster
    $bodies = array();
    for ($i = 0; $i < 2; $i++) {
        $user1 = ($i == 0) ? $_GET['account_id_1'] : $_GET['account_id_2'];
        $user2 = ($i == 0) ? $_GET['account_id_2'] : $_GET['account_id_1'];

        $body = array(
            "command" => array(
                "node" => "roster-fixer",
                "fields" => array(
                    array(
                        "var" => "roster-owner-jid",
                        "value" => "$user1@xmpp.yeetnite.ml" // ${i == 0 ? req.query.accountId1 : req.query.accountId2}
                    ),
                    array(
                        "var" => "roster-action",
                        "value" => "update"
                    ),
                    array(
                        "var" => "roster-buddy-list",
                        "value" => "$user2@xmpp.yeetnite.ml"
                    )
                )
            )
        );
        array_push($bodies, $body);
    }

    $header_keys = array("Content-Type", "Authorization");
    $header_values = array("application/json", TIGASE_HTTP_AUTHORIZATION);
    tigase_multi_web_request('https://xmpp.yeetnite.ml:1443/rest/adhoc/sess-man@xmpp.yeetnite.ml?api-key=' . TIGASE_API_KEY, $bodies, $header_keys, $header_values);

    http_response_code(204);
} else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $to_delete_id = find_existing_friend_request($_GET['account_id_1'], $_GET['account_id_2'], $database)[0]['friendRequest_id'];
    $database->delete('friendRequests', "friendRequest_id = $to_delete_id");

    $bodies = array();

    // Remove friend from XMPP roster
    for ($i = 0; $i < 2; $i++) {
        $user1 = ($i == 0) ? $_GET['account_id_1'] : $_GET['account_id_2'];
        $user2 = ($i == 0) ? $_GET['account_id_2'] : $_GET['account_id_1'];

        $body = array(
            "command" => array(
                "node" => "roster-fixer",
                "fields" => array(
                    array(
                        "var" => "roster-owner-jid",
                        "value" => "$user1@xmpp.yeetnite.ml"
                    ),
                    array(
                        "var" => "roster-action",
                        "value" => "remove"
                    ),
                    array(
                        "var" => "roster-buddy-list",
                        "value" => "$user2@xmpp.yeetnite.ml"
                    )
                )
            )
        );

        array_push($bodies, $body);
    }

    $header_keys = array("Content-Type", "Authorization");
    $header_values = array("application/json", TIGASE_HTTP_AUTHORIZATION);
    tigase_multi_web_request('https://xmpp.yeetnite.ml:1443/rest/adhoc/sess-man@xmpp.yeetnite.ml?api-key=' . TIGASE_API_KEY, $bodies, $header_keys, $header_values);
    http_response_code(204);
}

function find_existing_friend_request($account_id_1, $account_id_2, $database)
{
    $condition = <<<EOL
    WHERE (accountId = '$account_id_1' AND ownerAccountId = '$account_id_2')
    OR (accountId = '$account_id_2' AND ownerAccountId = '$account_id_1')
    EOL;
    return $database->select(array('accountId', 'ownerAccountId', 'friendRequest_id', 'status'), 'friendRequests', $condition);
}

function tigase_multi_web_request(string $url, array $bodies, array $header_keys, array $header_values)
{
    $ch1 = curl_init($url);
    $ch2 = curl_init($url);

    $headers = array_map('assoc_header_to_http', $header_keys, $header_values);

    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch1, CURLOPT_POST, 1);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($bodies[0]));
    curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch2, CURLOPT_POST, 1);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($bodies[1]));
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

    $mh = curl_multi_init();
    curl_multi_add_handle($mh, $ch1);
    curl_multi_add_handle($mh, $ch2);

    // execute all queries simultaneously and continue when all are complete
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running);

    curl_multi_remove_handle($mh, $ch1);
    curl_multi_remove_handle($mh, $ch2);
    curl_multi_close($mh);
}

function assoc_header_to_http($key, $value)
{
    return "$key: $value";
}
