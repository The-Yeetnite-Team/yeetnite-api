<?php
require_once 'database.php';

header('Content-Type: application/json');

if (!$_GET['accountId']) {
    http_response_code(204);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
        $_POST = json_decode(file_get_contents('php://input'), true) ?? array();
    else parse_str(file_get_contents('php://input'), $_POST);
}

$created_last_login = $database->select(array('created', 'lastLogin'), 'users', "WHERE username = '{$_GET['accountId']}'")[0];

// Assuming $_GET['profileId'] === 'common_core'
echo json_encode(array(
        '_id' => $_GET['accountId'],
        'created' => $created_last_login['created'],
        'updated' => $created_last_login['lastLogin'],
        'rvn' => 1,
        'wipeNumber' => 1,
        'accountId' => $_GET['accountId'],
        'profileId' => 'common_core',
        'version' => 'yeetnite_v3',
        'items' => array(
            'Currency:MtxPurchased' => array(
                'attributes' => array(
                    'platform' => 'EpicPC'
                ),
                'quantity' => 0,
                'templateId' => 'Currency:MtxPurchased'
            )
        ),
        'stats' => array(
            'attributes' => array(
                'mtx_affiliate' => 'Yeetnite',
                'current_mtx_platform' => 'EpicPC',
                'mtx_purchase_history' => new stdClass()
            )
        ),
        'profileChanges' => array(
            array(
                'changeType' => 'statModified',
                'name' => 'current_mtx_platform',
                'value' => $_POST['newPlatform'] ?? 'EpicPC'
            )
        )
    )
);