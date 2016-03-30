<?php

namespace Ruvents\HttpClient\Request;

/**
 * Class File
 */
class File extends \SplFileInfo
{
    /**
     * @var null|string
     */
    private $mimetype;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @param resource    $handle
     * @param null|string $mimetype
     * @param null|string $name
     * @return self
     */
    public static function getInstanceByResource($handle, $mimetype = null, $name = null)
    {
        $path = stream_get_meta_data($handle)['uri'];

        return new self($path, $mimetype, $name);
    }

    /**
     * @param string      $path     Path to the file
     * @param null|string $mimetype The MIME type
     * @param null|string $name     The name of the file (will replace the name from the $path)
     */
    public function __construct($path, $mimetype = null, $name = null)
    {
        parent::__construct($path);

        $this->mimetype = $mimetype;
        $this->name = $name;
    }

    /**
     * Returns the CURL representation of the file based on PHP version
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
     * @return null|string
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
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }
}
