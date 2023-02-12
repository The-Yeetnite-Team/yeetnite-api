<?php
require_once 'bootstrap.php';

header('Content-Type: application/json');

$_POST = json_decode(file_get_contents('php://input'), true) ?? array();

// Client wants temporary token (pre-login)
if (!isset($_POST['grant_type'])) {
    echo json_encode(
        array(
            'access_token' => substr(str_shuffle(MD5(microtime())), 0, 16),
            'client_id' => 'yeetniteclientlol',
            'client_service' => 'fortnite',
            'expires_at' => '9999-12-02T01:12:00Z',
            'expires_in' => 28800,
            'internal_client' => true,
            'token_type' => 'bearer'
        )
    );
}
// User logged in through the in-game login screen
else if ($_POST['grant_type'] === 'password' && isset($_POST['username']) && isset($_POST['password']) && $_POST['token_type'] === 'eg1') {
    $user_data = $database->select(array('password', 'accessToken'), 'users', "WHERE username='{$_POST['username']}'");

    // User failed authentication
    if (!$user_data || !password_verify($_POST['password'], $user_data[0]['password'])) {
        echo json_encode(
            array(
                'success' => false,
                'reason' => 'Invalid username or password'
            )
        );
        return;
    }

    // Username and password are valid
    generate_token_data($_POST['username'], $user_data[0]['accessToken']);
}
// User used the auto-login feature in the launcher or a token through launch arguments
else if ($_POST['grant_type'] === 'external_auth' && isset($_POST['external_auth_token']) && $_POST['token_type'] === 'eg1') {
    $user_data = $database->select(array('username'), 'users', "WHERE accessToken='{$_POST['external_auth_token']}'");

    // The Auth Token the client supplied doesn't exist
    if (!$user_data) {
        echo json_encode(
            array(
                'success' => false,
                'reason' => 'Invalid Auth Token during automatic login'
            )
        );
        return;
    }

    // The Auth Token the client supplied is linked to a valid user
    generate_token_data($user_data[0]['username'], $_POST['external_auth_token']);
}

// Print out the user's token data
function generate_token_data(string $username, string $accessToken) {
    echo json_encode(
        array(
            'access_token' => $accessToken,
            'expires_in' => 28800,
            'expires_at' => '9999-12-02T01:12:00Z',
            'token_type' => 'bearer',
            'refresh_token' => $accessToken,
            'refresh_expires' => 28800,
            'refresh_expires_at' => '9999-12-02T01:12:00Z',
            'account_id' => $username,
            'client_id' => 'yeetniteclientlol',
            'internal_client' => true,
            'client_service' => 'fortnite',
            'device_id' => 'yeetnitedeviceidlol',
            'app' => 'fortnite',
            'in_app_id' => $username
        )
    );
}