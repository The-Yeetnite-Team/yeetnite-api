<?php
require_once 'lib/date_utils.php';
header('Content-Type: application/json');

// Assuming $_GET['profileId'] === 'profile0'

// normally this would be a cacheable endpoint, but Fortnite likely wants up-to-date serverTime
header('X-Litespeed-Cache-Control: no-store');
define('RVN', intval($_GET['rvn']));
echo json_encode(array(
    'profileRevision' => RVN,
    'profileId' => 'profile0',
    'profileChangesBaseRevision' => RVN,
    'profileChanges' => [],
    'profileCommandRevision' => 0,
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));