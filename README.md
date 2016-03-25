# RUVENTS HTTP Client

## Installation

```
$ composer require ruvents/http-client
```

## Creating the request object

```php
<?php

$uri = Ruvents\HttpClient\Request\Uri::createHttp(
    // host (required)
    'host.com',
    // path (defaults to '')
    'path/to/somewhere',
    // query parameters (defaults to [])
    ['param' => 'value'],
    // use https? (defaults to false)
    true
);
$request = new Ruvents\HttpClient\Request\Request(
    // uri: string or instance of Uri (required)
    $uri,
    // data: a scalar or an array (defaults to null)
    ['data_name' => 'data_value'],
    // headers: array (defaults to [])
    ['header_name' => 'header_value'],
    // files: an array of resources, paths or File instances (defaults to [])
    ['file_name' => new Ruvents\HttpClient\Request\File('file.txt')]
);
```

## Sending the request

```php
<?php

use Ruvents\HttpClient\HttpClient;

HttpClient::get($request);
// or
HttpClient::get(
    'https://host.com/path/to/somewhere?param=value',
    ['param' => 'value'],
    ['header_name' => 'header_value']
);

HttpClient::post($request);
// or
HttpClient::post(
    'https://host.com/path/to/somewhere?param=value',
    ['param' => 'value'],
    ['header_name' => 'header_value'],
    ['file_name' => new Ruvents\HttpClient\Request\File('file.txt')]
);

// other methods
HttpClient::put($request);
HttpClient::patch($request);
HttpClient::delete($request);
```
