<?php
require_once 'cache_provider.php';

header('Content-Type: application/json');

echo $cache_provider->get('fortnite_api_storefront_v2_keychain');