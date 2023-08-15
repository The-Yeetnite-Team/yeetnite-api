<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

$build_id = strtr(explode('/', $_SERVER['HTTP_USER_AGENT'])[1], array(' Windows' => '-Windows'));

$items_cache = $cache_provider->get("launcher_assets:$build_id");

if ($items_cache) {
    echo $items_cache;
    return;
}

$items_data = $database->select(array('items_json'), 'launcher_assets', "WHERE build_id = '$build_id'");
$data = json_encode(array(
    'appName' => 'FortniteContentBuilds',
    'labelName' => "{$_GET['label']}-Windows",
    'buildVersion' => $build_id,
    'catalogItemId' => $_GET['catalog_item_id'],
    'metadata' => new stdClass(),
    'expires' => '2999-01-01T00:00:00.000Z',
    'items' => isset($items_data[0]['items_json']) ? json_decode($items_data[0]['items_json'], true) : new stdClass(),
    'assetId' => 'FortniteContentBuilds'
));

$cache_provider->set("launcher_assets:$build_id", $data);

echo $data;