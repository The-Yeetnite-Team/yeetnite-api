<?php
header('Content-Type: application/json');

echo json_encode(array(
    'serviceUrl' => 'wss://matchmaker.yeetnite.ml:4443',
    'ticketType' => 'mms-player',
    'payload' => '1=',
    'signature' => '2='
));