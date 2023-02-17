<?php
require_once 'database.php';
require_once 'cache_provider.php';
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

define('SERVER_TIME', current_zulu_time());
define('RVN', intval($_GET['rvn']));

switch ($_GET['profileId']) {
    case 'profile0':
        echo json_encode(array(
            'profileRevision' => RVN,
            'profileId' => 'profile0',
            'profileChangesBaseRevision' => RVN,
            'profileChanges' => [],
            'profileCommandRevision' => 282,
            'serverTime' => SERVER_TIME,
            'responseVersion' => 1
        ));
        break;
    case 'campaign':
        switch (RVN) {
            case -1:
                $campaign = $cache_provider->get('fortnite_api_game_v2_profile_expeditions_campaign');
                $created = $database->select(array('created'), 'users', "WHERE username = '{$_GET['accountId']}'")[0]['created'];
                //! The offsets will have to change if the file is updated
                $campaign = substr_replace($campaign, '"serverTime":"' . SERVER_TIME . '"', -36, 15);
                $campaign = substr_replace($campaign, "\"accountId\":\"{$_GET['accountId']}\"", 210, 14);
                $campaign = substr_replace($campaign, '"updated":"' . SERVER_TIME . '"', 172, 12);
                $campaign = substr_replace($campaign, "\"created\": \"$created\"", 159, 12);
                echo $campaign;
                break;
            default:
                echo json_encode(array(
                    'profileRevision' => RVN,
                    'profileId' => 'campaign',
                    'profileChangesBaseRevision' => RVN,
                    'profileChanges' => [],
                    'profileCommandRevision' => RVN - 10,
                    'serverTime' => SERVER_TIME,
                    'responseVersion' => 1
                ));
                break;
        }
        break;
}