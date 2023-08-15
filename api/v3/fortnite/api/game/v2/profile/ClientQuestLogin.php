<?php
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

switch($_GET['profileId']) {
    case 'athena':
        echo json_encode(array(
            'accountId' => $_GET['accountId'],
            'displayName' => $_GET['accountId']
        ));
        break;
    case 'campaign':
        define('RVN', intval($_GET['rvn']));
        echo json_encode(array(
            'profileRevision' => 17306,
            'profileId' => 'campaign',
            'profileChangesBaseRevision' => 17305,
            'profileChanges' => [],
            'profileCommandRevision' => RVN + 1,
            'serverTime'=> current_zulu_time(),
            'responseVersion' => 1
        ));
        break;
}