<?php

namespace Ruvents\HttpClient\Response;

/**
 * Class Response
 * @package Ruvents\HttpClient\Response
 */
class Response
{
    /**
     * @var string
     */
    private $rawBody;

    /**
     * @var int
     */
    private $code;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @param string $rawBody
     * @param int    $code
     * @param array  $headers
     */
    public function __construct($rawBody, $code, array $headers)
    {
        $this->rawBody = $rawBody;
        $this->code = (int)$code;
        $this->headers = $headers;
    }

    /**
     * @param bool $assoc
     * @param int  $depth
     * @param int  $options
     * @return mixed
     */
    public function jsonDecode($assoc = false, $depth = 512, $options = 0)
    {
        $result = json_decode($this->getRawBody(), $assoc, $depth, $options);

        return $result;
    }

    /**
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        $name = strtoupper($name);

        return isset($this->headers[$name]);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string      $name
     * @param string|null $default
     * @return string|null
     */
    public function getHeader($name, $default = null)
    {
        $name = strtoupper($name);

        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        return $default;
    }
}
