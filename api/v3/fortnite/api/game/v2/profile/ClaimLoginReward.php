<?php
require_once 'lib/date_utils.php';

header('Content-Type: application/json');

$server_time = current_zulu_time();
define('RVN', intval($_GET['rvn']));
echo json_encode(array(
    'profileRevision' => RVN + 1,
    'profileId' => 'campaign',
    'profileChangesBaseRevision' => RVN,
    'profileChanges' => array(
        array(
            'changeType' => 'statModified',
            'name' => 'daily_rewards',
            'value' => array('nextDefaultReward' => 70,
                'totalDaysLoggedIn' => 70,
                'lastClaimDate' => $server_time,
                'additionalSchedules' => array(
                    'founderspackdailyrewardtoken' => array(
                        'rewardsClaimed' => 70,
                        'claimedToday' => true
                    )
                )
            )
        )
    ),
    'profileCommandRevision' => RVN - 9,
    'serverTime' => $server_time,
    'responseVersion' => 1
));