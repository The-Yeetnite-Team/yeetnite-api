<?php
require_once 'cache_provider.php';

header('Content-Type: application/json');

echo $cache_provider->get('api_v1_fortnite-br_surfaces_motd_target');