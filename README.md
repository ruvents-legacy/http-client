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
    ['name' => 'b'],
    // use https? (defaults to false)
    true
);
$request = new Ruvents\HttpClient\Request\Request(
    // the Uri instance (required)
    $uri,
    // a scalar or an array (defaults to [])
    ['name' => 'value'],
    // an array of headers (defaults to [])
    ['name' => 'value'],
    // an array of File instances (defaults to [])
    ['name' => new Ruvents\HttpClient\Request\File('file.txt')]
);
```

## Sending the request

```php
<?php

use Ruvents\HttpClient\HttpClient;

// GET
HttpClient::get($request);

// POST
HttpClient::post($request);

// any
HttpClient::send($request, HttpClient::METHOD_GET);
```
