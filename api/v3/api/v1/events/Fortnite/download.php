<?php
require_once 'cache_provider.php';

header('Content-Type: application/json');

$events = $cache_provider->get('api_v1_events_Fortnite');

$events = substr_replace($events, "\"accountId\":\"{$_GET['accountId']}\"", 31, 14);

echo $events;