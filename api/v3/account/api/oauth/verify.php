<?php
header('Content-Type: application/json');

echo json_encode(
    array(
        'access_token' => substr($_SERVER['HTTP_AUTHORIZATION'], 7),
        'client_id' => 'yeetnite-client',
        'client_service' => 'prod-fn',
        'expires_at' => '9999-12-02T01:12:00Z',
        'expires_in' => 28800,
        'internal_client' => true,
        'token_type' => 'bearer',
        'app' => 'prod-fn',
        'product_id' => 'prod-fn',
        'sandbox_id' => 'fn',
        'scope' => array('basic_profile','friends_list','openid','presence')
    )
);