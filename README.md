# RUVENTS HTTP Client

## Installation

```
$ composer require ruvents/http-client
```

## Usage

```php
<?php

use Ruvents\HttpClient\HttpClient;
use Ruvents\HttpClient\Request\Uri;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\File;

// Creating the request object

$uri = Uri::createHttp(
    // host (required)
    'host.com',
    // path (defaults to '')
    'path/to/somewhere',
    // query parameters (defaults to array())
    array('param' => 'value'),
    // use https? (defaults to false)
    true
);

$request = new Request(
    // uri: string or instance of Uri (required)
    $uri,
    // data: a scalar or an array (defaults to null)
    array('data_name' => 'data_value'),
    // headers: array (defaults to array())
    array('header_name' => 'header_value'),
    // files: an array of resources, paths or File instances (defaults to array())
    array('file_name' => new File('file.txt'))
);

// Sending the request

HttpClient::get($request);
// or
HttpClient::get(
    'https://host.com/path/to/somewhere?param=value',
    array('data_name' => 'data_value'),
    array('header_name' => 'header_value')
);

HttpClient::post($request);
// or
HttpClient::post(
    'https://host.com/path/to/somewhere?param=value',
    array('data_name' => 'data_value'),
    array('header_name' => 'header_value'),
    array('file_name' => new File('file.txt'))
);

// other methods
HttpClient::put($request);
HttpClient::patch($request);
HttpClient::delete($request);
```
