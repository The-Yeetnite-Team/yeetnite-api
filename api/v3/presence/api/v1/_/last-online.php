<?php
require_once 'database.php';
require_once 'lib/friend_utils.php';

header('Content-Type: application/json');

$friends = $database->raw(
    <<<EOL
    SELECT FR.accountId, FR.ownerAccountId, U.lastLogin
    FROM users U
    INNER JOIN friendRequests FR
    ON (FR.accountId = U.username OR FR.ownerAccountId = U.username)
    WHERE FR.status = 'ACCEPTED' AND U.username != '{$_GET['accountId']}' AND (FR.accountId = '{$_GET['accountId']}' OR FR.ownerAccountId = '{$_GET['accountId']}');
    EOL,
    true
);

if (count($friends) === 0)
{
    echo '{}';
    return;
}

$data = array();

foreach ($friends as $friend) {
    $data[$friend['accountId'] === $_GET['accountId'] ? $friend['ownerAccountId'] : $friend['accountId']] = array(
        array( 'last_online' => $friend['lastLogin'] )
    );
}

echo json_encode($data);