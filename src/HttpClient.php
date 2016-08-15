<?php

namespace Ruvents\HttpClient;

use Ruvents\HttpClient\Exception\CurlException;
use Ruvents\HttpClient\Exception\RuntimeException;
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
    public static function get($requestOrUri, $queryData = null, array $headers = array())
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
    public static function post($requestOrUri, $data = null, array $headers = array(), array $files = array())
    {
        return self::send('POST', $requestOrUri, $data, $headers, $files);
    }

    /**
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $queryData
     * @param string[]           $headers
     * @return Response
     */
    public static function delete($requestOrUri, $queryData = null, array $headers = array())
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
    public static function put($requestOrUri, $data = null, array $headers = array(), array $files = array())
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
    public static function patch($requestOrUri, $data = null, array $headers = array(), array $files = array())
    {
        return self::send('PATCH', $requestOrUri, $data, $headers, $files);
    }

    /**
     * @param string             $method
     * @param Request|Uri|string $request
     * @param null|string|array  $data
     * @param string[]           $headers
     * @param array              $files
     * @throws RuntimeException|CurlException
     * @return Response
     */
    public static function send($method, $request, $data = null, array $headers = array(), array $files = array())
    {
        if (!in_array('curl', get_loaded_extensions())) {
            throw new RuntimeException('Http Client requires cURL PHP extension.');
        }

        $method = strtoupper($method);

        if (!$request instanceof Request) {
            $request = new Request($request, $data, $headers, $files);
        }

        $ch = curl_init();

        switch ($method) {
            case 'GET':
            case 'DELETE':
                $queryData = is_array($request->getData()) ? $request->getData() : array();
                $request->getUri()->addQueryParams($queryData);
                break;

            case 'POST':
            case 'PUT':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getPostFields());
                break;

            default:
                throw new RuntimeException(sprintf(
                    'Method "%s" is not supported',
                    $method
                ));
        }

        $request->addHeader('Expect', '');

        curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $request->getUri()->buildUri(),
            CURLOPT_HTTPHEADER => $request->getCurlHeaders(),
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ));

        $responseRaw = curl_exec($ch);

        if (false === $responseRaw) {
            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        $response = self::createResponse($ch, $responseRaw, $request);

        curl_close($ch);

        return $response;
    }

    /**
     * @param resource $ch
     * @param string   $raw
     * @param Request  $request
     * @return Response
     */
    protected static function createResponse($ch, $raw, $request)
    {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($raw, 0, $headerSize);
        $rawBody = substr($raw, $headerSize);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headers = self::parseRawHeaders($rawHeaders);

        return new Response($rawBody, $code, $headers, $request);
    }

    /**
     * @param string $raw
     * @return array
     */
    protected static function parseRawHeaders($raw)
    {
        $headers = array();

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
