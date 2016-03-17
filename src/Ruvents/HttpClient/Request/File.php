<?php

namespace Ruvents\HttpClient\Request;

/**
 * Class File
 * @package Ruvents\HttpClient\Request
 */
class File extends \SplFileInfo
{
    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $path     Path to the file
     * @param string $mimetype The MIME type
     * @param string $name     The name of the file (will replace the name from the $path)
     */
    public function __construct($path, $mimetype = null, $name = null)
    {
        parent::__construct($path);

        $this->mimetype = $mimetype;
        $this->name = $name;
    }

    /**
     * Returns the CURL representation of the file, based on PHP version
     * @return \CURLFile|string
     */
    public function getCurl()
    {
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            return new \CURLFile($this->getRealPath(), $this->mimetype, $this->name);
        }

        $curlFileString = '@'.$this->getRealPath()
            .(isset($this->name) ? ';filename='.$this->name : '')
            .(isset($this->mimetype) ? ';type='.$this->mimetype : '');

        return $curlFileString;
    }

    /**
     * @param string $mimetype
     * @return $this
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
