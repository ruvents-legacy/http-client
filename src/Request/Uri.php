<?php

namespace Ruvents\HttpClient\Request;

/**
 * Class Uri
 */
class Uri
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $query = [];

    /**
     * @var string
     */
    private $fragment;

    /**
     * @param string       $host
     * @param string       $path
     * @param array|string $query
     * @param bool         $secure
     * @return self
     */
    public static function createHttp($host, $path = '', $query = [], $secure = false)
    {
        $scheme = 'http'.($secure ? 's' : '');

        return new self($scheme, null, null, $host, null, $path, $query, null);
    }

    /**
     * @param string $uri
     * @return self
     */
    public static function createFromString($uri)
    {
        $parsedUri = parse_url($uri);

        return new self(
            isset($parsedUri['scheme']) ? $parsedUri['scheme'] : null,
            isset($parsedUri['user']) ? $parsedUri['user'] : null,
            isset($parsedUri['pass']) ? $parsedUri['pass'] : null,
            isset($parsedUri['host']) ? $parsedUri['host'] : null,
            isset($parsedUri['port']) ? $parsedUri['port'] : null,
            isset($parsedUri['path']) ? $parsedUri['path'] : null,
            isset($parsedUri['query']) ? $parsedUri['query'] : null,
            isset($parsedUri['fragment']) ? $parsedUri['fragment'] : null
        );
    }

    /**
     * @param string       $scheme
     * @param string       $user
     * @param string       $pass
     * @param string       $host
     * @param int          $port
     * @param string       $path
     * @param array|string $query
     * @param string       $fragment
     */
    public function __construct($scheme, $user, $pass, $host, $port, $path, $query, $fragment)
    {
        $this->scheme = $scheme;
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        if (is_array($query)) {
            $this->query = $query;
        } else {
            parse_str($query, $this->query);
        }
        $this->fragment = $fragment;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->buildUri();
    }

    /**
     * @return string
     */
    public function buildUri()
    {
        return http_build_url([
            'scheme' => $this->scheme,
            'user' => $this->user,
            'pass' => $this->pass,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'query' => $this->getQueryString(),
            'fragment' => $this->fragment,
        ]);
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $pass
     * @return $this
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = trim($host, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param int $port
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = '/'.ltrim($path, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addQueryParams(array $params)
    {
        $this->query = array_replace_recursive($this->query, $params);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function addQueryParam($name, $value)
    {
        $this->query[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasQueryParam($name)
    {
        return isset($this->query[$name]);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function removeQueryParam($name)
    {
        if (isset($this->query[$name])) {
            unset($this->query[$name]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    public function getQueryParam($name, $default = null)
    {
        if ($this->hasQueryParam($name)) {
            return $this->query[$name];
        }

        return $default;
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return empty($this->query) ? null : http_build_query($this->query);
    }

    /**
     * @param string $fragment
     * @return $this
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }
}
