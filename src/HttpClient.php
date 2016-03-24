<?php

namespace Ruvents\HttpClient;

use Ruvents\HttpClient\Exception\InvalidArgumentException;
use Ruvents\HttpClient\Request\Request;
use Ruvents\HttpClient\Request\Uri;
use Ruvents\HttpClient\Response\Response;

/**
 * Class HttpClient
 * @package Ruvents\HttpClient
 */
class HttpClient
{
    const METHOD_GET = 'get';

    const METHOD_POST = 'post';

    /**
     * @param Request $request
     * @param string  $method
     * @throws InvalidArgumentException
     * @return Response
     */
    public static function send(Request $request, $method = self::METHOD_GET)
    {
        if (!in_array($method, self::getSupportedMethods(), true)) {
            throw new InvalidArgumentException(
                InvalidArgumentException::haystackMsg(self::getSupportedMethods())
            );
        }

        $ch = curl_init();

        switch ($method) {
            case self::METHOD_GET:
                $request->getUri()->addQueryParams($request->getDataArray());
                break;

            case self::METHOD_POST:
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getAllData());
                break;
        }

        $request->addHeader('Expect', '');

        curl_setopt_array($ch, [
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
     * @param Request|Uri|string $requestOrUri
     * @param null|string|array  $data
     * @param string[]           $headers
     * @return Response
     */
    public static function get($requestOrUri, $data = null, array $headers = [])
    {
        if ($requestOrUri instanceof Request) {
            $request = $requestOrUri;
        } else {
            $request = new Request($requestOrUri, $data, $headers);
        }

        return self::send($request, self::METHOD_GET);
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
        if ($requestOrUri instanceof Request) {
            $request = $requestOrUri;
        } else {
            $request = new Request($requestOrUri, $data, $headers, $files);
        }

        return self::send($request, self::METHOD_POST);
    }

    /**
     * @return array
     */
    public static function getSupportedMethods()
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
        ];
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
    private static function parseRawHeaders($raw)
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
