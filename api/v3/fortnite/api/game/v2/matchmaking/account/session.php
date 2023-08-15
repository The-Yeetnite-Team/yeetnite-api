<?php
header('Content-Type: application/json');

echo json_encode(array(
    'accountId' => $_GET['accountId'],
    'sessionId' => $_GET['sessionId'],
    'key' => 'AOJEv8uTFmUh7XM2328kq9rlAzeQ5xzWzPIiyKn2s7s=' // TODO Make key dynamic just in case it needs to be for each player?
));