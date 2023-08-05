<?php
require_once 'database.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');
// no opportunity for caching
header('X-Litespeed-Cache-Control: no-store');

$session_info = $database->select(
    array(
        'ownerId',
        'ownerName',
        'serverName',
        'serverAddress',
        'serverPort',
        'maxPublicPlayers',
        'openPublicPlayers',
        'attributes',
        'publicPlayers'
    ),
    'matchmaking_sessions',
    "WHERE id_string = '{$_GET['sessionId']}'"
)[0];

if (!$session_info) {
    echo '{"success":false,"reason":"Invalid session ID"}';
    return;
}

echo json_encode(array(
    'id' => $_GET['sessionId'],
    'ownerId' => $session_info['ownerId'],
    'ownerName' => $session_info['ownerName'],
    'serverName' => $session_info['serverName'],
    'serverAddress' => $session_info['serverAddress'],
    'serverPort' => $session_info['serverPort'],
    'maxPublicPlayers' => $session_info['maxPublicPlayers'],
    'openPublicPlayers' => $session_info['openPublicPlayers'],
    'maxPrivatePlayers' => 0,
    'openPrivatePlayers' => 0,
    'attributes' => json_decode($session_info['attributes'], true),
    'publicPlayers' => json_decode($session_info['publicPlayers'], true),
    'privatePlayers' => [],
    'totalPlayers' => $session_info['maxPublicPlayers'] - $session_info['openPublicPlayers'],
    'allowJoinInProgress' => false,
    'shouldAdvertise' => false,
    'isDedicated' => false,
    'usesStats' => false,
    'allowInvites' => false,
    'usesPresence' => false,
    'allowJoinViaPresence' => true,
    'allowJoinViaPresenceFriendsOnly' => false,
    'buildUniqueId' => $_COOKIE['currentbuildUniqueId'] ?? '0', // buildUniqueId is different for every build, this uses the netver of the version you're currently using
    'lastUpdated' => current_zulu_time(),
    'started' => false
));