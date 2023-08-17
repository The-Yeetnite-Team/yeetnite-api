<?php
require_once 'database.php';

header('Content-Type: application/json');
header('X-Litespeed-Cache-Control: no-store');

$data =  json_decode(file_get_contents('php://input'), true);

$access_token = strtr($_SERVER['HTTP_AUTHORIZATION'], array('bearer ' => ''));
$data['accountId'] = $database->select(array('username'), 'users', "WHERE accessToken = '$access_token'")[0]['username'];

echo json_encode($data);