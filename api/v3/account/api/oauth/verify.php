<?php
header('Content-Type: application/json');

echo json_encode(
    array(
        'access_token' => substr($_SERVER['HTTP_AUTHORIZATION'], 7),
        'client_id' => 'yeetniteclientlol',
        'client_service' => 'fortnite',
        'expires_at' => '9999-12-02T01:12:00Z',
        'expires_in' => 28800,
        'internal_client' => true,
        'token_type' => 'bearer'
    )
);