<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';
require_once 'lib/season_utils.php';

header('Content-Type: application/json');

define('SERVER_TIME', current_zulu_time());
define('CREATED_LAST_LOGIN', $database->select(array('created', 'lastLogin'), 'users', "WHERE username = '{$_GET['accountId']}'")[0]);

switch ($_GET['profileId']) {
    case 'athena':
        header("X-LiteSpeed-Tag: queryProfileAthena/{$_GET['accountId']}");
        $athena_profile = $cache_provider->get('fortnite_api_game_v2_profile_athena');
        $version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);
        $locker_data = $database->select(
            array(
                'banner_icon',
                'banner_color',
                'favorite_victorypose',
                'favorite_consumableemote',
                'favorite_callingcard',
                'favorite_character',
                'favorite_spray',
                'favorite_loadingscreen',
                'favorite_hat',
                'favorite_battlebus',
                'favorite_mapmarker',
                'favorite_vehicledeco',
                'favorite_backpack',
                'favorite_dance',
                'favorite_skydivecontrail',
                'favorite_pickaxe',
                'favorite_glider',
                'favorite_musicpack',
                'favorite_itemwrap'
            ),
            'locker',
            "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')"
        )[0];

        //! These offsets will have to be changed if the file changes
        // first doesn't need strpos() because file is unchanged initially
        $athena_profile = substr_replace($athena_profile, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', 142, 12);
        $athena_profile = substr_replace($athena_profile, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($athena_profile, '"updated":""', 155), 12);
        $athena_profile = substr_replace($athena_profile, "\"accountId\":\"{$_GET['accountId']}\"", strpos($athena_profile, '"accountId":""', 191), 14);
        $athena_profile = substr_replace($athena_profile, "\"season_num\":{$version_info['season']}", strpos($athena_profile, '"season_num":-1', -800), 15);
        $athena_profile = substr_replace($athena_profile, "\"banner_icon\":\"{$locker_data['banner_icon']}\"", strpos($athena_profile, '"banner_icon":""', -167), 16);
        $athena_profile = substr_replace($athena_profile, "\"banner_color\":\"{$locker_data['banner_color']}\"", strpos($athena_profile, '"banner_color":""', -739), 17);
        $athena_profile = substr_replace($athena_profile, "\"favorite_victorypose\":\"{$locker_data['favorite_victorypose']}\"", strpos($athena_profile, '"favorite_victorypose":""', -934), 25);
        $athena_profile = substr_replace($athena_profile, "\"favorite_consumableemote\":\"{$locker_data['favorite_consumableemote']}\"", strpos($athena_profile, '"favorite_consumableemote":""', -769), 29);
        $athena_profile = substr_replace($athena_profile, "\"favorite_callingcard\":\"{$locker_data['favorite_callingcard']}\"", strpos($athena_profile, '"favorite_callingcard":""', -721), 25);
        $athena_profile = substr_replace($athena_profile, "\"favorite_character\":\"{$locker_data['favorite_character']}\"", strpos($athena_profile, '"favorite_character":""', -695), 23);
        $athena_profile = substr_replace($athena_profile, "\"favorite_spray\":{$locker_data['favorite_spray']}", strpos($athena_profile, '"favorite_spray":[]', -671), 19);
        $athena_profile = substr_replace($athena_profile, "\"favorite_loadingscreen\":\"{$locker_data['favorite_loadingscreen']}\"", strpos($athena_profile, '"favorite_loadingscreen":""', -637), 27);
        $athena_profile = substr_replace($athena_profile, "\"favorite_hat\":\"{$locker_data['favorite_hat']}\"", strpos($athena_profile, '"favorite_hat":""', -569), 17);
        $athena_profile = substr_replace($athena_profile, "\"favorite_battlebus\":\"{$locker_data['favorite_battlebus']}\"", strpos($athena_profile, '"favorite_battlebus":""', -539), 23);
        $athena_profile = substr_replace($athena_profile, "\"favorite_mapmarker\":\"{$locker_data['favorite_mapmarker']}\"", strpos($athena_profile, '"favorite_mapmarker":""', -515), 23);
        $athena_profile = substr_replace($athena_profile, "\"favorite_vehicledeco\":\"{$locker_data['favorite_vehicledeco']}\"", strpos($athena_profile, '"favorite_vehicledeco":""', -491), 25);
        $athena_profile = substr_replace($athena_profile, "\"favorite_backpack\":\"{$locker_data['favorite_backpack']}\"", strpos($athena_profile, '"favorite_backpack":""', -446), 22);
        $athena_profile = substr_replace($athena_profile, "\"favorite_dance\":{$locker_data['favorite_dance']}", strpos($athena_profile, '"favorite_dance":[]', -406), 19);
        $athena_profile = substr_replace($athena_profile, "\"favorite_skydivecontrail\":\"{$locker_data['favorite_skydivecontrail']}\"", strpos($athena_profile, '"favorite_skydivecontrail":""', -334), 29);
        $athena_profile = substr_replace($athena_profile, "\"favorite_pickaxe\":\"{$locker_data['favorite_pickaxe']}\"", strpos($athena_profile, '"favorite_pickaxe":""', -304), 21);
        $athena_profile = substr_replace($athena_profile, "\"favorite_glider\":\"{$locker_data['favorite_glider']}\"", strpos($athena_profile, '"favorite_glider":""', -282), 20);
        $athena_profile = substr_replace($athena_profile, "\"favorite_musicpack\":\"{$locker_data['favorite_musicpack']}\"", strpos($athena_profile, '"favorite_musicpack":""', -176), 23);
        $athena_profile = substr_replace($athena_profile, "\"favorite_itemwraps\":{$locker_data['favorite_itemwrap']}", strpos($athena_profile, '"favorite_itemwraps":[]', -115), 23);

        echo $athena_profile;
        break;
    case 'common_core':
        $common_core = $cache_provider->get('fortnite_api_game_v2_profile_common_core');

        $common_core = substr_replace($common_core, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', 145, 12);
        $common_core = substr_replace($common_core, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($common_core, '"updated":""', 158), 12);
        $common_core = substr_replace($common_core, '"accountId":"' . $_GET['accountId'] . '"', strpos($common_core, '"accountId":""', 196), 14);

        echo $common_core;
        break;
    case 'common_public':
        $common_public = $cache_provider->get('fortnite_api_game_v2_profile_common_public');

        $common_public = substr_replace($common_public, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', 143, 12);
        $common_public = substr_replace($common_public, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($common_public, '"updated":""', 156), 12);
        $common_public = substr_replace($common_public, "\"accountId\":\"{$_GET['accountId']}\"", strpos($common_public, '"accountId":""', 192), 14);
        $common_public = substr_replace($common_public, "\"homebase_name\":\"{$_GET['accountId']}\"", strpos($common_public, '"homebase_name":""', 323), 18);

        echo $common_public;
        break;
    case 'profile0':
        $profile0 = $cache_provider->get('fortnite_api_game_v2_profile_profile0');

        $profile0 = substr_replace($profile0, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', 183, 12);
        $profile0 = substr_replace($profile0, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($profile0, '"updated":""', 196), 12);
        $profile0 = substr_replace($profile0, "\"accountId\":\"{$_GET['accountId']}\"", strpos($profile0, '"accountId":""', 234), 14);

        echo $profile0;
        break;
    case 'collection_book_people0':
        $collection_book_people0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_people0');

        $collection_book_people0 = substr_replace($collection_book_people0, "\"accountId\":\"{$_GET['accountId']}\"", 219, 14);
        $collection_book_people0 = substr_replace($collection_book_people0, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', strpos($collection_book_people0, '"created":""', 170), 12);
        $collection_book_people0 = substr_replace($collection_book_people0, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($collection_book_people0, '"updated":""', 183), 12);
        $collection_book_people0 = substr_replace($collection_book_people0, '"serverTime":"' . SERVER_TIME . '"', strpos($collection_book_people0, '"serverTime":""', -36), 15);

        echo $collection_book_people0;
        break;
    case 'collection_book_schematics0':
        $collection_book_schematics0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_schematics0');

        $collection_book_schematics0 = substr_replace($collection_book_schematics0, "\"accountId\":\"{$_GET['accountId']}\"", 223, 14);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', strpos($collection_book_schematics0, '"created":""', 174), 12);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($collection_book_schematics0, '"updated":""', 187), 12);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"serverTime":"' . SERVER_TIME . '"', strpos($collection_book_schematics0, '"serverTime":""', -36), 15);

        echo $collection_book_schematics0;
        break;
    case 'campaign':
        define('RVN', intval($_GET['rvn']));
        echo json_encode(array(
            'profileRevision' => RVN,
            'profileId' => 'campaign',
            'profileChangesBaseRevision' => RVN,
            'profileChanges' => array(),
            'profileCommandRevision' => RVN - 10,
            'serverTime' => SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'metadata':
        $metadata = $cache_provider->get('fortnite_api_game_v2_profile_metadata');

        $metadata = substr_replace($metadata, "\"accountId\":\"{$_GET['accountId']}\"", 213, 14);
        $metadata = substr_replace($metadata, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', strpos($metadata, '"created":""', 161), 12);
        $metadata = substr_replace($metadata, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($metadata, '"updated":""', 174), 12);
        $metadata = substr_replace($metadata, '"serverTime":"' . SERVER_TIME . '"', strpos($metadata, '"serverTime":""', -36), 15);

        echo $metadata;
        break;
    case 'theater0':
        $theater0 = $cache_provider->get('fortnite_api_game_v2_profile_theater0');

        $theater0 = substr_replace($theater0, "\"accountId\":\"{$_GET['accountId']}\"", 216, 14);
        $theater0 = substr_replace($theater0, '"created":"' . CREATED_LAST_LOGIN['created'] . '"', strpos($theater0, '"created":""', 163), 12);
        $theater0 = substr_replace($theater0, '"updated":"' . CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($theater0, '"updated":""', 176), 12);
        $theater0 = substr_replace($theater0, '"serverTime":"' . SERVER_TIME . '"', strpos($theater0, '"serverTime":""', -36), 15);

        echo $theater0;
        break;
    case 'outpost0':
        echo json_encode(array(
            'profileRevision' => 1,
            'profileId' => 'outpost0',
            'profileChangesBaseRevision' => 1,
            'profileChanges' => array(
                array(
                    'changeType' => 'fullProfileUpdate',
                    'profile' => array(
                        '_id' => 'Yeetnite',
                        'created' => CREATED_LAST_LOGIN['created'],
                        'updated' => CREATED_LAST_LOGIN['lastLogin'],
                        'rvn' => 1,
                        'wipeNumber' => 1,
                        'accountId' => $_GET['accountId'],
                        'profileId' => 'outpost0',
                        'version' => 'no_version',
                        'items' => new stdClass(),
                        'stats' => array(
                            'attributes' => array(
                                'inventory_limit_bonus' => 0
                            )
                        ),
                        'commandRevision' => 0
                    )
                )
            ),
            'profileCommandRevision' => 0,
            'serverTime' => SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
}