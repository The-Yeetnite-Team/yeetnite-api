<?php
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

$values = $cache_provider->get('v1_epic-settings_public_users_values');
$current_time_float = current_timestamp();
$current_timestamp_ms = round($current_time_float * 1000);
$current_zulu_time = zulu_time_from_timestamp(round($current_time_float, 3));
$graduated_timestamp = $current_timestamp_ms - 86400000; // yesterday

$values = strtr($values, array(
    '"preferredValueUpdatedAt":-1' => "\"preferredValueUpdatedAt\":$current_timestamp_ms",
    '"lastGraduatedAt":-1' => "\"lastGraduatedAt\":$graduated_timestamp"
));
$values = substr_replace($values, "\"timestamp\":\"$current_zulu_time\"", -16, 14);

echo $values;