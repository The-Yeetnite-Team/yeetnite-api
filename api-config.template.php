<?php
const DB_HOST = 'abcd';
const DB_USERNAME = 'abcd';
const DB_PASSWORD = 'abcd';

if (file_exists(dirname(__FILE__) . "/testing_mode")) {
    define('DB_NAME', 'test_db_name');
} else {
    define('DB_NAME', 'db_name');
}

const MEMCACHED_HOST = 'abcd';
const MEMCACHED_PORT = 11211;
const TIGASE_API_KEY = 'abcd';
const TIGASE_HTTP_AUTHORIZATION = 'abcd';
const DEFAULT_MEMCACHED_KEYS = array(
    'friend_request_error_self_accept' => '{"success":false,"reason":"You can\'t accept your own friend request"}',
    'content_api_pages_fortnite_game' => 'file|static/content_api_pages_fortnite_game.json',
    'fortnite_api_calendar_v1_timeline' => 'file|static/fortnite_api_calendar_v1_timeline.json',
    'fortnite_api_cloudstorage_system' => 'file|static/fortnite_api_cloudstorage_system.json',
    'fortnite_api_cloudstorage_transports' => 'file|static/fortnite_api_cloudstorage_transports.json',
    'fortnite_api_game_v2_profile_athena' => 'file|static/fortnite_api_game_v2_profile_athena.json',
    'fortnite_api_game_v2_profile_collection_book_people0' => 'file|static/.json',
    'fortnite_api_game_v2_profile_collection_book_schematics0' => 'file|static/fortnite_api_game_v2_profile_collection_book_schematics0.json',
    'fortnite_api_game_v2_profile_common_core' => 'file|static/fortnite_api_game_v2_profile_common_core.json',
    'fortnite_api_game_v2_profile_common_public' => 'file|static/fortnite_api_game_v2_profile_common_public.json',
    'fortnite_api_game_v2_profile_expeditions_campaign' => 'file|static/fortnite_api_game_v2_profile_expeditions_campaign.json',
    'fortnite_api_game_v2_profile_metadata' => 'file|static/fortnite_api_game_v2_profile_metadata.json',
    'fortnite_api_game_v2_profile_profile0' => 'file|static/fortnite_api_game_v2_profile_profile0.json',
    'fortnite_api_game_v2_profile_theater0' => 'file|static/fortnite_api_game_v2_profile_theater0.json',
    'fortnite_api_game_v2_world_info' => 'file|static/fortnite_api_game_v2_world_info.json',
    'fortnite_api_storefront_v2_catalog' => 'file|static/fortnite_api_storefront_v2_catalog.json',
    'fortnite_api_storefront_v2_keychain' => 'file|static/fortnite_api_storefront_v2_keychain.json',
    'api_v1_events_Fortnite' => 'file|static/api_v1_events_Fortnite.json',
    'api_v1_fortnite-br_surfaces_motd_target' => 'file|static/api_v1_fortnite-br_surfaces_motd_target.json',
    'fortnite_api_game_v2_profile_collections' => 'file|static/fortnite_api_game_v2_profile_collections.json',
    'v1_epic-settings_public_users_values' => 'file|static/v1_epic-settings_public_users_values.json'
);
