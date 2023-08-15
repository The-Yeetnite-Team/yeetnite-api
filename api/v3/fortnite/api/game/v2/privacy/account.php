<?php
header('Content-Type: application/json');

echo json_encode(array(
    'accountId' => $_GET['accountId'],
    'optOutOfPublicLeaderboards' => false
));