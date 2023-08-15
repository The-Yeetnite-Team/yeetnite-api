<?php
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

echo json_encode(array(
    'lastUpdated' => current_zulu_time(),
    'disableV2' => true,
    'isAuthenticated' => true,
    'enumerateFilesPath' => '/api/cloudstorage/user',
    'enableMigration' => false,
    'enableWrites' => true,
    'epicAppName' => 'Live',
    'transports' => json_decode($cache_provider->get('fortnite_api_cloudstorage_transports'), true)
));