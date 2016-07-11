<?php

use Ruvents\HttpClient\Exception\CurlException;
use Ruvents\HttpClient\HttpClient;
use Ruvents\HttpClient\Request\File;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\Uri;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../vendor/autoload.php';

$request = new Request(
    Uri::createHttp($_SERVER['HTTP_HOST'], 'tests/server.php', ['a' => 'b']),
    [
        'a' => 'a',
        'b' => [
            'b1' => 1,
            'b2' => [
                'c' => 2,
                'f' => new File(__FILE__),
            ],
        ],
    ],
    ['header' => 'value']
);

try {
    $response = HttpClient::send('post', $request);
} catch (CurlException $e) {
    var_dump($e);
    throw $e;
}

var_dump($response->jsonDecode());
