<?php
require_once 'bootstrap.php';
require_once 'lib/season_utils.php';

// we need this for Carbon
set_include_path($_SERVER['DOCUMENT_ROOT']);
require 'vendor/autoload.php';

use Carbon\Carbon;

header('Content-Type: application/json');

if (isset($_GET['fullAccountInfo'])) {
    $current_zulu_time = Carbon::now()->toIso8601ZuluString('millisecond');

    // huge time savings if we find user data in cache
    $user_data = $cache_provider->get("full_user_data:{$_GET["accountId"]}");
    if ($user_data) {
        // Update user's last login time and return cached data
        $user_data = preg_replace('/[0-9]+-[0-9]+-[0-9]+T[0-9]+:[0-9]+:[0-9]+\.[0-9]+Z/i', $current_zulu_time, $user_data);
        echo $user_data;
        return;
    }

    // the user is not in our cache yet, continuing...
    $user_data = $database->select(array('lastLogin', 'email', 'username', 'preferredLanguage'), 'users', "WHERE username='{$_GET['accountId']}'");

    if (!$user_data) {
        echo json_encode(
            array(
                'success' => false,
                'reason' => 'Account not found'
            )
        );
        http_response_code(400);
        return;
    }


    if (!$database->update('users', array('lastLogin'), array($current_zulu_time), "WHERE username='{$_GET['accountId']}'")) {
        echo 'Failed to update last login time';
        exit(1);
    }

    $user_data = $user_data[0];
    $data = json_encode(
        array(
            'id' => $_GET['accountId'],
            'displayName' => $_GET['accountId'],
            'name' => 'placeholder',
            'lastName' => 'placeholder',
            'email' => $user_data['email'],
            'failedLoginAttempts' => 0,
            'lastLogin' => $current_zulu_time,
            'numberOfDisplayNameChanges' => 0,
            'ageGroup' => 'UNKNOWN',
            'headless' => false,
            'country' => 'US',
            'preferredLanguage' => $user_data['preferredLanguage'],
            'tfaEnabled' => false
        )
    );
    $cache_provider->set("full_user_data:{$_GET["accountId"]}", $data);
    echo $data;
} else {
    if (substr_count($_SERVER['QUERY_STRING'], 'accountId') > 1) {
        $version_info = fortnite_version_info($_SERVER["HTTP_USER_AGENT"]);
        $usernames = explode('&', strtr($_SERVER['QUERY_STRING'], array('accountId=' => '')));
        if ($version_info['season'] < 6) {
            echo json_encode(
                array(array(
                    "id" => $usernames,
                    "displayName" => $usernames,
                    "externalAuths" => new stdClass()
                ))
            );
        } else {
            echo '[' . implode(',', array_map('generate_minimal_account_info', $usernames)) . ']';
        }
    } else {
        echo generate_minimal_account_info($_GET['accountId']);
    }
}

function generate_minimal_account_info(string $username)
{
    return json_encode(
        array(
            'id' => $username,
            'displayName' => $username,
            'externalAuths' => new stdClass()
        )
    );
}