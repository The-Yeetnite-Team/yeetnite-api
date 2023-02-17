<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

if (!isset($_GET['accountId'])) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(array(
        'success' => false,
        'reason' => 'Failed to provide a username'
    ));
    return;
}

if (isset($_GET['fileInfo'])) {
    header('Content-Type: application/json');

    // Try to find file info in cache
    $client_settings_info = $cache_provider->get("client_settings_file_info:{$_GET['accountId']}");
    if ($client_settings_info) {
        echo $client_settings_info;
        return;
    }

    $client_settings_info = $database->select(array('clientSettings', 'clientSettingsLastUpdated'), 'users', "WHERE username = '{$_GET['accountId']}'")[0];
    if (!$client_settings_info['clientSettings']) {
        echo '[]';
        return;
    }

    $client_settings = gzinflate(base64_decode($client_settings_info['clientSettings']));
    echo client_settings_file_info($client_settings, $client_settings_info['clientSettingsLastUpdated']);
} else {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'PUT':
            $client_settings = file_get_contents('php://input'); // raw version
            $client_settings_sav = base64_encode(gzdeflate($client_settings, 9)); // version stored in database
            $client_settings_last_updated = current_zulu_time();
            $database->update('users', array('clientSettings', 'clientSettingsLastUpdated'), array($client_settings_sav, $client_settings_last_updated), "WHERE username = '{$_GET['accountId']}'");
            // Update fileInfo cache
            $cache_provider->set("client_settings_file_info:{$_GET['accountId']}", client_settings_file_info($client_settings, $client_settings_last_updated));
            $cache_provider->set("raw_client_settings:{$_GET['accountId']}", $client_settings);
            http_response_code(204);
            break;
        case 'GET':
            header('Content-Type: application/octet-stream');

            // Try to find client settings in cache
            $client_settings = $cache_provider->get("raw_client_settings:{$_GET['accountId']}");
            if ($client_settings) {
                echo $client_settings;
                return;
            }

            // Client settings weren't found in cache
            $client_settings_info = $database->select(array('clientSettings'), 'users', "WHERE username = '{$_GET['accountId']}'");
            if (!$client_settings_info) {
                http_response_code(204);
                return;
            }
            echo gzinflate(base64_decode($client_settings_info[0]['clientSettings']));
            break;
        default:
            http_response_code(400);
            echo json_encode(array(
               'success' => false,
               'reason' => "Invalid request method of {$_SERVER['REQUEST_METHOD']}"
            ));
            break;
    }
}

function client_settings_file_info($client_settings, $last_updated): string {
    return json_encode(array(
        "uniqueFilename" => "ClientSettings.Sav",
        "fileName" => "ClientSettings.Sav",
        "hash" => hash('sha1', $client_settings),
        "hash256" => hash('sha256', $client_settings),
        "length" => strlen($client_settings),
        "contentType" => "application/octet-stream",
        "uploaded" => $last_updated,
        "storageType" => "S3",
        "doNotCache" => true
    ));
}