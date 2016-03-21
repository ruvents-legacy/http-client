<?php

use Ruvents\HttpClient\HttpClient;
use Ruvents\HttpClient\Request\File;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\Uri;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';

$request = new Request(
    Uri::createHttp($_SERVER['HTTP_HOST'], 'server.php', ['a' => 'b']),
    ['post' => 123],
    ['header' => 'value'],
    ['a' => new File(__FILE__, 'text/php', 'a.txt')]
);

$response = HttpClient::send($request, HttpClient::METHOD_POST);

var_dump($response->jsonDecode(true), $response->getHeaders());
