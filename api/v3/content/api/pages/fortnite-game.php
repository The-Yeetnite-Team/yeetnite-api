<?php
require_once 'cache_provider.php';

header('Content-Type: application/json');

echo $cache_provider->get('content_api_pages_fortnite_game');