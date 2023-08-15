<?php
require_once 'cache_provider.php';
require_once 'lib/season_utils.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

$version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
$event_flag_season = "EventFlag.Season{$version_info['season']}";
$current_time = current_zulu_time();
$timeline = $cache_provider->get('fortnite_api_calendar_v1_timeline');

//! These offsets will have to be replaced if the file changes
$timeline = substr_replace($timeline, "\"currentTime\":\"$current_time\"", -17, 16);
$timeline = substr_replace($timeline, "\"seasonNumber\":{$version_info['season']}", 934, 17);
$timeline = substr_replace($timeline, "\"eventType\":\"$event_flag_season\"", 681, 14);
$previous_event_added_offset = strlen($event_flag_season); // the string has been updated and second eventType offset is now invalid
$timeline = substr_replace($timeline, "\"eventType\":\"EventFlag.{$version_info['lobby']}\"", 780 + $previous_event_added_offset, 14);

echo $timeline;
