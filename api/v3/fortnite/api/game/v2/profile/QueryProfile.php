<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';
require_once 'lib/season_utils.php';

header('Content-Type: application/json');

$SERVER_TIME = current_zulu_time();
$CREATED_LAST_LOGIN = $database->select(array('created', 'lastLogin'), 'users', "WHERE username = '{$_GET['accountId']}'")[0];
$RVN = intval($_GET['rvn']) ?? -1;

switch ($_GET['profileId']) {
    case 'athena':
        header("X-LiteSpeed-Tag: queryProfileAthena/{$_GET['accountId']}");
        switch ($RVN) {
            case -1:
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
                // TODO somehow manage to use substr_replace for all of them (strtr is quite slow in comparison)
                // first doesn't need strpos() because file is unchanged initially
                $athena_profile = substr_replace($athena_profile, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', 1, 12);
                $athena_profile = substr_replace($athena_profile, "\"accountId\":\"{$_GET['accountId']}\"", -1751020, 14);
                $athena_profile = substr_replace($athena_profile, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', -102399, 12);
                $athena_profile = substr_replace($athena_profile, "\"season_num\":{$version_info['season']}", -691, 15);

                $athena_profile = strtr(
                    $athena_profile,
                    array(
                        '"banner_icon":""' => "\"banner_icon\":\"{$locker_data['banner_icon']}\"",
                        '"banner_color":""' => "\"banner_color\":\"{$locker_data['banner_color']}\"",
                        '"favorite_consumableemote":""' => "\"favorite_consumableemote\":\"{$locker_data['favorite_consumableemote']}\"",
                        '"favorite_character":""' => "\"favorite_character\":\"{$locker_data['favorite_character']}\"",
                        '"favorite_spray":[]' => "\"favorite_spray\":{$locker_data['favorite_spray']}",
                        '"favorite_loadingscreen":""' => "\"favorite_loadingscreen\":\"{$locker_data['favorite_loadingscreen']}\"",
                        '"favorite_hat":""' => "\"favorite_hat\":\"{$locker_data['favorite_hat']}\"",
                        '"favorite_vehicledeco":""' => "\"favorite_vehicledeco\":\"{$locker_data['favorite_vehicledeco']}\"",
                        '"favorite_backpack":""' => "\"favorite_backpack\":\"{$locker_data['favorite_backpack']}\"",
                        '"favorite_dance":[]' => "\"favorite_dance\":{$locker_data['favorite_dance']}",
                        '"favorite_skydivecontrail":""' => "\"favorite_skydivecontrail\":\"{$locker_data['favorite_skydivecontrail']}\"",
                        '"favorite_pickaxe":""' => "\"favorite_pickaxe\":\"{$locker_data['favorite_pickaxe']}\"",
                        '"favorite_glider":""' => "\"favorite_glider\":\"{$locker_data['favorite_glider']}\"",
                        '"favorite_musicpack":""' => "\"favorite_musicpack\":\"{$locker_data['favorite_musicpack']}\"",
                        '"favorite_itemwraps":[]' => "\"favorite_itemwraps\":{$locker_data['favorite_itemwrap']}"
                    )
                );

                echo $athena_profile;
                break;
            default:
                echo json_encode(array(
                    'profileRevision' => 17306,
                    'profileId' => 'athena',
                    'profileChangesBaseRevision' => 17306,
                    'profileChanges' => array(),
                    'profileCommandRevision' => $RVN,
                    'serverTime' => $SERVER_TIME,
                    'responseVersion' => 1
                ));
                break;
        }
        break;
    case 'common_core':
        switch ($RVN) {
            case -1:
                $common_core = $cache_provider->get('fortnite_api_game_v2_profile_common_core');

                $common_core = substr_replace($common_core, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', 1, 12);
                $common_core = substr_replace($common_core, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', -39138, 12);
                $common_core = substr_replace($common_core, "\"accountId\":\"{$_GET['accountId']}\"", -39102, 14);

                echo $common_core;
                break;
            default:
                echo json_encode(array(
                    'profileRevision' => 2409,
                    'profileId' => 'common_core',
                    'profileChangesBaseRevision' => 2409,
                    'profileChanges' => [],
                    'profileCommandRevision' => $RVN,
                    'serverTime' => $SERVER_TIME,
                    'responseVersion' => 1
                ));
                break;
        }
        break;
    case 'common_public':
        $banner_info = $database->select(array('banner_icon', 'banner_color'), 'locker', "WHERE user_id IN (SELECT user_id FROM users WHERE username = '{$_GET['accountId']}')")[0];

        $common_public = $cache_provider->get('fortnite_api_game_v2_profile_common_public');

        $common_public = substr_replace($common_public, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', -291, 12);
        $common_public = substr_replace($common_public, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', -278, 12);
        $common_public = substr_replace($common_public, "\"banner_color\": \"{$banner_info['banner_color']}\"", -142, 17);
        $common_public = substr_replace($common_public, "\"banner_icon\": \"{$banner_info['banner_icon']}\"", -105, 16);
        $common_public = substr_replace($common_public, '"serverTime":"' . $SERVER_TIME . '"', -36, 15);

        echo $common_public;
        break;
    case 'profile0':
        $profile0 = $cache_provider->get('fortnite_api_game_v2_profile_profile0');

        $profile0 = substr_replace($profile0, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', 183, 12);
        $profile0 = substr_replace($profile0, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($profile0, '"updated":""', 196), 12);
        $profile0 = substr_replace($profile0, "\"accountId\":\"{$_GET['accountId']}\"", strpos($profile0, '"accountId":""', 234), 14);

        echo $profile0;
        break;
    case 'creative':
        echo json_encode(array(
            'profileRevision' => 203,
            'profileId' => 'creative',
            'profileChangesBaseRevision' => 203,
            'profileChanges' => array(
                array(
                    'changeType' => 'fullProfileUpdate',
                    'profile' => array(
                        '_id' => $_GET['accountId'],
                        'created' => $CREATED_LAST_LOGIN['created'],
                        'updated' => $CREATED_LAST_LOGIN['lastLogin'],
                        'rvn' => 203,
                        'wipeNumber' => 11,
                        'accountId' => $_GET['accountId'],
                        'profileId' => 'creative',
                        'version' => 'ensure_project_ids_october_2021',
                        'items' => new stdClass(),
                        'stats' => array(
                            'attributes' => new stdClass()
                        ),
                        'commandRevision' => 197
                    )
                )
            ),
            'profileCommandRevision' => 197,
            'serverTime' => '',
            'responseVersion' => 1
        ));
        break;
    case 'collection_book_people0':
        $collection_book_people0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_people0');

        $collection_book_people0 = substr_replace($collection_book_people0, "\"accountId\":\"{$_GET['accountId']}\"", 219, 14);
        $collection_book_people0 = substr_replace($collection_book_people0, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', strpos($collection_book_people0, '"created":""', 170), 12);
        $collection_book_people0 = substr_replace($collection_book_people0, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($collection_book_people0, '"updated":""', 183), 12);
        $collection_book_people0 = substr_replace($collection_book_people0, '"serverTime":"' . $SERVER_TIME . '"', strpos($collection_book_people0, '"serverTime":""', -36), 15);

        echo $collection_book_people0;
        break;
    case 'collection_book_schematics0':
        $collection_book_schematics0 = $cache_provider->get('fortnite_api_game_v2_profile_collection_book_schematics0');

        $collection_book_schematics0 = substr_replace($collection_book_schematics0, "\"accountId\":\"{$_GET['accountId']}\"", 223, 14);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', strpos($collection_book_schematics0, '"created":""', 174), 12);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($collection_book_schematics0, '"updated":""', 187), 12);
        $collection_book_schematics0 = substr_replace($collection_book_schematics0, '"serverTime":"' . $SERVER_TIME . '"', strpos($collection_book_schematics0, '"serverTime":""', -36), 15);

        echo $collection_book_schematics0;
        break;
    case 'campaign':
        echo json_encode(array(
            'profileRevision' => $RVN,
            'profileId' => 'campaign',
            'profileChangesBaseRevision' => $RVN,
            'profileChanges' => array(),
            'profileCommandRevision' => $RVN - 10,
            'serverTime' => $SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'metadata':
        $metadata = $cache_provider->get('fortnite_api_game_v2_profile_metadata');

        $metadata = substr_replace($metadata, "\"accountId\":\"{$_GET['accountId']}\"", 213, 14);
        $metadata = substr_replace($metadata, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', strpos($metadata, '"created":""', 161), 12);
        $metadata = substr_replace($metadata, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($metadata, '"updated":""', 174), 12);
        $metadata = substr_replace($metadata, '"serverTime":"' . $SERVER_TIME . '"', strpos($metadata, '"serverTime":""', -36), 15);

        echo $metadata;
        break;
    case 'theater0':
        $theater0 = $cache_provider->get('fortnite_api_game_v2_profile_theater0');

        $theater0 = substr_replace($theater0, "\"accountId\":\"{$_GET['accountId']}\"", 216, 14);
        $theater0 = substr_replace($theater0, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', strpos($theater0, '"created":""', 163), 12);
        $theater0 = substr_replace($theater0, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', strpos($theater0, '"updated":""', 176), 12);
        $theater0 = substr_replace($theater0, '"serverTime":"' . $SERVER_TIME . '"', strpos($theater0, '"serverTime":""', -36), 15);

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
                        'created' => $CREATED_LAST_LOGIN['created'],
                        'updated' => $CREATED_LAST_LOGIN['lastLogin'],
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
            'serverTime' => $SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'collections':
        $collections = $cache_provider->get('fortnite_api_game_v2_profile_collections');
        $version_info = fortnite_version_info($_SERVER['HTTP_USER_AGENT']);

        $collections = substr_replace($collections, '"created":"' . $CREATED_LAST_LOGIN['created'] . '"', 186, 12);
        $collections = substr_replace($collections, '"updated":"' . $CREATED_LAST_LOGIN['lastLogin'] . '"', -556, 12);
        $collections = substr_replace($collections, "\"accountId\":\"{$_GET['accountId']}\"", -518, 14);
        $collections = substr_replace($collections, "\"current_season\":{$version_info['season']}", -111, 18);
        $collections = substr_replace($collections, '"serverTime":"' . $SERVER_TIME, -36, 14); // not sure why no ending quote is needed here :(

        echo $collections;
}