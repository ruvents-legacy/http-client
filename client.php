<?php

use Ruvents\HttpClient\HttpClient;
use Ruvents\HttpClient\Request\File;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\Uri;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';

$uri = Uri::createHttp($_SERVER['HTTP_HOST'], 'server.php', ['a' => 'b']);
$request = new Request(
    $uri,
    ['post' => 123],
    ['header' => 'value'],
    ['a' => new File(__FILE__, 'text/php', 'a.txt')]
);
$httpClient = new HttpClient();

$response = $httpClient->send($request, HttpClient::METHOD_POST);

var_dump(json_decode($response->getRawBody()), $response->getHeaders());
