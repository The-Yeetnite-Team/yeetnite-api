<?php
require_once 'database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mnemonic_json = $database->select(array('mnemonic_json'), 'mnemonics', "WHERE mnemonic_name = '{$_GET['name']}'");
    if (count($mnemonic_json) < 1) {
        echo '{}';
        return;
    }

    echo $mnemonic_json[0]['mnemonic_json'];
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = json_decode(file_get_contents('php://input'), true);

    $sql_in_data = implode(',', array_map(function ($mnemonic) { return "'{$mnemonic['mnemonic']}'"; }, $_POST));

    $mnemonic_info = $database->select(array('mnemonic_json'), 'mnemonics', "WHERE mnemonic_name IN ($sql_in_data)");

    $mnemonics_json_data = implode(',', array_map(function($mnemonic_info) { return $mnemonic_info['mnemonic_json']; }, $mnemonic_info));

    echo "[$mnemonics_json_data]";
}