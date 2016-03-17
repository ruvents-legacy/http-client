<?php

header('header_response: test');

echo json_encode([
    'headers' => getallheaders(),
    'get' => $_GET,
    'post' => $_POST,
    'post_raw' => file_get_contents('php://input'),
    'files' => $_FILES,
]);
