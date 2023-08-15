<?php
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

// Assuming $_GET['profileId'] === 'profile0'
echo json_encode(array(
    'profileRevision' => intval($_GET['rvn']),
    'profileId' => 'profile0',
    'profileChangesBaseRevision' => 498,
    'profileChanges' => [],
    'profileCommandRevision' => 281,
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));