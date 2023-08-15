<?php
require_once 'database.php';
require_once 'lib/friend_utils.php';

header('Content-Type: application/json');

$friends = friends_of($_GET['accountId'], $database);
$incoming_friend_requests = incoming_friend_requests_of($_GET['accountId'], $database);
$outgoing_friend_requests = outgoing_friend_requests_of($_GET['accountId'], $database);
$blocklist = json_decode(get_block_list($_GET['accountId'], $database), true);

$data = array();

$data['friends'] = array_map(
    function ($accepted_request)
    {
        return array(
            'accountId' => $accepted_request['accountId'] === $_GET['accountId'] ? $accepted_request['ownerAccountId'] : $accepted_request['accountId'],
            'groups' => [],
            'mutual' => 0,
            'alias' => '',
            'note' => '',
            'favorite' => $accepted_request['favorite'] === 1,
            'created' => $accepted_request['created']
        );
    },
    $friends
);

$data['incoming'] = array_map(
    function (array $incoming_request)
    {
        return array(
            'accountId' => $incoming_request['ownerAccountId'],
            'mutual' => 0,
            'favorite' => false,
            'created' => $incoming_request['created']
        );
    },
    $incoming_friend_requests
);

$data['outgoing'] = array_map(
    function (array $outgoing_request)
    {
        return array(
            'accountId' => $outgoing_request['accountId'],
            'mutual' => 0,
            'favorite' => false,
            'created' => $outgoing_request['created']
        );
    },
    $outgoing_friend_requests
);

$data['blocklist'] = array_map(
    function (string $blocked_user)
    {
        return array('accountId' => $blocked_user);
    },
    $blocklist
);

$data['suggested'] = [];

$data['settings'] = array(
    'acceptInvites' => 'public',
    'mutualPrivacy' => 'NONE'
);

$data['limitsReached'] = array(
    'incoming' => false,
    'outgoing' => false,
    'accepted' => false
);

echo json_encode($data);