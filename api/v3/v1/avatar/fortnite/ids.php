<?php
require_once 'database.php';

header('Content-Type: application/json');

$account_ids = explode(',', $_GET['accountIds']);

$sql_in_data = implode(',', array_map(function(string $accountId) { return "'$accountId'"; }, $account_ids));

$character_info = $database->raw(
    <<<EOL
    SELECT users.username, locker.favorite_character FROM locker INNER JOIN users ON locker.user_id=users.user_id WHERE locker.user_id IN (SELECT user_id FROM users WHERE username IN ($sql_in_data));
    EOL,
    true
);

$data = array_map(
    function (array $character_info) {
      return array(
        'accountId' => $character_info['username'],
        'namespace' => 'fortnite',
        'avatarId' => strtoupper($character_info['favorite_character'])
      );
    },
    $character_info
);

echo json_encode($data);