<?php
require_once 'database.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');
// no opportunity for caching
header('X-Litespeed-Cache-Control: no-store');

if (str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
    $_POST = json_decode(file_get_contents('php://input'), true) ?? array();
else parse_str(file_get_contents('php://input'), $_POST);

define('RVN', intval($_GET['rvn']));

$database->update('locker', array('banner_icon', 'banner_color'), array($_POST['homebaseBannerIconId'], $_POST['homebaseBannerColorId']), "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')");

header("X-LiteSpeed-Purge: private, tag=fullAccountInfo/{$_GET['accountId']}");

echo json_encode(array(
    'profileRevision' => RVN + 1,
    'profileId' => 'athena',
    'profileChangesBaseRevision' => RVN,
    'profileChanges' => array(
        array(
            'changeType' => 'statModified',
            'name' => 'banner_icon',
            'value' => $_POST['homebaseBannerIconId']
        ),
        array(
            'changeType' => 'statModified',
            'name' => 'banner_color',
            'value' => $_POST['homebaseBannerColorId']
        )
    ),
    'profileCommandRevision' => 2638,
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));