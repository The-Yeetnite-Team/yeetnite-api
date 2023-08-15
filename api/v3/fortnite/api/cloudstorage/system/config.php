<?php
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

echo json_encode(array(
    'lastUpdated' => current_zulu_time(),
    'disableV2' => false,
    'isAuthenticated' => true,
    'enumerateFilesPath' => '/api/cloudstorage/system',
    'enableMigration' => false,
    'enableWrites' => false,
    'epicAppName' => 'Live',
    'transports' => json_decode($cache_provider->get('fortnite_api_cloudstorage_transports'), true)
));
