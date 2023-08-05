<?php
require_once 'database.php';
require_once 'cache_provider.php';

header('Content-Type: application/json');

if (!isset($_GET['username']) || !isset($_GET['password'])) {
    echo json_encode(array('success' => false, 'reason' => 'We have received invalid data and are unable to receive your request'));
    return;
}

$username = $_GET['username'];
$user_info = $database->select(array('password', 'accessToken'), 'users', "WHERE username='$username'");

if (!$user_info || !password_verify($_GET['password'], $user_info[0]['password'])) {
    echo json_encode(array('success' => false, 'reason' => 'Invalid username or password'));
    return;
}

echo json_encode(
    array(
        'success' => true,
        'username' => $username,
        'accessToken' => $user_info[0]['accessToken']
    )
);