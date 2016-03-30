<?php

namespace Ruvents\HttpClient;

use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\Uri;
use Ruvents\HttpClient\Response\Response;

/**
 * Class HttpClient
 */
class HttpClient
{
    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $queryData
     * @param string[]           $headers
     * @return Response
     */
    public static function get($requestOrUri, $queryData = null, array $headers = [])
    {
        return self::send('GET', $requestOrUri, $queryData, $headers);
    }

    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $data
     * @param string[]           $headers
     * @param array              $files
     * @return Response
     */
    public static function post($requestOrUri, $data = null, array $headers = [], array $files = [])
    {
        return self::send('POST', $requestOrUri, $data, $headers, $files);
    }

    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $queryData
     * @param string[]           $headers
     * @return Response
     */
    public static function delete($requestOrUri, $queryData = null, array $headers = [])
    {
        return self::send('DELETE', $requestOrUri, $queryData, $headers);
    }

    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $data
     * @param string[]           $headers
     * @param array              $files
     * @return Response
     */
    public static function put($requestOrUri, $data = null, array $headers = [], array $files = [])
    {
        return self::send('PUT', $requestOrUri, $data, $headers, $files);
    }

    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $data
     * @param string[]           $headers
     * @param array              $files
     * @return Response
     */
    public static function patch($requestOrUri, $data = null, array $headers = [], array $files = [])
    {
        return self::send('PATCH', $requestOrUri, $data, $headers, $files);
    }

    /**
     * @param string             $method
     * @param Request|Uri|string $request
     * @param null|string|array  $data
     * @param string[]           $headers
     * @param array              $files
     * @return Response
     */
    protected static function send($method, $request, $data = null, array $headers = [], array $files = [])
    {
        if (!$request instanceof Request) {
            $request = new Request($request, $data, $headers, $files);
        }

        $ch = curl_init();

        switch ($method) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getPostFields());
                break;

            default:
                $queryData = is_array($request->getData()) ? $request->getData() : [];
                $request->getUri()->addQueryParams($queryData);
        }

        $request->addHeader('Expect', '');

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $request->getUri()->buildUri(),
            CURLOPT_HTTPHEADER => $request->getCurlHeaders(),
            CURLOPT_HEADER => true,
        ]);

        $responseRaw = curl_exec($ch);
        $response = self::createResponse($ch, $responseRaw);

        curl_close($ch);

        return $response;
    }

    /**
     * @param resource $ch
     * @param string   $raw
     * @return Response
     */
    protected static function createResponse($ch, $raw)
    {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($raw, 0, $headerSize);
        $rawBody = substr($raw, $headerSize);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headers = self::parseRawHeaders($rawHeaders);

        return new Response($rawBody, $code, $headers);
    }

    /**
     * @param string $raw
     * @return array
     */
    protected static function parseRawHeaders($raw)
    {
        $headers = [];

        foreach (explode("\r\n", $raw) as $line) {
            if (false !== strpos($line, ':')) {
                list ($name, $value) = explode(':', $line);
                $name = strtoupper(trim($name));

                $headers[$name] = trim($value);
            } else {
                $line = trim($line);

                if (!empty($line)) {
                    $headers[] = $line;
                }
            }
        }

        return $headers;
    }
}
