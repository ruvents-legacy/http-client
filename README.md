# RUVENTS Http Client

## Installation

```
$ composer require ruvents/http-client
```

## Usage

```php
<?php
$httpClient = new Ruvents\HttpClient\HttpClient();
```

### Creating a Request object

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

### Sending the request

```php
<?php
use Ruvents\HttpClient\HttpClient;

// GET
$httpClient->get($request);

// POST
$httpClient->post($request);

// or you can pass the method as the second parameter
// to HttpClient::send
$httpClient->send($request, HttpClient::METHOD_GET);
```
