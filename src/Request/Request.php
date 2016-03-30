<?php

namespace Ruvents\HttpClient\Request;

use Ruvents\HttpClient\Exception\InvalidArgumentException;
use Ruvents\HttpClient\Exception\RuntimeException;

/**
 * Class Request
 */
class Request
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var null|string|array
     */
    private $data;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * Request constructor.
     * @param Uri|string        $uri
     * @param null|string|array $data
     * @param string[]          $headers
     * @param File[]            $files deprecated 3.0.0
     * @throws InvalidArgumentException
     */
    public function __construct($uri, $data = null, array $headers = [], array $files = [])
    {
        if ($uri instanceof Uri) {
            $this->uri = $uri;
        } elseif (is_string($uri)) {
            $this->uri = Uri::createFromString($uri);
        } else {
            throw new InvalidArgumentException(
                InvalidArgumentException::typeMsg($uri, 'instance of Ruvents\HttpClient\Request\Uri or string')
            );
        }

        if (is_null($data) || is_array($data) || is_scalar($data)) {
            $this->data = $data;
        } else {
            throw new InvalidArgumentException(
                InvalidArgumentException::typeMsg($data, 'null, array or scalar')
            );
        }

        # TODO: remove in 3.0.0
        if ($files) {
            if (is_array($data)) {
                $this->data = array_replace_recursive($this->data, $files);
            } elseif (is_null($data)) {
                $this->data = $files;
            }
        }

        $this->headers = $headers;
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @throws RuntimeException
     * @return $this
     */
    public function addDataParam($name, $value)
    {
        if (!is_array($this->data)) {
            throw new RuntimeException('Data property of this Request was not configured to be an array!');
        }

        $this->data[$name] = $value;

        return $this;
    }

    /**
     * @return null|string|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string[]
     */
    public function getCurlHeaders()
    {
        $curlHeaders = [];

        foreach ($this->headers as $name => $value) {
            $curlHeaders[] = "$name: $value";
        }

        return $curlHeaders;
    }

    /**
     * @return array|null
     */
    public function getPostFields()
    {
        if (!is_array($this->data)) {
            return $this->data;
        }

        $fields = [];
        $this->buildPostFields($this->data, '', $fields);

        return $fields;
    }

    /**
     * @param mixed  $data
     * @param string $path
     * @param array  $fields
     */
    protected function buildPostFields($data, $path, array &$fields)
    {
        switch (true) {
            case is_array($data):
            case $data instanceof \Traversable:
                foreach ($data as $key => $value) {
                    $nextPath = empty($path) ? $key : $path."[$key]";
                    $this->buildPostFields($value, $nextPath, $fields);
                }
                break;

            case $data instanceof File:
                $fields[$path] = $data->getCurl();
                break;

            default:
                $fields[$path] = $data;
        }
    }
}
