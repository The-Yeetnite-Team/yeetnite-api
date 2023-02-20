<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/season_utils.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

if (isset($_GET['fullAccountInfo'])) {
    $current_zulu_time = current_zulu_time();

    // huge time savings if we find user data in cache
    $user_data = $cache_provider->get("full_user_data:{$_GET["accountId"]}");
    if ($user_data) {
        // Update user's last login time and return cached data
        echo strtr($user_data, array('"lastLogin":""' => "\"lastLogin\":\"$current_zulu_time\""));
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
    $data = array(
        'id' => $_GET['accountId'],
        'displayName' => $_GET['accountId'],
        'name' => 'placeholder',
        'lastName' => 'placeholder',
        'email' => $user_data['email'],
        'failedLoginAttempts' => 0,
        'lastLogin' => $current_zulu_time,
        'numberOfDisplayNameChanges' => 0,
        'ageGroup' => 'ADULT',
        'headless' => false,
        'country' => 'US',
        'preferredLanguage' => $user_data['preferredLanguage'],
        'tfaEnabled' => false,
        'canUpdateDisplayName' => false,
        'emailVerified' => true,
        'minorVerified' => false,
        'minorExpected' => false,
        'minorStatus' => 'NOT_MINOR',
        'cabinedMode' => false
    );
    echo json_encode($data);
    $data['lastLogin'] = ''; // faster to replace empty string in cache than using preg_replace()
    $cache_provider->set("full_user_data:{$_GET['accountId']}", json_encode($data));
} else {
    if (substr_count($_SERVER['QUERY_STRING'], 'accountId') > 1) {
        $version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
        $usernames = explode('&', strtr($_SERVER['QUERY_STRING'], array('accountId=' => '')));
        if ($version_info['season'] < 6) {
            echo json_encode(
                array(array(
                    'id' => $usernames,
                    'displayName' => $usernames,
                    'minorVerified' => false,
                    'externalAuths' => new stdClass(),
                    'minorStatus' => 'NOT_MINOR',
                    'cabinedMode' => false
                ))
            );
        } else {
            echo '[' . implode(',', array_map('generate_minimal_account_info', $usernames)) . ']';
        }
    } else {
        echo generate_minimal_account_info($_GET['accountId']);
    }
}

function generate_minimal_account_info(string $username): string
{
    return json_encode(
        array(
            'id' => $username,
            'displayName' => $username,
            'minorVerified' => false,
            'externalAuths' => new stdClass(),
            'minorStatus' => 'NOT_MINOR',
            'cabinedMode' => false
        )
    );
}