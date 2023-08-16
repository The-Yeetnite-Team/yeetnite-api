<?php
require_once 'api-config.php';
require_once 'cache_provider.php';

// update the friends cache of two users using update_friend_cache()
function update_friend_list_caches(string $user1, string $user2, CacheProvider $cache_provider, Database $database): void
{
    update_friend_list_cache($user1, $cache_provider, $database);
    update_friend_list_cache($user2, $cache_provider, $database);
}

// update the friends cache of a user
function update_friend_list_cache(string $user, CacheProvider $cache_provider, Database $database): void
{
    $friend_requests = $database->select(
        array(
            'accountId',
            'ownerAccountId',
            'status',
            'created',
            'favorite'
        ),
        'friendRequests',
        "WHERE (accountId = '$user') OR (ownerAccountId = '$user')"
    ) ?? array();

    $friends_list = array();

    if (count($friend_requests) > 0) {
        $accountIds = array_map(function () use ($user) {
            return $user;
        }, range(0, count($friend_requests) - 1));
        $friends_list = array_map('friend_list', $friend_requests, $accountIds);
    }

    $cache_provider->set(
        "friend_list:$user",
        json_encode($friends_list)
    );
}

function user_pair_exists(string $user1, string $user2, Database $database): bool
{
    return count($database->select(array('user_id'), 'users', "WHERE username IN ('$user1', '$user2')")) === 2;
}

function friends_of(string $user, Database $database): array
{
    return $database->select(
        array('accountId', 'ownerAccountId', 'created', 'favorite'),
        'friendRequests',
        <<<EOL
        WHERE (accountId = '$user' OR ownerAccountId = '$user') AND status = 'ACCEPTED'
        EOL
    );
}

function incoming_friend_requests_of(string $user, Database $database): array
{
    return $database->select(array('ownerAccountId', 'created'), 'friendRequests', "WHERE accountId = '$user' AND status = 'PENDING'") ?? array();
}

function outgoing_friend_requests_of(string $user, Database $database): array
{
    return $database->select(array('accountId', 'created'), 'friendRequests', "WHERE ownerAccountId = '$user' AND status = 'PENDING'") ?? array();
}

function unfriend(string $user1, string $user2, Database $database, CacheProvider $cache_provider): void
{
    $to_delete_request = get_friend_request($user1, $user2, $database);
    if (!$to_delete_request) return;

    $to_delete_id = $to_delete_request[0]['friendRequest_id'];
    $database->delete('friendRequests', "friendRequest_id = $to_delete_id");

    update_xmpp_friendship($user1, $user2, false);

    update_friend_list_caches($user1, $user2, $cache_provider, $database);
}

function update_xmpp_friendship(string $user1, string $user2, bool $adding): void
{
    for ($i = 0; $i < 2; $i++) {
        if ($i == 1)
        {
            $temp1 = $user1;
            $user1 = $user2;
            $user2 = $temp1;
        }

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
                        "value" => $adding ? "update" : "remove"
                    ),
                    array(
                        "var" => "roster-buddy-list",
                        "value" => "$user2@xmpp.yeetnite.ml"
                    )
                )
            )
        );

        $bodies[] = $body;
    }

    $header_keys = array("Content-Type", "Authorization");
    $header_values = array("application/json", TIGASE_HTTP_AUTHORIZATION);
    tigase_multi_web_request(TIGASE_HOST . '/rest/adhoc/sess-man@xmpp.yeetnite.ml?api-key=' . TIGASE_API_KEY, $bodies, $header_keys, $header_values);
}

function tigase_multi_web_request(string $url, array $bodies, array $header_keys, array $header_values): void
{
    $ch1 = curl_init($url);
    $ch2 = curl_init($url);

    $headers = array_map('assoc_header_to_http', $header_keys, $header_values);

    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch1, CURLOPT_POST, 1);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($bodies[0]));
    curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch2, CURLOPT_POST, 1);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($bodies[1]));
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);

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

function get_friend_request($account_id_1, $account_id_2, $database)
{
    $condition = <<<EOL
    WHERE (accountId = '$account_id_1' AND ownerAccountId = '$account_id_2')
    OR (accountId = '$account_id_2' AND ownerAccountId = '$account_id_1')
    EOL;
    return $database->select(array('accountId', 'ownerAccountId', 'friendRequest_id', 'status'), 'friendRequests', $condition);
}

function get_block_list(string $username, Database $database): string
{
    return $database->select(array('blockList'), 'users', "WHERE username = '$username'")[0]['blockList'];
}

function update_block_list(string $username, string $blockList, Database $database): void
{
    $database->update('users', array('blockList'), array($blockList), "WHERE username = '$username'");
}

function assoc_header_to_http($key, $value): string
{
    return "$key: $value";
}

function friend_list(array $friend_request, string $accountId): array
{
    return array(
        'accountId' => ($friend_request['accountId'] === $accountId) ? $friend_request['ownerAccountId'] : $friend_request['accountId'],
        'status' => $friend_request['status'],
        'direction' => ($friend_request['ownerAccountId'] === $accountId) ? 'OUTBOUND' : 'INBOUND',
        'created' => $friend_request['created'],
        'favorite' => $friend_request['favorite'] === 1,
    );
}