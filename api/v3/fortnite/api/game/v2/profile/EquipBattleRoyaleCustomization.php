<?php
/** @noinspection DuplicatedCode */
require_once 'database.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

// no opportunity for caching
header('X-Litespeed-Cache-Control: no-store');

if (str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
    $_POST = json_decode(file_get_contents('php://input'), true) ?? array();
else parse_str(file_get_contents('php://input'), $_POST);

$PROFILE_REVISION = intval($_GET['rvn']);
$LOCKER_ITEM_NAME = strtolower($_POST['slotName']);

const INDEXED_ITEMS_LIST = array('itemwrap', 'dance');
$IS_INDEXED_ITEM = in_array($LOCKER_ITEM_NAME, INDEXED_ITEMS_LIST);

$profile_changes = $_POST['itemToSlot'];

if ($IS_INDEXED_ITEM) {
    $current_locker_item = json_decode($database->select(array('favorite_' . $LOCKER_ITEM_NAME), 'locker',
        "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')")[0]['favorite_' . $LOCKER_ITEM_NAME], true);
    $current_locker_item[intval($_POST['indexWithinSlot'])] = $_POST['itemToSlot'];
    $profile_changes = json_encode($current_locker_item);
}

$database->update('locker', array('favorite_' . $LOCKER_ITEM_NAME), array($profile_changes),
    "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')");

header("X-LiteSpeed-Purge: private, tag=fullAccountInfo/{$_GET['accountId']}, tag=queryProfileAthena/{$_GET['accountId']}");

echo json_encode(array(
    'profileRevision' => $PROFILE_REVISION + 1,
    'profileId' => 'athena',
    'profileChangesBaseRevision' => $PROFILE_REVISION,
    'profileChanges' => array(
        array(
            'changeType' => 'statModified',
            'name' => 'favorite_' . $LOCKER_ITEM_NAME,
            'value' => json_decode($profile_changes, true),
        )
    ),
    'profileCommandRevision' => 6895, // NOTE: this is really `athena.profileCommandRevision` (the athena profile)
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));