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
     * @var null|string|array
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
     * @param Uri|string        $uri
     * @param null|string|array $data
     * @param string[]          $headers
     * @param array             $files
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
     * @return null|string|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getDataArray()
    {
        switch (true) {
            case empty($this->data):
                return [];
                break;

            case is_array($this->data):
                return $this->data;
                break;

            default:
                return ['data' => $this->data];
        }
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
     * @param string                 $name
     * @param \string|\resource|File $file
     * @return $this
     */
    public function addFile($name, $file)
    {
        switch (true) {
            case is_resource($file):
                $file = File::getInstanceByResource($file);
                break;

            case is_string($file):
                $file = new File($file);
                break;

            case $file instanceof File:
                break;

            default:
                throw new InvalidArgumentException(
                    InvalidArgumentException::typeMsg($file,
                        'string (path), resource or instance of Ruvents\HttpClient\Request\File')
                );
        }

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
     * @return string[]|\CURLFile[]
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
        if (empty($this->files)) {
            return $this->data;
        } else {
            return array_merge($this->getDataArray(), $this->getCurlFiles());
        }
    }
}
