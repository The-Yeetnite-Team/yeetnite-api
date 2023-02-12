<?php
require_once 'cache_provider.php';
require_once 'lib/season_utils.php';
// we need this for Carbon
set_include_path($_SERVER['DOCUMENT_ROOT']);
require 'vendor/autoload.php';

use Carbon\Carbon;

header('Content-Type: application/json');

$version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
$timeline = json_decode($cache_provider->get('fortnite_api_calendar_v1_timeline'), true);

$timeline['currentTime'] = Carbon::now()->toIso8601ZuluString('millisecond');
$timeline['channels']['client-events']['states'][0]['state']['seasonNumber'] = $version_info['season'];
$timeline['channels']['client-events']['states'][0]['state']['seasonTemplateId'] = "AthenaSeason:athenaseason{$version_info['season']}";
$timeline['channels']['client-events']['states'][0]['activeEvents'][0]['eventType'] = "EventFlag.Season{$version_info['season']}";
$timeline['channels']['client-events']['states'][0]['activeEvents'][1]['eventType'] = "EventFlag.{$version_info['lobby']}";

echo json_encode($timeline);
