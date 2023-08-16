<?php
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

$RVN = intval($_GET['rvn']);
echo json_encode(array(
    'profileRevision' => $RVN,
    'profileId' => 'campaign',
    'profileChangesBaseRevision' => $RVN,
    'profileChanges' => [],
    'profileCommandRevision' => $RVN - 10,
    'serverTime' => current_zulu_time(),
    'responseVersion' => 1
));