<?php
require_once 'lib/date_utils.php';
header('Content-Type: application/json');

// since this is used for login, it's likely important to have up to date time
header('X-Litespeed-Cache-Control: no-store');

// Assuming $_GET['profileId'] === 'profile0'

$RVN = intval($_GET['rvn']);
echo json_encode(array(
    'profileRevision' => $RVN,
    'profileId' => 'profile0',
    'profileChangesBaseRevision' => $RVN,
    'profileChanges' => [],
    'profileCommandRevision' => 0,
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));