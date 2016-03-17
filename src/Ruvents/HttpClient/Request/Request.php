<?php

namespace Ruvents\HttpClient\Request;

use Ruvents\HttpClient\Exception\InvalidArgumentException;
use Ruvents\HttpClient\Exception\RuntimeException;

/**
 * Class Request
 * @package Ruvents\HttpClient\Request
 */
class Request
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var array|string
     */
    private $data;

    /**
     * @var string[]
     */
    private $headers = [];

    /**
     * @var File[]
     */
    private $files = [];

    /**
     * Request constructor.
     * @param Uri          $uri
     * @param string|array $data
     * @param string[]     $headers
     * @param File[]       $files
     * @throws InvalidArgumentException
     */
    public function __construct(Uri $uri, $data = null, array $headers = [], array $files = [])
    {
        if (!isset($data) && !(is_array($data) || is_scalar($data))) {
            throw new InvalidArgumentException(
                InvalidArgumentException::typeMsg($data, 'array or scalar')
            );
        }

        $this->uri = $uri;
        $this->data = $data;
        $this->headers = $headers;

        foreach ($files as $name => $file) {
            $this->addFile($name, $file);
        }
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
     * @return array|string
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
     * @param string $name
     * @param File   $file
     * @return $this
     */
    public function addFile($name, File $file)
    {
        $this->files[$name] = $file;

        return $this;
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return \CURLFile[]|string[]
     */
    public function getCurlFiles()
    {
        $curlFiles = [];

        foreach ($this->files as $name => $file) {
            $curlFiles[$name] = $file->getCurl();
        }

        return $curlFiles;
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        return array_merge($this->data, $this->getCurlFiles());
    }
}
