<?php

function fortnite_version_info(string $user_agent) {
    $res = array(
        'season' => 0,
        'build_id' => 0.0,
        'CL' => '',
        'lobby' => ''
    );

    try {
        $build_id = explode(',', explode('-', $user_agent)[3])[0];
        if (is_numeric($build_id)) {
            $res['CL'] = intval($build_id);
        }

        if (!is_numeric($build_id)) {
            $build_id = explode(' ', explode('-', $user_agent)[3])[0];
            if (is_numeric($build_id)) {
                $res['CL'] = intval($build_id);
            }
        }
    } catch (Exception) {
        try {
            $build_id = explode('+', explode('-', $user_agent)[1])[0];
            if (is_numeric($build_id)) {
                $res['CL'] = $build_id;
            }
        } catch (Exception) {}
    }

    try {
        $build_id = explode('-', explode('Release-', $user_agent)[1])[0];

        $value = explode('.', $build_id);
        if (count($value) === 3) {
            $build_id = "{$value[0]}.{$value[1]}{$value[2]}";
        }

        $res['season'] = intval(explode('.', $build_id)[0]);
        $res['build_id'] = floatval($build_id);
        $res['lobby'] = 'LobbySeason' . $res['season'];

        if (!is_numeric($res['season'])) {
            throw new ErrorException('Extracted season value is not a number ( !is_numeric() )', -1, E_ERROR);
        }
    } catch (Exception) {
        $res['season'] = 2;
        $res['build_id'] = 2.0;
        $res['lobby'] = "LobbyWinterDecor";
    }

    return $res;
}