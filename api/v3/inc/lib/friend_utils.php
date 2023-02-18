<?php
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
        $accountIds = array_map(function() use ($user) {return $user;}, range(0, count($friend_requests) - 1));
        $friends_list = array_map('friend_list', $friend_requests, $accountIds);
    }

    $cache_provider->set(
        "friend_list:$user",
        json_encode($friends_list)
    );
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