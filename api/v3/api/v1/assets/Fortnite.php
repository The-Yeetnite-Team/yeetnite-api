<?php
header('Content-Type: application/json');

$_POST = json_decode(file_get_contents('php://input'), true);

$data = array();

array_walk($_POST,
    function ($value, $key) use(&$data)
    {
        $data[$key] = array(
            'meta' => array(
                'promotion' => $value
            ),
            'assets' => new stdClass()
        );
    }
);

echo json_encode($data);