<?php

namespace Ruvents\HttpClient\Response;

use Ruvents\HttpClient\Request\Request;

/**
 * Class Response
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
    private $headers = array();

    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @param string       $rawBody
     * @param int          $code
     * @param array        $headers
     * @param null|Request $request
     */
    public function __construct($rawBody, $code, array $headers = array(), Request $request = null)
    {
        $this->rawBody = $rawBody;
        $this->code = (int)$code;
        $this->headers = $headers;
        $this->request = $request;
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

    /**
     * @return null|Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
