<?php
require_once 'database.php';
require_once 'cache_provider.php';

header('Content-Type: application/json');

// a user could change their password, we don't want to cache the old password
header('X-Litespeed-Cache-Control: no-store');

if (str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
    $_POST = json_decode(file_get_contents('php://input'), true) ?? array();
else parse_str(file_get_contents('php://input'), $_POST);

switch ($_POST['grant_type']) {
    // user logged in through the in-game login screen
    case 'password':
        $user_data = $database->select(array('password', 'accessToken'), 'users', "WHERE username='{$_POST['username']}'");

        // User failed authentication
        if (!$user_data || !password_verify($_POST['password'], $user_data[0]['password'])) {
            echo '{"success":false,"reason":"Invalid username or password"}';
            return;
        }

        // Username and password are valid
        echo generate_token_data($_POST['username'], $user_data[0]['accessToken']);
        break;
    // User used the auto-login feature in the launcher or a token through launch arguments
    case 'external_auth':
        $user_data = $database->select(array('username'), 'users', "WHERE accessToken='{$_POST['external_auth_token']}'");

        // The Auth Token the client supplied doesn't exist
        if (!$user_data) {
            echo '{"success":false,"reason":"Invalid Auth Token during automatic login"}';
            return;
        }

        // The Auth Token the client supplied is linked to a valid user
        echo generate_token_data($user_data[0]['username'], $_POST['external_auth_token']);
        break;
    default:
        echo generate_token_data('Yeetnite', substr(str_shuffle(MD5(microtime())), 0, 16));
        break;
}

// Generate user's token data
function generate_token_data(string $username, string $accessToken): string
{
    return json_encode(
        array(
            'access_token' => $accessToken,
            'expires_in' => 28800,
            'expires_at' => '9999-12-02T01:12:00Z',
            'token_type' => 'bearer',
            'refresh_token' => $accessToken,
            'refresh_expires' => 28800,
            'refresh_expires_at' => '9999-12-02T01:12:00Z',
            'account_id' => $username,
            'client_id' => 'yeetnite-client',
            'internal_client' => true,
            'client_service' => 'fortnite',
            'device_id' => 'yeetnitedeviceidlol',
            'app' => 'fortnite',
            'in_app_id' => $username
        )
    );
}